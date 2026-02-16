<?php

require_once __DIR__ . '/../config/database.php';

class Usuario {
    public static function findByEmail(string $email): ?array {
        $sql = "SELECT id, nome, email, senha, nivel_acesso, ativo, foto FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = db()->prepare($sql);
        $stmt->execute(['email' => $email]);
        $u = $stmt->fetch();
        return $u ?: null;
    }
}
