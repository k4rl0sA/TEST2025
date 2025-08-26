<?php
ini_set('display_errors','1');
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (!isset($_SESSION["us_sds"])) {
    header("Location: /index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Roles</title>
    <meta name="description" content="Panel de administración para la gestión de roles y permisos en el sistema. Filtra, crea, edita y elimina roles de forma sencilla.">
    <meta name="keywords" content="roles, gestión, administración, permisos, sistema, filtros, seguridad">
    <meta name="author" content="Carlos Eduardo Acevedo Arevalo">
    <link rel="canonical" href="https://tusitio.com/adm-ajustes/index.html">
    <link rel="stylesheet" href="../lib/css/app.css?v=17">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="stylesheet" href="../lib/css/choices.min.css?v=17">
    <script src="../lib/js/choices.min.js"></script>
    <script>window.CSRF_TOKEN = "<?php echo $_SESSION['csrf_token']; ?>";</script>
    <script src="../lib/js/app.js?v=17" defer></script>
    <script src="ajustes.js?v=17"></script>
</head>
<body>
    <div class="toast-container">
        <!-- Las notificaciones se agregarán aquí dinámicamente -->
    </div>
<div class="content-card">
    <div class='load' id='loader' z-index='0'></div>
    <!-- Filtros tipo chips -->
    <div class="filters-bar">
        <button class="btn btn-chip" id="toggle-filters-btn" title="Mostrar/ocultar filtros">
            <i class="fa fa-filter"></i>
            <span class="chip-text">Filtros</span>
            <span id="active-filters-count" class="chip-count">0</span>
        </button>
        <div id="active-filters-chips" class="chips-list"></div>
    </div>
    <!-- Panel de filtros -->
    <div class="form-section hidden" id="filters-panel" style="margin-bottom:1.5em;">
        <form id="filter-form" class="filter-section">
            <div class="input-group">
                <input type="text" id="fil-modulo" placeholder="">
                <label for="fil-modulo">Modulo</label>
            </div>
            <div class="input-group">
                <select id="fil-perfil" multiple>
                </select>
                <label for="fil-perfil">Perfil</label>
            </div>
            <div class="input-group">
                <select id="fil-estado" multiple>
                </select>
                <label for="fil-estado">Estado</label>
            </div>
            <div class="input-group">
                <input type="text" id="fil-search" placeholder="">
                <label for="fil-search">Buscar</label>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary filter-btn" title="Aplicar filtros">
                    <i class="fa fa-search"></i> Aplicar Filtros
                </button>
                <button type="button" class="btn btn-outline filter-reset" id="clear-filters" title="Limpiar filtros">
                    <i class="fa fa-sync"></i>
                </button>
            </div>
        </form>
    </div>
    <!-- Tabla y header -->
    <div class="table-header">
        <h2>Roles Registrados</h2>
        <span id="range-info" class="range-info"></span>
         <span id="total-roles" class="total-count"></span>
        <button class="btn btn-add" id="add-btn" title="Agregar nuevo rol"><i class="fa fa-plus"></i><span> Nuevo Rol</span></button>
        <!-- Boton para personalizar columnas -->
        <div class="column-settings">
            <button class="btn btn-outline" id="toggle-columns-btn" title='Personalizar columnas'><i class="fa fa-table"></i></button>
            <div id="columns-panel" class="columns-panel hidden">
                <label><input type="checkbox" class="col-toggle" data-col="modulo" checked> Módulo</label>
                <label><input type="checkbox" class="col-toggle" data-col="perfil" checked> Perfil</label>
                <label><input type="checkbox" class="col-toggle" data-col="componente" checked> Componente</label>
                <label><input type="checkbox" class="col-toggle" data-col="consultar" checked> Consultar</label>
                <label><input type="checkbox" class="col-toggle" data-col="editar" checked> Editar</label>
                <label><input type="checkbox" class="col-toggle" data-col="crear" checked> Crear</label>
                <label><input type="checkbox" class="col-toggle" data-col="ajustar" checked> Ajustar</label>
                <label><input type="checkbox" class="col-toggle" data-col="importar" checked> Importar</label>
                <label><input type="checkbox" class="col-toggle" data-col="estado" checked> Estado</label>
            </div>
        </div>
        <button class="btn btn-help" id="help-btn" title="Ayuda"><i class="fa fa-question-circle"></i></button>
    </div>
    <!-- Acciones masivas -->
    <div class="bulk-actions-bar" id="bulk-actions-bar">
        <span id="bulk-count" class="bulk-count">0 items</span>
        <button class="bulk-btn" id="bulk-activate"><i class="fa fa-check"></i> Activar</button>
        <button class="bulk-btn" id="bulk-inactivate"><i class="fa fa-ban"></i> Inactivar</button>
        <button class="bulk-btn" id="bulk-delete"><i class="fa fa-trash"></i> Eliminar</button>
        <button class="bulk-btn bulk-close" id="bulk-close" title="Cerrar"><i class="fa fa-times"></i></button>
    </div>
    <!-- Modal de Ayuda -->
    <div id="help-modal" class="modal-content hidden">
        <div class="modal-header">
            <h3><i class="fa fa-question-circle"></i> Ayuda</h3>
            <button id="close-help" class="btn btn-outline"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body" id="help-content"></div>
    </div>
    <!-- Tabla de roles -->
    <div class="table-container" id="table-section">
        <table id="roles-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-roles"></th>
                    <th>Acciones</th>
                    <!-- <th data-sort="id_rol">ID</th> NO MOSTRAR EL ID-->
                    <th class="col-modulo" data-sort="modulo">Módulo</th>
                    <th class="col-perfil" data-sort="perfil">Perfil</th>
                    <th class="col-componente" data-sort="componente">Componente</th>
                    <th class="col-consultar" data-sort="consultar">Consultar</th>
                    <th class="col-editar" data-sort="editar">Editar</th>
                    <th class="col-crear" data-sort="crear">Crear</th>
                    <th class="col-ajustar" data-sort="ajustar">Ajustar</th>
                    <th class="col-importar" data-sort="importar">Importar</th>
                    <th class="col-estado" data-sort="estado">Estado</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se cargan los roles -->
            </tbody>
        </table>
        <div class="paginator" id="paginator"></div>
    </div>
    <div id="form-ajustes" class="form-section modal-content hidden">
        <div class="table-header">
            <h3 id="form-title"><i class="fas fa-edit"></i> Ingresar Cupo</h3>
            <button id="close-win" class="btn btn-outline" title="Cerrar ventana"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="role-form">
            <input type="hidden" id="id_rol">
            <div class="form-row">
                <div class="input-group">
                    <input type="text" id="modulo" name="modulo" required maxlength="15" placeholder="">
                    <label for="modulo">Módulo</label>
                </div>
                <div class="input-group">
                    <input type="text" id="perfil" name="perfil" required maxlength="11" placeholder="">
                    <label for="perfil">Perfil</label>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <input type="text" id="componente" name="componente" required placeholder="">
                    <label for="componente">Componente</label>
                </div>
                <div class="input-group">
                    <select id="consultar" name="consultar" required>
                    </select>
                    <label for="consultar">Consultar</label>    
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <select id="editar" name="editar" required>
                    </select>
                    <label for="editar">Editar</label>
                </div>
                <div class="input-group">
                    <select id="crear" name="crear" required>
                    </select>
                    <label for="crear">Crear</label>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <select id="ajustar" name="ajustar" required>
                    </select>
                    <label for="ajustar">Ajustar</label>
                </div>
                <div class="input-group">
                    <select id="importar" name="importar" required>
                    </select>
                    <label for="importar">Importar</label>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <select id="estado" name="estado" required>
                    </select>
                    <label for="estado">Estado</label>
                </div>
            </div>
                <div class="form-actions">
                <button type="submit" id="submit-btn" class="btn btn-primary" title="Guardar rol"><i class="fas fa-save"></i>Guardar</button>
                <button type="button" id="cancel-btn" class="btn btn-outline" title="Cancelar"><i class="fas fa-times"></i>Cancelar</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>