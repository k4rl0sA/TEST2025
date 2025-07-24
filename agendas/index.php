<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Modular con Filtros, Tabla y Formularios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4caf50;
            --warning: #ff9800;
            --error: #f44336;
            --info: #2196f3;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 8px;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 6px 16px rgba(0, 0, 0, 0.12);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .main-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 25px;
        }

        /* Filtros - Diseño modular con tarjetas */
        .filters-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            height: fit-content;
        }

        .filters-card h2 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-group {
            margin-bottom: 25px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: 3px solid rgba(67, 97, 238, 0.3);
            border-color: var(--primary);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
            box-shadow: var(--shadow-hover);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray);
            color: var(--dark);
        }

        .btn-outline:hover {
            background-color: var(--light-gray);
        }

        /* Tabla de registros */
        .content-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            overflow: hidden;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-header h2 {
            font-size: 1.5rem;
            color: var(--dark);
        }

        .btn-add {
            background-color: var(--success);
            color: white;
            padding: 10px 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        th {
            background-color: #f1f3f9;
            font-weight: 600;
            color: var(--dark);
            position: sticky;
            top: 0;
        }

        tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }

        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-active {
            background-color: rgba(76, 175, 80, 0.15);
            color: var(--success);
        }

        .status-pending {
            background-color: rgba(255, 152, 0, 0.15);
            color: var(--warning);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray);
        }

        .action-btn:hover {
            background: var(--light-gray);
            color: var(--dark);
        }

        .edit-btn:hover {
            color: var(--primary);
        }

        .delete-btn:hover {
            color: var(--error);
        }

        /* Formulario con etiquetas flotantes */
        .form-section {
            background-color: #f8f9fc;
            border-radius: var(--border-radius);
            padding: 30px;
            margin-top: 30px;
            box-shadow: var(--shadow);
            border: 1px solid #e2e8f0;
        }

        .form-section h3 {
            font-size: 1.4rem;
            margin-bottom: 25px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .input-group {
            position: relative;
        }

        .input-group label {
            position: absolute;
            top: 16px;
            left: 15px;
            color: var(--gray);
            pointer-events: none;
            transition: var(--transition);
        }

        .input-group input,
        .input-group select,
        .input-group textarea {
            width: 100%;
            padding: 16px 15px 5px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background-color: white;
        }

        .input-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .input-group input:focus,
        .input-group select:focus,
        .input-group textarea:focus {
            outline: 3px solid rgba(67, 97, 238, 0.3);
            border-color: var(--primary);
        }

        .input-group input:focus ~ label,
        .input-group input:not(:placeholder-shown) ~ label,
        .input-group select:focus ~ label,
        .input-group select:not([value=""]) ~ label,
        .input-group textarea:focus ~ label,
        .input-group textarea:not(:placeholder-shown) ~ label {
            top: 6px;
            font-size: 0.8rem;
            color: var(--primary);
        }

        .error-message {
            color: var(--error);
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .toast {
            padding: 18px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-hover);
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            animation: slideIn 0.3s ease-out;
            transform: translateX(0);
            min-width: 300px;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        .toast i {
            font-size: 1.5rem;
        }

        .toast-success {
            background-color: var(--success);
        }

        .toast-error {
            background-color: var(--error);
        }

        .toast-info {
            background-color: var(--info);
        }

        .toast-warning {
            background-color: var(--warning);
        }

        /* Wizard de pasos */
        .wizard-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 30px;
            margin-top: 30px;
        }

        .wizard-steps {
            display: flex;
            margin-bottom: 30px;
            position: relative;
        }

        .wizard-step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 700;
            color: var(--gray);
            border: 2px solid var(--light-gray);
        }

        .step-label {
            font-weight: 500;
            color: var(--gray);
        }

        .wizard-step.active .step-number {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .wizard-step.active .step-label {
            color: var(--primary);
        }

        .wizard-step.complete .step-number {
            background: var(--success);
            color: white;
            border-color: var(--success);
        }

        .wizard-step.complete .step-label {
            color: var(--success);
        }

        .wizard-steps:before {
            content: "";
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--light-gray);
            z-index: 1;
        }

        .wizard-content {
            padding: 20px 0;
        }

        .wizard-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }

        /* Botón flotante para móviles */
        .floating-action-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
            z-index: 99;
            border: none;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
            
            .filters-card {
                margin-bottom: 20px;
            }
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .floating-action-btn {
                display: flex;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .btn-add {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }
            
            .filters-card, 
            .content-card, 
            .form-section {
                padding: 20px 15px;
            }
            
            .wizard-container {
                padding: 20px 15px;
            }
        }

        /* Accesibilidad */
        a:focus, button:focus, input:focus, select:focus, textarea:focus {
            outline: 3px solid rgba(67, 97, 238, 0.5);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">Sistema Modular</div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i> Admin
            </div>
        </header>
        
        <div class="main-layout">
            <!-- Panel de Filtros -->
            <aside class="filters-card">
                <h2><i class="fas fa-filter"></i> Filtros</h2>
                
                <div class="filter-group">
                    <label for="search">Buscar</label>
                    <input type="text" id="search" placeholder="Ingrese término...">
                </div>
                
                <div class="filter-group">
                    <label for="status">Estado</label>
                    <select id="status">
                        <option value="">Todos</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                        <option value="pending">Pendiente</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="category">Categoría</label>
                    <select id="category">
                        <option value="">Todas</option>
                        <option value="tech">Tecnología</option>
                        <option value="finance">Finanzas</option>
                        <option value="hr">Recursos Humanos</option>
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
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>001</td>
                                <td>Carlos Martínez</td>
                                <td>carlos@example.com</td>
                                <td>Tecnología</td>
                                <td><span class="status status-active">Activo</span></td>
                                <td>12/05/2023</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit-btn" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete-btn" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>002</td>
                                <td>Ana Rodríguez</td>
                                <td>ana@example.com</td>
                                <td>Finanzas</td>
                                <td><span class="status status-pending">Pendiente</span></td>
                                <td>15/05/2023</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit-btn" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete-btn" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>003</td>
                                <td>Luis González</td>
                                <td>luis@example.com</td>
                                <td>Recursos Humanos</td>
                                <td><span class="status status-active">Activo</span></td>
                                <td>18/05/2023</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit-btn" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete-btn" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
</body>
</html>