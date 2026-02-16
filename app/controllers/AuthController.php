<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../core/Auth.php';

class AuthController
{
    // GET: exibe a tela de login
    public static function login(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        require __DIR__ . '/../views/auth/login.php';
    }

    // POST: processa login com CSRF
    public static function loginPost(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        Csrf::check($_POST['_csrf'] ?? null);

        $email = trim((string)($_POST['email'] ?? ''));
        $senha = (string)($_POST['senha'] ?? '');

        $user = Usuario::findByEmail($email);

        if (
            !$user ||
            (int)$user['ativo'] !== 1 ||
            !password_verify($senha, $user['senha'])
        ) {
            $_SESSION['flash_error'] = 'E-mail ou senha inválidos.';
            header('Location: /?r=login');
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'    => (int)$user['id'],
            'nome'  => $user['nome'],
            'email' => $user['email'],
            'nivel' => $user['nivel_acesso'],
            'foto'  => $user['foto'],
        ];

        header('Location: /?r=dashboard');
        exit;
    }

    public static function logout(): void
    {
        Auth::logout();
        header('Location: /?r=login');
        exit;
    }
}
