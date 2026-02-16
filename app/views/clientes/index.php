<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="page-head">
    <div>
        <h2><i class="fa-solid fa-users"></i> Clientes</h2>
        <p class="muted">Gerencie clientes, serviços e documentos vinculados</p>
    </div>

    <a class="btn btn--green" href="/?r=clientes_create">
        <i class="fa-solid fa-user-plus"></i> Novo Cliente
    </a>
</div>

<?php if (!empty($flash)): ?>
<div class="alert <?= ($flash['type'] ?? '') === 'error' ? 'alert--error' : '' ?>">
    <i class="fa-solid <?= ($flash['type'] ?? '') === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-check' ?>"></i>
    <?= htmlspecialchars($flash['msg'] ?? '') ?>
</div>
<?php endif; ?>

<div class="card table-card">

<div class="table-wrap">
<table class="table">
<thead>
<tr>
<th>Cliente</th>
<th>Email</th>
<th>Serviços</th>
<th>Status</th>
<th class="th-actions" style="width:170px;">Ações</th>
</tr>
</thead>

<tbody>

<?php if (!empty($clientes)): ?>
<?php foreach ($clientes as $c): ?>

<tr>

<td>
<div class="td-title">
    <div class="avatar">
        <i class="fa-solid fa-user"></i>
    </div>
    <div>
        <div class="name"><?= htmlspecialchars($c['nome']) ?></div>
        <div class="sub">ID #<?= (int)$c['id'] ?></div>
    </div>
</div>
</td>

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
<span class="badge badge--ok">
<i class="fa-solid fa-circle-check"></i> Ativo
</span>
<?php else: ?>
<span class="badge badge--off">
<i class="fa-solid fa-circle-xmark"></i> Inativo
</span>
<?php endif; ?>
</td>

<td class="td-actions">

<a class="btn btn--ghost"
   href="/?r=clientes_edit&id=<?= (int)$c['id'] ?>"
   title="Editar Cliente">
<i class="fa-solid fa-pen"></i>
</a>

<a class="btn btn--ghost"
   href="/?r=clientes_servicos&id=<?= (int)$c['id'] ?>"
   title="Gerenciar Serviços">
<i class="fa-solid fa-screwdriver-wrench"></i>
</a>

<a class="btn btn--ghost"
   href="/?r=clientes_view&id=<?= (int)$c['id'] ?>"
   title="Arquivos e Comentários">
<i class="fa-solid fa-folder-open"></i>
</a>

</td>

</tr>

<?php endforeach; ?>
<?php else: ?>

<tr>
<td colspan="5" class="td-empty">
<i class="fa-solid fa-circle-info"></i>
Nenhum cliente cadastrado.
</td>
</tr>

<?php endif; ?>

</tbody>
</table>
</div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
