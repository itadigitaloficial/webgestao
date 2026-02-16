<?php
require __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../core/Csrf.php';

$isEdit = isset($servico);
$tipo = $servico['tipo'] ?? 'servico';
$ativo = $isEdit ? (int)($servico['ativo'] ?? 1) : 1;
?>

<div class="form-wrapper">
  <div class="form-card enterprise">

    <div class="form-header">
      <div class="form-header__left">
        <div class="kicker"><i class="fa-solid fa-sparkles"></i> Cadastro Enterprise</div>
        <h2><i class="fa-solid fa-screwdriver-wrench"></i> <?= $isEdit ? 'Editar Serviço/Produto' : 'Novo Serviço/Produto' ?></h2>
        <p>Defina tipo, categoria, preço, custo e (se produto) controle de estoque.</p>
      </div>

      <div class="form-header__right">
        <a class="btn btn--ghost" href="?r=servicos" title="Voltar">
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

    <form method="post"
          action="?r=<?= $isEdit ? 'servicos_update' : 'servicos_store' ?>"
          class="form-body"
          data-servico-form>

      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$servico['id'] ?>">
      <?php endif; ?>

      <div class="form-topbar">
        <div class="segmented">
          <label class="segmented__item">
            <input type="radio" name="tipo" value="servico" <?= $tipo === 'servico' ? 'checked' : '' ?>>
            <span><i class="fa-solid fa-wand-magic-sparkles"></i> Serviço</span>
          </label>

          <label class="segmented__item">
            <input type="radio" name="tipo" value="produto" <?= $tipo === 'produto' ? 'checked' : '' ?>>
            <span><i class="fa-solid fa-box"></i> Produto</span>
          </label>
        </div>

        <label class="toggle">
          <input type="checkbox" name="ativo" value="1" <?= $ativo === 1 ? 'checked' : '' ?>>
          <span class="toggle__ui"></span>
          <span class="toggle__label"><i class="fa-solid fa-circle-check"></i> Ativo</span>
        </label>
      </div>

      <section class="form-section">
        <div class="section-head">
          <div class="section-title"><i class="fa-solid fa-pen-ruler"></i> Dados do Item</div>
          <div class="section-sub">Nome, categoria e descrição.</div>
        </div>

        <div class="form-grid">
          <div class="input-group span-2">
            <i class="fa-solid fa-tag"></i>
            <input type="text" name="nome" placeholder="Nome do Serviço/Produto" required
                   value="<?= htmlspecialchars($servico['nome'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-layer-group"></i>
            <select name="categoria_id" class="select-inset">
              <option value="">Sem categoria</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>"
                  <?= ((string)($servico['categoria_id'] ?? '') === (string)$cat['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="input-group">
            <i class="fa-solid fa-ruler-combined"></i>
            <input type="text" name="unidade" placeholder="Unidade (ex: UN, H, M²)"
                   value="<?= htmlspecialchars($servico['unidade'] ?? 'UN') ?>">
          </div>
        </div>

        <textarea name="descricao" placeholder="Descrição (opcional)" rows="4"><?= htmlspecialchars($servico['descricao'] ?? '') ?></textarea>
      </section>

      <section class="form-section">
        <div class="section-head">
          <div class="section-title"><i class="fa-solid fa-coins"></i> Valores</div>
          <div class="section-sub">Preço de venda e custo (para margem).</div>
        </div>

        <div class="form-grid">
          <div class="input-group">
            <i class="fa-solid fa-tag"></i>
            <input type="text" name="preco_venda" placeholder="Preço de Venda (ex: 199,90)"
                   value="<?= htmlspecialchars($servico['preco_venda'] ?? '0.00') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-receipt"></i>
            <input type="text" name="preco_custo" placeholder="Preço de Custo (ex: 120,00)"
                   value="<?= htmlspecialchars($servico['preco_custo'] ?? '0.00') ?>">
          </div>
        </div>
      </section>

      <section class="form-section" data-estoque-area>
        <div class="section-head">
          <div class="section-title"><i class="fa-solid fa-warehouse"></i> Estoque</div>
          <div class="section-sub">Aparece apenas para produtos.</div>
        </div>

        <div class="form-grid">
          <div class="input-group">
            <i class="fa-solid fa-cubes"></i>
            <input type="number" name="estoque" placeholder="Estoque"
                   value="<?= htmlspecialchars($servico['estoque'] ?? '0') ?>" min="0">
          </div>
          <div class="input-group">
            <i class="fa-solid fa-scale-balanced"></i>
            <input type="text" name="unidade" placeholder="Unidade"
                   value="<?= htmlspecialchars($servico['unidade'] ?? 'UN') ?>">
          </div>
        </div>
      </section>

      <div class="form-footer">
        <button class="btn btn--primary" type="submit">
          <i class="fa-solid fa-floppy-disk"></i> Salvar
        </button>
      </div>
    </form>

  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
