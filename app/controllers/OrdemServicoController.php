<?php

require_once __DIR__ . '/../models/OrdemServico.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Servico.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';

class OrdemServicoController
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

    /* =====================================================
       LISTAGEM
    ===================================================== */

    public static function index()
    {
        Auth::requireLogin();
        self::start();

        $status = $_GET['status'] ?? '';

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $ordens = OrdemServico::paginate($status, $limit, $offset);
        $total = OrdemServico::count($status);
        $pages = ceil($total / $limit);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/os/index.php';
    }

    /* =====================================================
       DETALHE
    ===================================================== */

    public static function detalhe()
    {
        Auth::requireLogin();
        self::start();

        $id = (int)($_GET['id'] ?? 0);

        $os = OrdemServico::findWithRelations($id);

        if (!$os) {
            self::flash('error', 'Ordem não encontrada.');
            header("Location: ?r=os");
            exit;
        }

        $itens = OrdemServico::items($id);
        $servicos = Servico::paginate('', '', '', 1000, 0);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/os/detalhe.php';
    }

    /* =====================================================
       NOVA OS
    ===================================================== */

    public static function create()
    {
        Auth::requireLogin();
        self::start();

        $clientes = Cliente::paginate('', 1000, 0);

        require __DIR__ . '/../views/os/form.php';
    }

    public static function store()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $clienteId = (int)$_POST['cliente_id'];
        $usuarioId = $_SESSION['usuario']['id'] ?? null;
        $obs = $_POST['observacoes'] ?? null;

        OrdemServico::create($clienteId, $usuarioId, $obs);

        self::flash('success', 'OS criada com sucesso.');
        header("Location: ?r=os");
        exit;
    }

    /* =====================================================
       ITENS
    ===================================================== */

    public static function addItem()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $ordemId = (int)$_POST['ordem_id'];
        $servicoId = (int)$_POST['servico_id'];
        $quantidade = (float)$_POST['quantidade'];

        OrdemServico::addItem($ordemId, $servicoId, $quantidade);

        header("Location: ?r=os_detalhe&id=" . $ordemId);
        exit;
    }

    public static function removeItem()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $ordemId = (int)$_POST['ordem_id'];
        $itemId = (int)$_POST['item_id'];

        OrdemServico::removeItem($ordemId, $itemId);

        header("Location: ?r=os_detalhe&id=" . $ordemId);
        exit;
    }

    /* =====================================================
       STATUS
    ===================================================== */

    public static function updateStatus()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $id = (int)$_POST['id'];
        $status = $_POST['status'];

        OrdemServico::updateStatus($id, $status);

        header("Location: ?r=os_detalhe&id=" . $id);
        exit;
    }

    /* =====================================================
       DELETE
    ===================================================== */

    public static function delete()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $id = (int)$_POST['id'];

        OrdemServico::delete($id);

        self::flash('success', 'OS removida com sucesso.');
        header("Location: ?r=os");
        exit;
    }
}
