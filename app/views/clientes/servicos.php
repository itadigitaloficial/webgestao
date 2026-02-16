<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../core/Csrf.php'; ?>

<div class="page-head">
    <div>
        <h2>
            <i class="fa-solid fa-screwdriver-wrench"></i>
            Serviços do Cliente
        </h2>
        <p class="muted"><?= htmlspecialchars($cliente['nome']) ?></p>
    </div>

    <a class="btn btn--ghost" href="?r=clientes">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
</div>

<?php if (!empty($flash)): ?>
<div class="alert <?= $flash['type'] === 'error' ? 'alert--error' : '' ?>">
    <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif; ?>

<div class="card">

<form method="post" action="?r=clientes_add_servico" style="display:flex;gap:15px;margin-bottom:20px;">
    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
    <input type="hidden" name="cliente_id" value="<?= $cliente['id'] ?>">

    <select name="servico_id" required class="select">
        <option value="">Selecionar serviço</option>
        <?php foreach ($todosServicos as $s): ?>
            <option value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button class="btn btn--primary">
        <i class="fa-solid fa-plus"></i> Vincular
    </button>
</form>

<table class="table">
<thead>
<tr>
<th>Serviço</th>
<th>Status</th>
<th>Ações</th>
</tr>
</thead>
<tbody>

<?php if (empty($servicosCliente)): ?>
<tr>
<td colspan="3" class="td-empty">
Nenhum serviço vinculado.
</td>
</tr>
<?php else: ?>

<?php foreach ($servicosCliente as $s): ?>

<tr>
<td>
<i class="fa-solid fa-box"></i>
<?= htmlspecialchars($s['nome']) ?>
</td>

<td>
<?php if ($s['ativo']): ?>
<span class="badge badge--ok">Ativo</span>
<?php else: ?>
<span class="badge badge--off">Inativo</span>
<?php endif; ?>
</td>

<td>
<a class="btn btn--ghost"
href="?r=clientes_toggle_servico&id=<?= $s['id'] ?>&cliente=<?= $cliente['id'] ?>">
<i class="fa-solid fa-power-off"></i>
</a>

<a class="btn btn--ghost"
href="?r=clientes_remove_servico&id=<?= $s['id'] ?>&cliente=<?= $cliente['id'] ?>"
data-confirm="Remover este serviço do cliente?">
<i class="fa-solid fa-trash"></i>
</a>
</td>

</tr>

<?php endforeach; ?>
<?php endif; ?>

</tbody>
</table>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
