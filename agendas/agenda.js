            // --- DATOS DE EJEMPLO ---
            const mockData = {
                profiles: [
                    { id: 1, name: 'Médicos' },
                    { id: 2, name: 'Psicólogos' },
                    { id: 3, name: 'Odontólogos' },
                ],
                professionals: [
                    { id: 101, profileId: 1, name: 'Dr. Carlos Fernández' },
                    { id: 102, profileId: 1, name: 'Dra. Ana Martínez' },
                    { id: 201, profileId: 2, name: 'Psic. María Duarte' },
                    { id: 202, profileId: 2, name: 'Psic. Jorge Rojas' },
                    { id: 301, profileId: 3, name: 'Od. Laura Gómez' },
                ],
                // Simulación de una base de datos de pacientes y citas
                patients: [
                    { docType: 'Cédula de Ciudadanía', docNumber: '12345678', fullName: 'Juan Pérez', phone: '3001234567', address: 'Calle Falsa 123' }
                ],
                appointments: []
            };

            // --- ESTADO DE LA APLICACIÓN ---
            let currentDate = new Date();
            let selectedProfessionalId = null;

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
            function init() {
                populateProfiles();
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
                
                // FIX: Add event listener to the calendar container for event delegation
                calendarContainer.addEventListener('click', handleCalendarClick);

                updateCalendar();
            }

            // --- LÓGICA DE POBLACIÓN DE DATOS ---
            function populateProfiles() {
                mockData.profiles.forEach(profile => {
                    const option = document.createElement('option');
                    option.value = profile.id;
                    option.textContent = profile.name;
                    profileSelect.appendChild(option);
                });
            }

            function onProfileChange() {
                const profileId = parseInt(profileSelect.value);
                professionalSelect.innerHTML = '<option value="">-- Seleccione un Profesional --</option>';
                professionalSelect.disabled = true;
                selectedProfessionalId = null;
                updateCalendar();

                if (profileId) {
                    const professionals = mockData.professionals.filter(p => p.profileId === profileId);
                    professionals.forEach(prof => {
                        const option = document.createElement('option');
                        option.value = prof.id;
                        option.textContent = prof.name;
                        professionalSelect.appendChild(option);
                    });
                    professionalSelect.disabled = false;
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
                const diff = startOfWeek.getDate() - day + (day === 0 ? -6 : 1); // Adjust when day is Sunday
                startOfWeek.setDate(diff);

                const week = [];
                for (let i = 0; i < 7; i++) {
                    const weekDay = new Date(startOfWeek);
                    weekDay.setDate(startOfWeek.getDate() + i);
                    week.push(weekDay);
                }
                return week;
            }

            function updateCalendar() {
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
    
                renderCalendarGrid(weekDays);
            }

            function renderCalendarGrid(weekDays) {
                calendarContainer.innerHTML = '';
                const grid = document.createElement('div');
                grid.className = 'calendar-grid';

                // Header
                grid.innerHTML += `<div class="font-bold text-center p-2 border-b border-r">Cupo</div>`;
                weekDays.forEach(day => {
                    grid.innerHTML += `<div class="font-bold text-center p-2 border-b">${day.toLocaleDateString('es-ES', { weekday: 'short' })}<br>${day.getDate()}/${day.getMonth()+1}</div>`;
                });

                // Slots (8 per day)
                for (let i = 1; i <= 8; i++) {
                    const time = `${+ i}`;
                    grid.innerHTML += `<div class="font-bold text-center p-2 border-r">${time}</div>`;
                    weekDays.forEach(day => {
                        const dateStr = day.toISOString().split('T')[0];
                        const appointment = findAppointment(dateStr, time);
                        
                        let cellClass = 'bg-green hover:bg-green cursor-pointer';
                        let cellContent = 'Disponible';
                        let appointmentId = '';
                        
                        if (appointment) {
                             switch(appointment.status) {
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
                        
                        // FIX: Remove inline onclick and use data attributes
                        grid.innerHTML += `<div class="text-center p-2 border-t text-sm ${cellClass} transition duration-200 calendar-slot" data-date="${dateStr}" data-time="${time}" data-appointment-id="${appointmentId}">${cellContent}</div>`;
                    });
                }
                calendarContainer.appendChild(grid);
            }

            function findAppointment(date, time) {
                return mockData.appointments.find(a => 
                    a.professionalId === selectedProfessionalId &&
                    a.date === date &&
                    a.time === time
                );
            }

            // --- LÓGICA DEL MODAL ---

            // FIX: Create a handler for calendar clicks
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
                
                const appointment = appointmentId ? mockData.appointments.find(a => a.id === appointmentId) : null;

                if (appointment) {
                    // Cita existente: mostrar información y opciones de estado
                    modalTitle.textContent = 'Detalles de la Cita';
                    fillFormWithAppointmentData(appointment);
                    setFormReadOnly(true);
                    submitBtn.classList.add('hidden');
                    statusSection.classList.remove('hidden');
                    appointmentStatusSelect.value = appointment.status;
                    reassignBtn.classList.toggle('hidden', appointment.status !== 'Reasignado');
                } else {
                    // Nueva cita: mostrar formulario para agendar
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
                // setTimeout(() => modal.querySelector('.modal-content').classList.replace('scale-95', 'scale-100'), 10);
            }

            function closeModal() {
                // modal.querySelector('.modal-content').classList.replace('scale-100', 'scale-95');
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

                const patient = mockData.patients.find(p => p.docNumber === docNumber && p.docType === docType);

                if (patient) {
                    document.getElementById('full-name').value = patient.fullName;
                    document.getElementById('phone').value = patient.phone;
                    document.getElementById('address').value = patient.address;
                    alert('Paciente encontrado.');
                } else {
                    alert('Paciente no encontrado. Por favor, complete la información.');
                    document.getElementById('full-name').value = '';
                    document.getElementById('phone').value = '';
                    document.getElementById('address').value = '';
                    document.getElementById('full-name').focus();
                }
            }

            function handleFormSubmit(e) {
                e.preventDefault();
                
                // Recolectar datos del formulario
                const newAppointment = {
                    id: `APT-${Date.now()}`,
                    professionalId: parseInt(document.getElementById('slot-professional-id').value),
                    date: document.getElementById('appointment-date').value,
                    time: document.getElementById('slot-time').value,
                    status: 'Agendado',
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

                // Validaciones
                if (!newAppointment.patient.fullName || !newAppointment.patient.docNumber) {
                    alert('El nombre completo y el número de documento son obligatorios.');
                    return;
                }

                // Guardar la cita
                mockData.appointments.push(newAppointment);

                // Guardar o actualizar paciente
                const existingPatientIndex = mockData.patients.findIndex(p => p.docNumber === newAppointment.patient.docNumber);
                if (existingPatientIndex > -1) {
                    mockData.patients[existingPatientIndex] = newAppointment.patient;
                } else {
                    mockData.patients.push(newAppointment.patient);
                }

                alert('Cita programada exitosamente.');
                closeModal();
                updateCalendar();
            }

            function updateAppointmentStatus() {
                const appointmentId = document.getElementById('appointment-id').value;
                const newStatus = appointmentStatusSelect.value;
                
                const appointment = mockData.appointments.find(a => a.id === appointmentId);
                if (appointment) {
                    appointment.status = newStatus;
                    alert(`El estado de la cita ha sido actualizado a "${newStatus}".`);
                    if (newStatus !== 'Reasignado') {
                       closeModal();
                    }
                    updateCalendar();
                }
            }
            
            function reassignAppointment() {
                alert("Funcionalidad de reasignación: Por favor, seleccione un nuevo cupo disponible en el calendario para mover esta cita.");
                // Aquí se podría implementar una lógica más compleja, como guardar el ID de la cita a reasignar
                // y al hacer clic en un nuevo cupo, mover los datos en lugar de crear una nueva cita.
                closeModal();
            }

            // Iniciar la aplicación
            init();