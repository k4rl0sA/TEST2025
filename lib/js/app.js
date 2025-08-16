function validateFormFields(rules) {
    rules.forEach(rule => {
        const el = document.getElementById(rule.field);
        if (el) {
            el.classList.remove('input-error');
            // Elimina mensaje de error anterior
            const msg = el.parentNode.querySelector('.error-message');
            if (msg) msg.remove();
        }
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
                // Muestra mensaje de error debajo del input
                let errorMsg = document.createElement('span');
                errorMsg.className = 'error-message';
                errorMsg.textContent = rule.message;
                el.parentNode.appendChild(errorMsg);
            }
            return false;
        }
    }
    return true;
}

// Loader barra superior
function showLoader() {
    const loader = document.getElementById('loader');
    if (loader) loader.classList.add('active');
}
function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) loader.classList.remove('active');
}


// Toast mejorado (ya tienes showToast, pero aquí con iconos y colores)
function showToast(message, type = 'info', timeout = 3500) {
    const container = document.querySelector('.toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    let icon = '';
    if (type === 'success') icon = '<i class="fa fa-check-circle"></i>';
    else if (type === 'error') icon = '<i class="fa fa-times-circle"></i>';
    else if (type === 'warning') icon = '<i class="fa fa-exclamation-triangle"></i>';
    else icon = '<i class="fa fa-info-circle"></i>';
    toast.innerHTML = `${icon}<span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('fade');
        setTimeout(() => toast.remove(), 500);
    }, timeout);
}

// Fetch universal con loader y manejo de errores/success
async function fetchWithLoader(url, options = {}, onSuccess = null, onError = null) {
    showLoader();
    try {
        const res = await fetch(url, options);
        let data;
        try{
            data = await res.json();
        }catch(e){
            hideLoader();
            window.location.href = '/index.php';
            return null;
        }
        hideLoader();
      // Si la sesión expiró, redirige
        if (data && typeof data === 'object' && data.success === false && data.redirect) {
            window.location.href = data.redirect;
            return null;
        }
        if (data.success) {
            if (onSuccess) onSuccess(data);
            if (data.message) showToast(data.message, 'success');
        } else {
            if (onError) onError(data);
            showToast(data.error || 'Error inesperado', 'error');
        }
        return data;
    } catch (e) {
        hideLoader();
        showToast('Error de red o formato de respuesta', 'error');
        if (onError) onError({error: e.message});
        return null;
    }
}

function loadSelectChoices(selectId, options, placeholder = '-- Seleccione --', selectedValue = null) {
    const select = document.getElementById(selectId);
    if (!select) return;
    const inputGroup = select.closest('.input-group');
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
            item: 'choices__item'
        }
    });
    // Selecciona valor si existe (para múltiple, acepta array)
    if (selectedValue) {
        select.choicesInstance.setChoiceByValue(selectedValue);
        inputGroup.classList.add('has-value');
    } else {
        inputGroup.classList.remove('has-value');
    }
    // Detecta cambios para subir/bajar el label dinámicamente
    select.addEventListener('change', function() {
        if (select.value) {
            inputGroup.classList.add('has-value');
        } else {
            inputGroup.classList.remove('has-value');
        }
    });
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

// Panel de filtros
const filtersPanel = document.getElementById('filters-panel');
    const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
    let filtersVisible = false;
    
    toggleFiltersBtn.onclick = function() {
        filtersVisible = !filtersVisible;
        filtersPanel.classList.toggle('hidden', !filtersVisible);
    };

    // Agrega automáticamente el token CSRF a todos los formularios antes de enviar
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form.tagName === 'FORM') {
        // Si ya existe el input, actualiza el valor
        let csrfInput = form.querySelector('input[name="csrf_token"]');
        if (!csrfInput) {
            csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            form.appendChild(csrfInput);
        }
        csrfInput.value = window.CSRF_TOKEN;
    }
}, true);

// --- Menú contextual universal ---
function toggleActionMenu(e, id) {
    e.stopPropagation();
    document.querySelectorAll('.action-menu.show').forEach(menu => menu.classList.remove('show'));
    const menu = document.getElementById(`action-menu-${id}`);
    if (menu) {
        // Calcula la posición del botón
        const btn = e.currentTarget;
        const rect = btn.getBoundingClientRect();
        // Posiciona el menú debajo del botón
        menu.style.top = `${rect.bottom + window.scrollY}px`;
        menu.style.left = `${rect.left + window.scrollX}px`;
        menu.classList.add('show');
    }
}
document.addEventListener('click', () => {
    document.querySelectorAll('.action-menu.show').forEach(menu => menu.classList.remove('show'));
});
window.addEventListener('scroll', () => {
    document.querySelectorAll('.action-menu.show').forEach(menu => menu.classList.remove('show'));
});

// Actualiza el contador de filtros activos en el botón
function updateFiltersCount(count) {
    const btn = document.getElementById('toggle-filters-btn');
    const span = document.getElementById('active-filters-count');
    if (!btn || !span) return;
    if (count > 0) {
        btn.classList.add('has-filters');
        span.classList.add('animated');
        setTimeout(() => span.classList.remove('animated'), 400);
    } else {
        btn.classList.remove('has-filters');
        span.classList.remove('animated');
    }
}

 function enableMobileRowActions() {
    if (window.innerWidth > 600) return; // Solo móvil
    document.querySelectorAll('#roles-table tbody tr').forEach(tr => {
        // Obtén el id del registro desde el botón de acciones
        const btn = tr.querySelector('.action-menu-btn');
        if (!btn) return;
        const id = btn.getAttribute('onclick').match(/\((\d+)\)/)[1];
        tr.addEventListener('click', function(e) {
            e.stopPropagation();
            // Cierra otros menús abiertos
            document.querySelectorAll('.action-menu.show').forEach(menu => menu.classList.remove('show'));
            // Abre el menú contextual de este registro
            const menu = document.getElementById(`action-menu-${id}`);
            if (menu) menu.classList.add('show');
        });
    });
    }