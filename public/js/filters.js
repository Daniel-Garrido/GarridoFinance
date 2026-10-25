/* ============================================================
   FILTROS COMPARTIDOS DE LOS LISTADOS
   - Desplegables (x-filter-dropdown): guardan el valor en su input oculto
     y disparan "change" para que cada vista reaccione.
   - Periodo (x-period-filter): muestra solo el campo de la opción elegida.
   - Buscador ligado al formulario de filtros: al borrarlo con la "x" se vuelve a filtrar.
   ============================================================ */
(function () {
    document.querySelectorAll('.gf-filter-dropdown').forEach(function (dropdown) {
        const input = dropdown.querySelector('input[type="hidden"]');
        const label = dropdown.querySelector('.gf-dropdown-value');

        dropdown.querySelectorAll('.dropdown-item').forEach(function (item) {
            item.addEventListener('click', function () {
                dropdown.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                input.value = item.dataset.value;
                label.textContent = item.textContent.trim();
                input.dispatchEvent(new Event('change'));
            });
        });
    });

    const period = document.getElementById('filter_period');
    if (period) {
        period.addEventListener('change', function () {
            document.querySelectorAll('.gf-period-input').forEach(function (field) {
                field.hidden = field.dataset.period !== period.value;
            });
        });
    }

    document.querySelectorAll('input[type="search"][form]').forEach(function (search) {
        search.addEventListener('search', function () {
            if (this.value === '') document.getElementById(this.getAttribute('form')).submit();
        });
    });
})();
