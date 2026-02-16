<?php
require __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../core/Csrf.php';
$err = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
?>

<div class="auth">
  <section class="auth__card">
    <div class="auth__hero">
      <div class="auth__badge"><i class="fa-solid fa-shield-halved"></i> Acesso seguro</div>
      <h1>Entrar no <span class="grad">ITA Gestão</span></h1>
      <p>Clientes, serviços e financeiro em um painel moderno, rápido e organizado.</p>
    </div>

    <form class="auth__form" method="post" action="/?r=login">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

      <?php if ($err): ?>
        <div class="alert alert--error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <label class="field">
        <span>E-mail</span>
        <div class="field__input">
          <i class="fa-solid fa-at"></i>
          <input name="email" type="email" placeholder="admin@sistema.com" required autocomplete="email">
        </div>
      </label>

      <label class="field">
        <span>Senha</span>
        <div class="field__input">
          <i class="fa-solid fa-lock"></i>
          <input name="senha" type="password" placeholder="••••••••" required autocomplete="current-password">
        </div>
      </label>

      <button class="btn btn--primary" type="submit">
        <i class="fa-solid fa-right-to-bracket"></i> Entrar
      </button>

      <div class="hint">
        <i class="fa-solid fa-circle-info"></i>
        Padrão: <b>admin@sistema.com</b> / <b>admin123</b>
      </div>
    </form>
  </section>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
