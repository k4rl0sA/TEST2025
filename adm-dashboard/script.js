// Global variables for charts
let ageChart, specialtyChart, disabilityChart, elderlyChart;
let dashboardData = null;

document.addEventListener('DOMContentLoaded', function() {
    fetch('lib.php')
        .then(res => res.json())
        .then(data => {
            dashboardData = data;
            initializeCharts(data);
            updateMetrics(data);
            setupEventListeners();
            startRealTimeUpdates();
        })
        .catch(err => {
            alert('Error cargando datos del backend');
            console.error(err);
        });
});

// Inicializar todos los gráficos con datos del backend
function initializeCharts(data) {
    initializeAgeChart(data);
    initializeSpecialtyChart(data);
    initializeDisabilityChart(data);
    initializeElderlyChart(data);
}

// Age distribution chart
function initializeAgeChart(data) {
    const ctx = document.getElementById('ageChart').getContext('2d');
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
function initializeSpecialtyChart(data) {
    const ctx = document.getElementById('specialtyChart').getContext('2d');
    specialtyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.specialtyConsultations.labels,
            datasets: [{
                label: 'Consultas',
                data: data.specialtyConsultations.values,
                backgroundColor: [
                    '#0066CC',
                    '#00D4FF',
                    '#FF6B9D',
                    '#10B981',
                    '#A855F7',
                    '#F59E0B'
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
    animateCounter('totalPatients', data.totalPatients);
    animateCounter('totalFamilies', data.totalFamilies);
    animateCounter('pregnantWomen', data.pregnantWomen);
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

// ...existing code...

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

    // Filter change listeners
    document.getElementById('departmentFilter').addEventListener('change', handleFilterChange);
    document.getElementById('municipalityFilter').addEventListener('change', handleFilterChange);
    document.getElementById('dateFilterFr').addEventListener('change', handleFilterChange);
    document.getElementById('dateFilterTo').addEventListener('change', handleFilterChange);
}

// ÚNICA función para manejar los filtros y consultar el backend
/* function handleFilterChange() {
    const subred = document.getElementById('departmentFilter').value;
    const territorio = document.getElementById('municipalityFilter').value;
    const fecha_inicio = document.getElementById('dateFilterFr').value;
    const fecha_fin = document.getElementById('dateFilterTo').value;

    const params = new URLSearchParams();
    params.append('subred', subred);
    params.append('territorio', territorio);
    params.append('fecha_inicio', fecha_inicio);
    params.append('fecha_fin', fecha_fin);

    document.body.classList.add('loading');

    fetch('lib.php', {
        method: 'POST',
        body: params
    })
    .then(res => res.json())
    .then(data => {
        document.body.classList.remove('loading');
        if (data.error) {
            alert(data.error);
            return;
        }
        dashboardData = data;
        initializeCharts(data);
        updateMetrics(data);
    })
    .catch(err => {
        document.body.classList.remove('loading');
        alert('Error cargando datos del backend');
        console.error(err);
    });
} */

// Toggle between age and gender charts
function toggleChart(chartType) {
    if (chartType === 'gender') {
        ageChart.data.labels = dashboardData.genderDistribution.labels;
        ageChart.data.datasets[0].data = dashboardData.genderDistribution.values;
        ageChart.data.datasets[0].backgroundColor = ['#0066CC', '#FF6B9D', '#A855F7'];
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
    const fecha_inicio = document.getElementById('dateFilterFr').value;
    const fecha_fin = document.getElementById('dateFilterTo').value;

    const params = new URLSearchParams();
    params.append('subred', subred);
    params.append('territorio', territorio);
    params.append('fecha_inicio', fecha_inicio);
    params.append('fecha_fin', fecha_fin);

    document.body.classList.add('loading');

    fetch('lib.php', {
        method: 'POST',
        body: params
    })
    .then(res => res.json())
    .then(data => {
        document.body.classList.remove('loading');
        if (data.error) {
            alert(data.error);
            return;
        }
        dashboardData = data;
        initializeCharts(data);
        updateMetrics(data);
    })
    .catch(err => {
        document.body.classList.remove('loading');
        alert('Error cargando datos del backend');
        console.error(err);
    });
}

// Update all charts
function updateCharts() {
    ageChart.data.datasets[0].data = dashboardData.ageDistribution.values;
    ageChart.update();

    specialtyChart.data.datasets[0].data = dashboardData.specialtyConsultations.values;
    specialtyChart.update();

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

// Export functions for global access
window.refreshData = refreshData;

if (ageChart) ageChart.destroy();
if (specialtyChart) specialtyChart.destroy();
if (disabilityChart) disabilityChart.destroy();
if (elderlyChart) elderlyChart.destroy();