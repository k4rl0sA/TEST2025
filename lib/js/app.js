function validateFormFields(rules) {
    rules.forEach(rule => {
        const el = document.getElementById(rule.field);
        if (el) el.classList.remove('input-error');
    });
    for (const rule of rules) {
        const el = document.getElementById(rule.field);
        if (!el) continue;
        const value = (el.type === 'checkbox' || el.type === 'radio') ? el.checked : el.value.trim();
        if (!rule.validate(value, el)) {
            showToast(rule.message, 'error');
            if (el.type !== 'hidden' && el.offsetParent !== null) {
                el.classList.add('input-error');
                el.focus();
            }
            return false;
        }
    }
    return true;
}

function showToast(message, type = 'info', timeout = 3500) {
    const container = document.querySelector('.toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('fade');
        setTimeout(() => toast.remove(), 500);
    }, timeout);
}

async function fetchJsonWithSessionCheck(url, options) {
    const res = await fetch(url, options);
    let data;
    try {
        data = await res.json();
    } catch (e) {
        window.location.href = '/index.php';
        return null;
    }
    // Solo redirige si es un objeto con success === false
    if (data && typeof data === 'object' && !Array.isArray(data) && data.success === false && data.error) {
        window.location.href = '/index.php';
        return null;
    }
    return data;
}

function loadSelectChoices(selectId, options, placeholder = '-- Seleccione --', selectedValue = null) {
    const select = document.getElementById(selectId);
    if (!select) return;

    // Destruye instancia previa de Choices si existe
    if (select.choicesInstance) {
        select.choicesInstance.destroy();
        select.choicesInstance = null;
    }

    // Limpia opciones previas
    select.innerHTML = '';

    // Si es múltiple, no agregues placeholder como opción seleccionable
    if (!select.multiple && placeholder) {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder;
        select.appendChild(opt);
    }

    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.label;
        if (selectedValue && selectedValue == opt.value) option.selected = true;
        select.appendChild(option);
    });

    // Inicializa Choices.js
    select.choicesInstance = new Choices(select, {
        removeItemButton: select.multiple,
        searchEnabled: true,
        shouldSort: false,
        placeholder: true,
        placeholderValue: placeholder,
        itemSelectText: '',
        classNames: {
            containerOuter: select.multiple ? 'choices app-select-multiple' : 'choices app-select-single',
            input: 'choices__input',
            list: 'choices__list',
            item: 'choices__item',
            // Puedes agregar más clases personalizadas si lo deseas
        }
    });

    // Selecciona valor si existe (para múltiple, acepta array)
    if (selectedValue) {
        select.choicesInstance.setChoiceByValue(selectedValue);
    }
}

function loadSelectChoicesSafe(selectId, endpoint, placeholder = '-- Seleccione --', selectedValue = null, retries = 5) {
    const select = document.getElementById(selectId);
    if (!select && retries > 0) {
        setTimeout(() => loadSelectChoicesSafe(selectId, endpoint, placeholder, selectedValue, retries - 1), 100);
        return;
    }
    if (select) {
        fetchJsonWithSessionCheck(endpoint)
            .then(data => {
                if (data) loadSelectChoices(selectId, data, placeholder, selectedValue);
            });
    }
}

function initDynamicSelect(selectId, endpoint, placeholder = '-- Seleccione --', onChange = null) {
    loadSelectChoicesSafe(selectId, endpoint, placeholder);
    const select = document.getElementById(selectId);
    if (select && typeof onChange === 'function') {
        select.addEventListener('change', onChange);
    }
}

const filtersPanel = document.getElementById('filters-panel');
const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
let filtersVisible = true;
toggleFiltersBtn.onclick = function() {
    filtersVisible = !filtersVisible;
    filtersPanel.classList.toggle('hidden', !filtersVisible);
    toggleFiltersBtn.innerHTML = filtersVisible
        ? '<i class="fas fa-filter"></i> Filtros'
        : '<i class="fas fa-filter"></i> Mostrar Filtros';
};