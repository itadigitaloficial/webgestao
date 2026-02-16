<?php

require_once __DIR__ . '/../config/database.php';

class Cliente
{
    /* =====================================================
       LISTAGEM
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
                COALESCE(SUM(CASE WHEN cs.ativo = 1 THEN 1 ELSE 0 END),0) AS servicos_ativos
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
        $stmt->execute([':id' => (int)$id]);
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
            ':id' => (int)$id
        ]);
    }

    public static function delete($id)
    {
        $stmt = db()->prepare("DELETE FROM clientes WHERE id = :id");
        return $stmt->execute([':id' => (int)$id]);
    }

    /* =====================================================
       SERVIÇOS
    ===================================================== */

    public static function getServicos($clienteId)
    {
        $stmt = db()->prepare("
            SELECT cs.*, s.nome, s.preco_venda
            FROM clientes_servicos cs
            JOIN servicos s ON s.id = cs.servico_id
            WHERE cs.cliente_id = :cliente
        ");
        $stmt->execute([':cliente' => (int)$clienteId]);
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
            ':cliente' => (int)$clienteId,
            ':servico' => (int)$servicoId
        ]);
    }

    public static function toggleServico($id)
    {
        db()->query("UPDATE clientes_servicos SET ativo = NOT ativo WHERE id = " . (int)$id);
    }

    public static function removeServico($id)
    {
        db()->query("DELETE FROM clientes_servicos WHERE id = " . (int)$id);
    }

    /* =====================================================
       CRM - ARQUIVOS
    ===================================================== */

    public static function arquivos($clienteId)
    {
        $stmt = db()->prepare("
            SELECT * FROM cliente_arquivos
            WHERE cliente_id = :id
            ORDER BY criado_em DESC
        ");
        $stmt->execute([':id' => (int)$clienteId]);
        return $stmt->fetchAll();
    }

    public static function addArquivo($clienteId, $nome, $descricao, $caminho)
    {
        $stmt = db()->prepare("
            INSERT INTO cliente_arquivos
            (cliente_id, nome_arquivo, descricao, caminho)
            VALUES (:cliente_id, :nome, :descricao, :caminho)
        ");

        return $stmt->execute([
            ':cliente_id' => (int)$clienteId,
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':caminho' => $caminho
        ]);
    }

    /* =====================================================
       CRM - COMENTÁRIOS
    ===================================================== */

    public static function comentarios($clienteId)
    {
        $stmt = db()->prepare("
            SELECT * FROM cliente_comentarios
            WHERE cliente_id = :id
            ORDER BY criado_em DESC
        ");
        $stmt->execute([':id' => (int)$clienteId]);
        return $stmt->fetchAll();
    }

    public static function addComentario($clienteId, $comentario)
    {
        $stmt = db()->prepare("
            INSERT INTO cliente_comentarios
            (cliente_id, comentario)
            VALUES (:cliente_id, :comentario)
        ");

        return $stmt->execute([
            ':cliente_id' => (int)$clienteId,
            ':comentario' => $comentario
        ]);
    }
}
