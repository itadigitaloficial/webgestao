<?php

require_once __DIR__ . '/../models/Financeiro.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';

class FinanceiroController
{
    private static function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function index()
    {
        Auth::requireLogin();
        self::start();

        $tipo = $_GET['tipo'] ?? '';
        $status = $_GET['status'] ?? '';

        $resumo = Financeiro::resumo();
        $movs = Financeiro::listar($tipo, $status);

        require __DIR__ . '/../views/financeiro/index.php';
    }

    public static function marcarPago()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $id = (int)$_POST['id'];

        Financeiro::marcarPago($id);

        header("Location: ?r=financeiro");
        exit;
    }
}
