<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
function money($v) { return 'R$ ' . number_format((float)$v, 2, ',', '.'); }
function dmy($date) { return $date ? date('d/m/Y', strtotime($date)) : '-'; }
?>

<div class="page-head">
  <div>
    <h2><i class="fa-solid fa-gauge-high"></i> Dashboard Geral</h2>
    <p class="muted">Visão unificada de Clientes, Serviços, OS e Financeiro.</p>
  </div>
</div>

<div class="grid" style="grid-template-columns: repeat(4, 1fr);">
  <div class="card">
    <div class="card__label"><i class="fa-solid fa-users"></i> Clientes</div>
    <div class="card__value"><?= (int)$metrics['clientesTotal'] ?></div>
    <div class="card__foot"><span class="badge badge--ok"><i class="fa-solid fa-circle-check"></i> <?= (int)$metrics['clientesAtivos'] ?> ativos</span></div>
  </div>

  <div class="card">
    <div class="card__label"><i class="fa-solid fa-screwdriver-wrench"></i> Serviços/Produtos</div>
    <div class="card__value"><?= (int)$metrics['servicosTotal'] ?></div>
    <div class="card__foot"><span class="badge badge--ok"><i class="fa-solid fa-circle-check"></i> <?= (int)$metrics['servicosAtivos'] ?> ativos</span></div>
  </div>

  <div class="card">
    <div class="card__label"><i class="fa-solid fa-file-invoice"></i> OS Abertas</div>
    <div class="card__value"><?= (int)$metrics['osAbertas'] ?></div>
    <div class="card__foot">
      <span class="badge" style="background: rgba(245,158,11,.15);border-color: rgba(245,158,11,.3);">
        <i class="fa-solid fa-hourglass-half"></i> <?= (int)$metrics['osAndamento'] ?> em andamento
      </span>
    </div>
  </div>

  <div class="card">
    <div class="card__label"><i class="fa-solid fa-sack-dollar"></i> Saldo (Pago)</div>
    <div class="card__value"><?= money($metrics['saldoPago']) ?></div>
    <div class="card__foot">
      <span class="badge badge--ok"><i class="fa-solid fa-arrow-trend-up"></i> <?= money($metrics['receitasPagas']) ?> receitas</span>
      <span class="badge badge--off" style="margin-left:8px;"><i class="fa-solid fa-arrow-trend-down"></i> <?= money($metrics['despesasPagas']) ?> despesas</span>
    </div>
  </div>
</div>

<div class="grid" style="grid-template-columns: repeat(3, 1fr); margin-top:14px;">
  <div class="card">
    <div class="card__label"><i class="fa-solid fa-clock"></i> Pendências</div>
    <div class="card__value"><?= money($metrics['receitasPendentes'] + $metrics['despesasPendentes']) ?></div>
    <div class="card__foot">
      <span class="badge"><i class="fa-solid fa-circle-dollar-to-slot"></i> <?= money($metrics['receitasPendentes']) ?> a receber</span>
      <span class="badge" style="margin-left:8px;"><i class="fa-solid fa-file-invoice-dollar"></i> <?= money($metrics['despesasPendentes']) ?> a pagar</span>
    </div>
  </div>

  <div class="card">
    <div class="card__label"><i class="fa-solid fa-triangle-exclamation"></i> Atrasados</div>
    <div class="card__value"><?= money($metrics['atrasados']) ?></div>
    <div class="card__foot muted">Pendentes com vencimento anterior a hoje.</div>
  </div>

  <div class="card">
    <div class="card__label"><i class="fa-solid fa-badge-check"></i> OS Concluídas</div>
    <div class="card__value"><?= (int)$metrics['osConcluidas'] ?></div>
    <div class="card__foot">
      <span class="badge badge--off"><i class="fa-solid fa-ban"></i> <?= (int)$metrics['osCanceladas'] ?> canceladas</span>
    </div>
  </div>
</div>

<div class="grid" style="grid-template-columns: 1.2fr .8fr; margin-top:14px;">
  <div class="card table-card">
    <div class="table-top" style="justify-content:space-between;">
      <div style="font-weight:900; display:flex; gap:10px; align-items:center;">
        <i class="fa-solid fa-list"></i> Últimas Ordens de Serviço
      </div>
      <a class="btn btn--ghost" href="?r=os"><i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Nº</th>
            <th>Cliente</th>
            <th>Status</th>
            <th>Valor</th>
            <th>Data</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($metrics['ultimasOs'])): ?>
            <tr><td colspan="5" class="td-empty"><i class="fa-regular fa-face-meh"></i> Sem OS ainda.</td></tr>
          <?php else: ?>
            <?php foreach ($metrics['ultimasOs'] as $os): ?>
              <tr>
                <td>
                  <a class="link" href="?r=os_detalhe&id=<?= (int)$os['id'] ?>">
                    <b><?= htmlspecialchars($os['numero']) ?></b>
                  </a>
                </td>
                <td><?= htmlspecialchars($os['cliente_nome']) ?></td>
                <td>
                  <?php if ($os['status'] === 'aberta'): ?>
                    <span class="badge">Aberta</span>
                  <?php elseif ($os['status'] === 'em_andamento'): ?>
                    <span class="badge" style="background:#f59e0b20;border-color:#f59e0b;">Em andamento</span>
                  <?php elseif ($os['status'] === 'concluida'): ?>
                    <span class="badge badge--ok">Concluída</span>
                  <?php else: ?>
                    <span class="badge badge--off">Cancelada</span>
                  <?php endif; ?>
                </td>
                <td><b><?= money($os['valor_final']) ?></b></td>
                <td><?= dmy($os['data_abertura']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card table-card">
    <div class="table-top" style="justify-content:space-between;">
      <div style="font-weight:900; display:flex; gap:10px; align-items:center;">
        <i class="fa-solid fa-money-bill-transfer"></i> Últimas Movimentações
      </div>
      <a class="btn btn--ghost" href="?r=financeiro"><i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($metrics['ultimasMovs'])): ?>
            <tr><td colspan="3" class="td-empty"><i class="fa-regular fa-face-meh"></i> Sem lançamentos.</td></tr>
          <?php else: ?>
            <?php foreach ($metrics['ultimasMovs'] as $m): ?>
              <tr>
                <td>
                  <?php if ($m['tipo'] === 'receita'): ?>
                    <span class="badge badge--ok"><i class="fa-solid fa-arrow-up"></i> Receita</span>
                  <?php else: ?>
                    <span class="badge badge--off"><i class="fa-solid fa-arrow-down"></i> Despesa</span>
                  <?php endif; ?>
                  <div class="muted" style="margin-top:6px; font-size:12px;">
                    <?= htmlspecialchars(mb_strimwidth((string)$m['descricao'], 0, 36, '…')) ?>
                    <?php if (!empty($m['os_numero'])): ?>
                      <div class="muted">OS: <?= htmlspecialchars($m['os_numero']) ?></div>
                    <?php endif; ?>
                  </div>
                </td>
                <td><b><?= money($m['valor']) ?></b></td>
                <td>
                  <?php if ($m['status'] === 'pago'): ?>
                    <span class="badge badge--ok">Pago</span>
                  <?php elseif ($m['status'] === 'cancelado'): ?>
                    <span class="badge badge--off">Cancelado</span>
                  <?php else: ?>
                    <span class="badge">Pendente</span>
                  <?php endif; ?>
                  <div class="muted" style="margin-top:6px; font-size:12px;">Venc.: <?= dmy($m['data_vencimento']) ?></div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
