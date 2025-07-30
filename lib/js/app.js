/**
 * Carga dinámicamente opciones en un select usando Choices.js
 * @param {string} selectId - ID del select
 * @param {Array} options - Array de objetos {value, label}
 * @param {string} placeholder - Texto para la opción por defecto
 * @param {string|null} selectedValue - Valor a seleccionar automáticamente (si existe)
 */
function loadSelectChoices(selectId, options, placeholder = '-- Seleccione --', selectedValue = null) {
    const select = document.getElementById(selectId);
    if (!select) return;
    // Limpia opciones previas
    select.innerHTML = '';
    // Agrega placeholder
    const placeholderOption = document.createElement('option');
    placeholderOption.value = '';
    placeholderOption.textContent = placeholder;
    select.appendChild(placeholderOption);
    // Agrega opciones
    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.label;
        select.appendChild(option);
    });
    // Inicializa Choices.js (destruye si ya existe)
    if (select.choicesInstance) {
        select.choicesInstance.destroy();
    }
    select.choicesInstance = new Choices(select, {
        searchEnabled: true,
        itemSelectText: '',
        shouldSort: false,
        placeholder: true,
        placeholderValue: placeholder
    });
    // Selecciona valor si existe
    if (selectedValue) {
        select.choicesInstance.setChoiceByValue(selectedValue);
    }
}