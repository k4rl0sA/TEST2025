<?php
ini_set('display_errors','1');
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Menú</title>
    <link rel="stylesheet" href="../lib/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="stylesheet" href="../lib/css/choices.min.css?v=3">
    <script src="../lib/js/choices.min.js"></script>
    <script>window.CSRF_TOKEN = "<?php echo $_SESSION['csrf_token']; ?>";</script>
    <script src="../lib/js/app.js?v=3" defer></script>
    <script src="menu.js?v=3"></script>
</head>
<body>
    <div class="toast-container">
        <!-- Las notificaciones se agregarán aquí dinámicamente -->
    </div>
<div class="content-card">
    <div class='load' id='loader' z-index='0'></div>
    <div class="filters-bar">
        <button class="btn btn-chip" id="toggle-filters-btn" title="Mostrar/ocultar filtros">
            <i class="fa fa-filter"></i>
            <span class="chip-text">Filtros</span>
            <span id="active-filters-count" class="chip-count">0</span>
        </button>
        <div id="active-filters-chips" class="chips-list"></div>
    </div>
    <div class="form-section hidden" id="filters-panel" style="margin-bottom:1.5em;">
        <form id="filter-form" class="filter-section">
            <div class="input-group">
                <input type="text" id="fil-link" placeholder="">
                <label for="fil-link">Link</label>
            </div>
            <div class="input-group">
                <input type="text" id="fil-tipo" placeholder="">
                <label for="fil-tipo">Tipo</label>
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
                <button type="submit" class="btn btn-primary filter-btn" title="Aplicar filtros"><i class="fa fa-search"></i> Aplicar Filtros</button>
                <button type="button" class="btn btn-outline filter-reset" id="clear-filters" title="Limpiar filtros"><i class="fa fa-sync"></i></button>
            </div>
        </form>
    </div>
    <div class="table-header">
        <h2>Menú Registrado</h2>
        <button class="btn btn-add" id="add-btn" title="Agregar nuevo rol"><i class="fa fa-plus"></i><span> Nuevo Menú</span></button>
    </div>
    <div class="table-container" id="table-section">
        <table id="menu-table">
            <thead>
                <tr>
                    <th>Acciones</th>
                    <!-- <th data-sort="id">ID</th> -->
                    <th data-sort="link">Link</th>
                    <th data-sort="icono">Icono</th>
                    <th data-sort="tipo">Tipo</th>
                    <th data-sort="enlace">Enlace</th>
                    <th data-sort="menu">Menu</th>
                    <th data-sort="contenedor">Contenedor</th>
                    <th data-sort="estado">Estado</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="paginator" id="paginator"></div>
    </div>
    <div id="form-menu" class="form-section modal-content hidden">
        <div class="table-header">
            <h3 id="form-title"><i class="fas fa-edit"></i> Menú</h3>
            <button id="close-win" class="btn btn-outline" title="Cerrar ventana"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="menu-form">
            <input type="hidden" id="id">
            <div class="form-row">
                <div class="input-group">
                    <input type="text" id="link" name="link" required maxlength="30">
                    <label for="link">Link</label>
                </div>
                <div class="input-group">
                    <input type="text" id="icono" name="icono" maxlength="50">
                    <label for="icono">Icono</label>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <input type="text" id="tipo" name="tipo" required maxlength="3">
                    <label for="tipo">Tipo</label>
                </div>
                <div class="input-group">
                    <input type="text" id="enlace" name="enlace" required maxlength="50">
                    <label for="enlace">Enlace</label>
                </div>
            </div>
            <div class="form-row">
                <div class="input-group">
                    <input type="text" id="menu" name="menu" required maxlength="2">
                    <label for="menu">Menu</label>
                </div>
                <div class="input-group">
                    <select id="contenedor" name="contenedor" required>
                    </select>
                    <label for="contenedor">Contenedor</label>
                </div>
                <div class="input-group">
                    <select id="estado" name="estado" required>
                    </select>
                    <label for="estado">Estado</label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" id="submit-btn" class="btn btn-primary"><i class="fas fa-save"></i>Guardar</button>
                <button type="button" id="cancel-btn" class="btn btn-outline"><i class="fas fa-times"></i>Cancelar</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>