// Global variables for charts
let ageChart, vspChart, disabilityChart, elderlyChart;
let dashboardData = null;

document.addEventListener('DOMContentLoaded', function() {
    fetch('lib.php')
     .then(res => res.text()) // <-- Obtén el texto crudo
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.error) {
                    showError(data.error);
                    console.error('Backend error:', data.error, data);
                    return;
                }
                dashboardData = data;
                initializeCharts(data);
                updateMetrics(data);
                setupEventListeners();
                startRealTimeUpdates();
            } catch (e) {
                showError('Error de formato en la respuesta del backend');
                console.error('Respuesta cruda del backend:', text);
                console.error('Error al parsear JSON:', e);
            }
        })
        .catch(err => {
            showError('Error cargando datos del backend');
            console.error(err);
        });
});

// Inicializar todos los gráficos con datos del backend
function initializeCharts(data) {
    initializeAgeChart(data);
    const eventoSeleccionado = document.getElementById('vspEventFilter').value || '20';
    initializevspChart(data, eventoSeleccionado);
    updateVspPorcentaje(data, eventoSeleccionado);
    initializeDisabilityChart(data);
    initializeElderlyChart(data);
}


// Age distribution chart
function initializeAgeChart(data) {
    const el = document.getElementById('ageChart');
    if (!el) return;
    const ctx = el.getContext('2d');
    if (ageChart) ageChart.destroy();
    ageChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.ageDistribution.labels,
            datasets: [{
                data: data.ageDistribution.values,
                backgroundColor: [
                    '#FF6B9D', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

// Specialty consultations chart
function initializevspChart(data,evento='1') {
    const vspEvento = data.Vsp[evento];
    const ctx = document.getElementById('vspChart').getContext('2d');
    if (vspChart) vspChart.destroy(); // <-- destruye el anterior
    if (!data.Vsp || !data.Vsp[evento]) {
        showError('No hay datos para el evento seleccionado');
        return;
    }
    vspChart = new Chart(ctx, {
        type: 'bar',
        data: {
            // labels: data.specialtyConsultations.labels,
            labels:vspEvento.labels,
            datasets: [{
                /* label: 'Consultas',
                data: data.specialtyConsultations.values,
                backgroundColor: [
                    '#0066CC',
                    '#00D4FF',
                    '#FF6B9D',
                    '#10B981',
                    '#A855F7',
                    '#F59E0B'
                ], */
                 label: vspEvento.evento,
                data: vspEvento.totales,
                backgroundColor: [
                    '#0066CC',
                    '#00D4FF',
                    '#FF6B9D'
                ],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f0f0f0'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function updateVspPorcentaje(data, evento = '1') {
    if (!data.Vsp || !data.Vsp[evento]) {
        document.getElementById('vspPercen').textContent = '';
        return;
    }
    const vspEvento = data.Vsp[evento];
    document.getElementById('vspPercen').textContent = vspEvento.vspPercen[0]+' %';
}

// Disability chart
function initializeDisabilityChart(data) {
    const ctx = document.getElementById('disabilityChart').getContext('2d');
      if (disabilityChart) disabilityChart.destroy();
    disabilityChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.disability.distribution.labels,
            datasets: [{
                data: data.disability.distribution.values,
                backgroundColor: [
                    '#0066CC',
                    '#00D4FF',
                    '#FF6B9D',
                    '#10B981',
                    '#A855F7'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

// Elderly chart
function initializeElderlyChart(data) {
    const ctx = document.getElementById('elderlyChart').getContext('2d');
    if (elderlyChart) elderlyChart.destroy(); // <-- destruye el anterior
    elderlyChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.elderly.distribution.labels,
            datasets: [{
                data: data.elderly.distribution.values,
                backgroundColor: [
                    '#10B981',
                    '#059669',
                    '#047857'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

// Update metrics with animation
function updateMetrics(data) {
    animateCounter('famCreate', data.famCreate);
    animateCounter('totalPatients', data.totalPatients);
    animateCounter('totalFamilies', data.totalFamilies);
    animateCounter('totalPeople', data.totalPeople);
    animateCounter('total', data.total);
    animateCounter('familyUpdate', data.familyUpdate);
    animateCounter('monthlyConsultations', data.monthlyConsultations);
    document.getElementById('disabilityTotal').textContent = formatNumber(data.disability.total);
    document.getElementById('disabilityPercentage').textContent = data.disability.percentage + '% de la población';
}

// Animate counter function
function animateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    const startValue = 0;
    const duration = 2000;
    const startTime = performance.now();

    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const currentValue = Math.floor(startValue + (targetValue - startValue) * progress);
        element.textContent = formatNumber(currentValue);

        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    }

    requestAnimationFrame(updateCounter);
}

// Format number with dots as thousands separator
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Setup event listeners
function setupEventListeners() {
    // Chart toggle buttons
    const chartButtons = document.querySelectorAll('.chart-btn');
    chartButtons.forEach(button => {
        button.addEventListener('click', function() {
            chartButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const chartType = this.dataset.chart;
            toggleChart(chartType);
        });
    });

    // SOLO el botón aplica los filtros
    document.getElementById('filterBtn').addEventListener('click', handleFilterChange);
//listener para filtros de eventos  VSP
    document.getElementById('vspEventFilter').addEventListener('change', function() {
    initializevspChart(dashboardData, this.value);
    updateVspPorcentaje(dashboardData, this.value);

});

}

// Toggle between age and gender charts
function toggleChart(chartType) {
    if (chartType === 'gender') {
        ageChart.data.labels = dashboardData.genderDistribution.labels;
        ageChart.data.datasets[0].data = dashboardData.genderDistribution.values;
        ageChart.data.datasets[0].backgroundColor = ['#0066CC', '#A855F7', '#FF6B9D'];
    } else {
        ageChart.data.labels = dashboardData.ageDistribution.labels;
        ageChart.data.datasets[0].data = dashboardData.ageDistribution.values;
        ageChart.data.datasets[0].backgroundColor = [
            '#FF6B9D', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD'
        ];
    }
    ageChart.update();
}

// Update data based on filters (simulación local, para producción haz fetch con filtros)
function handleFilterChange() {
    const subred = document.getElementById('departmentFilter').value;
    const territorio = document.getElementById('municipalityFilter').value;
    const localidad = document.getElementById('localityFilter').value;
    const fecha_inicio = document.getElementById('dateFilterFr').value;
    const fecha_fin = document.getElementById('dateFilterTo').value;

    console.log({subred, territorio,localidad, fecha_inicio, fecha_fin});

    const params = new URLSearchParams();
    params.append('subred', subred);
    params.append('territorio', territorio);
    params.append('localidad', localidad);
    params.append('fecha_inicio', fecha_inicio);
    params.append('fecha_fin', fecha_fin);

    showLoader();
    fetch('lib.php', {
        method: 'POST',
        body: params
    })
    .then(res => res.text()) // <-- OBTÉN TEXTO CRUDO
    .then(text => {
        hideLoader();
        // Intenta parsear JSON, pero primero muestra el texto recibido
        try {
            const data = JSON.parse(text);
            if (data.error) {
                showError(data.error);
                console.error('Backend error:', data.error, data);
                return;
            }
            dashboardData = data;
            initializeCharts(data);
            updateMetrics(data);
        } catch (e) {
            // Aquí puedes ver el texto que llegó y el error de parseo
            showError('Error de formato en la respuesta del backend');
            console.error('Respuesta cruda del backend:', text);
            console.error('Error al parsear JSON:', e);
        }
    })
    .catch(err => {
        hideLoader();
        showError('Error cargando datos del backend');
        console.error('Error en fetch:', err);
    });
}

// Update all charts
function updateCharts() {
    ageChart.data.datasets[0].data = dashboardData.ageDistribution.values;
    ageChart.update();

    vspChart.data.datasets[0].data = dashboardData.specialtyConsultations.values;
    vspChart.update();

    disabilityChart.data.datasets[0].data = dashboardData.disability.distribution.values;
    disabilityChart.update();

    elderlyChart.data.datasets[0].data = dashboardData.elderly.distribution.values;
    elderlyChart.update();
}

// Refresh data function
function refreshData() {
    const refreshBtn = document.querySelector('.refresh-btn');
    refreshBtn.style.transform = 'rotate(360deg)';
    refreshBtn.style.transition = 'transform 0.5s ease';
    
    setTimeout(() => {
        refreshBtn.style.transform = 'rotate(0deg)';
    }, 500);
    
    // Simulate data refresh
    // updateDataBasedOnFilters('', '', '', '');
    
    // Add new activity
    addNewActivity();
}

// Add new activity to the list
function addNewActivity() {
    const activities = [
        {
            title: 'Nueva campaña de prevención iniciada',
            time: 'Hace unos segundos',
            type: 'info'
        },
        {
            title: 'Actualización de datos completada',
            time: 'Hace unos segundos',
            type: 'success'
        },
        {
            title: 'Recordatorio: Revisión mensual pendiente',
            time: 'Hace unos segundos',
            type: 'warning'
        }
    ];
    
    const randomActivity = activities[Math.floor(Math.random() * activities.length)];
    const activityList = document.querySelector('.activity-list');
    
    const activityItem = document.createElement('div');
    activityItem.className = 'activity-item';
    activityItem.innerHTML = `
        <div class="activity-icon ${randomActivity.type}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" fill="currentColor"/>
            </svg>
        </div>
        <div class="activity-content">
            <div class="activity-title">${randomActivity.title}</div>
            <div class="activity-time">${randomActivity.time}</div>
        </div>
    `;
    
    activityList.insertBefore(activityItem, activityList.firstChild);
    
    // Remove last item if more than 5 activities
    if (activityList.children.length > 5) {
        activityList.removeChild(activityList.lastChild);
    }
}

// Start real-time updates
function startRealTimeUpdates() {
    setInterval(() => {
        // Simulate small changes in consultation numbers
        const currentConsultations = parseInt(document.getElementById('monthlyConsultations').textContent.replace(/\./g, ''));
        const change = Math.floor(Math.random() * 20) - 10; // Random change between -10 and +10
        const newConsultations = Math.max(0, currentConsultations + change);
        
        document.getElementById('monthlyConsultations').textContent = formatNumber(newConsultations);
        
        // Update progress bars randomly
        const progressBars = document.querySelectorAll('.progress-fill');
        progressBars.forEach(bar => {
            const currentWidth = parseInt(bar.style.width);
            const change = Math.floor(Math.random() * 6) - 3; // Random change between -3 and +3
            const newWidth = Math.max(0, Math.min(100, currentWidth + change));
            bar.style.width = newWidth + '%';
            
            // Update corresponding value
            const indicator = bar.closest('.indicator');
            if (indicator) {
                const valueElement = indicator.querySelector('.indicator-value');
                if (valueElement) {
                    valueElement.textContent = newWidth + '%';
                }
            }
        });
    }, 30000); // Update every 30 seconds
}

// Show loader
function showLoader() {
    document.getElementById('custom-loader').style.display = 'flex';
}

// Hide loader
function hideLoader() {
    document.getElementById('custom-loader').style.display = 'none';
}

// Show error message
function showError(message) {
    const toast = document.getElementById('toast-error');
    toast.textContent = message;
    toast.style.display = 'block';
    // Oculta el toast después de 4 segundos
    setTimeout(() => {
        toast.style.display = 'none';
    }, 4000);
}

// Export functions for global access
window.refreshData = refreshData;

if (ageChart) ageChart.destroy();
if (vspChart) vspChart.destroy();
if (elderlyChart) elderlyChart.destroy();