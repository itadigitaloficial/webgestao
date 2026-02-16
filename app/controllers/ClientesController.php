<?php

require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Servico.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';

class ClientesController
{
    private static function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private static function flash($type, $msg)
    {
        self::start();
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    /* ============================
       LISTAGEM
    ============================ */

    public static function index()
    {
        Auth::requireLogin();
        self::start();

        $search = $_GET['search'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $clientes = Cliente::paginate($search, $limit, $offset);
        $total = Cliente::count($search);
        $pages = ceil($total / $limit);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/clientes/index.php';
    }

    /* ============================
       NOVO
    ============================ */

    public static function create()
    {
        Auth::requireLogin();
        self::start();

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/clientes/form.php';
    }

    /* ============================
       SALVAR
    ============================ */

    public static function store()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $clienteId = Cliente::create($_POST);

        self::flash('success', 'Cliente cadastrado com sucesso!');
        header("Location: ?r=clientes");
        exit;
    }

    /* ============================
       EDITAR
    ============================ */

    public static function edit()
    {
        Auth::requireLogin();
        self::start();

        $id = (int)($_GET['id'] ?? 0);
        $cliente = Cliente::find($id);

        if (!$cliente) {
            self::flash('error', 'Cliente não encontrado.');
            header("Location: ?r=clientes");
            exit;
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/clientes/form.php';
    }

    /* ============================
       ATUALIZAR
    ============================ */

    public static function update()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $id = (int)$_POST['id'];

        Cliente::update($id, $_POST);

        self::flash('success', 'Cliente atualizado com sucesso!');
        header("Location: ?r=clientes");
        exit;
    }

    /* ============================
       EXCLUIR
    ============================ */

    public static function delete()
    {
        Auth::requireLogin();
        self::start();

        $id = (int)($_GET['id'] ?? 0);

        Cliente::delete($id);

        self::flash('success', 'Cliente excluído com sucesso!');
        header("Location: ?r=clientes");
        exit;
    }

    /* ============================
       GERENCIAR SERVIÇOS
    ============================ */

    public static function servicos()
    {
        Auth::requireLogin();
        self::start();

        $clienteId = (int)($_GET['id'] ?? 0);
        $cliente = Cliente::find($clienteId);

        if (!$cliente) {
            self::flash('error', 'Cliente não encontrado.');
            header("Location: ?r=clientes");
            exit;
        }

        $servicosCliente = Cliente::getServicos($clienteId);
        $todosServicos = Servico::paginate('', '', '', 1000, 0);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/clientes/servicos.php';
    }

    public static function addServico()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $clienteId = (int)$_POST['cliente_id'];
        $servicoId = (int)$_POST['servico_id'];
        $usuarioId = $_SESSION['usuario']['id'] ?? null;

        Cliente::addServico($clienteId, $servicoId);
        Cliente::gerarOrdemServicoAutomatica($clienteId, $servicoId, $usuarioId);

        self::flash('success', 'Serviço vinculado e OS criada automaticamente.');

        header("Location: ?r=clientes_servicos&id=" . $clienteId);
        exit;
    }

    public static function toggleServico()
    {
        Auth::requireLogin();

        $id = (int)$_GET['id'];
        $clienteId = (int)$_GET['cliente'];

        Cliente::toggleServico($id);

        header("Location: ?r=clientes_servicos&id=" . $clienteId);
        exit;
    }

    public static function removeServico()
    {
        Auth::requireLogin();
        self::start();

        $id = (int)$_GET['id'];
        $clienteId = (int)$_GET['cliente'];

        Cliente::removeServico($id);

        self::flash('success', 'Serviço removido.');

        header("Location: ?r=clientes_servicos&id=" . $clienteId);
        exit;
    }
}
