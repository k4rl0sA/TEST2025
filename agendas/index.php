<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Modular con Filtros, Tabla y Formularios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../libs/css/app.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- <header>
            <div class="logo">Sistema Modular</div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i> Admin
            </div>
        </header> -->
        
        <div class="main-layout">
            <!-- Panel de Filtros -->
            <aside class="filters-card">
                <h2><i class="fas fa-filter"></i> Filtros</h2>
                
                <div class="filter-group">
                    <label for="profile">Perfil Profesional</label>
                    <select id="profile">
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="professional">Profesional</label>
                    <select id="professional">
                        <option value="">Todas</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date">Fecha</label>
                    <input type="date" id="date">
                </div>
                
                <div class="filter-actions">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i> Aplicar Filtros
                    </button>
                    <button class="btn btn-outline">
                        <i class="fas fa-sync"></i>
                    </button>
                </div>
            </aside>
            
            <!-- Contenido Principal -->
            <main class="content-card">
                <div class="table-header">
                    <h2>Registros del Sistema</h2>
                    <button class="btn btn-add">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </button>
                </div>
                
                <div class="table-container">
                    <div id="calendar-container">
                    <div class="flex justify-between items-center mb-4">
                        <button id="prev-week" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300"><i class="fas fa-chevron-left"></i> Semana Anterior</button>
                        <h2 id="week-range" class="text-xl font-bold text-center text-blue-700"></h2>
                        <button id="next-week" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Siguiente Semana <i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div id="calendar" class="overflow-x-auto">
                        <!-- El calendario se generará aquí -->
                    </div>
                     <div class="mt-4 text-sm text-gray-600 flex flex-wrap gap-4">
                        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-green-200 border border-green-400"></span> Disponible</div>
                        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-red-200 border border-red-400"></span> Ocupado</div>
                        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-blue-200 border border-blue-400"></span> Realizado</div>
                        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-yellow-200 border border-yellow-400"></span> Reasignado</div>
                    </div>
                </div>
                </div>
                
                <!-- Formulario Embebido -->
                <div class="form-section">
                    <h3><i class="fas fa-edit"></i> Editar Registro</h3>
                    
                    <form id="record-form">
                        <div class="form-row">
                            <div class="input-group">
                                <input type="text" id="name" placeholder=" " required>
                                <label for="name">Nombre Completo</label>
                                <span class="error-message">Este campo es obligatorio</span>
                            </div>
                            
                            <div class="input-group">
                                <input type="email" id="email" placeholder=" " required>
                                <label for="email">Email</label>
                                <span class="error-message">Ingrese un email válido</span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="input-group">
                                <select id="category-form" required>
                                    <option value=""></option>
                                    <option value="tech">Tecnología</option>
                                    <option value="finance">Finanzas</option>
                                    <option value="hr">Recursos Humanos</option>
                                </select>
                                <label for="category-form">Categoría</label>
                            </div>
                            
                            <div class="input-group">
                                <input type="tel" id="phone" placeholder=" ">
                                <label for="phone">Teléfono</label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="input-group">
                                <textarea id="notes" placeholder=" "></textarea>
                                <label for="notes">Notas</label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="reset" class="btn btn-outline">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Wizard de pasos -->
                <div class="wizard-container">
                    <h3><i class="fas fa-magic"></i> Proceso en Pasos</h3>
                    
                    <div class="wizard-steps">
                        <div class="wizard-step complete">
                            <div class="step-number">1</div>
                            <div class="step-label">Información</div>
                        </div>
                        <div class="wizard-step active">
                            <div class="step-number">2</div>
                            <div class="step-label">Detalles</div>
                        </div>
                        <div class="wizard-step">
                            <div class="step-number">3</div>
                            <div class="step-label">Confirmación</div>
                        </div>
                    </div>
                    
                    <div class="wizard-content">
                        <!-- Contenido del paso actual -->
                        <p>Este es el contenido del paso 2 del proceso. Aquí se solicitan detalles adicionales para completar el registro.</p>
                        
                        <div class="form-row" style="margin-top: 20px;">
                            <div class="input-group">
                                <input type="text" id="detail1" placeholder=" ">
                                <label for="detail1">Detalle 1</label>
                            </div>
                            
                            <div class="input-group">
                                <input type="text" id="detail2" placeholder=" ">
                                <label for="detail2">Detalle 2</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="wizard-actions">
                        <button class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button class="btn btn-primary">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Botón flotante para móviles -->
    <button class="floating-action-btn">
        <i class="fas fa-plus"></i>
    </button>
    
    <!-- Contenedor de Toast Notifications -->
    <div class="toast-container">
        <!-- Las notificaciones se agregarán aquí dinámicamente -->
    </div>
    
    <script>
        // Función para mostrar toast notifications
        function showToast(message, type) {
            const toastContainer = document.querySelector('.toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            let icon = '';
            switch(type) {
                case 'success': icon = '<i class="fas fa-check-circle"></i>'; break;
                case 'error': icon = '<i class="fas fa-exclamation-circle"></i>'; break;
                case 'warning': icon = '<i class="fas fa-exclamation-triangle"></i>'; break;
                case 'info': icon = '<i class="fas fa-info-circle"></i>'; break;
                default: icon = '<i class="fas fa-bell"></i>';
            }
            
            toast.innerHTML = `
                ${icon}
                <div>${message}</div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Eliminar el toast después de 5 segundos
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
        
        // Ejemplo de uso
        document.querySelector('.btn-add').addEventListener('click', () => {
            showToast('Nuevo registro creado exitosamente', 'success');
        });
        
        document.querySelector('.delete-btn').addEventListener('click', () => {
            showToast('Registro eliminado permanentemente', 'error');
        });
        
        // Scroll suave al campo activo
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('focus', function() {
                this.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
        
        // Manejo del formulario
        document.getElementById('record-form').addEventListener('submit', function(e) {
            e.preventDefault();
            showToast('Cambios guardados correctamente', 'success');
        });
    </script>
    <script src="agenda.js"></script>
</body>
</html>