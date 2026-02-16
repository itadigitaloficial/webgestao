<?php

require_once __DIR__ . '/../config/database.php';

class Cliente
{
    /* =====================================================
       LISTAGEM COM CONTAGEM DE SERVIÇOS
    ===================================================== */

    public static function paginate($search = '', $limit = 10, $offset = 0)
    {
        $pdo = db();

        $where = " WHERE 1=1 ";
        $params = [];

        if ($search !== '') {
            $where .= " AND (c.nome LIKE :search 
                        OR c.cpf_cnpj LIKE :search 
                        OR c.email LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $sql = "
            SELECT 
                c.*,

                COUNT(cs.id) AS total_servicos,

                COALESCE(
                    SUM(
                        CASE 
                            WHEN cs.ativo = 1 THEN 1 
                            ELSE 0 
                        END
                    ), 0
                ) AS servicos_ativos

            FROM clientes c

            LEFT JOIN clientes_servicos cs 
                ON cs.cliente_id = c.id

            {$where}

            GROUP BY c.id

            ORDER BY c.id DESC

            LIMIT :limit OFFSET :offset
        ";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function count($search = '')
    {
        $pdo = db();

        $where = " WHERE 1=1 ";
        $params = [];

        if ($search !== '') {
            $where .= " AND (nome LIKE :search 
                        OR cpf_cnpj LIKE :search 
                        OR email LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clientes {$where}");
        $stmt->execute($params);

        return (int)($stmt->fetch()['total'] ?? 0);
    }

    /* =====================================================
       CRUD
    ===================================================== */

    public static function find($id)
    {
        $stmt = db()->prepare("SELECT * FROM clientes WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $pdo = db();

        $sql = "INSERT INTO clientes
            (tipo_pessoa, nome, cpf_cnpj, email, telefone, celular,
             endereco, numero, complemento, bairro, cidade, estado, cep, observacoes)
            VALUES
            (:tipo_pessoa, :nome, :cpf_cnpj, :email, :telefone, :celular,
             :endereco, :numero, :complemento, :bairro, :cidade, :estado, :cep, :observacoes)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':tipo_pessoa' => $data['tipo_pessoa'] ?? 'fisica',
            ':nome' => $data['nome'],
            ':cpf_cnpj' => $data['cpf_cnpj'] ?? null,
            ':email' => $data['email'] ?? null,
            ':telefone' => $data['telefone'] ?? null,
            ':celular' => $data['celular'] ?? null,
            ':endereco' => $data['endereco'] ?? null,
            ':numero' => $data['numero'] ?? null,
            ':complemento' => $data['complemento'] ?? null,
            ':bairro' => $data['bairro'] ?? null,
            ':cidade' => $data['cidade'] ?? null,
            ':estado' => $data['estado'] ?? null,
            ':cep' => $data['cep'] ?? null,
            ':observacoes' => $data['observacoes'] ?? null
        ]);

        return $pdo->lastInsertId();
    }

    public static function update($id, $data)
    {
        $pdo = db();

        $sql = "UPDATE clientes SET
                tipo_pessoa = :tipo_pessoa,
                nome = :nome,
                cpf_cnpj = :cpf_cnpj,
                email = :email,
                telefone = :telefone,
                celular = :celular,
                endereco = :endereco,
                numero = :numero,
                complemento = :complemento,
                bairro = :bairro,
                cidade = :cidade,
                estado = :estado,
                cep = :cep,
                observacoes = :observacoes
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':tipo_pessoa' => $data['tipo_pessoa'],
            ':nome' => $data['nome'],
            ':cpf_cnpj' => $data['cpf_cnpj'] ?? null,
            ':email' => $data['email'] ?? null,
            ':telefone' => $data['telefone'] ?? null,
            ':celular' => $data['celular'] ?? null,
            ':endereco' => $data['endereco'] ?? null,
            ':numero' => $data['numero'] ?? null,
            ':complemento' => $data['complemento'] ?? null,
            ':bairro' => $data['bairro'] ?? null,
            ':cidade' => $data['cidade'] ?? null,
            ':estado' => $data['estado'] ?? null,
            ':cep' => $data['cep'] ?? null,
            ':observacoes' => $data['observacoes'] ?? null,
            ':id' => $id
        ]);
    }

    /* =====================================================
       DELETE SEGURO
    ===================================================== */

    public static function delete($id)
    {
        $pdo = db();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    /* =====================================================
       SERVIÇOS DO CLIENTE
    ===================================================== */

    public static function getServicos($clienteId)
    {
        $stmt = db()->prepare("
            SELECT cs.*, s.nome, s.preco_venda
            FROM clientes_servicos cs
            JOIN servicos s ON s.id = cs.servico_id
            WHERE cs.cliente_id = :cliente
        ");
        $stmt->execute([':cliente' => $clienteId]);
        return $stmt->fetchAll();
    }

    public static function addServico($clienteId, $servicoId)
    {
        $stmt = db()->prepare("
            INSERT INTO clientes_servicos
            (cliente_id, servico_id, ativo)
            VALUES (:cliente, :servico, 1)
        ");
        $stmt->execute([
            ':cliente' => $clienteId,
            ':servico' => $servicoId
        ]);
    }

    public static function toggleServico($id)
    {
        db()->query("UPDATE clientes_servicos 
                     SET ativo = NOT ativo 
                     WHERE id = " . (int)$id);
    }

    public static function removeServico($id)
    {
        db()->query("DELETE FROM clientes_servicos 
                     WHERE id = " . (int)$id);
    }

    /* =====================================================
       GERAR OS AUTOMÁTICA
    ===================================================== */

    public static function gerarOrdemServicoAutomatica($clienteId, $servicoId, $usuarioId)
    {
        $pdo = db();

        $servico = $pdo->prepare("SELECT * FROM servicos WHERE id = :id");
        $servico->execute([':id' => $servicoId]);
        $servico = $servico->fetch();

        if (!$servico) return false;

        $numero = 'OS-' . date('YmdHis') . '-' . rand(100,999);

        $stmt = $pdo->prepare("
            INSERT INTO ordens_servico
            (numero, cliente_id, usuario_id, data_abertura, status,
             valor_total, valor_final)
            VALUES
            (:numero, :cliente, :usuario, CURDATE(), 'aberta',
             :valor, :valor)
        ");

        $stmt->execute([
            ':numero' => $numero,
            ':cliente' => $clienteId,
            ':usuario' => $usuarioId,
            ':valor' => $servico['preco_venda']
        ]);

        $ordemId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
            INSERT INTO itens_ordem
            (ordem_id, servico_id, quantidade, preco_unitario, subtotal)
            VALUES
            (:ordem, :servico, 1, :preco, :preco)
        ");

        $stmt->execute([
            ':ordem' => $ordemId,
            ':servico' => $servicoId,
            ':preco' => $servico['preco_venda']
        ]);

        return true;
    }
}
