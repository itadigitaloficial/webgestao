<?php

class Auth {
    public static function check(): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        return !empty($_SESSION['user']);
    }

    public static function user(): ?array {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        return $_SESSION['user'] ?? null;
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            header('Location: /?r=login');
            exit;
        }
    }

    public static function logout(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_destroy();
    }
}
