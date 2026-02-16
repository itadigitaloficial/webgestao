<?php
require __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../core/Csrf.php';

function money($v) { return 'R$ ' . number_format((float)$v, 2, ',', '.'); }
?>

<div class="page-head">
  <div>
    <h2><i class="fa-solid fa-file-circle-check"></i> Detalhes da OS</h2>
    <p class="muted"><b><?= htmlspecialchars($os['numero']) ?></b> • Cliente: <?= htmlspecialchars($os['cliente_nome']) ?></p>
  </div>

  <div class="actions">
    <a class="btn btn--ghost" href="?r=os" title="Voltar"><i class="fa-solid fa-arrow-left"></i></a>
  </div>
</div>

<?php if (!empty($flash)): ?>
  <div class="alert <?= ($flash['type'] ?? '') === 'error' ? 'alert--error' : '' ?>">
    <i class="fa-solid <?= ($flash['type'] ?? '') === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-check' ?>"></i>
    <?= htmlspecialchars($flash['msg'] ?? '') ?>
  </div>
<?php endif; ?>

<div class="grid" style="grid-template-columns: repeat(4, 1fr);">
  <div class="card">
    <div class="card__label"><i class="fa-solid fa-user"></i> Cliente</div>
    <div class="card__value" style="font-size:16px;line-height:1.2;"><?= htmlspecialchars($os['cliente_nome']) ?></div>
    <div class="card__foot">
      <?= htmlspecialchars($os['cliente_email'] ?? '-') ?><br>
      <?= htmlspecialchars($os['cliente_celular'] ?: ($os['cliente_telefone'] ?? '-')) ?>
    </div>
  </div>

  <div class="card">
    <div class="card__label"><i class="fa-solid fa-user-gear"></i> Responsável</div>
    <div class="card__value" style="font-size:16px;"><?= htmlspecialchars($os['usuario_nome'] ?? '-') ?></div>
    <div class="card__foot">Abertura: <?= !empty($os['data_abertura']) ? date('d/m/Y', strtotime($os['data_abertura'])) : '-' ?></div>
  </div>

  <div class="card">
    <div class="card__label"><i class="fa-solid fa-flag"></i> Status</div>
    <div class="card__value" style="font-size:16px;text-transform:capitalize;"><?= str_replace('_', ' ', htmlspecialchars($os['status'])) ?></div>
    <div class="card__foot">Conclusão: <?= !empty($os['data_conclusao']) ? date('d/m/Y', strtotime($os['data_conclusao'])) : '-' ?></div>
  </div>

  <div class="card">
    <div class="card__label"><i class="fa-solid fa-sack-dollar"></i> Valor Final</div>
    <div class="card__value"><?= money($os['valor_final']) ?></div>
    <div class="card__foot">Total: <?= money($os['valor_total']) ?> • Desconto: <?= money($os['desconto']) ?></div>
  </div>
</div>

<div class="card" style="margin-top:14px;">
  <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    <div>
      <div style="font-weight:900; font-size:16px; display:flex; gap:10px; align-items:center;">
        <i class="fa-solid fa-sliders"></i> Atualizar Status
      </div>
      <div class="muted" style="margin-top:6px;">Atualize o andamento da OS com segurança (CSRF).</div>
    </div>

    <form method="post" action="?r=os_status_update" style="display:flex; gap:10px; align-items:center;">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <input type="hidden" name="id" value="<?= (int)$os['id'] ?>">

      <select name="status" class="select">
        <option value="aberta" <?= $os['status'] === 'aberta' ? 'selected' : '' ?>>Aberta</option>
        <option value="em_andamento" <?= $os['status'] === 'em_andamento' ? 'selected' : '' ?>>Em andamento</option>
        <option value="concluida" <?= $os['status'] === 'concluida' ? 'selected' : '' ?>>Concluída</option>
        <option value="cancelada" <?= $os['status'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
      </select>

      <button class="btn btn--primary" type="submit">
        <i class="fa-solid fa-floppy-disk"></i> Salvar
      </button>
    </form>
  </div>
</div>

<!-- ADD ITEM -->
<div class="card" style="margin-top:14px;">
  <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
    <div>
      <div style="font-weight:900; font-size:16px; display:flex; gap:10px; align-items:center;">
        <i class="fa-solid fa-cart-plus"></i> Adicionar Item
      </div>
      <div class="muted" style="margin-top:6px;">Adicionar item recalcula o total automaticamente.</div>
    </div>

    <form method="post" action="?r=os_add_item" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <input type="hidden" name="ordem_id" value="<?= (int)$os['id'] ?>">

      <select name="servico_id" class="select" required>
        <option value="">Selecione um serviço/produto</option>
        <?php foreach ($servicos as $s): ?>
          <option value="<?= (int)$s['id'] ?>">
            <?= htmlspecialchars($s['nome']) ?> — R$ <?= number_format((float)$s['preco_venda'], 2, ',', '.') ?>
          </option>
        <?php endforeach; ?>
      </select>

      <input class="select" style="width:120px;" type="number" name="quantidade" min="1" step="1" value="1" required>

      <button class="btn btn--green" type="submit">
        <i class="fa-solid fa-plus"></i> Adicionar
      </button>
    </form>
  </div>
</div>

<!-- ITENS LIST -->
<div class="card table-card" style="margin-top:14px;">
  <div class="table-top" style="justify-content:space-between;">
    <div style="font-weight:900; display:flex; gap:10px; align-items:center;">
      <i class="fa-solid fa-list-check"></i> Itens da OS
    </div>
    <div class="muted">Qtd itens: <b><?= (int)count($itens) ?></b></div>
  </div>

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Item</th>
          <th>Tipo</th>
          <th>Qtd</th>
          <th>Preço</th>
          <th>Subtotal</th>
          <th class="th-actions">Remover</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($itens)): ?>
          <tr>
            <td colspan="6" class="td-empty">
              <i class="fa-regular fa-face-meh"></i> Nenhum item nesta OS.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($itens as $it): ?>
            <tr>
              <td>
                <div class="td-title">
                  <div class="avatar">
                    <i class="fa-solid <?= ($it['servico_tipo'] ?? 'servico') === 'produto' ? 'fa-box' : 'fa-wand-magic-sparkles' ?>"></i>
                  </div>
                  <div>
                    <div class="name"><?= htmlspecialchars($it['servico_nome'] ?? '-') ?></div>
                    <div class="sub"><?= htmlspecialchars(mb_strimwidth((string)($it['descricao'] ?? ''), 0, 60, '…')) ?></div>
                  </div>
                </div>
              </td>

              <td>
                <?php if (($it['servico_tipo'] ?? '') === 'produto'): ?>
                  <span class="badge badge--ok"><i class="fa-solid fa-box"></i> Produto</span>
                <?php else: ?>
                  <span class="badge" style="background: rgba(124,58,237,.12); border-color: rgba(124,58,237,.22);">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Serviço
                  </span>
                <?php endif; ?>
              </td>

              <td><?= htmlspecialchars($it['quantidade']) ?> <span class="muted"><?= htmlspecialchars($it['servico_unidade'] ?? '') ?></span></td>
              <td><b><?= money($it['preco_unitario']) ?></b></td>
              <td><b><?= money($it['subtotal']) ?></b></td>

              <td class="td-actions">
                <form method="post" action="?r=os_remove_item" style="display:inline;">
                  <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                  <input type="hidden" name="ordem_id" value="<?= (int)$os['id'] ?>">
                  <input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
                  <button class="btn btn--ghost" type="submit" data-confirm="Remover este item da OS?">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
