const path = '/adm-menu/';
document.addEventListener('DOMContentLoaded', function() {
    let editingId = null;
    let menus = [];
    let filters = { link:'', tipo:'', estado:'' };
    let sortField = 'id', sortDir = 'asc', page = 1, pageSize = 10, totalPages = 1;

    function removeFilterCallback(key) {
        filters[key] = '';
        fetchMenus();
        updateActiveFiltersChips(
            filters,
            {}, // No hay opciones extra
            'active-filters-chips',
            'active-filters-count',
            removeFilterCallback
        );
    }

    function renderMenusTable(data) {
        menus = data.menus || [];
        totalPages = data.totalPages || 1;
        const tbody = document.querySelector('#menu-table tbody');
        tbody.innerHTML = '';
        menus.forEach(menu => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="position:relative;">
                    <button class="action-menu-btn" onclick="toggleActionMenu(event, ${menu.id})">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <div class="action-menu" id="action-menu-${menu.id}">
                        <button class="menu-item edit-btn" onclick="editMenu(${menu.id})">
                            <i class="fa fa-edit"></i>
                            <span class="label">Editar</span>
                        </button>
                        <button class="menu-item delete-btn" onclick="deleteMenu(${menu.id})">
                            <i class="fa fa-trash"></i>
                            <span class="label">Eliminar</span>
                        </button>
                    </div>
                </td>
                <td>${menu.link}</td>
                <td>${menu.icono}</td>
                <td>${menu.tipo}</td>
                <td>${menu.enlace}</td>
                <td>${menu.menu}</td>
                <td>${menu.contenedor}</td>
                <td>${menu.estado}</td>
            `;
            tbody.appendChild(tr);
        });
        renderPaginator('#paginator', page, totalPages, function(newPage) {
            page = newPage;
            fetchMenus();
        });
        updateSortIcons('#menu-table', sortField, sortDir);
        enableMobileRowActions('#menu-table', '.action-menu-btn');
    }

    function fetchMenus() {
        showLoader();
        const params = new URLSearchParams({
            ...filters,
            sort: sortField,
            dir: sortDir,
            page,
            pageSize
        }).toString();
        fetchWithLoader(path + `lib.php?a=list&${params}`, {}, function(data) {
            renderMenusTable(data);
        });
    }

    function loadAllMenuSelects(selected = {}) {
        fetchWithLoader(path+'lib.php?a=opciones', {}, function(data) {
            if (data.opciones && data.opciones.estado) {
                window.estadoOptions = data.opciones.estado;
                loadSelectChoices('fil-estado', data.opciones.estado, '-- Estado --', selected.estado || 'A');
            }
        });
    }

    function initMenu() {
        fetchMenus();
        updateActiveFiltersChips(
            filters,
            {estado: window.estadoOptions},
            'active-filters-chips',
            'active-filters-count',
            removeFilterCallback
        );
        loadAllMenuSelects();
    }

    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        filters.link = document.getElementById('fil-link').value.trim();
        filters.tipo = document.getElementById('fil-tipo').value.trim();
        filters.estado = document.getElementById('fil-estado').value;
        page = 1;
        fetchMenus();
        updateActiveFiltersChips(
            filters,
            {estado: window.estadoOptions},
            'active-filters-chips',
            'active-filters-count',
            removeFilterCallback
        );
        document.getElementById('filters-panel').classList.add('hidden');
    });

    document.getElementById('clear-filters').onclick = function() {
        clearFilters(filters, 'filter-form', () => {
            page = 1;
            fetchMenus();
            updateActiveFiltersChips(
                filters,
                {estado: window.estadoOptions},
                'active-filters-chips',
                'active-filters-count',
                removeFilterCallback
            );
            loadAllMenuSelects();
        });
    };

    document.querySelectorAll('#menu-table th[data-sort]').forEach(th => {
        th.onclick = function() {
            const field = th.getAttribute('data-sort');
            if (sortField === field) sortDir = (sortDir === 'asc') ? 'desc' : 'asc';
            else { sortField = field; sortDir = 'asc'; }
            page = 1;
            fetchMenus();
            updateSortIcons('#menu-table', sortField, sortDir);
        };
    });

    document.getElementById('add-btn').onclick = function() {
        editingId = null;
        document.getElementById('menu-form').reset();
        loadAllMenuSelects();
        document.getElementById('form-title').textContent = 'Nuevo Menú';
        document.getElementById('form-menu').classList.remove('hidden');
        document.getElementById('table-section').classList.add('hidden');
    };

    document.getElementById('cancel-btn').onclick = function() {
        document.getElementById('form-menu').classList.add('hidden');
        document.getElementById('table-section').classList.remove('hidden');
    };
    document.getElementById('close-win').onclick = function() {
        document.getElementById('form-menu').classList.add('hidden');
        document.getElementById('table-section').classList.remove('hidden');
    };

    window.editMenu = function(token) {
        document.getElementById('menu-form').reset();
        fetchWithLoader(path+`lib.php?a=get&token=${token}`, {}, function(data) {
            const datos = data.datos;
            editingId = token;
              loadAllMenuSelects({
                estado: datos.estado
            });
            document.getElementById('id').value = datos.id;
            document.getElementById('link').value = datos.link;
            document.getElementById('icono').value = datos.icono;
            document.getElementById('tipo').value = datos.tipo;
            document.getElementById('enlace').value = datos.enlace;
            document.getElementById('menu').value = datos.menu;
            document.getElementById('contenedor').value = datos.contenedor;
            document.getElementById('estado').value = datos.estado;
            document.getElementById('form-title').textContent = 'Editar Menú';
            document.getElementById('table-section').classList.add('hidden');
            document.getElementById('form-menu').classList.remove('hidden');
        });
    };

    window.deleteMenu = function(token) {
        if (!confirm('¿Está seguro de eliminar este menú?')) return;
        const formData = new FormData();
        formData.append('csrf_token', window.CSRF_TOKEN);
        formData.append('token', token);
        fetchWithLoader(path+`lib.php?a=delete`, {
            method: 'POST',
            body: formData
        }, () => fetchMenus());
    };

    document.getElementById('menu-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        if (editingId) formData.append('id', editingId);
        formData.append('csrf_token', window.CSRF_TOKEN);
        fetchWithLoader(path+`lib.php?a=${editingId ? 'update' : 'create'}`, {
            method: 'POST',
            body: formData
        }, () => {
            document.getElementById('form-menu').classList.add('hidden');
            document.getElementById('table-section').classList.remove('hidden');
            document.getElementById('menu-form').reset();
            editingId = null;
            fetchMenus();
        });
    });

    initMenu();
});