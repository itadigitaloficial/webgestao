<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';

$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- CSS Principal (Anti Cache) -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css?v=<?= time(); ?>">
</head>
<body>

<header class="topbar">
    <div class="topbar__inner">

        <!-- LOGO -->
        <a class="brand" href="<?= APP_URL ?>/?r=dashboard">
            <span class="brand__logo">
                <i class="fa-solid fa-gem"></i>
            </span>
            <span class="brand__text"><?= htmlspecialchars(APP_NAME) ?></span>
        </a>

        <?php if ($user): ?>

            <!-- MENU HORIZONTAL -->
            <nav class="menu">
                <a class="menu__link" href="<?= APP_URL ?>/?r=dashboard">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>

                <a class="menu__link" href="<?= APP_URL ?>/?r=clientes">
                    <i class="fa-solid fa-users"></i> Clientes
                </a>

                <a class="menu__link" href="<?= APP_URL ?>/?r=servicos">
                    <i class="fa-solid fa-screwdriver-wrench"></i> Serviços
                </a>

                <a class="menu__link" href="<?= APP_URL ?>/?r=os">
                    <i class="fa-solid fa-receipt"></i> OS / Vendas
                </a>

                <a class="menu__link" href="<?= APP_URL ?>/?r=financeiro">
                    <i class="fa-solid fa-wallet"></i> Financeiro
                </a>
            </nav>

            <!-- USUÁRIO -->
            <div class="userbox">
                <div class="userbox__meta">
                    <div class="userbox__name">
                        <?= htmlspecialchars($user['nome']) ?>
                    </div>
                    <div class="userbox__role">
                        <?= htmlspecialchars($user['nivel']) ?>
                    </div>
                </div>

                <a class="btn btn--ghost" href="<?= APP_URL ?>/logout.php" title="Sair">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>

        <?php endif; ?>

    </div>
</header>

<main class="container">
