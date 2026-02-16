<?php

require_once __DIR__ . '/../config/database.php';

class OrdemServico
{
    /* =====================================================
       LISTAGEM
    ===================================================== */

    public static function paginate($status = '', $limit = 15, $offset = 0)
    {
        $where = '';
        $params = [];

        if ($status !== '') {
            $where = " WHERE os.status = :status ";
            $params[':status'] = $status;
        }

        $sql = "SELECT
                    os.*,
                    c.nome AS cliente_nome,
                    u.nome AS usuario_nome
                FROM ordens_servico os
                JOIN clientes c ON c.id = os.cliente_id
                LEFT JOIN usuarios u ON u.id = os.usuario_id
                {$where}
                ORDER BY os.id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = db()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function count($status = '')
    {
        if ($status !== '') {
            $stmt = db()->prepare("SELECT COUNT(*) as total FROM ordens_servico WHERE status = :status");
            $stmt->execute([':status' => $status]);
        } else {
            $stmt = db()->query("SELECT COUNT(*) as total FROM ordens_servico");
        }

        $r = $stmt->fetch();
        return (int)($r['total'] ?? 0);
    }

    /* =====================================================
       DETALHE
    ===================================================== */

    public static function findWithRelations($id)
    {
        $sql = "SELECT
                    os.*,
                    c.nome AS cliente_nome,
                    c.email AS cliente_email,
                    c.telefone AS cliente_telefone,
                    c.celular AS cliente_celular,
                    u.nome AS usuario_nome
                FROM ordens_servico os
                JOIN clientes c ON c.id = os.cliente_id
                LEFT JOIN usuarios u ON u.id = os.usuario_id
                WHERE os.id = :id
                LIMIT 1";

        $stmt = db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function items($ordemId)
    {
        $sql = "SELECT
                    io.*,
                    s.nome AS servico_nome,
                    s.tipo AS servico_tipo,
                    s.unidade AS servico_unidade
                FROM itens_ordem io
                JOIN servicos s ON s.id = io.servico_id
                WHERE io.ordem_id = :ordem
                ORDER BY io.id ASC";

        $stmt = db()->prepare($sql);
        $stmt->bindValue(':ordem', (int)$ordemId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* =====================================================
       CRIAR OS MANUAL
    ===================================================== */

    public static function create($clienteId, $usuarioId, $obs = null)
    {
        $pdo = db();
        $numero = 'OS-' . date('YmdHis') . '-' . random_int(100, 999);

        $sql = "INSERT INTO ordens_servico
                (numero, cliente_id, usuario_id, data_abertura, status,
                 valor_total, desconto, valor_final, observacoes)
                VALUES
                (:numero, :cliente_id, :usuario_id, CURDATE(), 'aberta',
                 0.00, 0.00, 0.00, :obs)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':numero' => $numero,
            ':cliente_id' => (int)$clienteId,
            ':usuario_id' => $usuarioId ? (int)$usuarioId : null,
            ':obs' => $obs
        ]);

        return (int)$pdo->lastInsertId();
    }

    /* =====================================================
       ATUALIZAR STATUS + GERAR RECEITA AUTOMÁTICA
    ===================================================== */

    public static function updateStatus($id, $status)
    {
        $allowed = ['aberta', 'em_andamento', 'concluida', 'cancelada'];
        if (!in_array($status, $allowed, true)) return false;

        $pdo = db();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT * FROM ordens_servico WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $os = $stmt->fetch();

            if (!$os) throw new Exception("OS não encontrada");

            $dataConclusao = null;
            if ($status === 'concluida') {
                $dataConclusao = date('Y-m-d');
            }

            $stmt = $pdo->prepare("
                UPDATE ordens_servico
                SET status = :status,
                    data_conclusao = :data_conclusao
                WHERE id = :id
            ");

            $stmt->execute([
                ':status' => $status,
                ':data_conclusao' => $dataConclusao,
                ':id' => $id
            ]);

            /* GERAR MOVIMENTAÇÃO FINANCEIRA */
            if ($status === 'concluida' && $os['status'] !== 'concluida') {

                $check = $pdo->prepare("
                    SELECT id FROM movimentacoes
                    WHERE ordem_id = :ordem AND tipo = 'receita'
                    LIMIT 1
                ");
                $check->execute([':ordem' => $id]);

                if (!$check->fetch()) {

                    $cat = $pdo->query("
                        SELECT id FROM categorias_financeiras
                        WHERE tipo = 'receita' AND ativo = 1
                        LIMIT 1
                    ")->fetch();

                    $conta = $pdo->query("
                        SELECT id FROM contas_bancarias
                        WHERE ativo = 1
                        LIMIT 1
                    ")->fetch();

                    if ($cat && $conta) {

                        $stmt = $pdo->prepare("
                            INSERT INTO movimentacoes
                            (tipo, categoria_id, conta_id, cliente_id, ordem_id,
                             descricao, valor, data_vencimento, status, forma_pagamento)
                            VALUES
                            ('receita', :categoria, :conta, :cliente, :ordem,
                             :descricao, :valor, CURDATE(), 'pendente', 'dinheiro')
                        ");

                        $stmt->execute([
                            ':categoria' => $cat['id'],
                            ':conta' => $conta['id'],
                            ':cliente' => $os['cliente_id'],
                            ':ordem' => $id,
                            ':descricao' => 'Receita referente à OS ' . $os['numero'],
                            ':valor' => $os['valor_final']
                        ]);
                    }
                }
            }

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    /* =====================================================
       ADICIONAR ITEM + RECALCULAR
    ===================================================== */

    public static function addItem($ordemId, $servicoId, $quantidade = 1)
    {
        $pdo = db();
        $ordemId = (int)$ordemId;
        $servicoId = (int)$servicoId;
        $qtd = max(1, (float)$quantidade);

        try {
            $pdo->beginTransaction();

            $st = $pdo->prepare("SELECT preco_venda, descricao FROM servicos WHERE id = :id");
            $st->execute([':id' => $servicoId]);
            $s = $st->fetch();
            if (!$s) throw new Exception("Serviço não encontrado");

            $preco = (float)$s['preco_venda'];
            $subtotal = $qtd * $preco;

            $stmt = $pdo->prepare("
                INSERT INTO itens_ordem
                (ordem_id, servico_id, quantidade, preco_unitario, subtotal, descricao)
                VALUES
                (:ordem, :servico, :qtd, :preco, :subtotal, :descricao)
            ");

            $stmt->execute([
                ':ordem' => $ordemId,
                ':servico' => $servicoId,
                ':qtd' => $qtd,
                ':preco' => $preco,
                ':subtotal' => $subtotal,
                ':descricao' => $s['descricao']
            ]);

            self::recalcularTotais($ordemId, $pdo);

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    public static function removeItem($ordemId, $itemId)
    {
        $pdo = db();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("DELETE FROM itens_ordem WHERE id = :id AND ordem_id = :ordem");
            $stmt->execute([':id' => $itemId, ':ordem' => $ordemId]);

            self::recalcularTotais($ordemId, $pdo);

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    public static function recalcularTotais($ordemId, $pdo = null)
    {
        $pdo = $pdo ?: db();

        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(subtotal),0) AS total
            FROM itens_ordem
            WHERE ordem_id = :ordem
        ");
        $stmt->execute([':ordem' => $ordemId]);
        $total = (float)($stmt->fetch()['total'] ?? 0);

        $stmt = $pdo->prepare("SELECT desconto FROM ordens_servico WHERE id = :id");
        $stmt->execute([':id' => $ordemId]);
        $desconto = (float)($stmt->fetch()['desconto'] ?? 0);

        $final = max(0, $total - $desconto);

        $up = $pdo->prepare("
            UPDATE ordens_servico
            SET valor_total = :t,
                valor_final = :f
            WHERE id = :id
        ");

        return $up->execute([
            ':t' => $total,
            ':f' => $final,
            ':id' => $ordemId
        ]);
    }
}
