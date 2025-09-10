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
        <link rel="stylesheet" href="style.css?v=4">
    <script>window.CSRF_TOKEN = "<?php echo $_SESSION['csrf_token']; ?>";</script>
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
                            <th>Prioridad</th>
                            <th>Progreso</th>
                            <th>Fecha Límite</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                   
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
                    <label for="projectStatus">Estado</label>
                    <select id="projectStatus" class="form-control">
                        <option value="analisis">1. Análisis</option>
                        <option value="desarrollo">2. Desarrollo</option>
                        <option value="pruebas">3. Pruebas</option>
                        <option value="aprobacion">4. Aprobación Técnica</option>
                        <option value="manual">5. Manual o Video</option>
                        <option value="pruebasSub">6. Pruebas Subred</option>
                        <option value="socializacion">7.Socialización</option>
                        <option value="implementacion">8.Implementación</option>
                        <option value="notifica">9. Notificación</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="projectTeam">Responsable</label>
                    <select id="projectTeam" class="form-control" required>
                        <option value="">-- Seleccione un responsable --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">Prioridad</label>
                    <select id="priority" class="form-control">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="projectDeadline">Fecha Límite</label>
                    <input type="date" id="projectDeadline" class="form-control">
                </div>
                <div class="form-group" id="fileGroup" style="display:none;">
                    <label for="projectFile">Archivo para Desarrollo (máx 2MB, PDF/JPG/PNG/XLSX/DOCX)</label>
                    <input type="file" id="projectFile" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.xlsx,.docx">
                    <div id="fileStatus" style="font-size:12px;color:#888;"></div>
                </div>
                <input type="hidden" id="cloudinaryUrl">
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal()">Cancelar</button>
                <button class="btn btn-primary">Crear Proyecto</button>
            </div>
        </div>
    </div>
    
    <script>
    // Usar URL absoluta para forzar envío de Origin
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
            fetch(`${API}?a=list_proyectos&search=${encodeURIComponent(search)}`, {
                credentials: 'include'
            })
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
                        <div class="project-name">${proy.id}-${proy.nombre}</div>
                        <div class="project-details">Responsable: ${proy.responsable || '-'}</div>
                    </td>
                    <td><span class="status status-${proy.estado}">${proy.estado}</span></td>
                    <td><span class="status status-development priority priority-${proy.prioridad}">${proy.prioridad}</span></td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress" style="width: ${proy.progreso}%; background:#ece915ff;"></div>
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
    // Campos adicionales
    const fecha_inicio = '';
    const prioridad = 'media';
    const progreso = 0;
    const presupuesto = 0;
    const cliente = '';

    if (!nombre) return alert('El nombre es obligatorio');
    if (document.getElementById('projectStatus').value === 'desarrollo') {
    if (archivoSubiendo) return alert('Espera a que termine la carga del archivo.');
    if (!document.getElementById('cloudinaryUrl').value) return alert('Debes subir un archivo para Desarrollo.');
}
    fetch(API, {
        method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        credentials: 'include',
        body: new URLSearchParams({
            a: 'crear_proyecto',
            nombre,
            descripcion,
            responsable_id,
            estado,
            fecha_fin_estimada,
            fecha_inicio,
            prioridad,
            progreso,
            presupuesto,
            cliente,
            csrf_token: window.CSRF_TOKEN
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal();
            cargarProyectos();
             notificarResponsable(
                document.getElementById('projectName').value,
            document.getElementById('projectStatus').value,
            document.getElementById('priority').value,
            document.getElementById('projectDeadline').value,
                document.getElementById('projectTeam').value
        );
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
     cargarResponsables();

    if (modo === 'crear') {
        document.querySelector('.modal-header h3').textContent = 'Nuevo Proyecto';
        document.querySelector('.modal-footer .btn.btn-primary').textContent = 'Crear Proyecto';
        habilitarCamposModal(true);
        document.querySelector('.modal-footer .btn.btn-primary').onclick = crearProyecto;
        document.getElementById('projectModal').style.display = 'flex';

        //predeterminar estado y ocultar campo archivo
        document.getElementById('projectStatus').value = 'analisis';
        document.getElementById('projectStatus').disabled = true;
        document.getElementById('fileGroup').style.display = 'none';

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
                document.getElementById('projectStatus').value = 'analisis';
    document.getElementById('projectDeadline').value = '';
}

function cargarResponsables() {
    return fetch('lib.php?a=list_responsables')
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('projectTeam');
            select.innerHTML = '<option value="">Seleccione...</option>';
            if (data.success) {
                data.usuarios.forEach(u => {
                    select.innerHTML += `<option value="${u.id_usuario}">${u.nombre}</option>`;
                });
            }
         asignarResponsablePorEstado();
        });
}

function llenarModal(proy) {
    document.getElementById('projectName').value = proy.nombre || '';
    document.getElementById('projectDescription').value = proy.descripcion || '';
    document.getElementById('projectStatus').value = proy.estado || 'analisis';
    document.getElementById('projectDeadline').value = proy.fecha_fin_estimada || '';
    document.getElementById('priority').value = proy.prioridad || 'media';

      cargarResponsables().then(() => {
    document.getElementById('projectTeam').value = proy.responsable_id || '';
});
}

function habilitarCamposModal(habilitar) {
    document.getElementById('projectName').disabled = !habilitar;
    document.getElementById('projectDescription').disabled = !habilitar;
    document.getElementById('projectTeam').disabled = false;
    document.getElementById('projectStatus').disabled = !habilitar;
    document.getElementById('projectDeadline').disabled = !habilitar;
    document.getElementById('priority').disabled = !habilitar;
    document.getElementById('projectFile').disabled = !habilitar;
    document.getElementById('fileGroup').style.display = habilitar ? '' : 'none';
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
    const prioridad = document.getElementById('priority').value;

    if (!nombre) return alert('El nombre es obligatorio');
    if (document.getElementById('projectStatus').value === 'desarrollo') {
    if (archivoSubiendo) return alert('Espera a que termine la carga del archivo.');
    if (!document.getElementById('cloudinaryUrl').value) return alert('Debes subir un archivo para Desarrollo.');
}
    fetch(API, {
        method: 'POST',
         headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        // credentials: 'include',
        body: new URLSearchParams({
            a: 'actualizar_proyecto',
            id: proyectoActualId,
            nombre,
            descripcion,
            responsable_id,
            estado,
            fecha_fin_estimada,
            prioridad,
            archivo_url: document.getElementById('cloudinaryUrl').value,
            csrf_token: window.CSRF_TOKEN
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal();
            cargarProyectos();
             notificarResponsable(
                document.getElementById('projectName').value,
            document.getElementById('projectStatus').value,
            document.getElementById('priority').value,
            document.getElementById('projectDeadline').value,
            document.getElementById('projectTeam').value
        );
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
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        credentials: 'include',
        body: new URLSearchParams({a: 'eliminar_proyecto', id, csrf_token: window.CSRF_TOKEN})
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

// Mapeo de estado a nombre de responsable
const responsablesPorEstado = {
    1022358140:{nombre:"EDISON FERNANDO MATEUS VASQUEZ",telefono: "3112161501"},
    80811594:{nombre:"CARLOS EDUARDO ACEVEDO",telefono: "3115852782"},
    1024545090:{nombre:"OSCAR ARLEY CURREA JIMENEZ",telefono: "+573212096277"},
    1032428656: {nombre:"SINDY SANCHEZ",telefono: "3005274354"},
    1024522905: {nombre:"JEISSON CURREA JIMENEZ",telefono: "3017389220"},
    1024522905:{nombre:"JEISSON CURREA JIMENEZ",telefono: "3017389220"},
    1022358140:{nombre:"EDISON FERNANDO MATEUS VASQUEZ",telefono: "3112161501"},
    80811594: {nombre:"CARLOS EDUARDO ACEVEDO",telefono: "3115852782"},     
    1032428656: {nombre:"SINDY SANCHEZ", telefono: "3005274354"}
};
function asignarResponsablePorEstado() {
    const estado = document.getElementById('projectStatus').value;
    const nombreResponsable =document.getElementById('projectTeam').value;
    const select = document.getElementById('projectTeam');
    // Mostrar campo de archivo solo si es desarrollo
    document.getElementById('fileGroup').style.display = (estado === 'desarrollo' || estado === 'aprobacion' || estado === 'socializacion' ) ? '' : 'none';
}

function notificarResponsable(proyecto,estado, prioridad, fechaLimite, responsableId) {
    const responsable = responsablesPorEstado[responsableId];
    if (!responsable || !responsable.telefono) return;
    const mensaje = encodeURIComponent(
        `Se le ha asignado en "${proyecto.toUpperCase()}" la siguiente tarea:\nEstado: ${estado}\nPrioridad: ${prioridad}\nFecha límite: ${fechaLimite}\nhttps://pruebagtaps.site/proyectos/ `
    );
    // Abre WhatsApp Web con el mensaje
    window.open(`https://wa.me/${responsable.telefono}?text=${mensaje}`, '_blank');
}

// Cuando cambia el estado, asigna el responsable correspondiente
document.getElementById('projectStatus').addEventListener('change', function() {
    asignarResponsablePorEstado();
    actualizarEstadoBotonGuardar();
});

// Manejo de subida de archivos a Cloudinary
let archivoSubiendo = false;
document.getElementById('projectFile').addEventListener('change', function() {
    const file = this.files[0];
    const status = document.getElementById('fileStatus');
    const btnGuardar = document.querySelector('.modal-footer .btn.btn-primary');
    status.textContent = '';
    document.getElementById('cloudinaryUrl').value = '';
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
        status.textContent = 'El archivo supera los 2MB.';
        this.value = '';
        return;
    }

    // Validar nombre del archivo
    const nombreProyecto = document.getElementById('projectName').value.trim();
    const estado = document.getElementById('projectStatus');
    const estadoTexto = estado.options[estado.selectedIndex].text.trim();
    const nombreEsperado = `${nombreProyecto} - ${estadoTexto}`.replace(/[\/\\?%*:|"<>]/g, '-').toLowerCase();

    // Obtener nombre del archivo sin extensión
    const nombreArchivoSinExt = file.name.replace(/\.[^/.]+$/, "").trim().toLowerCase();

    if (nombreArchivoSinExt !== nombreEsperado) {
        status.textContent = `El nombre del archivo debe ser exactamente: "${nombreEsperado}"`;
        this.value = '';
        return;
    }

    archivoSubiendo = true;
    btnGuardar.disabled = true;
    status.textContent = 'Subiendo archivo...';

    // Cloudinary config
    const url = 'https://api.cloudinary.com/v1_1/dxfcy3nam/upload';
    const preset = 'gtapps';
    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', preset);

    // Usar el nombre esperado como public_id
    formData.append('public_id', nombreEsperado);

    fetch(url, { method: 'POST', 
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        credentials: 'include',
        body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.secure_url) {
                document.getElementById('cloudinaryUrl').value = data.secure_url;
                status.textContent = 'Archivo subido correctamente.';
            } else {
                status.textContent = 'Error al subir archivo.';
            }
            actualizarEstadoBotonGuardar();
        })
        .catch(() => {
            status.textContent = 'Error al subir archivo.';
            actualizarEstadoBotonGuardar();
        })
        .finally(() => {
            archivoSubiendo = false;
            actualizarEstadoBotonGuardar();
        });
});

// Actualiza el estado del botón Guardar según el estado y la subida de archivos
function actualizarEstadoBotonGuardar() {
    const estado = document.getElementById('projectStatus').value;
    const btnGuardar = document.querySelector('.modal-footer .btn.btn-primary');
    const archivoUrl = document.getElementById('cloudinaryUrl').value;
    if (estado === 'desarrollo' || estado === 'aprobacion' || estado === 'socializacion') {
        // Solo habilita si el archivo está subido y no está subiendo
        btnGuardar.disabled = archivoSubiendo || !archivoUrl;
    } else {
        btnGuardar.disabled = false;
    }
}

    </script>
</body>
</html>