/* =====================================================
   ITA GESTÃO - ENTERPRISE CORE JS
   - Loading em submit
   - Confirmações
   - Toast helper
   - Máscaras: CPF/CNPJ, Telefone, CEP
   - ViaCEP: preencher endereço automático
===================================================== */

document.addEventListener("DOMContentLoaded", () => {
  // Loading automático em submit
  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", () => {
      const btn = form.querySelector("button[type='submit']");
      if (btn && !btn.disabled) {
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
        btn.disabled = true;
      }
    });
  });

  // Confirmações
  document.querySelectorAll("[data-confirm]").forEach((el) => {
    el.addEventListener("click", (e) => {
      const msg = el.getAttribute("data-confirm") || "Deseja realmente continuar?";
      if (!confirm(msg)) e.preventDefault();
    });
  });

  // Toast (pode usar futuramente)
  window.showToast = function (message, type = "success") {
    const toast = document.createElement("div");
    toast.className = "toast toast--" + type;
    toast.innerHTML = `
      <span>${message}</span>
      <button class="toast__close" aria-label="Fechar">&times;</button>
    `;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 50);

    toast.querySelector(".toast__close").addEventListener("click", () => toast.remove());

    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => toast.remove(), 250);
    }, 4000);
  };

  /* ===============================
     MÁSCARAS
  =============================== */
  const onlyDigits = (v) => (v || "").toString().replace(/\D+/g, "");
  const setCaretEnd = (el) => {
    try { el.setSelectionRange(el.value.length, el.value.length); } catch (_) {}
  };

  const maskCep = (v) => {
    v = onlyDigits(v).slice(0, 8);
    if (v.length <= 5) return v;
    return v.slice(0, 5) + "-" + v.slice(5);
  };

  const maskCpfCnpj = (v) => {
    v = onlyDigits(v).slice(0, 14);
    if (v.length <= 11) {
      // CPF: 000.000.000-00
      v = v.replace(/^(\d{3})(\d)/, "$1.$2");
      v = v.replace(/^(\d{3})\.(\d{3})(\d)/, "$1.$2.$3");
      v = v.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, "$1.$2.$3-$4");
      return v;
    }
    // CNPJ: 00.000.000/0000-00
    v = v.replace(/^(\d{2})(\d)/, "$1.$2");
    v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
    v = v.replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3/$4");
    v = v.replace(/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d{1,2})$/, "$1.$2.$3/$4-$5");
    return v;
  };

  const maskPhone = (v) => {
    v = onlyDigits(v).slice(0, 11);
    if (v.length <= 10) {
      // (00) 0000-0000
      v = v.replace(/^(\d{2})(\d)/, "($1) $2");
      v = v.replace(/^(\(\d{2}\)\s)(\d{4})(\d{1,4})$/, "$1$2-$3");
      return v;
    }
    // (00) 00000-0000
    v = v.replace(/^(\d{2})(\d)/, "($1) $2");
    v = v.replace(/^(\(\d{2}\)\s)(\d{5})(\d{1,4})$/, "$1$2-$3");
    return v;
  };

  document.querySelectorAll("[data-mask]").forEach((input) => {
    const type = input.getAttribute("data-mask");
    const apply = () => {
      const old = input.value;
      if (type === "cep") input.value = maskCep(old);
      if (type === "cpfcnpj") input.value = maskCpfCnpj(old);
      if (type === "phone") input.value = maskPhone(old);
      setCaretEnd(input);
    };
    input.addEventListener("input", apply);
    input.addEventListener("blur", apply);
    // aplica no load (quando editar)
    apply();
  });

  /* ===============================
     ViaCEP (ENTERPRISE)
  =============================== */
  const form = document.querySelector("[data-enterprise-cliente]");
  if (form) {
    const cepInput = form.querySelector("[data-cep]");
    const cepBtn = form.querySelector("[data-cep-btn]");

    const endereco = form.querySelector('input[name="endereco"]');
    const bairro = form.querySelector('input[name="bairro"]');
    const cidade = form.querySelector('input[name="cidade"]');
    const estado = form.querySelector('input[name="estado"]');

    const setLoadingCep = (isLoading) => {
      if (!cepBtn) return;
      cepBtn.disabled = isLoading;
      cepBtn.innerHTML = isLoading
        ? '<i class="fa-solid fa-spinner fa-spin"></i>'
        : '<i class="fa-solid fa-magnifying-glass-location"></i>';
    };

    const fetchCep = async () => {
      const cep = onlyDigits(cepInput?.value || "");
      if (!cepInput || cep.length !== 8) {
        showToast("Informe um CEP válido (8 dígitos).", "error");
        return;
      }

      try {
        setLoadingCep(true);
        const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`, { cache: "no-store" });
        const data = await res.json();

        if (data.erro) {
          showToast("CEP não encontrado.", "error");
          return;
        }

        if (endereco && !endereco.value) endereco.value = data.logradouro || "";
        if (bairro && !bairro.value) bairro.value = data.bairro || "";
        if (cidade && !cidade.value) cidade.value = data.localidade || "";
        if (estado && !estado.value) estado.value = data.uf || "";

        showToast("Endereço preenchido pelo CEP.", "success");
      } catch (e) {
        showToast("Falha ao consultar CEP. Tente novamente.", "error");
      } finally {
        setLoadingCep(false);
      }
    };

    if (cepBtn) cepBtn.addEventListener("click", fetchCep);

    // Auto-busca quando completar 8 dígitos
    if (cepInput) {
      cepInput.addEventListener("blur", () => {
        const cep = onlyDigits(cepInput.value);
        if (cep.length === 8) fetchCep();
      });
    }

    // PF/PJ muda placeholder do documento
    const docInput = form.querySelector('input[name="cpf_cnpj"]');
    const radios = form.querySelectorAll('input[name="tipo_pessoa"]');
    const syncDocPlaceholder = () => {
      const tipo = form.querySelector('input[name="tipo_pessoa"]:checked')?.value || "fisica";
      if (!docInput) return;
      docInput.placeholder = (tipo === "juridica") ? "CNPJ" : "CPF";
    };
    radios.forEach(r => r.addEventListener("change", syncDocPlaceholder));
    syncDocPlaceholder();
  }
});
// ====== Serviços: mostrar/ocultar estoque conforme tipo ======
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("[data-servico-form]");
  if (!form) return;

  const estoqueArea = form.querySelector("[data-estoque-area]");
  const radios = form.querySelectorAll('input[name="tipo"]');

  const sync = () => {
    const tipo = form.querySelector('input[name="tipo"]:checked')?.value || "servico";
    if (!estoqueArea) return;
    estoqueArea.style.display = (tipo === "produto") ? "block" : "none";
  };

  radios.forEach(r => r.addEventListener("change", sync));
  sync();
});
