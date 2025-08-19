// --- Estado SPA ---
document.addEventListener('DOMContentLoaded', function() {
let editingId = null, roles = [], filters = { search:'', modulo:'', perfil:'', estado:'' };
let sortField = 'id_rol', sortDir = 'asc', page = 1, pageSize = 10, totalPages = 1;

// --- Filtros tipo chips ---
// En vez de tu función local, usa la global:
updateActiveFiltersChips(
    filters,
    { estado: window.estadoOptions, perfil: window.perfilOptions },
    'active-filters-chips',
    'active-filters-count',
    function(key) {
        filters[key] = '';
        fetchRoles();
        updateActiveFiltersChips(
            filters,
            { estado: window.estadoOptions, perfil: window.perfilOptions },
            'active-filters-chips',
            'active-filters-count'
        );
    }
);

window.removeFilter = function(key) {
    filters[key] = '';
    if (key === 'search') document.getElementById('fil-search').value = '';
    if (key === 'modulo') document.getElementById('fil-modulo').value = '';
    if (key === 'perfil') document.getElementById('fil-perfil').value = '';
    if (key === 'estado') document.getElementById('fil-estado').value = '';
    page = 1;
    fetchRoles();
    updateActiveFiltersChips(
        filters,
        { estado: window.estadoOptions, perfil: window.perfilOptions }, // Opciones para mostrar label
        'active-filters-chips',
        'active-filters-count',
        function(key) {
            filters[key] = '';
            fetchRoles();
            updateActiveFiltersChips(
                filters,
                { estado: window.estadoOptions, perfil: window.perfilOptions },
                'active-filters-chips',
                'active-filters-count'
            );
        }
    );

};

// --- Filtros funcionalidad ---
document.getElementById('filter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    filters.search = document.getElementById('fil-search').value.trim();
    filters.modulo = document.getElementById('fil-modulo').value.trim();
    filters.perfil = document.getElementById('fil-perfil').value.trim();
    filters.estado = document.getElementById('fil-estado').value;
    page = 1;
    fetchRoles();
    updateActiveFiltersChips(
        filters,
        { estado: window.estadoOptions, perfil: window.perfilOptions }, // Opciones para mostrar label
        'active-filters-chips',
        'active-filters-count',
        function(key) {
            filters[key] = '';
            fetchRoles();
            updateActiveFiltersChips(
                filters,
                { estado: window.estadoOptions, perfil: window.perfilOptions },
                'active-filters-chips',
                'active-filters-count'
            );
        }
    );

    filtersPanel.classList.add('hidden');
    filtersVisible = false;
});
// Mostrar/ocultar filtros
document.getElementById('clear-filters').onclick = function() {
    clearFilters(filters, 'filter-form', () => {
        page = 1;
        fetchRoles();
        updateActiveFiltersChips(
            filters,
            { estado: window.estadoOptions, perfil: window.perfilOptions },
            'active-filters-chips',
            'active-filters-count'
        );
        loadAllRoleSelects();
    });
};

// --- Tabla, paginador y ordenamiento ---
function renderRolesTable(data) {
    roles = data.roles || [];
    totalPages = data.totalPages || 1;
    const tbody = document.querySelector('#roles-table tbody');
    tbody.innerHTML = '';
    roles.forEach(role => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
        <td style="position:relative;">
                <button class="action-menu-btn" onclick="toggleActionMenu(event, ${role.id_rol})">
                    <i class="fa fa-ellipsis-v"></i>
                </button>
                <div class="action-menu" id="action-menu-${role.id_rol}">
                    <button class="menu-item edit-btn" onclick="editRole(${role.id_rol})">
                        <i class="fa fa-edit"></i>
                        <span class="label">Editar</span>
                    </button>
                    <button class="menu-item delete-btn" onclick="deleteRole(${role.id_rol})">
                        <i class="fa fa-trash"></i>
                        <span class="label">Eliminar</span>
                    </button>
                    <button class="menu-item edit-btn" onclick="deleteRole(${role.id_rol})">
                        <i class="fa fa-home"></i>
                        <span class="label">Caracterizar</span>
                    </button>
                </div>
            </td>
            <td data-label="ID">${role.id_rol}</td>
            <td data-label="Modulo">${role.modulo}</td>
            <td data-label="Perfil">${role.perfil}</td>
            <td data-label="Componente">${role.componente}</td>
            <td data-label="Consultar">${role.consultar}</td>
            <td data-label="Editar">${role.editar}</td>
            <td data-label="Crear">${role.crear}</td>
            <td data-label="Ajustar">${role.ajustar}</td>
            <td data-label="Importar">${role.importar}</td>
            <td data-label="Estado">${role.estado}</td>
        `;
        tbody.appendChild(tr);
    });
    renderPaginator();
    updateSortIcons('#roles-table', sortField, sortDir);
    enableMobileRowActions('#roles-table', '.action-menu-btn');
}

renderPaginator('#paginator', page, totalPages, function(newPage) {
    page = newPage;
    fetchRoles();
});


document.querySelectorAll('#roles-table th[data-sort]').forEach(th => {
    th.onclick = function() {
        const field = th.getAttribute('data-sort');
        if (sortField === field) sortDir = (sortDir === 'asc') ? 'desc' : 'asc';
        else { sortField = field; sortDir = 'asc'; }
        page = 1;
        fetchRoles();
        updateSortIcons('#roles-table', sortField, sortDir);
    };
});

// --- CRUD SPA ---
// FUNCION DE CREAR
document.getElementById('add-btn').onclick = function() {
    editingId = null;
    document.getElementById('role-form').reset();
    loadAllRoleSelects();
    const formTitle = document.getElementById('form-title');
    formTitle.innerHTML = '';
    const icon = document.createElement('i');
    icon.className = 'fa-solid fa-calendar-days';
    formTitle.appendChild(icon);
    formTitle.appendChild(document.createTextNode(' Nuevo Rol'));
    document.getElementById('form-ajustes').classList.remove('hidden');
    document.getElementById('table-section').classList.add('hidden');
};
document.getElementById('cancel-btn').onclick = function() {
    document.getElementById('form-ajustes').classList.add('hidden');
    document.getElementById('table-section').classList.remove('hidden');
};
document.getElementById('close-win').onclick = function() {
    document.getElementById('form-ajustes').classList.add('hidden');
    document.getElementById('table-section').classList.remove('hidden');
};
//FUNCION DE EDITAR
window.editRole = function(id) {
    document.getElementById('role-form').reset();
    fetchWithLoader(`/ajustes/lib.php?a=get&id=${id}`, {}, function(data) {
            const datos= data.datos;
            editingId = datos.id_rol;
            loadAllRoleSelects({
                consultar: datos.consultar,
                editar: datos.editar,
                crear: datos.crear,
                ajustar: datos.ajustar,
                importar: datos.importar,
                estado: datos.estado
            });
            document.getElementById('id_rol').value = datos.id_rol;
            document.getElementById('modulo').value = datos.modulo;
            document.getElementById('perfil').value = datos.perfil;
            document.getElementById('componente').value = datos.componente;
            document.getElementById('form-title').textContent = 'Editar Rol';
            document.getElementById('table-section').classList.add('hidden');
            document.getElementById('form-ajustes').classList.remove('hidden');
        });
};
// FUNCION DE ELIMINAR
window.deleteRole = function(id) {
    if (!confirm('¿Está seguro de eliminar este rol?')) return;
    const formData = new FormData();
    formData.append('csrf_token', window.CSRF_TOKEN);
    fetchWithLoader(`/ajustes/lib.php?a=delete&id=${id}`, {
        method: 'POST',
        body: formData
    }, () => fetchRoles());
};
document.getElementById('role-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    if (editingId) formData.append('id_rol', editingId);
    formData.append('csrf_token', window.CSRF_TOKEN);
   /*  for (const [key, value] of formData.entries()) {
      console.log(`${key}: ${value}`);
    } */
    fetchWithLoader(`/ajustes/lib.php?a=${editingId ? 'update' : 'create'}`, {
        method: 'POST',
        body: formData
    }, () => {
        document.getElementById('form-ajustes').classList.add('hidden');
        document.getElementById('table-section').classList.remove('hidden');
        document.getElementById('role-form').reset();
        editingId = null;
        fetchRoles();
    });
});
// --- Fetch roles con filtros, paginador y orden ---
function fetchRoles() {
    showLoader();
    const params = new URLSearchParams({
        ...filters,
        sort: sortField,
        dir: sortDir,
        page,
        pageSize
    }).toString();
    fetchWithLoader(`/ajustes/lib.php?a=list&${params}`, {}, function(data) {
        renderRolesTable(data);
    });
}

// Inicializar
fetchRoles();
updateActiveFiltersChips(
        filters,
        { estado: window.estadoOptions, perfil: window.perfilOptions }, // Opciones para mostrar label
        'active-filters-chips',
        'active-filters-count',
        function(key) {
            filters[key] = '';
            fetchRoles();
            updateActiveFiltersChips(
                filters,
                { estado: window.estadoOptions, perfil: window.perfilOptions },
                'active-filters-chips',
                'active-filters-count'
            );
        }
    );

loadAllRoleSelects();

    // Solo en móvil: mostrar botón de acciones al hacer doble clic en la fila
   

function loadAllRoleSelects(selected = {}) {
    fetchWithLoader('/ajustes/lib.php?a=opciones', {}, function(data) {
        if (data.opciones && data.opciones.estado)
            window.estadoOptions = data.opciones.estado;
            loadSelectChoices('fil-estado', data.opciones.estado, '-- Estado --', selected.estado || 'A');
        if (data.opciones && data.opciones.perfil)
            window.perfilOptions = data.opciones.perfil;
            loadSelectChoices('fil-perfil', data.opciones.perfil, '-- Perfil --', selected.perfil || '');
        if (data.opciones && data.opciones.rta) {
            loadSelectChoices('consultar', data.opciones.rta, '-- Consultar --', selected.consultar ?? '1');
            loadSelectChoices('editar', data.opciones.rta, '-- Editar --', selected.editar ?? '1');
            loadSelectChoices('crear', data.opciones.rta, '-- Crear --', selected.crear ?? '1');
            loadSelectChoices('ajustar', data.opciones.rta, '-- Ajustar --', selected.ajustar ?? '1');
            loadSelectChoices('importar', data.opciones.rta, '-- Importar --', selected.importar ?? '1');
            loadSelectChoices('estado', data.opciones.estado, '-- Estado --', selected.estado || 'A');
        }
    });
}

function initAjustes() {
    fetchRoles();
    updateActiveFiltersChips(
        filters,
        { estado: window.estadoOptions, perfil: window.perfilOptions },
        'active-filters-chips',
        'active-filters-count',
        removeFilterCallback
    );
    loadAllRoleSelects();
}

function removeFilterCallback(key) {
    filters[key] = '';
    fetchRoles();
    updateActiveFiltersChips(
        filters,
        { estado: window.estadoOptions, perfil: window.perfilOptions },
        'active-filters-chips',
        'active-filters-count',
        removeFilterCallback
    );
}

initAjustes();

});