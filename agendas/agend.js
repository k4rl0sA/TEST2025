// --- ELEMENTOS DEL DOM ---
const profileSelect = document.getElementById('profile');
const professionalSelect = document.getElementById('professional');
const calendarContainer = document.getElementById('calendar');
const weekRangeEl = document.getElementById('week-range');
const prevWeekBtn = document.getElementById('prev-week');
const nextWeekBtn = document.getElementById('next-week');
const noProfessionalSelected = document.getElementById('no-professional-selected');
const calendarView = document.getElementById('calendar-container');

// Modal elements
const modal = document.getElementById('appointment-window');
const modalTitle = document.getElementById('win-title');
const closeModalBtn = document.getElementById('close-win');
const cancelBtn = document.getElementById('cancel-btn');
const form = document.getElementById('appointment-form');
const searchPatientBtn = document.getElementById('search-patient');
const docNumberInput = document.getElementById('doc-number');
const docTypeInput = document.getElementById('doc-type');

const patientInfoFields = ['full-name', 'phone', 'address', 'activity', 'appointment-date', 'notes'];
const submitBtn = document.getElementById('submit-btn');
const statusSection = document.getElementById('appointment-status-section');
const updateStatusBtn = document.getElementById('update-status-btn');
const reassignBtn = document.getElementById('reassign-btn');
const appointmentStatusSelect = document.getElementById('appointment-status');

// --- ESTADO DE LA APLICACIÓN ---
let currentDate = new Date();
let selectedProfessionalId = null;
let appointments = [];
let selectedProfiles = Array.from(profileSelect.selectedOptions).map(opt => opt.value);

// --- INICIALIZACIÓN ---
document.addEventListener('DOMContentLoaded', () => {
    initDynamicSelect('profile', '/agendas/lib.php?a=getProfiles', '-- Seleccione un Perfil --', updateProfileSelection);
    initDynamicSelect('doc-type', '/agendas/lib.php?a=getDocTypes', '-- Seleccione un Tipo de Documento --');
    // ...otros selects dinámicos aquí si los tienes...
    init();
});

function init() {
    professionalSelect.addEventListener('change', onProfessionalChange);
    prevWeekBtn.addEventListener('click', () => changeWeek(-1));
    nextWeekBtn.addEventListener('click', () => changeWeek(1));
    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    form.addEventListener('submit', handleFormSubmit);
    searchPatientBtn.addEventListener('click', searchPatient);
    updateStatusBtn.addEventListener('click', updateAppointmentStatus);
    reassignBtn.addEventListener('click', reassignAppointment);
    appointmentStatusSelect.addEventListener('change', () => {
        reassignBtn.classList.toggle('hidden', appointmentStatusSelect.value !== 'Reasignado');
    });
    calendarContainer.addEventListener('click', handleCalendarClick);
    updateCalendar();
}

// --- FUNCIONES DE SELECTS DINÁMICOS Y REUTILIZABLES ---
function initDynamicSelect(selectId, endpoint, placeholder = '-- Seleccione --', onChange = null) {
    loadSelectChoicesSafe(selectId, endpoint, placeholder);
    const select = document.getElementById(selectId);
    if (select && typeof onChange === 'function') {
        select.addEventListener('change', onChange);
    }
}

function cargarProfesionales(profileIds) {
    loadSelectChoicesSafe(
        'professional',
        `/agendas/lib.php?a=getProfessionals&profileId=${profileIds}`,
        '-- Seleccione un Profesional --'
    );
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

// --- LÓGICA DE SELECTS DEPENDIENTES ---
function onProfessionalChange() {
    selectedProfessionalId = parseInt(professionalSelect.value);
    updateCalendar();
}

// --- LÓGICA DEL CALENDARIO ---
function changeWeek(direction) {
    currentDate.setDate(currentDate.getDate() + 7 * direction);
    updateCalendar();
}

function getWeekDays(date) {
    const startOfWeek = new Date(date);
    const day = startOfWeek.getDay();
    const diff = startOfWeek.getDate() - day + (day === 0 ? -6 : 1);
    startOfWeek.setDate(diff);

    const week = [];
    for (let i = 0; i < 7; i++) {
        const weekDay = new Date(startOfWeek);
        weekDay.setDate(startOfWeek.getDate() + i);
        week.push(weekDay);
    }
    return week;
}

async function updateCalendar() {
    if (!selectedProfessionalId) {
        calendarView.classList.add('hidden');
        noProfessionalSelected.classList.remove('hidden');
        modal.classList.add('hidden');
        weekRangeEl.textContent = '';
        return;
    }

    calendarView.classList.remove('hidden');
    noProfessionalSelected.classList.add('hidden');

    const weekDays = getWeekDays(currentDate);
    const firstDay = weekDays[0];
    const lastDay = weekDays[6];

    weekRangeEl.textContent = `Semana ${firstDay.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' })} - ${lastDay.toLocaleDateString('es-ES', { month: 'short', day: 'numeric', year: 'numeric' })}`;
    // Obtener citas del backend
    const weekStart = firstDay.toISOString().split('T')[0];
    const weekEnd = lastDay.toISOString().split('T')[0];
    appointments = await getAppointments(selectedProfessionalId, weekStart, weekEnd);
    if (!Array.isArray(appointments)) {
        appointments = [];
    }
    renderCalendarGrid(weekDays);
}

function renderCalendarGrid(weekDays) {
    calendarContainer.innerHTML = '';
    const grid = document.createElement('div');
    grid.className = 'calendar-grid';

    // Header
    grid.innerHTML += `<div class="font-bold text-center p-2 border-b border-r">Cupo</div>`;
    weekDays.forEach(day => {
        grid.innerHTML += `<div class="font-bold text-center p-2 border-b">${day.toLocaleDateString('es-ES', { weekday: 'short' })}<br>${day.getDate()}/${day.getMonth() + 1}</div>`;
    });

    // Slots (8 per day)
    for (let i = 1; i <= 8; i++) {
    const cupo = `${i}`;
    grid.innerHTML += `<div class="font-bold text-center p-2 border-r">${cupo}</div>`;
    weekDays.forEach(day => {
        const dateStr = day.toISOString().split('T')[0];
        const appointment = findAppointment(dateStr, cupo);

            let cellClass = 'bg-green hover:bg-green cursor-pointer';
            let cellContent = 'Disponible';
            let appointmentId = '';

            if (appointment) {
                switch (appointment.status) {
                    case '1':
                        cellClass = 'bg-red hover:bg-red cursor-pointer';
                        cellContent = 'Ocupado';
                        break;
                    case '2':
                        cellClass = 'bg-blue hover:bg-blue cursor-pointer';
                        cellContent = 'Realizado';
                        break;
                    case '3':
                        cellClass = 'bg-yellow hover:bg-yellow cursor-pointer';
                        cellContent = 'Reasignado';
                        break;
                }
                appointmentId = appointment.id;
            }

            grid.innerHTML += `<div class="text-center p-2 border-t text-sm ${cellClass} transition duration-200 calendar-slot" data-date="${dateStr}" data-cupo="${cupo}" data-appointment-id="${appointmentId}">${cellContent}</div>`;

        });
    }
    calendarContainer.appendChild(grid);
}

function findAppointment(date,cupo) {
    if (!selectedProfessionalId) return undefined;
    return appointments.find(a =>
        String(a.professionalId) === String(selectedProfessionalId) &&
        a.date === date &&
        String(a.cupo) === String(cupo)
    );
}

// --- LÓGICA DEL MODAL ---
function handleCalendarClick(event) {
    const slot = event.target.closest('.calendar-slot');
    if (!slot) return;

    const date = slot.dataset.date;
    const cupo = slot.dataset.cupo;
    const appointmentId = slot.dataset.appointmentId || null;

    openModal(date, cupo, appointmentId);
}

function openModal(date, cupo, appointmentId = null) {
    form.reset();
    resetModalState();
    loadSelectChoicesSafe('doc-type', '/agendas/lib.php?a=getDocTypes', '-- Seleccione un Tipo de Documento --');

    document.getElementById('slot-date').value = date;
    document.getElementById('slot-time').value = cupo;
    document.getElementById('slot-professional-id').value = selectedProfessionalId;

    const appointment = appointmentId ? appointments.find(a => a.id == appointmentId) : null;

    if (appointment) {
        modalTitle.textContent = 'Detalles de la Cita';
        fillFormWithAppointmentData(appointment);
        setFormReadOnly(true);
        submitBtn.classList.add('hidden');
        statusSection.classList.remove('hidden');
        appointmentStatusSelect.value = appointment.status;
        reassignBtn.classList.toggle('hidden', appointment.status !== 'Reasignado');
    } else {
        modalTitle.innerHTML = '';
        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-calendar-days';
        modalTitle.appendChild(icon);
        modalTitle.appendChild(document.createTextNode(' Programar Agenda'));
        const today = new Date().toISOString().split('T')[0];
        const maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 15);
        const maxDateStr = maxDate.toISOString().split('T')[0];

        document.getElementById('appointment-date').value = date;
        document.getElementById('appointment-date').min = today;
        document.getElementById('appointment-date').max = maxDateStr;

        setFormReadOnly(false);
        submitBtn.classList.remove('hidden');
        statusSection.classList.add('hidden');
    }

    modal.classList.remove('hidden');
    calendarView.classList.add('hidden');
}

function closeModal() {
    calendarView.classList.remove('hidden');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

function resetModalState() {
    form.reset();
    document.getElementById('appointment-id').value = '';
    patientInfoFields.forEach(id => {
        const el = document.getElementById(id);
        el.value = '';
        el.readOnly = false;
    });
    document.getElementById('doc-type').disabled = false;
    document.getElementById('doc-number').readOnly = false;
    searchPatientBtn.disabled = false;
}

function setFormReadOnly(isReadOnly) {
    patientInfoFields.forEach(id => {
        document.getElementById(id).readOnly = isReadOnly;
    });
    document.getElementById('doc-type').disabled = isReadOnly;
    document.getElementById('doc-number').readOnly = isReadOnly;
    searchPatientBtn.disabled = isReadOnly;
}

function fillFormWithAppointmentData(appointment) {
    document.getElementById('appointment-id').value = appointment.id;
    document.getElementById('doc-type').value = appointment.patient.docType;
    document.getElementById('doc-number').value = appointment.patient.docNumber;
    document.getElementById('full-name').value = appointment.patient.fullName;
    document.getElementById('phone').value = appointment.patient.phone;
    document.getElementById('address').value = appointment.patient.address;
    document.getElementById('activity').value = appointment.activity;
    document.getElementById('appointment-date').value = appointment.date;
    document.getElementById('notes').value = appointment.notes;
}

// --- LÓGICA DEL FORMULARIO ---
function searchPatient() {
    const docType = docTypeInput.value;
    const docNumber = docNumberInput.value.trim();

    if (!docNumber) {
        showToast('Por favor, ingrese un número de documento.', 'warning');
        return;
    }

    fetch(`/agendas/lib.php?a=searchPatient&docType=${encodeURIComponent(docType)}&docNumber=${encodeURIComponent(docNumber)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.patient) {
                document.getElementById('full-name').value = data.patient.fullName;
                document.getElementById('phone').value = data.patient.phone;
                document.getElementById('address').value = data.patient.address;
                // Guarda los IDs ocultos en el input
                docNumberInput.dataset.idpeople = data.patient.idpeople;
                docNumberInput.dataset.idgeo = data.patient.idgeo;
                showToast('Paciente encontrado.', 'success');
            } else {
                showToast('Paciente no encontrado. Por favor, complete la información.', 'warning');
                document.getElementById('full-name').value = '';
                document.getElementById('phone').value = '';
                document.getElementById('address').value = '';
                docNumberInput.dataset.idpeople = '';
                docNumberInput.dataset.idgeo = '';
                document.getElementById('full-name').focus();
            }
        })
        .catch(() => showToast('Error al buscar paciente.', 'error'));
}

function handleFormSubmit(e) {
    e.preventDefault();
    showSpinner();

    // Debes tener estos datos del paciente tras la búsqueda
    const idpeople = document.getElementById('doc-number').dataset.idpeople || '';
    const idgeo = document.getElementById('doc-number').dataset.idgeo || '';
    const cupo = parseInt(document.getElementById('slot-time').value);
    const profesionalid = parseInt(document.getElementById('slot-professional-id').value);
    const fecha = document.getElementById('appointment-date').value;
    const actividad = parseInt(document.getElementById('activity').value);
    const notas = document.getElementById('notes').value.trim();

    // Validación básica
     if (!idpeople || !idgeo || !cupo || !profesionalid || !fecha) {
        showToast('Todos los campos obligatorios deben estar completos.', 'error');
        hideSpinner();
        return;
    }

    const newAppointment = {
        cupo,
        profesionalid,
        idpeople: parseInt(idpeople),
        idgeo: parseInt(idgeo),
        fecha,
        actividad: isNaN(actividad) ? null : actividad,
        notas
    };

    fetch('/agendas/lib.php?a=saveAppointment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(newAppointment)
    })
    .then(res => res.json())
    .then(data => {
        hideSpinner();
        if (data.success) {
            showToast('Cita programada exitosamente.', 'success');
            closeModal();
            updateCalendar();
        } else {
            showToast('Error al guardar la cita: ' + (data.error || ''), 'error');
        }
    })
    .catch(() => {
        hideSpinner();
        showToast('Error de red al guardar la cita.', 'error');
    });
}

function updateAppointmentStatus() {
    const appointmentId = document.getElementById('appointment-id').value;
    const newStatus = appointmentStatusSelect.value;

    fetch('/agendas/lib.php?a=updateAppointmentStatus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: appointmentId, status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(`El estado de la cita ha sido actualizado a "${newStatus}".`, 'success'); 
            if (newStatus !== 'Reasignado') {
                closeModal();
            }
            updateCalendar();
        } else {
            showToast('Error al actualizar el estado de la cita: ' + (data.error || ''), 'error');
        }
    })
    .catch(() => showToast('Error de red al actualizar el estado de la cita.', 'error'));
}

function reassignAppointment() {
        showToast('Funcionalidad de reasignación: Por favor, seleccione un nuevo cupo disponible en el calendario para mover esta cita.', 'info');
    closeModal();
}

function getAppointments(professionalId, weekStart, weekEnd) {
    return fetch(`/agendas/lib.php?a=getAppointments&professionalId=${professionalId}&weekStart=${weekStart}&weekEnd=${weekEnd}`)
        .then(res => res.json())
        .then(data => Array.isArray(data) ? data : []);
}

// --- CONTROL DE SESIÓN Y FETCH SEGURO ---
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

// --- UTILIDADES ---
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

function showSpinner() {
    document.getElementById('loading-spinner').classList.remove('hidden');
}
function hideSpinner() {
    document.getElementById('loading-spinner').classList.add('hidden');
}

// --- CÓDIGO NUEVO ---
function updateProfileSelection() {
    const selectedOptions = Array.from(profileSelect.selectedOptions);
    selectedProfiles = selectedOptions.map(option => option.value);
    const profileIds = selectedProfiles.join(',');
    // Llama a la función para cargar los profesionales dependientes
    cargarProfesionales(profileIds);
    // Aquí puedes hacer algo con los IDs de los perfiles seleccionados, si es necesario
    console.log('Perfiles seleccionados:', profileIds);
}