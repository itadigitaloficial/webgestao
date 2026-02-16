<?php

require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Servico.php';
require_once __DIR__ . '/../models/CategoriaServico.php';

class ServicosController
{
    private static function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }

    private static function flash($type, $msg)
    {
        self::start();
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    private static function moneyToDb($v)
    {
        // Aceita "1.234,56" ou "1234.56" e converte para "1234.56"
        $v = trim((string)$v);
        if ($v === '') return '0.00';

        $v = str_replace(['R$', ' '], '', $v);
        $hasComma = strpos($v, ',') !== false;

        if ($hasComma) {
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
        }

        $v = preg_replace('/[^0-9.]/', '', $v);
        if ($v === '' || $v === '.') return '0.00';

        return number_format((float)$v, 2, '.', '');
    }

    private static function clean($data)
    {
        $out = [];
        foreach ($data as $k => $v) {
            $out[$k] = is_string($v) ? trim($v) : $v;
        }

        $out['nome'] = $out['nome'] ?? '';
        $out['descricao'] = $out['descricao'] ?? null;

        $out['tipo'] = ($out['tipo'] ?? 'servico');
        if ($out['tipo'] !== 'servico' && $out['tipo'] !== 'produto') $out['tipo'] = 'servico';

        $out['categoria_id'] = $out['categoria_id'] ?? '';

        $out['preco_venda'] = self::moneyToDb($out['preco_venda'] ?? '0');
        $out['preco_custo'] = self::moneyToDb($out['preco_custo'] ?? '0');

        $out['estoque'] = (int)preg_replace('/\D+/', '', (string)($out['estoque'] ?? 0));
        $out['unidade'] = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', (string)($out['unidade'] ?? 'UN')), 0, 10));
        if ($out['unidade'] === '') $out['unidade'] = 'UN';

        $out['ativo'] = isset($out['ativo']) ? 1 : 0;

        return $out;
    }

    public static function index()
    {
        Auth::requireLogin();
        self::start();

        $search = $_GET['search'] ?? '';
        $tipo = $_GET['tipo'] ?? '';
        $categoria = $_GET['categoria'] ?? '';

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $categorias = CategoriaServico::allActive();
        $servicos = Servico::paginate($search, $tipo, $categoria, $limit, $offset);

        $total = Servico::count($search, $tipo, $categoria);
        $pages = max(1, (int)ceil($total / $limit));

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/servicos/index.php';
    }

    public static function create()
    {
        Auth::requireLogin();
        self::start();

        $categorias = CategoriaServico::allActive();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/servicos/form.php';
    }

    public static function store()
    {
        Auth::requireLogin();
        self::start();

        Csrf::check($_POST['_csrf'] ?? null);

        $data = self::clean($_POST);

        if ($data['nome'] === '') {
            self::flash('error', 'Informe o nome do serviço/produto.');
            header("Location: ?r=servicos_novo");
            exit;
        }

        Servico::create($data);

        self::flash('success', 'Serviço/Produto cadastrado com sucesso!');
        header("Location: ?r=servicos");
        exit;
    }

    public static function edit()
    {
        Auth::requireLogin();
        self::start();

        $id = (int)($_GET['id'] ?? 0);
        $servico = Servico::find($id);

        if (!$servico) {
            self::flash('error', 'Registro não encontrado.');
            header("Location: ?r=servicos");
            exit;
        }

        $categorias = CategoriaServico::allActive();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require __DIR__ . '/../views/servicos/form.php';
    }

    public static function update()
    {
        Auth::requireLogin();
        self::start();

        Csrf::check($_POST['_csrf'] ?? null);

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            self::flash('error', 'Registro inválido.');
            header("Location: ?r=servicos");
            exit;
        }

        $data = self::clean($_POST);

        if ($data['nome'] === '') {
            self::flash('error', 'Informe o nome do serviço/produto.');
            header("Location: ?r=servicos_editar&id=" . $id);
            exit;
        }

        Servico::update($id, $data);

        self::flash('success', 'Serviço/Produto atualizado com sucesso!');
        header("Location: ?r=servicos");
        exit;
    }

    public static function delete()
    {
        Auth::requireLogin();
        self::start();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            self::flash('error', 'Registro inválido.');
            header("Location: ?r=servicos");
            exit;
        }

        Servico::delete($id);

        self::flash('success', 'Registro excluído com sucesso!');
        header("Location: ?r=servicos");
        exit;
    }
}
