<?php

require_once __DIR__ . '/../config/database.php';

class CategoriaServico
{
    public static function allActive()
    {
        $stmt = db()->query("SELECT id, nome, cor FROM categorias_servicos WHERE ativo = 1 ORDER BY nome ASC");
        return $stmt->fetchAll();
    }
}
