<?php

class Csrf {

    public static function token() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public static function check($token) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (
            !$token ||
            empty($_SESSION['_csrf']) ||
            !hash_equals($_SESSION['_csrf'], $token)
        ) {
            http_response_code(419);
            exit('CSRF inválido.');
        }
    }
}
