<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-head">
  <div>
    <h2><i class="fa-solid fa-screwdriver-wrench"></i> Serviços & Produtos</h2>
    <p class="muted">Cadastre serviços, produtos, preços, custo e controle de estoque.</p>
  </div>

  <div class="actions">
    <a class="btn btn--green" href="?r=servicos_novo">
      <i class="fa-solid fa-plus"></i> Novo
    </a>
  </div>
</div>

<?php if (!empty($flash)): ?>
  <div class="alert <?= ($flash['type'] ?? '') === 'error' ? 'alert--error' : '' ?>">
    <i class="fa-solid <?= ($flash['type'] ?? '') === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-check' ?>"></i>
    <?= htmlspecialchars($flash['msg'] ?? '') ?>
  </div>
<?php endif; ?>

<div class="card table-card">
  <form method="get" class="table-top">
    <input type="hidden" name="r" value="servicos">

    <div class="search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Buscar por nome ou descrição">
    </div>

    <select name="tipo" class="select">
      <option value="">Todos</option>
      <option value="servico" <?= ($tipo ?? '') === 'servico' ? 'selected' : '' ?>>Serviço</option>
      <option value="produto" <?= ($tipo ?? '') === 'produto' ? 'selected' : '' ?>>Produto</option>
    </select>

    <select name="categoria" class="select">
      <option value="">Categorias</option>
      <?php foreach ($categorias as $cat): ?>
        <option value="<?= (int)$cat['id'] ?>" <?= ((string)($categoria ?? '') === (string)$cat['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['nome']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button class="btn btn--primary" type="submit"><i class="fa-solid fa-filter"></i> Filtrar</button>
  </form>

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Item</th>
          <th>Categoria</th>
          <th>Tipo</th>
          <th>Preço</th>
          <th>Estoque</th>
          <th>Status</th>
          <th class="th-actions">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($servicos)): ?>
          <tr>
            <td colspan="7" class="td-empty">
              <i class="fa-regular fa-face-meh"></i>
              Nenhum registro encontrado.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($servicos as $s): ?>
            <tr>
              <td>
                <div class="td-title">
                  <div class="avatar">
                    <i class="fa-solid <?= ($s['tipo'] === 'produto') ? 'fa-box' : 'fa-wand-magic-sparkles' ?>"></i>
                  </div>
                  <div>
                    <div class="name"><?= htmlspecialchars($s['nome'] ?? '') ?></div>
                    <div class="sub"><?= htmlspecialchars(mb_strimwidth((string)($s['descricao'] ?? ''), 0, 48, '…')) ?></div>
                  </div>
                </div>
              </td>

              <td>
                <?php if (!empty($s['categoria_nome'])): ?>
                  <span class="badge" style="background: rgba(255,255,255,.03); border-color: rgba(255,255,255,.10);">
                    <span class="dot" style="background: <?= htmlspecialchars($s['categoria_cor'] ?? '#6366f1') ?>"></span>
                    <?= htmlspecialchars($s['categoria_nome']) ?>
                  </span>
                <?php else: ?>
                  <span class="muted">—</span>
                <?php endif; ?>
              </td>

              <td>
                <?php if ($s['tipo'] === 'produto'): ?>
                  <span class="badge badge--ok"><i class="fa-solid fa-box"></i> Produto</span>
                <?php else: ?>
                  <span class="badge" style="background: rgba(124,58,237,.12); border-color: rgba(124,58,237,.22);">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Serviço
                  </span>
                <?php endif; ?>
              </td>

              <td><b>R$ <?= number_format((float)$s['preco_venda'], 2, ',', '.') ?></b></td>

              <td>
                <?php if ($s['tipo'] === 'produto'): ?>
                  <?= (int)$s['estoque'] ?> <span class="muted"><?= htmlspecialchars($s['unidade'] ?? 'UN') ?></span>
                <?php else: ?>
                  <span class="muted">—</span>
                <?php endif; ?>
              </td>

              <td>
                <?php if ((int)$s['ativo'] === 1): ?>
                  <span class="badge badge--ok"><i class="fa-solid fa-circle-check"></i> Ativo</span>
                <?php else: ?>
                  <span class="badge badge--off"><i class="fa-solid fa-circle-xmark"></i> Inativo</span>
                <?php endif; ?>
              </td>

              <td class="td-actions">
                <a class="btn btn--ghost" href="?r=servicos_editar&id=<?= (int)$s['id'] ?>" title="Editar">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>

                <a class="btn btn--ghost"
                   href="?r=servicos_delete&id=<?= (int)$s['id'] ?>"
                   data-confirm="Deseja excluir este item? Essa ação não pode ser desfeita."
                   title="Excluir">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="table-footer">
    <div class="muted">Total: <b><?= (int)$total ?></b></div>

    <div class="pager">
      <?php
        $cur = (int)($page ?? 1);
        $prev = max(1, $cur - 1);
        $next = min((int)$pages, $cur + 1);

        $qs = http_build_query([
          'r' => 'servicos',
          'search' => $search ?? '',
          'tipo' => $tipo ?? '',
          'categoria' => $categoria ?? '',
        ]);
      ?>
      <a class="pager__btn <?= $cur <= 1 ? 'is-disabled' : '' ?>" href="?<?= $qs ?>&page=<?= $prev ?>"><i class="fa-solid fa-chevron-left"></i></a>
      <span class="pager__info">Página <b><?= $cur ?></b> de <b><?= (int)$pages ?></b></span>
      <a class="pager__btn <?= $cur >= (int)$pages ? 'is-disabled' : '' ?>" href="?<?= $qs ?>&page=<?= $next ?>"><i class="fa-solid fa-chevron-right"></i></a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
