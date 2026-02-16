<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../core/Csrf.php'; ?>

<div class="page-head">
    <div>
        <h2><i class="fa-solid fa-file-invoice"></i> Ordem de Serviço</h2>
        <p class="muted">
            Nº <?= htmlspecialchars($os['numero']) ?> • 
            Cliente: <?= htmlspecialchars($os['cliente_nome']) ?>
        </p>
    </div>
</div>

<div class="grid" style="grid-template-columns: 2fr 1fr; gap:20px;">

    <!-- ITENS -->
    <div class="card table-card">

        <div class="table-top" style="justify-content:space-between;">
            <div style="font-weight:900;">
                <i class="fa-solid fa-list"></i> Itens da OS
            </div>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th>Qtd</th>
                        <th>Preço</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                <?php if (empty($itens)): ?>
                    <tr>
                        <td colspan="5" class="td-empty">
                            Nenhum item adicionado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['servico_nome']) ?></td>
                            <td><?= number_format($item['quantidade'],2,',','.') ?></td>
                            <td>R$ <?= number_format($item['preco_unitario'],2,',','.') ?></td>
                            <td><strong>R$ <?= number_format($item['subtotal'],2,',','.') ?></strong></td>
                            <td>
                                <form method="post" action="?r=os_remove_item">
                                    <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                    <input type="hidden" name="ordem_id" value="<?= $os['id'] ?>">
                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                    <button class="btn btn--ghost btn-sm">
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

        <!-- ADICIONAR ITEM -->
        <div style="margin-top:20px;">
            <form method="post" action="?r=os_add_item" style="display:flex; gap:10px;">
                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                <input type="hidden" name="ordem_id" value="<?= $os['id'] ?>">

                <select name="servico_id" class="select" required>
                    <option value="">Selecionar serviço</option>
                    <?php foreach ($servicos as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= htmlspecialchars($s['nome']) ?> 
                            (R$ <?= number_format($s['preco_venda'],2,',','.') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="number" name="quantidade" value="1" min="1" step="1" class="input">

                <button class="btn btn--primary">
                    <i class="fa-solid fa-plus"></i> Adicionar
                </button>
            </form>
        </div>

    </div>

    <!-- RESUMO -->
    <div class="card">

        <div class="card__label">Resumo</div>

        <div style="margin-top:15px;">
            <p><strong>Status:</strong></p>

            <form method="post" action="?r=os_update_status">
                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                <input type="hidden" name="id" value="<?= $os['id'] ?>">

                <select name="status" class="select">
                    <option value="aberta" <?= $os['status']=='aberta'?'selected':'' ?>>Aberta</option>
                    <option value="em_andamento" <?= $os['status']=='em_andamento'?'selected':'' ?>>Em andamento</option>
                    <option value="concluida" <?= $os['status']=='concluida'?'selected':'' ?>>Concluída</option>
                    <option value="cancelada" <?= $os['status']=='cancelada'?'selected':'' ?>>Cancelada</option>
                </select>

                <button class="btn btn--green" style="margin-top:10px;">
                    Atualizar Status
                </button>
            </form>
        </div>

        <hr style="margin:20px 0;">

        <p><strong>Total:</strong></p>
        <h3>R$ <?= number_format($os['valor_final'],2,',','.') ?></h3>

        <hr style="margin:20px 0;">

        <form method="post" action="?r=os_delete"
              onsubmit="return confirm('Deseja realmente excluir esta OS?');">
            <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
            <input type="hidden" name="id" value="<?= $os['id'] ?>">

            <button class="btn btn--danger" style="width:100%;">
                <i class="fa-solid fa-trash"></i> Excluir OS
            </button>
        </form>

    </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
