<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../../core/Csrf.php'; ?>

<div class="page-head">
  <div>
    <h2><i class="fa-solid fa-user"></i> <?= htmlspecialchars($cliente['nome'] ?? '') ?></h2>
    <p class="muted"><?= htmlspecialchars($cliente['email'] ?? '') ?></p>
  </div>

  <div class="actions">
    <a class="btn btn--ghost" href="/?r=clientes"><i class="fa-solid fa-arrow-left"></i></a>
    <a class="btn btn--primary" href="/?r=clientes_edit&id=<?= (int)$cliente['id'] ?>"><i class="fa-solid fa-pen"></i> Editar</a>
  </div>
</div>

<?php if (!empty($flash)): ?>
  <div class="alert <?= ($flash['type'] ?? '') === 'error' ? 'alert--error' : '' ?>">
    <i class="fa-solid <?= ($flash['type'] ?? '') === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-check' ?>"></i>
    <?= htmlspecialchars($flash['msg'] ?? '') ?>
  </div>
<?php endif; ?>

<div class="grid" style="grid-template-columns: 1.2fr .8fr; gap:16px;">

  <!-- ARQUIVOS -->
  <div class="card">
    <div class="section-head" style="margin-bottom:12px;">
      <div>
        <div class="section-title"><i class="fa-solid fa-paperclip"></i> Arquivos</div>
        <div class="section-sub">Anexe documentos e descreva o que é cada arquivo.</div>
      </div>
    </div>

    <form method="post" action="/?r=clientes_upload_arquivo" enctype="multipart/form-data" style="display:grid; gap:12px;">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <input type="hidden" name="cliente_id" value="<?= (int)$cliente['id'] ?>">

      <div class="input-group">
        <i class="fa-solid fa-tag"></i>
        <input type="text" name="nome_arquivo" placeholder="Nome do arquivo (ex: Contrato assinado)">
      </div>

      <textarea name="descricao" placeholder="Descrição / Observações (ex: Assinado em 10/02/2026, válido por 12 meses)" rows="3"></textarea>

      <div class="input-group">
        <i class="fa-solid fa-file-arrow-up"></i>
        <input type="file" name="arquivo" required>
      </div>

      <button class="btn btn--green" type="submit"><i class="fa-solid fa-upload"></i> Enviar arquivo</button>
    </form>

    <hr style="margin:16px 0;border-color:rgba(255,255,255,.06)">

    <?php if (empty($arquivos)): ?>
      <div class="muted">Nenhum arquivo anexado ainda.</div>
    <?php else: ?>
      <?php foreach ($arquivos as $a): ?>
        <div class="card" style="padding:14px;margin-bottom:10px;background:rgba(0,0,0,.12);">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
            <div>
              <div style="font-weight:800;">
                <i class="fa-solid fa-file-lines" style="margin-right:8px;color:var(--purple2)"></i>
                <?= htmlspecialchars($a['nome_arquivo'] ?? '') ?>
              </div>
              <?php if (!empty($a['descricao'])): ?>
                <div class="muted" style="margin-top:6px;"><?= nl2br(htmlspecialchars($a['descricao'])) ?></div>
              <?php endif; ?>
              <div class="muted" style="margin-top:8px;font-size:12px;">
                <i class="fa-regular fa-clock"></i>
                <?= !empty($a['criado_em']) ? date('d/m/Y H:i', strtotime($a['criado_em'])) : '' ?>
              </div>
            </div>

            <a class="btn btn--ghost" href="/<?= htmlspecialchars($a['caminho'] ?? '') ?>" target="_blank" title="Abrir">
              <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- COMENTÁRIOS -->
  <div class="card">
    <div class="section-head" style="margin-bottom:12px;">
      <div>
        <div class="section-title"><i class="fa-solid fa-comments"></i> Comentários</div>
        <div class="section-sub">Registro interno de contato/observações.</div>
      </div>
    </div>

    <form method="post" action="/?r=clientes_add_comentario" style="display:grid; gap:12px;">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
      <input type="hidden" name="cliente_id" value="<?= (int)$cliente['id'] ?>">
      <textarea name="comentario" placeholder="Digite um comentário..." rows="4" required></textarea>
      <button class="btn btn--primary" type="submit"><i class="fa-solid fa-paper-plane"></i> Salvar comentário</button>
    </form>

    <hr style="margin:16px 0;border-color:rgba(255,255,255,.06)">

    <?php if (empty($comentarios)): ?>
      <div class="muted">Nenhum comentário ainda.</div>
    <?php else: ?>
      <?php foreach ($comentarios as $c): ?>
        <div class="card" style="padding:14px;margin-bottom:10px;background:rgba(0,0,0,.12);">
          <div class="muted" style="font-size:12px;margin-bottom:8px;">
            <i class="fa-regular fa-clock"></i>
            <?= !empty($c['criado_em']) ? date('d/m/Y H:i', strtotime($c['criado_em'])) : '' ?>
          </div>
          <div><?= nl2br(htmlspecialchars($c['comentario'] ?? '')) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
