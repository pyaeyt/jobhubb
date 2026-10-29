(function () {
  const stipend = document.querySelector('#stipend');
  const radios = document.querySelectorAll('input[name="paid"]');

  function syncStipend() {
    if (!stipend || !radios.length) return;
    const selected = document.querySelector('input[name="paid"]:checked');
    const paid = selected && selected.value === '1';
    stipend.disabled = !paid;
    const field = document.querySelector('[data-stipend]');
    if (field) field.hidden = !paid;
  }

  radios.forEach((radio) => radio.addEventListener('change', syncStipend));
  syncStipend();

  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const message = form.getAttribute('data-confirm');
      if (message && !window.confirm(message)) event.preventDefault();
    });
  });
})();
