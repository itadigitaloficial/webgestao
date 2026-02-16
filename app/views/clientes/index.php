<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-head">
    <div>
        <h2><i class="fa-solid fa-users"></i> Clientes</h2>
        <p class="muted">Gerencie clientes e serviços vinculados</p>
    </div>
    <a class="btn btn--green" href="?r=clientes_novo">
        <i class="fa-solid fa-user-plus"></i> Novo Cliente
    </a>
</div>

<div class="card table-card">

<table class="table">
<thead>
<tr>
<th>Cliente</th>
<th>Email</th>
<th>Serviços</th>
<th>Status</th>
<th>Ações</th>
</tr>
</thead>
<tbody>

<?php foreach ($clientes as $c): ?>

<tr>
<td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
<td><?= htmlspecialchars($c['email']) ?></td>

<td>
<span class="badge">
<i class="fa-solid fa-layer-group"></i>
<?= (int)$c['total_servicos'] ?> total
</span>

<span class="badge badge--ok">
<i class="fa-solid fa-circle-check"></i>
<?= (int)$c['servicos_ativos'] ?> ativos
</span>
</td>

<td>
<?php if ($c['ativo']): ?>
<span class="badge badge--ok">Ativo</span>
<?php else: ?>
<span class="badge badge--off">Inativo</span>
<?php endif; ?>
</td>

<td>
<a class="btn btn--ghost" href="?r=clientes_editar&id=<?= $c['id'] ?>">
<i class="fa-solid fa-pen"></i>
</a>
<a class="btn btn--ghost" href="?r=clientes_servicos&id=<?= $c['id'] ?>">
<i class="fa-solid fa-screwdriver-wrench"></i>
</a>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
