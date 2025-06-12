<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Salud - EBEH</title>
    <link rel="stylesheet" href="styles.css?v=11.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <div class="logo-section">
                        <img src="../../libs/img/masBienestar.webp" alt="Logo MAS Bienestar" style="height:100px;width:100px;object-fit:contain;vertical-align:middle;">
                    <h1>Equipos MAS Bienestar</h1>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Subred</label>
                        <select id="departmentFilter">
                            <option value="">Seleccionar Subred</option>
                            <option value="1">Norte</option>
                            <option value="2">Sur</option>
                            <option value="3">Centro Oriente</option>
                            <option value="4">Sur Occidente</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Territorio</label>
                        <select id="municipalityFilter">
                            <option value="">Seleccionar Territorio</option>
                            <option value="101">Medellín</option>
                            <option value="102">Bogotá</option>
                            <option value="103">Cali</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Localidad</label>
                        <select id="localityFilter" name="localidad">
                            <?php
                                require_once __DIR__ . '/../../libs/gestion.php';
                                echo opc_sql(
                                    "SELECT idcatadeta, descripcion FROM catadeta WHERE idcatalogo = 2 ORDER BY 1",
                                    $_POST['localidad'] ?? ''
                                );
                            ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Fecha Desde</label>
                        <input type="date" id="dateFilterFr" value="2025-01-01">
                    </div>
                    <div class="filter-group">
                        <label>Fecha Hasta</label>
                        <input type="date" id="dateFilterTo" value="">
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var today = new Date();
                                var yyyy = today.getFullYear();
                                var mm = String(today.getMonth() + 1).padStart(2, '0');
                                var dd = String(today.getDate()).padStart(2, '0');
                                var formatted = yyyy + '-' + mm + '-' + dd;
                                document.getElementById('dateFilterTo').value = formatted;
                                // Primer día del mes actual
                                var firstDay = yyyy + '-' + mm + '-01';
                                document.getElementById('dateFilterFr').value = firstDay;
                            });
                        </script>
                    </div>
                    <div class="filter-group" style="align-self: flex-end;">
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <button id="filterBtn" class="filter-btn" type="button">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;">
                                    <path d="M3 5h18M6 12h12M10 19h4" stroke="#0066cc" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Filtrar
                            </button>
                            <button id="closeTabBtn" onclick="cerrarPestana()" style="display:flex;align-items:center;gap:6px;background:#ef4444;color:#fff;border:none;padding:6px 14px;border-radius:5px;cursor:pointer;font-size:15px;">
                                <i class="fa fa-times" aria-hidden="true"></i> Cerrar pestaña
                            </button>
                        </div>
                        <script>
                          function cerrarPestana() {
                            window.close();
                          }
                        </script>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Key Metrics -->
            <section class="metrics-section">
                
                <div class="metric-card consultations">
                    <div class="metric-icon">
                        <i class="fa-solid fa-people-roof"></i>
                    </div>
                    <div class="metric-content">
                        <h3>Familias Creadas</h3>
                        <div class="metric-value" id="famCreate">89.456</div>
                        <div class="metric-subtitle">Familias Caracterizadas y Sin Caracterizar</div>
                    </div>
                </div>

                <div class="metric-card families">
                    <div class="metric-icon">
                        <i class="fa-solid fa-house-user"></i>
                    </div>
                    <div class="metric-content">
                        <h3>Familias Caracterizadas</h3>
                        <div class="metric-value" id="totalFamilies">1.570.758</div>
                        <div class="metric-subtitle">Unicas</div>
                    </div>
                </div>

                 <div class="metric-card patients">
                    <div class="metric-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="metric-content">
                        <h3>Individuos</h3>
                        <div class="metric-value" id="totalPeople">3.553.128</div>
                        <div class="metric-subtitle"> Caracterizados y Actualizados</div>
                    </div>
                </div>

                

            </section>
            
   <!--          <section class="metrics-section">

                <div class="metric-card patients">
                    <div class="metric-icon">
                        <i class="fa-solid fa-people-roof"></i>
                    </div>
                    <div class="metric-content">
                        <h3>Familias Clasificadas</h3>
                        <div class="metric-value" id="totalPatients">3.553.128</div>
                        <div class="metric-subtitle">Prioridades Medias</div>
                    </div>
                </div>


                <div class="metric-card families">
                    <div class="metric-icon">
                        <i class="fa-solid fa-people-roof"></i>
                    </div>
                    <div class="metric-content">
                        <h3>Disponible</h3>
                        <div class="metric-value" id="total">1.570.758</div>
                        <div class="metric-subtitle">Descripcion</div>
                    </div>
                </div>

                <div class="metric-card pregnant">
                    <div class="metric-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="metric-content">
                        <h3>Familias Con Actualización</h3>
                        <div class="metric-value" id="familyUpdate">25.933</div>
                        <div class="metric-subtitle"></div>
                    </div>
                </div>

               

                <div class="metric-card pregnant">
                    <div class="metric-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M9 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm7 9c-.83 0-1.5-.67-1.5-1.5 0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5c0 .83-.67 1.5-1.5 1.5zm-3 7v-3h-2v3c0 .55-.45 1-1 1s-1-.45-1-1v-3c-1.1 0-2-.9-2-2v-3c0-1.1.9-2 2-2h6c1.1 0 2 .9 2 2v3c0 1.1-.9 2-2 2v3c0 .55-.45 1-1 1s-1-.45-1-1z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="metric-content">
                        <h3>Familias Con Actualización</h3>
                        <div class="metric-value" id="familyUpdate">25.933</div>
                        <div class="metric-subtitle">Última actualización: hace 1 hora</div>
                    </div>
                </div>

                <div class="metric-card consultations">
                    <div class="metric-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="metric-content">
                        <h3>Familias Clasificadas</h3>
                        <div class="metric-value" id="monthlyConsultations">89.456</div>
                        <div class="metric-subtitle">Prioridades Altas</div>
                    </div>
                </div>
            </section>
 -->
            <!-- Charts Section -->
            <section class="charts-section">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Individuos por Edad</h3>
                        <div class="chart-controls">
                            <button class="chart-btn active" data-chart="age">Edad</button>
                            <button class="chart-btn" data-chart="gender">Género</button>
                        </div>
                    </div>
                    <canvas id="ageChart"></canvas>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Consultas por Especialidad</h3>
                    </div>
                    <canvas id="specialtyChart"></canvas>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Indicadores de Salud</h3>
                    </div>
                    <div class="health-indicators">
                        <div class="indicator">
                            <div class="indicator-label">Vacunación Completa</div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 87%"></div>
                            </div>
                            <div class="indicator-value">87%</div>
                        </div>
                        <div class="indicator">
                            <div class="indicator-label">Control Prenatal</div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 92%"></div>
                            </div>
                            <div class="indicator-value">92%</div>
                        </div>
                        <div class="indicator">
                            <div class="indicator-label">Tamizaje Cáncer</div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 74%"></div>
                            </div>
                            <div class="indicator-value">74%</div>
                        </div>
                        <div class="indicator">
                            <div class="indicator-label">Hipertensión Controlada</div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 68%"></div>
                            </div>
                            <div class="indicator-value">68%</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Demographics Section -->
            <section class="demographics-section">
                <div class="demo-card">
                    <h3>Personas con Discapacidad</h3>
                    <div class="demo-value" id="disabilityTotal">155.409</div>
                    <div class="demo-percentage" id="disabilityPercentage">4.37% de la población</div>
                    <canvas id="disabilityChart" height="180px"></canvas>
                </div>

                <div class="demo-card">
                    <h3>Menores de 5 años</h3>
                    <div class="demo-value">341.866</div>
                    <div class="demo-percentage">9.62% de la población</div>
                    <div class="demo-breakdown">
                        <div class="breakdown-item">
                            <span class="breakdown-label">0-1 años</span>
                            <span class="breakdown-value">68,373</span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">2-3 años</span>
                            <span class="breakdown-value">136,746</span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">4-5 años</span>
                            <span class="breakdown-value">136,747</span>
                        </div>
                    </div>
                </div>

                <div class="demo-card">
                    <h3>Mayores de 60 años</h3>
                    <div class="demo-value">659.383</div>
                    <div class="demo-percentage">18.55% de la población</div>
                    <canvas id="elderlyChart"></canvas>
                </div>
            </section>

            <!-- Recent Activity -->
            <section class="activity-section">
                <div class="activity-header">
                    <h3>Actividad Reciente</h3>
                    <button class="refresh-btn" onclick="refreshData()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon success">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Campaña de vacunación completada</div>
                            <div class="activity-time">Hace 2 horas</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon warning">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Alerta: Aumento de casos respiratorios</div>
                            <div class="activity-time">Hace 4 horas</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon info">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Nuevo reporte mensual disponible</div>
                            <div class="activity-time">Hace 6 horas</div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div id="custom-loader" class="custom-loader-overlay" style="display:none;">
      <div class="custom-spinner">
        <svg width="64" height="64" viewBox="0 0 44 44">
          <circle class="spinner-bg" cx="22" cy="22" r="20" fill="none" stroke="#eee" stroke-width="4"/>
          <circle class="spinner-fg" cx="22" cy="22" r="20" fill="none" stroke="#0066cc" stroke-width="4"/>
        </svg>
        <div class="loader-text">Cargando datos...</div>
      </div>
    </div>

    <div id="toast-error" class="toast-error" style="display:none;"></div>

    <script src="script.js?v=12.0"></script>
</body>
</html>