<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Proyectos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #34495e;
            --gray: #7f8c8d;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            background: var(--secondary);
            color: white;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo i {
            font-size: 28px;
            margin-right: 10px;
            color: var(--primary);
        }
        
        .logo h1 {
            font-size: 22px;
            font-weight: 700;
        }
        
        .menu {
            list-style: none;
            margin-top: 20px;
        }
        
        .menu li {
            margin-bottom: 10px;
        }
        
        .menu a {
            color: #ddd;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .menu a:hover, .menu a.active {
            background: rgba(52, 152, 219, 0.2);
            color: white;
        }
        
        .menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .stats {
            margin-top: 30px;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 8px;
        }
        
        .stats h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #bbb;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        /* Main Content */
        .main-content {
            padding: 30px;
            overflow-y: auto;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .header h2 {
            font-size: 24px;
            color: var(--secondary);
        }
        
        .search-box {
            display: flex;
            width: 300px;
        }
        
        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px 0 0 6px;
            outline: none;
        }
        
        .search-box button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background: #27ae60;
        }
        
        /* Dashboard */
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            padding: 20px;
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .card-header h3 {
            font-size: 18px;
            color: var(--secondary);
        }
        
        .card-header i {
            font-size: 24px;
            color: var(--primary);
        }
        
        .card-body {
            margin-bottom: 15px;
        }
        
        .card-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .card-text {
            color: var(--gray);
            font-size: 14px;
        }
        
        /* Projects Section */
        .projects {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .projects-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }
        
        .projects-header h3 {
            color: var(--secondary);
        }
        
        .filters {
            display: flex;
            gap: 10px;
        }
        
        .filter-btn {
            padding: 8px 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .projects-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .projects-table th {
            text-align: left;
            padding: 15px 20px;
            background: #f8f9fa;
            color: var(--gray);
            font-weight: 600;
        }
        
        .projects-table td {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .project-name {
            font-weight: 600;
            color: var(--secondary);
        }
        
        .project-details {
            font-size: 13px;
            color: var(--gray);
        }
        
        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-planning {
            background: #ffeaa7;
            color: #d35400;
        }
        
        .status-analysis {
            background: #81ecec;
            color: #00cec9;
        }
        
        .status-development {
            background: #74b9ff;
            color: #0984e3;
        }
        
        .status-testing {
            background: #a29bfe;
            color: #6c5ce7;
        }
        
        .status-completed {
            background: #55efc4;
            color: #00b894;
        }
        
        .progress-bar {
            height: 8px;
            background: #eee;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress {
            height: 100%;
            border-radius: 4px;
        }
        
        .actions-cell {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            border: 1px solid #ddd;
            background: white;
            color: var(--dark);
        }
        
        .action-btn:hover {
            background: #f8f9fa;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            color: var(--secondary);
        }
        
        .close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .modal-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .dashboard {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <i class="fas fa-project-diagram"></i>
                <h1>Gestión de Proyectos</h1>
            </div>
            
            <ul class="menu">
                <li><a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-tasks"></i> Proyectos</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Equipos</a></li>
                <li><a href="#"><i class="fas fa-calendar"></i> Calendar</a></li>
                <li><a href="#"><i class="fas fa-chart-bar"></i> Reportes</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Configuración</a></li>
            </ul>
            
            <div class="stats">
                <h3>Estadísticas</h3>
                <div class="stat-item">
                    <span>Proyectos activos:</span>
                    <span>24</span>
                </div>
                <div class="stat-item">
                    <span>Tareas completadas:</span>
                    <span>128</span>
                </div>
                <div class="stat-item">
                    <span>Proyectos finalizados:</span>
                    <span>42</span>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h2>Dashboard de Proyectos</h2>
                <div class="search-box">
                    <input type="text" placeholder="Buscar proyecto...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <div class="actions">
                    <button class="btn btn-primary" onclick="openModal()">
                        <i class="fas fa-plus"></i> Nuevo Proyecto
                    </button>
                </div>
            </div>
            
            <div class="dashboard">
                <div class="card">
                    <div class="card-header">
                        <h3>Proyectos Activos</h3>
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="card-body">
                        <div class="card-number">24</div>
                        <p class="card-text">Proyectos en curso</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>En Análisis</h3>
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="card-body">
                        <div class="card-number">8</div>
                        <p class="card-text">Fase de análisis</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>En Desarrollo</h3>
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="card-body">
                        <div class="card-number">12</div>
                        <p class="card-text">Fase de desarrollo</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Completados</h3>
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-body">
                        <div class="card-number">42</div>
                        <p class="card-text">Proyectos finalizados</p>
                    </div>
                </div>
            </div>
            
            <div class="projects">
                <div class="projects-header">
                    <h3>Todos los Proyectos</h3>
                    <div class="filters">
                        <button class="filter-btn active">Todos</button>
                        <button class="filter-btn">Activos</button>
                        <button class="filter-btn">Finalizados</button>
                    </div>
                </div>
                
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th>Nombre del Proyecto</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                            <th>Fecha Límite</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="project-name">Sistema de Facturación</div>
                                <div class="project-details">Equipo: Desarrollo Web</div>
                            </td>
                            <td><span class="status status-development">Desarrollo</span></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 65%; background: #74b9ff;"></div>
                                </div>
                                <div>65%</div>
                            </td>
                            <td>15 Sep 2023</td>
                            <td class="actions-cell">
                                <button class="action-btn"><i class="fas fa-eye"></i></button>
                                <button class="action-btn"><i class="fas fa-edit"></i></button>
                                <button class="action-btn"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <div class="project-name">App Móvil</div>
                                <div class="project-details">Equipo: Móvil</div>
                            </td>
                            <td><span class="status status-analysis">Análisis</span></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 30%; background: #81ecec;"></div>
                                </div>
                                <div>30%</div>
                            </td>
                            <td>30 Oct 2023</td>
                            <td class="actions-cell">
                                <button class="action-btn"><i class="fas fa-eye"></i></button>
                                <button class="action-btn"><i class="fas fa-edit"></i></button>
                                <button class="action-btn"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <div class="project-name">Portal Cliente</div>
                                <div class="project-details">Equipo: Frontend</div>
                            </td>
                            <td><span class="status status-testing">Pruebas</span></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 80%; background: #a29bfe;"></div>
                                </div>
                                <div>80%</div>
                            </td>
                            <td>22 Ago 2023</td>
                            <td class="actions-cell">
                                <button class="action-btn"><i class="fas fa-eye"></i></button>
                                <button class="action-btn"><i class="fas fa-edit"></i></button>
                                <button class="action-btn"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <div class="project-name">API Servicios</div>
                                <div class="project-details">Equipo: Backend</div>
                            </td>
                            <td><span class="status status-completed">Completado</span></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 100%; background: #55efc4;"></div>
                                </div>
                                <div>100%</div>
                            </td>
                            <td>05 Jul 2023</td>
                            <td class="actions-cell">
                                <button class="action-btn"><i class="fas fa-eye"></i></button>
                                <button class="action-btn"><i class="fas fa-edit"></i></button>
                                <button class="action-btn"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <div class="project-name">Migración BD</div>
                                <div class="project-details">Equipo: Base de Datos</div>
                            </td>
                            <td><span class="status status-planning">Planificación</span></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 15%; background: #ffeaa7;"></div>
                                </div>
                                <div>15%</div>
                            </td>
                            <td>15 Nov 2023</td>
                            <td class="actions-cell">
                                <button class="action-btn"><i class="fas fa-eye"></i></button>
                                <button class="action-btn"><i class="fas fa-edit"></i></button>
                                <button class="action-btn"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal para nuevo proyecto -->
    <div class="modal" id="projectModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nuevo Proyecto</h3>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="projectName">Nombre del Proyecto</label>
                    <input type="text" id="projectName" class="form-control" placeholder="Ingrese el nombre del proyecto">
                </div>
                
                <div class="form-group">
                    <label for="projectDescription">Descripción</label>
                    <textarea id="projectDescription" class="form-control" rows="3" placeholder="Descripción del proyecto"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="projectTeam">Equipo</label>
                    <select id="projectTeam" class="form-control">
                        <option value="">Seleccione un equipo</option>
                        <option value="1">Desarrollo Web</option>
                        <option value="2">Móvil</option>
                        <option value="3">Frontend</option>
                        <option value="4">Backend</option>
                        <option value="5">Base de Datos</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="projectStatus">Estado</label>
                    <select id="projectStatus" class="form-control">
                        <option value="planning">Planificación</option>
                        <option value="analysis">Análisis</option>
                        <option value="development">Desarrollo</option>
                        <option value="testing">Pruebas</option>
                        <option value="completed">Completado</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="projectDeadline">Fecha Límite</label>
                    <input type="date" id="projectDeadline" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal()">Cancelar</button>
                <button class="btn btn-primary">Crear Proyecto</button>
            </div>
        </div>
    </div>
    
    <script>
        // Funciones para abrir y cerrar el modal
        function openModal() {
            document.getElementById('projectModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('projectModal').style.display = 'none';
        }
        
        // Cerrar modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            const modal = document.getElementById('projectModal');
            if (event.target === modal) {
                closeModal();
            }
        };
        
        // Simular carga de proyectos
        document.addEventListener('DOMContentLoaded', function() {
            // Aquí iría la lógica para cargar proyectos desde una API o base de datos
            console.log('Sistema de gestión de proyectos cargado');
        });
    </script>
</body>
</html>