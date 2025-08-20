<?php
ini_set('display_errors','1');
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Proyectos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="style.css">
    <script>window.CSRF_TOKEN = "<?php echo $_SESSION['csrf_token']; ?>";</script>
    <style>
       
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
        const API = 'lib.php';

        // Cargar proyectos al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarProyectos();
        });

        // Buscar proyectos
        document.querySelector('.search-box button').onclick = function() {
            cargarProyectos(document.querySelector('.search-box input').value);
        };

        function cargarProyectos(search = '') {
            fetch(`${API}?a=list_proyectos&search=${encodeURIComponent(search)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        renderProyectos(data.proyectos);
                    } else {
                        alert(data.error || 'Error al cargar proyectos');
                    }
                });
        }

        function renderProyectos(proyectos) {
            const tbody = document.querySelector('.projects-table tbody');
            tbody.innerHTML = '';
            proyectos.forEach(proy => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <div class="project-name">${proy.nombre}</div>
                        <div class="project-details">Responsable: ${proy.responsable || '-'}</div>
                    </td>
                    <td><span class="status status-${proy.estado}">${proy.estado}</span></td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress" style="width: ${proy.progreso}%; background: #74b9ff;"></div>
                        </div>
                        <div>${proy.progreso}%</div>
                    </td>
                    <td>${proy.fecha_fin_estimada || '-'}</td>
                    <td class="actions-cell">
                        <button class="action-btn" onclick="verProyecto(${proy.id})"><i class="fas fa-eye"></i></button>
                        <button class="action-btn" onclick="editarProyecto(${proy.id})"><i class="fas fa-edit"></i></button>
                        <button class="action-btn" onclick="eliminarProyecto(${proy.id})"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Modal: Crear proyecto
        document.querySelector('.btn.btn-primary').onclick = function() {
            openModal();
        };

        document.querySelector('.modal-footer .btn.btn-primary').onclick = function() {
            crearProyecto();
        };

        function crearProyecto() {
            const nombre = document.getElementById('projectName').value;
            const descripcion = document.getElementById('projectDescription').value;
            const responsable_id = document.getElementById('projectTeam').value;
            const estado = document.getElementById('projectStatus').value;
            const fecha_fin_estimada = document.getElementById('projectDeadline').value;
            if (!nombre) return alert('El nombre es obligatorio');
            fetch(API, {
                method: 'POST',
                body: new URLSearchParams({
                    a: 'crear_proyecto',
                    nombre,
                    descripcion,
                    responsable_id,
                    estado,
                    fecha_fin_estimada
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    cargarProyectos();
                } else {
                    alert(data.error || 'Error al crear proyecto');
                }
            });
        }

        let modoModal = 'crear'; // 'crear', 'ver', 'editar'
let proyectoActualId = null;

// Abrir modal para crear, ver o editar
function openModal(modo = 'crear', id = null) {
    modoModal = modo;
    proyectoActualId = id;
    limpiarModal();

    if (modo === 'crear') {
        document.querySelector('.modal-header h3').textContent = 'Nuevo Proyecto';
        document.querySelector('.modal-footer .btn.btn-primary').textContent = 'Crear Proyecto';
        habilitarCamposModal(true);
        document.querySelector('.modal-footer .btn.btn-primary').onclick = crearProyecto;
        document.getElementById('projectModal').style.display = 'flex';
    } else if (modo === 'ver' || modo === 'editar') {
        fetch(`lib.php?a=get_proyecto&id=${id}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    llenarModal(data.proyecto);
                    document.querySelector('.modal-header h3').textContent = (modo === 'ver') ? 'Ver Proyecto' : 'Editar Proyecto';
                    document.querySelector('.modal-footer .btn.btn-primary').textContent = (modo === 'ver') ? 'Cerrar' : 'Guardar Cambios';
                    habilitarCamposModal(modo === 'editar');
                    document.querySelector('.modal-footer .btn.btn-primary').onclick = (modo === 'ver') ? closeModal : actualizarProyecto;
                    document.getElementById('projectModal').style.display = 'flex';
                } else {
                    alert(data.error || 'No se pudo cargar el proyecto');
                }
            });
    }
}

function limpiarModal() {
    document.getElementById('projectName').value = '';
    document.getElementById('projectDescription').value = '';
    document.getElementById('projectTeam').value = '';
    document.getElementById('projectStatus').value = 'planning';
    document.getElementById('projectDeadline').value = '';
}

function llenarModal(proy) {
    document.getElementById('projectName').value = proy.nombre || '';
    document.getElementById('projectDescription').value = proy.descripcion || '';
    document.getElementById('projectTeam').value = proy.responsable_id || '';
    document.getElementById('projectStatus').value = proy.estado || 'planning';
    document.getElementById('projectDeadline').value = proy.fecha_fin_estimada || '';
}

function habilitarCamposModal(habilitar) {
    document.getElementById('projectName').disabled = !habilitar;
    document.getElementById('projectDescription').disabled = !habilitar;
    document.getElementById('projectTeam').disabled = !habilitar;
    document.getElementById('projectStatus').disabled = !habilitar;
    document.getElementById('projectDeadline').disabled = !habilitar;
}

// Sobrescribe funciones de botones de acciones
function verProyecto(id) {
    openModal('ver', id);
}
function editarProyecto(id) {
    openModal('editar', id);
}

// Actualizar proyecto
function actualizarProyecto() {
    const nombre = document.getElementById('projectName').value;
    const descripcion = document.getElementById('projectDescription').value;
    const responsable_id = document.getElementById('projectTeam').value;
    const estado = document.getElementById('projectStatus').value;
    const fecha_fin_estimada = document.getElementById('projectDeadline').value;
    if (!nombre) return alert('El nombre es obligatorio');
    fetch('lib.php', {
        method: 'POST',
        body: new URLSearchParams({
            a: 'actualizar_proyecto',
            id: proyectoActualId,
            nombre,
            descripcion,
            responsable_id,
            estado,
            fecha_fin_estimada
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal();
            cargarProyectos();
        } else {
            alert(data.error || 'Error al actualizar proyecto');
        }
    });
}

// Eliminar proyecto
function eliminarProyecto(id) {
    if (!confirm('¿Eliminar este proyecto?')) return;
    fetch(API, {
        method: 'POST',
        body: new URLSearchParams({a: 'eliminar_proyecto', id})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            cargarProyectos();
        } else {
            alert(data.error || 'Error al eliminar');
        }
    });
}

// Cerrar modal
function closeModal() {
    document.getElementById('projectModal').style.display = 'none';
}
    </script>
</body>
</html>