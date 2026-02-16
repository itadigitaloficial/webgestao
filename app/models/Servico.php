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

    /* =====================================================
       CREATE COMPLETO PROFISSIONAL
    ===================================================== */

    public static function create($data)
    {
        $sql = "INSERT INTO servicos
        (
            categoria_id, codigo, codigo_barras,
            nome, descricao, tipo,
            preco_venda, preco_promocional, preco_custo,
            margem_lucro, comissao, imposto_percentual,
            estoque, estoque_minimo, unidade,
            tempo_execucao, garantia_dias,
            fornecedor, observacoes,
            permite_sem_estoque, ativo
        )
        VALUES
        (
            :categoria_id, :codigo, :codigo_barras,
            :nome, :descricao, :tipo,
            :preco_venda, :preco_promocional, :preco_custo,
            :margem_lucro, :comissao, :imposto_percentual,
            :estoque, :estoque_minimo, :unidade,
            :tempo_execucao, :garantia_dias,
            :fornecedor, :observacoes,
            :permite_sem_estoque, :ativo
        )";

        $stmt = db()->prepare($sql);

        return $stmt->execute([
            ':categoria_id' => $data['categoria_id'] !== '' ? (int)$data['categoria_id'] : null,
            ':codigo' => $data['codigo'] ?? null,
            ':codigo_barras' => $data['codigo_barras'] ?? null,
            ':nome' => $data['nome'],
            ':descricao' => $data['descricao'],
            ':tipo' => $data['tipo'],
            ':preco_venda' => $data['preco_venda'],
            ':preco_promocional' => $data['preco_promocional'] !== '' ? $data['preco_promocional'] : null,
            ':preco_custo' => $data['preco_custo'],
            ':margem_lucro' => $data['margem_lucro'] !== '' ? $data['margem_lucro'] : null,
            ':comissao' => $data['comissao'] !== '' ? $data['comissao'] : null,
            ':imposto_percentual' => $data['imposto_percentual'] !== '' ? $data['imposto_percentual'] : null,
            ':estoque' => (int)$data['estoque'],
            ':estoque_minimo' => $data['estoque_minimo'] !== '' ? (int)$data['estoque_minimo'] : null,
            ':unidade' => $data['unidade'],
            ':tempo_execucao' => $data['tempo_execucao'] !== '' ? (int)$data['tempo_execucao'] : null,
            ':garantia_dias' => $data['garantia_dias'] !== '' ? (int)$data['garantia_dias'] : null,
            ':fornecedor' => $data['fornecedor'] ?? null,
            ':observacoes' => $data['observacoes'] ?? null,
            ':permite_sem_estoque' => isset($data['permite_sem_estoque']) ? 1 : 0,
            ':ativo' => (int)$data['ativo'],
        ]);
    }

    /* =====================================================
       UPDATE COMPLETO PROFISSIONAL
    ===================================================== */

    public static function update($id, $data)
    {
        $sql = "UPDATE servicos SET
            categoria_id = :categoria_id,
            codigo = :codigo,
            codigo_barras = :codigo_barras,
            nome = :nome,
            descricao = :descricao,
            tipo = :tipo,
            preco_venda = :preco_venda,
            preco_promocional = :preco_promocional,
            preco_custo = :preco_custo,
            margem_lucro = :margem_lucro,
            comissao = :comissao,
            imposto_percentual = :imposto_percentual,
            estoque = :estoque,
            estoque_minimo = :estoque_minimo,
            unidade = :unidade,
            tempo_execucao = :tempo_execucao,
            garantia_dias = :garantia_dias,
            fornecedor = :fornecedor,
            observacoes = :observacoes,
            permite_sem_estoque = :permite_sem_estoque,
            ativo = :ativo
        WHERE id = :id";

        $stmt = db()->prepare($sql);

        return $stmt->execute([
            ':categoria_id' => $data['categoria_id'] !== '' ? (int)$data['categoria_id'] : null,
            ':codigo' => $data['codigo'] ?? null,
            ':codigo_barras' => $data['codigo_barras'] ?? null,
            ':nome' => $data['nome'],
            ':descricao' => $data['descricao'],
            ':tipo' => $data['tipo'],
            ':preco_venda' => $data['preco_venda'],
            ':preco_promocional' => $data['preco_promocional'] !== '' ? $data['preco_promocional'] : null,
            ':preco_custo' => $data['preco_custo'],
            ':margem_lucro' => $data['margem_lucro'] !== '' ? $data['margem_lucro'] : null,
            ':comissao' => $data['comissao'] !== '' ? $data['comissao'] : null,
            ':imposto_percentual' => $data['imposto_percentual'] !== '' ? $data['imposto_percentual'] : null,
            ':estoque' => (int)$data['estoque'],
            ':estoque_minimo' => $data['estoque_minimo'] !== '' ? (int)$data['estoque_minimo'] : null,
            ':unidade' => $data['unidade'],
            ':tempo_execucao' => $data['tempo_execucao'] !== '' ? (int)$data['tempo_execucao'] : null,
            ':garantia_dias' => $data['garantia_dias'] !== '' ? (int)$data['garantia_dias'] : null,
            ':fornecedor' => $data['fornecedor'] ?? null,
            ':observacoes' => $data['observacoes'] ?? null,
            ':permite_sem_estoque' => isset($data['permite_sem_estoque']) ? 1 : 0,
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
