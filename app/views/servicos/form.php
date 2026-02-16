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
        <h2><i class="fa-solid fa-screwdriver-wrench"></i>
          <?= $isEdit ? 'Editar Serviço/Produto' : 'Novo Serviço/Produto' ?>
        </h2>
      </div>

      <div class="form-header__right">
        <a class="btn btn--ghost" href="/?r=servicos">
          <i class="fa-solid fa-arrow-left"></i>
        </a>
      </div>
    </div>

    <form method="post"
          action="/?r=<?= $isEdit ? 'servicos_update' : 'servicos_store' ?>"
          class="form-body">

      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$servico['id'] ?>">
      <?php endif; ?>

      <!-- ===================== ABAS ===================== -->
      <div class="tabs">
        <button type="button" class="tab active" data-tab="geral">Geral</button>
        <button type="button" class="tab" data-tab="financeiro">Financeiro</button>
        <button type="button" class="tab" data-tab="estoque">Estoque</button>
        <button type="button" class="tab" data-tab="operacional">Operacional</button>
      </div>

      <!-- ===================== GERAL ===================== -->
      <div class="tab-content active" id="geral">

        <div class="form-grid">
          <div class="input-group span-2">
            <i class="fa-solid fa-tag"></i>
            <input type="text" name="nome" placeholder="Nome do Serviço/Produto" required
                   value="<?= htmlspecialchars($servico['nome'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-barcode"></i>
            <input type="text" name="codigo" placeholder="Código Interno"
                   value="<?= htmlspecialchars($servico['codigo'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-barcode"></i>
            <input type="text" name="codigo_barras" placeholder="Código de Barras"
                   value="<?= htmlspecialchars($servico['codigo_barras'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-layer-group"></i>
            <select name="categoria_id">
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
            <input type="text" name="unidade"
                   value="<?= htmlspecialchars($servico['unidade'] ?? 'UN') ?>">
          </div>
        </div>

        <textarea name="descricao" placeholder="Descrição"><?= htmlspecialchars($servico['descricao'] ?? '') ?></textarea>

      </div>

      <!-- ===================== FINANCEIRO ===================== -->
      <div class="tab-content" id="financeiro">

        <div class="form-grid">
          <div class="input-group">
            <i class="fa-solid fa-dollar-sign"></i>
            <input type="text" name="preco_venda"
                   placeholder="Preço de Venda"
                   value="<?= htmlspecialchars($servico['preco_venda'] ?? '0.00') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-receipt"></i>
            <input type="text" name="preco_custo"
                   placeholder="Preço de Custo"
                   value="<?= htmlspecialchars($servico['preco_custo'] ?? '0.00') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-tags"></i>
            <input type="text" name="preco_promocional"
                   placeholder="Preço Promocional"
                   value="<?= htmlspecialchars($servico['preco_promocional'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-percent"></i>
            <input type="text" name="margem_lucro"
                   placeholder="Margem (%)"
                   value="<?= htmlspecialchars($servico['margem_lucro'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-hand-holding-dollar"></i>
            <input type="text" name="comissao"
                   placeholder="Comissão (%)"
                   value="<?= htmlspecialchars($servico['comissao'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <input type="text" name="imposto_percentual"
                   placeholder="Imposto (%)"
                   value="<?= htmlspecialchars($servico['imposto_percentual'] ?? '') ?>">
          </div>
        </div>

      </div>

      <!-- ===================== ESTOQUE ===================== -->
      <div class="tab-content" id="estoque">

        <div class="form-grid">
          <div class="input-group">
            <i class="fa-solid fa-cubes"></i>
            <input type="number" name="estoque"
                   value="<?= htmlspecialchars($servico['estoque'] ?? '0') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <input type="number" name="estoque_minimo"
                   placeholder="Estoque mínimo"
                   value="<?= htmlspecialchars($servico['estoque_minimo'] ?? '') ?>">
          </div>
        </div>

        <label class="toggle">
          <input type="checkbox" name="permite_sem_estoque"
                 <?= !empty($servico['permite_sem_estoque']) ? 'checked' : '' ?>>
          <span class="toggle__ui"></span>
          Permitir venda sem estoque
        </label>

      </div>

      <!-- ===================== OPERACIONAL ===================== -->
      <div class="tab-content" id="operacional">

        <div class="form-grid">
          <div class="input-group">
            <i class="fa-solid fa-clock"></i>
            <input type="number" name="tempo_execucao"
                   placeholder="Tempo Execução (min)"
                   value="<?= htmlspecialchars($servico['tempo_execucao'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-shield"></i>
            <input type="number" name="garantia_dias"
                   placeholder="Garantia (dias)"
                   value="<?= htmlspecialchars($servico['garantia_dias'] ?? '') ?>">
          </div>

          <div class="input-group span-2">
            <i class="fa-solid fa-truck"></i>
            <input type="text" name="fornecedor"
                   placeholder="Fornecedor"
                   value="<?= htmlspecialchars($servico['fornecedor'] ?? '') ?>">
          </div>
        </div>

        <textarea name="observacoes" placeholder="Observações internas"><?= htmlspecialchars($servico['observacoes'] ?? '') ?></textarea>

      </div>

      <div class="form-footer">
        <button class="btn btn--primary" type="submit">
          <i class="fa-solid fa-floppy-disk"></i> Salvar
        </button>
      </div>

    </form>

  </div>
</div>

<!-- JS ABAS -->
<script>
document.querySelectorAll('.tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

    tab.classList.add('active');
    document.getElementById(tab.dataset.tab).classList.add('active');
  });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
