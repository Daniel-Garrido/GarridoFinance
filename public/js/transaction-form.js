// Formularios de transacción: la categoría solo muestra las del tipo elegido
function filterCategoriesByType(typeSelect, categorySelect) {
    const selectedType = typeSelect.value;

    categorySelect.querySelectorAll('option').forEach(option => {
        option.hidden = option.dataset.type !== selectedType;
        if (option.hidden && option.selected) option.selected = false;
    });

    const current = categorySelect.querySelector('option:checked');
    if (!current || current.hidden) {
        const firstVisible = categorySelect.querySelector('option:not([hidden])');
        if (firstVisible) firstVisible.selected = true;
    }
}

// Conecta los selects de un formulario según su prefijo ('create' | 'edit')
function initTransactionForm(prefix) {
    const typeSelect = document.getElementById(prefix + '_type');
    const categorySelect = document.getElementById(prefix + '_category_id');

    typeSelect.addEventListener('change', () => filterCategoriesByType(typeSelect, categorySelect));
    filterCategoriesByType(typeSelect, categorySelect);
}
