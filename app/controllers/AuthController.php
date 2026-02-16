<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../core/Csrf.php';

class AuthController {
    public static function showLogin(): void {
        require __DIR__ . '/../views/auth/login.php';
    }

    public static function login(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        Csrf::check($_POST['_csrf'] ?? null);

        $email = trim((string)($_POST['email'] ?? ''));
        $senha = (string)($_POST['senha'] ?? '');

        $user = Usuario::findByEmail($email);

        if (!$user || (int)$user['ativo'] !== 1 || !password_verify($senha, $user['senha'])) {
            $_SESSION['flash_error'] = 'E-mail ou senha inválidos.';
            header('Location: /?r=login');
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'nome' => $user['nome'],
            'email' => $user['email'],
            'nivel' => $user['nivel_acesso'],
            'foto' => $user['foto'],
        ];

        header('Location: /?r=dashboard');
        exit;
    }
}
