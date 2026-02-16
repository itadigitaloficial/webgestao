<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-head">
  <div>
    <h2><i class="fa-solid fa-file-invoice"></i> Ordens de Serviço</h2>
    <p class="muted">Gerencie todas as OS do sistema</p>
  </div>

  <div class="actions">
    <a class="btn btn--green" href="?r=os_novo">
      <i class="fa-solid fa-plus"></i> Nova OS
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
    <input type="hidden" name="r" value="os">

    <select name="status" class="select">
      <option value="" <?= ($status ?? '') === '' ? 'selected' : '' ?>>Todos</option>
      <option value="aberta" <?= ($status ?? '') === 'aberta' ? 'selected' : '' ?>>Aberta</option>
      <option value="em_andamento" <?= ($status ?? '') === 'em_andamento' ? 'selected' : '' ?>>Em Andamento</option>
      <option value="concluida" <?= ($status ?? '') === 'concluida' ? 'selected' : '' ?>>Concluída</option>
      <option value="cancelada" <?= ($status ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
    </select>

    <button class="btn btn--primary">
      <i class="fa-solid fa-filter"></i> Filtrar
    </button>
  </form>

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Nº OS</th>
          <th>Cliente</th>
          <th>Responsável</th>
          <th>Status</th>
          <th>Valor</th>
          <th>Data</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($ordens)): ?>
          <tr>
            <td colspan="6" class="td-empty">
              <i class="fa-regular fa-face-meh"></i> Nenhuma ordem encontrada.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($ordens as $row): ?>
            <tr>
              <td>
                <a class="link" href="?r=os_detalhe&id=<?= (int)$row['id'] ?>">
                  <b><?= htmlspecialchars($row['numero']) ?></b>
                </a>
              </td>
              <td><?= htmlspecialchars($row['cliente_nome']) ?></td>
              <td><?= htmlspecialchars($row['usuario_nome'] ?? '-') ?></td>

              <td>
                <?php if ($row['status'] === 'aberta'): ?>
                  <span class="badge">Aberta</span>
                <?php elseif ($row['status'] === 'em_andamento'): ?>
                  <span class="badge" style="background:#f59e0b20;border-color:#f59e0b;">Em Andamento</span>
                <?php elseif ($row['status'] === 'concluida'): ?>
                  <span class="badge badge--ok">Concluída</span>
                <?php else: ?>
                  <span class="badge badge--off">Cancelada</span>
                <?php endif; ?>
              </td>

              <td><b>R$ <?= number_format((float)$row['valor_final'], 2, ',', '.') ?></b></td>
              <td><?= !empty($row['data_abertura']) ? date('d/m/Y', strtotime($row['data_abertura'])) : '-' ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
