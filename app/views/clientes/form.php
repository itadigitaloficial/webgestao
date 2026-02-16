<?php
require __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../core/Csrf.php';

$tipo = $cliente['tipo_pessoa'] ?? 'fisica';
$ativo = isset($cliente) ? (int)($cliente['ativo'] ?? 1) : 1;
?>

<div class="form-wrapper">

  <div class="form-card enterprise">

    <div class="form-header">
      <div class="form-header__left">
        <div class="kicker"><i class="fa-solid fa-sparkles"></i> Cadastro Enterprise</div>
        <h2>
          <i class="fa-solid fa-address-card"></i>
          <?= isset($cliente) ? 'Editar Cliente' : 'Novo Cliente' ?>
        </h2>
        <p>Dados completos PF/PJ, com preenchimento automático de endereço via CEP.</p>
      </div>

      <div class="form-header__right">
        <a class="btn btn--ghost" href="?r=clientes" title="Voltar">
          <i class="fa-solid fa-arrow-left"></i>
        </a>
      </div>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="alert <?= ($flash['type'] ?? '') === 'error' ? 'alert--error' : '' ?>">
        <i class="fa-solid <?= ($flash['type'] ?? '') === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-check' ?>"></i>
        <?= htmlspecialchars($flash['msg'] ?? '') ?>
      </div>
    <?php endif; ?>

    <form method="post"
          action="?r=<?= isset($cliente) ? 'clientes_update' : 'clientes_store' ?>"
          class="form-body"
          data-enterprise-cliente>

      <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

      <?php if(isset($cliente)): ?>
        <input type="hidden" name="id" value="<?= (int)$cliente['id'] ?>">
      <?php endif; ?>

      <!-- Top controls -->
      <div class="form-topbar">
        <div class="segmented" role="tablist" aria-label="Tipo de pessoa">
          <label class="segmented__item">
            <input type="radio" name="tipo_pessoa" value="fisica" <?= $tipo === 'fisica' ? 'checked' : '' ?>>
            <span><i class="fa-solid fa-user"></i> Pessoa Física</span>
          </label>
          <label class="segmented__item">
            <input type="radio" name="tipo_pessoa" value="juridica" <?= $tipo === 'juridica' ? 'checked' : '' ?>>
            <span><i class="fa-solid fa-building"></i> Pessoa Jurídica</span>
          </label>
        </div>

        <label class="toggle">
          <input type="checkbox" name="ativo" value="1" <?= $ativo === 1 ? 'checked' : '' ?>>
          <span class="toggle__ui"></span>
          <span class="toggle__label"><i class="fa-solid fa-circle-check"></i> Ativo</span>
        </label>
      </div>

      <!-- Identificação -->
      <section class="form-section">
        <div class="section-head">
          <div class="section-title"><i class="fa-solid fa-id-card"></i> Identificação</div>
          <div class="section-sub">Nome/Razão social, documento e contato principal.</div>
        </div>

        <div class="form-grid">
          <div class="input-group">
            <i class="fa-solid fa-user-tag"></i>
            <input type="text" name="nome" placeholder="Nome / Razão Social" required
                   value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-file-lines"></i>
            <input type="text" name="cpf_cnpj" placeholder="CPF" data-mask="cpfcnpj"
                   value="<?= htmlspecialchars($cliente['cpf_cnpj'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" placeholder="E-mail"
                   value="<?= htmlspecialchars($cliente['email'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-phone"></i>
            <input type="text" name="telefone" placeholder="Telefone" data-mask="phone"
                   value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-mobile-screen"></i>
            <input type="text" name="celular" placeholder="Celular" data-mask="phone"
                   value="<?= htmlspecialchars($cliente['celular'] ?? '') ?>">
          </div>
        </div>
      </section>

      <!-- Endereço -->
      <section class="form-section">
        <div class="section-head">
          <div class="section-title"><i class="fa-solid fa-location-dot"></i> Endereço</div>
          <div class="section-sub">Digite o CEP para preencher automaticamente.</div>
        </div>

        <div class="form-grid">
          <div class="input-group">
            <i class="fa-solid fa-map-pin"></i>
            <input type="text" name="cep" placeholder="CEP" data-mask="cep" data-cep
                   value="<?= htmlspecialchars($cliente['cep'] ?? '') ?>">
            <button class="inset-btn" type="button" data-cep-btn title="Buscar CEP">
              <i class="fa-solid fa-magnifying-glass-location"></i>
            </button>
          </div>

          <div class="input-group span-2">
            <i class="fa-solid fa-road"></i>
            <input type="text" name="endereco" placeholder="Endereço"
                   value="<?= htmlspecialchars($cliente['endereco'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-hashtag"></i>
            <input type="text" name="numero" placeholder="Número"
                   value="<?= htmlspecialchars($cliente['numero'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-square-plus"></i>
            <input type="text" name="complemento" placeholder="Complemento"
                   value="<?= htmlspecialchars($cliente['complemento'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-city"></i>
            <input type="text" name="bairro" placeholder="Bairro"
                   value="<?= htmlspecialchars($cliente['bairro'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-building"></i>
            <input type="text" name="cidade" placeholder="Cidade"
                   value="<?= htmlspecialchars($cliente['cidade'] ?? '') ?>">
          </div>

          <div class="input-group">
            <i class="fa-solid fa-flag"></i>
            <input type="text" name="estado" maxlength="2" placeholder="UF"
                   value="<?= htmlspecialchars($cliente['estado'] ?? '') ?>">
          </div>
        </div>
      </section>

      <!-- Observações -->
      <section class="form-section">
        <div class="section-head">
          <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Observações</div>
          <div class="section-sub">Informações internas, preferências, detalhes importantes.</div>
        </div>

        <textarea name="observacoes" placeholder="Observações..." rows="4"><?= htmlspecialchars($cliente['observacoes'] ?? '') ?></textarea>
      </section>

      <div class="form-footer">
        <button class="btn btn--primary" type="submit">
          <i class="fa-solid fa-floppy-disk"></i> Salvar Cliente
        </button>
      </div>

    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
