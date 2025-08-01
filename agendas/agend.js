// --- ESTADO DE LA APLICACIÓN ---
let currentDate = new Date();
let selectedProfessionalId = null;
let appointments = []; // Aquí se guardan las citas de la semana actual

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

// --- INICIALIZACIÓN ---
document.addEventListener('DOMContentLoaded', async () => {
    await loadOptions();
    init();
});

function init() {
    profileSelect.addEventListener('change', onProfileChange);
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

// --- CARGAR LOS DATOS DE LOS SELECTS ---
async function loadOptions() {
    // Perfiles
    let data = await fetchJsonWithSessionCheck('/agendas/lib.php?a=getProfiles');
    if (!data) return;
    loadSelectChoices('profile', data, '-- Seleccione un Perfil --');
    // Tipos de documento
    data = await fetchJsonWithSessionCheck('/agendas/lib.php?a=getDocTypes');
    if (!data) return;
    loadSelectChoices('doc-type', data, '-- Seleccione un Tipo de Documento --');
    // Profesionales (vacío al inicio)
    loadSelectChoices('professional', [], '-- Seleccione un Profesional --');
    professionalSelect.disabled = true;
}

async function onProfileChange() {
    const profileId = profileSelect.value;
    professionalSelect.innerHTML = '<option value="">-- Seleccione un Profesional --</option>';
    professionalSelect.disabled = true;
    selectedProfessionalId = null;
    updateCalendar();
    if (profileId) {
        const data = await fetchJsonWithSessionCheck(`/agendas/lib.php?a=getProfessionals&profileId=${profileId}`);
        console.log('Profesionales recibidos:', data);
        if (data) {
            loadSelectChoices('professional', data, '-- Seleccione un Profesional --');
            professionalSelect.disabled = false;
        }
    }
}

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
        const time = `${i}`;
        grid.innerHTML += `<div class="font-bold text-center p-2 border-r">${time}</div>`;
        weekDays.forEach(day => {
            const dateStr = day.toISOString().split('T')[0];
            const appointment = findAppointment(dateStr, time);

            let cellClass = 'bg-green hover:bg-green cursor-pointer';
            let cellContent = 'Disponible';
            let appointmentId = '';

            if (appointment) {
                switch (appointment.status) {
                    case 'Agendado':
                        cellClass = 'bg-red hover:bg-red cursor-pointer';
                        cellContent = 'Ocupado';
                        break;
                    case 'Realizado':
                        cellClass = 'bg-blue hover:bg-blue cursor-pointer';
                        cellContent = 'Realizado';
                        break;
                    case 'Reasignado':
                        cellClass = 'bg-yellow hover:bg-yellow cursor-pointer';
                        cellContent = 'Reasignado';
                        break;
                }
                appointmentId = appointment.id;
            }

            grid.innerHTML += `<div class="text-center p-2 border-t text-sm ${cellClass} transition duration-200 calendar-slot" data-date="${dateStr}" data-time="${time}" data-appointment-id="${appointmentId}">${cellContent}</div>`;
        });
    }
    calendarContainer.appendChild(grid);
}

function findAppointment(date, time) {
    return appointments.find(a =>
        parseInt(a.professionalId) === selectedProfessionalId &&
        a.date === date &&
        a.time === time
    );
}

// --- LÓGICA DEL MODAL ---
function handleCalendarClick(event) {
    const slot = event.target.closest('.calendar-slot');
    if (!slot) return;

    const date = slot.dataset.date;
    const time = slot.dataset.time;
    const appointmentId = slot.dataset.appointmentId || null;

    openModal(date, time, appointmentId);
}

function openModal(date, time, appointmentId = null) {
    form.reset();
    resetModalState();

    document.getElementById('slot-date').value = date;
    document.getElementById('slot-time').value = time;
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
        alert('Por favor, ingrese un número de documento.');
        return;
    }

    fetch(`/agendas/lib.php?a=searchPatient&docType=${encodeURIComponent(docType)}&docNumber=${encodeURIComponent(docNumber)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.patient) {
                document.getElementById('full-name').value = data.patient.fullName;
                document.getElementById('phone').value = data.patient.phone;
                document.getElementById('address').value = data.patient.address;
                alert('Paciente encontrado.');
            } else {
                alert('Paciente no encontrado. Por favor, complete la información.');
                document.getElementById('full-name').value = '';
                document.getElementById('phone').value = '';
                document.getElementById('address').value = '';
                document.getElementById('full-name').focus();
            }
        })
        .catch(() => alert('Error al buscar paciente.'));
}

function handleFormSubmit(e) {
    e.preventDefault();

    const newAppointment = {
        professionalId: parseInt(document.getElementById('slot-professional-id').value),
        date: document.getElementById('appointment-date').value,
        time: document.getElementById('slot-time').value,
        patient: {
            docType: document.getElementById('doc-type').value,
            docNumber: document.getElementById('doc-number').value.trim(),
            fullName: document.getElementById('full-name').value.trim(),
            phone: document.getElementById('phone').value.trim(),
            address: document.getElementById('address').value.trim(),
        },
        activity: document.getElementById('activity').value.trim(),
        notes: document.getElementById('notes').value.trim(),
    };

    if (!newAppointment.patient.fullName || !newAppointment.patient.docNumber) {
        alert('El nombre completo y el número de documento son obligatorios.');
        return;
    }

    fetch('/agendas/lib.php?a=saveAppointment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(newAppointment)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Cita programada exitosamente.');
            closeModal();
            updateCalendar();
        } else {
            alert('Error al guardar la cita: ' + (data.error || ''));
        }
    })
    .catch(() => alert('Error de red al guardar la cita.'));
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
            alert(`El estado de la cita ha sido actualizado a "${newStatus}".`);
            if (newStatus !== 'Reasignado') {
                closeModal();
            }
            updateCalendar();
        } else {
            alert('Error al actualizar el estado de la cita: ' + (data.error || ''));
        }
    })
    .catch(() => alert('Error de red al actualizar la cita.'));
}

function reassignAppointment() {
    alert("Funcionalidad de reasignación: Por favor, seleccione un nuevo cupo disponible en el calendario para mover esta cita.");
    closeModal();
}

function getAppointments(professionalId, weekStart, weekEnd) {
    return fetch(`/agendas/lib.php?a=getAppointments&professionalId=${professionalId}&weekStart=${weekStart}&weekEnd=${weekEnd}`)
        .then(res => res.json());
}

// --- CONTROL DE SESIÓN Y FETCH SEGURO ---
async function fetchJsonWithSessionCheck(url, options) {
    const res = await fetch(url, options);
    let data;
    try {
        data = await res.json();
    } catch (e) {
        // alert('Error parsing JSON response:', e);
        window.location.href = '/index.php';
        return null;
    }
    // Solo redirige si es un objeto con success === false
    if (data && typeof data === 'object' && !Array.isArray(data) && data.success === false && data.error) {
        // alert('Session expired or error:', data.error);
        window.location.href = '/index.php';
        return null;
    }
    return data;
}




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