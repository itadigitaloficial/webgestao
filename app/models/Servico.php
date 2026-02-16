<?php

require_once __DIR__ . '/../config/database.php';

class Servico
{
    public static function paginate($search = '', $tipo = '', $categoriaId = '', $limit = 10, $offset = 0)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if ($search !== '') {
            $where .= " AND (s.nome LIKE :s1 OR s.descricao LIKE :s2) ";
            $params[':s1'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
        }

        if ($tipo !== '') {
            $where .= " AND s.tipo = :tipo ";
            $params[':tipo'] = $tipo;
        }

        if ($categoriaId !== '') {
            $where .= " AND s.categoria_id = :cat ";
            $params[':cat'] = (int)$categoriaId;
        }

        $sql = "SELECT
                    s.*,
                    c.nome AS categoria_nome,
                    c.cor  AS categoria_cor
                FROM servicos s
                LEFT JOIN categorias_servicos c ON c.id = s.categoria_id
                {$where}
                ORDER BY s.id DESC
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

    public static function count($search = '', $tipo = '', $categoriaId = '')
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if ($search !== '') {
            $where .= " AND (nome LIKE :s1 OR descricao LIKE :s2) ";
            $params[':s1'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
        }

        if ($tipo !== '') {
            $where .= " AND tipo = :tipo ";
            $params[':tipo'] = $tipo;
        }

        if ($categoriaId !== '') {
            $where .= " AND categoria_id = :cat ";
            $params[':cat'] = (int)$categoriaId;
        }

        $sql = "SELECT COUNT(*) as total FROM servicos {$where}";
        $stmt = db()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        $r = $stmt->fetch();
        return (int)($r['total'] ?? 0);
    }

    public static function find($id)
    {
        $stmt = db()->prepare("SELECT * FROM servicos WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $sql = "INSERT INTO servicos
                (categoria_id, nome, descricao, tipo, preco_venda, preco_custo, estoque, unidade, ativo)
                VALUES
                (:categoria_id, :nome, :descricao, :tipo, :preco_venda, :preco_custo, :estoque, :unidade, :ativo)";

        $stmt = db()->prepare($sql);

        return $stmt->execute([
            ':categoria_id' => $data['categoria_id'] !== '' ? (int)$data['categoria_id'] : null,
            ':nome' => $data['nome'],
            ':descricao' => $data['descricao'],
            ':tipo' => $data['tipo'],
            ':preco_venda' => $data['preco_venda'],
            ':preco_custo' => $data['preco_custo'],
            ':estoque' => (int)$data['estoque'],
            ':unidade' => $data['unidade'],
            ':ativo' => (int)$data['ativo'],
        ]);
    }

    public static function update($id, $data)
    {
        $sql = "UPDATE servicos SET
                categoria_id = :categoria_id,
                nome = :nome,
                descricao = :descricao,
                tipo = :tipo,
                preco_venda = :preco_venda,
                preco_custo = :preco_custo,
                estoque = :estoque,
                unidade = :unidade,
                ativo = :ativo
                WHERE id = :id";

        $stmt = db()->prepare($sql);

        return $stmt->execute([
            ':categoria_id' => $data['categoria_id'] !== '' ? (int)$data['categoria_id'] : null,
            ':nome' => $data['nome'],
            ':descricao' => $data['descricao'],
            ':tipo' => $data['tipo'],
            ':preco_venda' => $data['preco_venda'],
            ':preco_custo' => $data['preco_custo'],
            ':estoque' => (int)$data['estoque'],
            ':unidade' => $data['unidade'],
            ':ativo' => (int)$data['ativo'],
            ':id' => (int)$id,
        ]);
    }

    public static function delete($id)
    {
        $stmt = db()->prepare("DELETE FROM servicos WHERE id = :id");
        return $stmt->execute([':id' => (int)$id]);
    }
}
