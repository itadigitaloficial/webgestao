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
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $clientes = Cliente::paginate($search, $limit, $offset);
        $total    = Cliente::count($search);
        $pages    = (int)ceil($total / $limit);

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

        Cliente::create($_POST);

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

        $id = (int)($_POST['id'] ?? 0);
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
       GERENCIAR SERVIÇOS DO CLIENTE
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
        $todosServicos   = Servico::paginate('', '', '', 1000, 0);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/clientes/servicos.php';
    }

    public static function addServico()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        $servicoId = (int)($_POST['servico_id'] ?? 0);
        $usuarioId = $_SESSION['usuario']['id'] ?? null;

        if ($clienteId <= 0 || $servicoId <= 0) {
            self::flash('error', 'Dados inválidos para vincular serviço.');
            header("Location: ?r=clientes");
            exit;
        }

        Cliente::addServico($clienteId, $servicoId);

        // Se seu projeto já usa OS automática, mantém:
        if (method_exists('Cliente', 'gerarOrdemServicoAutomatica')) {
            Cliente::gerarOrdemServicoAutomatica($clienteId, $servicoId, $usuarioId);
        }

        self::flash('success', 'Serviço vinculado e OS criada automaticamente.');
        header("Location: ?r=clientes_servicos&id=" . $clienteId);
        exit;
    }

    public static function toggleServico()
    {
        Auth::requireLogin();

        $id        = (int)($_GET['id'] ?? 0);
        $clienteId = (int)($_GET['cliente'] ?? 0);

        if ($id > 0) {
            Cliente::toggleServico($id);
        }

        header("Location: ?r=clientes_servicos&id=" . $clienteId);
        exit;
    }

    public static function removeServico()
    {
        Auth::requireLogin();
        self::start();

        $id        = (int)($_GET['id'] ?? 0);
        $clienteId = (int)($_GET['cliente'] ?? 0);

        if ($id > 0) {
            Cliente::removeServico($id);
        }

        self::flash('success', 'Serviço removido.');
        header("Location: ?r=clientes_servicos&id=" . $clienteId);
        exit;
    }

    /* ============================
       CRM: DETALHE / ARQUIVOS / COMENTÁRIOS
    ============================ */

    public static function view()
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

        $arquivos    = method_exists('Cliente', 'arquivos') ? Cliente::arquivos($id) : [];
        $comentarios = method_exists('Cliente', 'comentarios') ? Cliente::comentarios($id) : [];

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/clientes/view.php';
    }

    public static function uploadArquivo()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $clienteId = (int)($_POST['cliente_id'] ?? 0);

        if ($clienteId <= 0) {
            self::flash('error', 'Cliente inválido.');
            header("Location: ?r=clientes");
            exit;
        }

        if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== 0) {
            self::flash('error', 'Erro no upload do arquivo.');
            header("Location: ?r=clientes_view&id={$clienteId}");
            exit;
        }

        $nome = trim($_POST['nome_arquivo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');

        $dir = __DIR__ . '/../../uploads/clientes/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $original = $_FILES['arquivo']['name'] ?? 'arquivo';
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $fileName = uniqid('cli_' . $clienteId . '_', true) . ($ext ? '.' . $ext : '');
        $destino = $dir . $fileName;

        if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
            self::flash('error', 'Falha ao salvar o arquivo.');
            header("Location: ?r=clientes_view&id={$clienteId}");
            exit;
        }

        if (method_exists('Cliente', 'addArquivo')) {
            Cliente::addArquivo(
                $clienteId,
                $nome !== '' ? $nome : $original,
                $descricao,
                'uploads/clientes/' . $fileName
            );
        }

        self::flash('success', 'Arquivo anexado com sucesso!');
        header("Location: ?r=clientes_view&id={$clienteId}");
        exit;
    }

    public static function addComentario()
    {
        Auth::requireLogin();
        self::start();
        Csrf::check($_POST['_csrf'] ?? null);

        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');

        if ($clienteId <= 0) {
            self::flash('error', 'Cliente inválido.');
            header("Location: ?r=clientes");
            exit;
        }

        if ($comentario !== '' && method_exists('Cliente', 'addComentario')) {
            Cliente::addComentario($clienteId, $comentario);
            self::flash('success', 'Comentário adicionado!');
        }

        header("Location: ?r=clientes_view&id={$clienteId}");
        exit;
    }
}
