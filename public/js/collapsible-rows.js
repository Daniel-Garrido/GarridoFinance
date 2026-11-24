/* ============================================================
   FILAS DESPLEGABLES EN MÓVIL (.gf-table-collapsible)
   Transacciones y transferencias: al tocar ">" (o la fecha corta, el título,
   el subtítulo o el monto) se despliega u oculta el detalle de la fila.
   En escritorio el botón ">" está oculto y no hace nada.
   ============================================================ */
(function () {
    const TRIGGERS = '.gf-row-toggle, .gf-row-day, .gf-row-title, .gf-row-subtitle, .gf-row-amount';

    document.querySelectorAll('.gf-table-collapsible').forEach(function (table) {
        table.addEventListener('click', function (event) {
            const trigger = event.target.closest(TRIGGERS);
            if (!trigger) return;

            const row = trigger.closest('.gf-table-row');
            const toggle = row.querySelector('.gf-row-toggle');
            if (!toggle || getComputedStyle(toggle).display === 'none') return; // escritorio

            const open = row.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? 'Ocultar detalles' : 'Ver detalles');
        });
    });
})();
