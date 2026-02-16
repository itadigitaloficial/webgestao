<?php
require __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../core/Csrf.php';
?>

<div class="form-wrapper">
  <div class="form-card enterprise">

    <div class="form-header">
      <div class="form-header__left">
        <div class="kicker"><i class="fa-solid fa-sparkles"></i> Nova Ordem de Serviço</div>
        <h2><i class="fa-solid fa-file-circle-plus"></i> Criar OS</h2>
        <p>Selecione um cliente e crie uma OS em aberto. Depois, adicione os itens.</p>
      </div>

      <div class="form-header__right">
        <a class="btn btn--ghost" href="?r=os" title="Voltar">
          <i class="fa-solid fa-arrow-left"></i>
        </a>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="alert <?= ($flash['type'] ?? '') === 'error' ? 'alert--error' : '' ?>">
        <i class="fa-solid <?= ($flash['type'] ?? '') === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-check' ?>"></i>
        <?= htmlspecialchars($flash['msg'] ?? '') ?>
      </div>
    <?php endif; ?>

    <form method="post" action="?r=os_store" class="form-body">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

      <section class="form-section">
        <div class="section-head">
          <div class="section-title"><i class="fa-solid fa-user-check"></i> Cliente</div>
          <div class="section-sub">A OS será vinculada ao cliente selecionado.</div>
        </div>

        <div class="form-grid">
          <div class="input-group span-2">
            <i class="fa-solid fa-user"></i>
            <select name="cliente_id" class="select-inset" required>
              <option value="">Selecione um cliente...</option>
              <?php foreach ($clientes as $c): ?>
                <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <textarea name="observacoes" placeholder="Observações (opcional)..." rows="4"></textarea>
      </section>

      <div class="form-footer">
        <button class="btn btn--primary" type="submit">
          <i class="fa-solid fa-floppy-disk"></i> Criar OS
        </button>
      </div>
    </form>

  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
