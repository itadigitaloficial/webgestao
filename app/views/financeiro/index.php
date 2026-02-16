<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../core/Csrf.php'; ?>

<div class="page-head">
  <div>
    <h2><i class="fa-solid fa-chart-line"></i> Financeiro</h2>
    <p class="muted">Controle completo de receitas e despesas</p>
  </div>
</div>

<div class="grid" style="grid-template-columns: repeat(4, 1fr);">

<div class="card">
<div class="card__label">Receitas</div>
<div class="card__value text-green">R$ <?= number_format($resumo['receitas'],2,',','.') ?></div>
</div>

<div class="card">
<div class="card__label">Despesas</div>
<div class="card__value text-red">R$ <?= number_format($resumo['despesas'],2,',','.') ?></div>
</div>

<div class="card">
<div class="card__label">Pendentes</div>
<div class="card__value text-warning">R$ <?= number_format($resumo['pendente'],2,',','.') ?></div>
</div>

<div class="card">
<div class="card__label">Saldo</div>
<div class="card__value text-purple">R$ <?= number_format($resumo['saldo'],2,',','.') ?></div>
</div>

</div>

<div class="card table-card" style="margin-top:20px;">

<form method="get" style="display:flex; gap:10px; margin-bottom:15px;">
<input type="hidden" name="r" value="financeiro">

<select name="tipo" class="select">
<option value="">Todos</option>
<option value="receita">Receita</option>
<option value="despesa">Despesa</option>
</select>

<select name="status" class="select">
<option value="">Todos</option>
<option value="pendente">Pendente</option>
<option value="pago">Pago</option>
<option value="atrasado">Atrasado</option>
</select>

<button class="btn btn--primary">
<i class="fa-solid fa-filter"></i> Filtrar
</button>
</form>

<table class="table">
<thead>
<tr>
<th>Descrição</th>
<th>Cliente</th>
<th>OS</th>
<th>Conta</th>
<th>Valor</th>
<th>Status</th>
<th>Ação</th>
</tr>
</thead>
<tbody>

<?php foreach ($movs as $m): ?>

<tr>
<td><?= htmlspecialchars($m['descricao']) ?></td>
<td><?= htmlspecialchars($m['cliente_nome'] ?? '-') ?></td>
<td><?= htmlspecialchars($m['os_numero'] ?? '-') ?></td>
<td><?= htmlspecialchars($m['conta_nome'] ?? '-') ?></td>
<td><strong>R$ <?= number_format($m['valor'],2,',','.') ?></strong></td>

<td>
<?php if($m['status']=='pago'): ?>
<span class="badge badge--ok">Pago</span>
<?php else: ?>
<span class="badge">Pendente</span>
<?php endif; ?>
</td>

<td>
<?php if($m['status']!='pago'): ?>
<form method="post" action="?r=financeiro_pagar">
<input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
<input type="hidden" name="id" value="<?= $m['id'] ?>">
<button class="btn btn--green btn-sm">
<i class="fa-solid fa-check"></i>
</button>
</form>
<?php endif; ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
