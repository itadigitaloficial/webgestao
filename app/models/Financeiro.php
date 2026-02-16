<?php

require_once __DIR__ . '/../config/database.php';

class Financeiro
{
    public static function resumo()
    {
        $pdo = db();

        $receitas = $pdo->query("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM movimentacoes
            WHERE tipo = 'receita'
            AND status = 'pago'
        ")->fetch()['total'];

        $despesas = $pdo->query("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM movimentacoes
            WHERE tipo = 'despesa'
            AND status = 'pago'
        ")->fetch()['total'];

        $pendente = $pdo->query("
            SELECT COALESCE(SUM(valor),0) AS total
            FROM movimentacoes
            WHERE status = 'pendente'
        ")->fetch()['total'];

        return [
            'receitas' => $receitas,
            'despesas' => $despesas,
            'pendente' => $pendente,
            'saldo' => $receitas - $despesas
        ];
    }

    public static function listar($tipo = '', $status = '')
    {
        $pdo = db();

        $where = " WHERE 1=1 ";
        $params = [];

        if ($tipo !== '') {
            $where .= " AND m.tipo = :tipo ";
            $params[':tipo'] = $tipo;
        }

        if ($status !== '') {
            $where .= " AND m.status = :status ";
            $params[':status'] = $status;
        }

        $sql = "
            SELECT m.*, 
                   c.nome AS cliente_nome,
                   os.numero AS os_numero,
                   cb.nome AS conta_nome
            FROM movimentacoes m
            LEFT JOIN clientes c ON c.id = m.cliente_id
            LEFT JOIN ordens_servico os ON os.id = m.ordem_id
            LEFT JOIN contas_bancarias cb ON cb.id = m.conta_id
            $where
            ORDER BY m.id DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function marcarPago($id)
    {
        $pdo = db();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT * FROM movimentacoes WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $mov = $stmt->fetch();

            if (!$mov || $mov['status'] === 'pago') {
                throw new Exception("Movimentação inválida.");
            }

            $stmt = $pdo->prepare("
                UPDATE movimentacoes
                SET status = 'pago',
                    data_pagamento = CURDATE()
                WHERE id = :id
            ");
            $stmt->execute([':id' => $id]);

            // Atualizar saldo da conta
            if ($mov['conta_id']) {

                if ($mov['tipo'] === 'receita') {
                    $sql = "UPDATE contas_bancarias 
                            SET saldo_atual = saldo_atual + :valor
                            WHERE id = :conta";
                } else {
                    $sql = "UPDATE contas_bancarias 
                            SET saldo_atual = saldo_atual - :valor
                            WHERE id = :conta";
                }

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':valor' => $mov['valor'],
                    ':conta' => $mov['conta_id']
                ]);
            }

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
