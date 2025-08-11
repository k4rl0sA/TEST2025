// --- Estado SPA ---
document.addEventListener('DOMContentLoaded', function() {
let editingId = null, roles = [], filters = { search:'', modulo:'', perfil:'', estado:'' };
let sortField = 'id_rol', sortDir = 'asc', page = 1, pageSize = 10, totalPages = 1;

// --- Filtros tipo chips ---
function updateActiveFiltersChips() {
    const chipsList = document.getElementById('active-filters-chips');
    if (!chipsList) return;
    chipsList.innerHTML = '';
    let count = 0;
    if (filters.search) { chipsList.appendChild(createChip('Buscar', filters.search, 'search')); count++; }
    if (filters.modulo) { chipsList.appendChild(createChip('Módulo', filters.modulo, 'modulo')); count++; }
    if (filters.perfil) { chipsList.appendChild(createChip('Perfil', filters.perfil, 'perfil')); count++; }
    if (filters.estado) {
        let label = filters.estado === 'A' ? 'Activo' : 'Inactivo';
        chipsList.appendChild(createChip('Estado', label, 'estado')); count++;
    }
    const countSpan = document.getElementById('active-filters-count');
    if (countSpan) {
        countSpan.textContent = count;
        updateFiltersCount(count);
    }
}
function createChip(label, value, key) {
    const chip = document.createElement('span');
    chip.className = 'chip';
    chip.innerHTML = `<strong>${label}:</strong> ${value} <button class="chip-remove" title="Quitar filtro" onclick="removeFilter('${key}')">&times;</button>`;
    return chip;
}
window.removeFilter = function(key) {
    filters[key] = '';
    if (key === 'search') document.getElementById('fil-search').value = '';
    if (key === 'modulo') document.getElementById('fil-modulo').value = '';
    if (key === 'perfil') document.getElementById('fil-perfil').value = '';
    if (key === 'estado') document.getElementById('fil-estado').value = '';
    page = 1;
    fetchRoles();
    updateActiveFiltersChips();
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
    updateActiveFiltersChips();
    filtersPanel.classList.add('hidden');
    filtersVisible = false;
});
document.getElementById('clear-filters').onclick = function() {
    document.getElementById('filter-form').reset();
    filters = { search:'', modulo:'', perfil:'', estado:'' };
    page = 1;
    fetchRoles();
    updateActiveFiltersChips();
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
                        <span class="icon"><i class="fa fa-edit"></i></span>
                        <span class="label">Editar</span>
                    </button>
                    <button class="menu-item delete-btn" onclick="deleteRole(${role.id_rol})">
                        <span class="icon"><i class="fa fa-trash"></i></span>   
                        <span class="label">Editar</span>
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
    updateSortIcons();
    enableMobileRowActions();
}

function renderPaginator() {
    const pag = document.getElementById('paginator');
    pag.innerHTML = '';
    const maxButtons = 5;
    let start = Math.max(1, page - Math.floor(maxButtons / 2));
    let end = start + maxButtons - 1;
    if (end > totalPages) {
        end = totalPages;
        start = Math.max(1, end - maxButtons + 1);
    }
    if (page > 1) {
        const firstBtn = document.createElement('button');
        firstBtn.innerHTML = '<i class="fa fa-angle-double-left"></i>';
        firstBtn.title = "Primera página";
        firstBtn.onclick = () => { page = 1; fetchRoles(); };
        pag.appendChild(firstBtn);
    }
    if (page > 1) {
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<i class="fa fa-angle-left"></i>';
        prevBtn.title = "Anterior";
        prevBtn.onclick = () => { page = page - 1; fetchRoles(); };
        pag.appendChild(prevBtn);
    }
    for (let i = start; i <= end; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = (i === page) ? 'active' : '';
        btn.onclick = () => { page = i; fetchRoles(); };
        pag.appendChild(btn);
    }
    if (page < totalPages) {
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<i class="fa fa-angle-right"></i>';
        nextBtn.title = "Siguiente";
        nextBtn.onclick = () => { page = page + 1; fetchRoles(); };
        pag.appendChild(nextBtn);
    }
    if (page < totalPages) {
        const lastBtn = document.createElement('button');
        lastBtn.innerHTML = '<i class="fa fa-angle-double-right"></i>';
        lastBtn.title = "Última página";
        lastBtn.onclick = () => { page = totalPages; fetchRoles(); };
        pag.appendChild(lastBtn);
    }
}

// --- Ordenar por columna ---
function updateSortIcons() {
    document.querySelectorAll('#roles-table th[data-sort]').forEach(th => {
        const field = th.getAttribute('data-sort');
        const iconSpan = th.querySelector('.sort-icon');
        if (!iconSpan) return;
        if (sortField === field) {
            th.classList.add('sorted');
            iconSpan.innerHTML = '';
            const icon = document.createElement('i');
            icon.className = sortDir === 'asc' ? 'fa fa-arrow-up' : 'fa fa-arrow-down';
            iconSpan.appendChild(icon);
        } else {
            th.classList.remove('sorted');
            iconSpan.innerHTML = '<i class="fa fa-sort"></i>';
        }
    });
}
document.querySelectorAll('#roles-table th[data-sort]').forEach(th => {
    th.onclick = function() {
        const field = th.getAttribute('data-sort');
        if (sortField === field) sortDir = (sortDir === 'asc') ? 'desc' : 'asc';
        else { sortField = field; sortDir = 'asc'; }
        page = 1;
        fetchRoles();
        updateSortIcons();
    };
});

// --- CRUD SPA ---
document.getElementById('add-btn').onclick = function() {
    editingId = null;
    document.getElementById('role-form').reset();
    const formTitle = document.getElementById('form-title');
    formTitle.innerHTML = '';
    const icon = document.createElement('i');
    icon.className = 'fa-solid fa-calendar-days';
    formTitle.appendChild(icon);
    formTitle.appendChild(document.createTextNode(' Nuevo Rol'));
    document.getElementById('modal-bg').classList.remove('hidden');
    document.getElementById('table-section').classList.add('hidden');
};
document.getElementById('cancel-btn').onclick = function() {
    document.getElementById('modal-bg').classList.add('hidden');
    document.getElementById('table-section').classList.remove('hidden');
};
document.getElementById('close-win').onclick = function() {
    document.getElementById('modal-bg').classList.add('hidden');
    document.getElementById('table-section').classList.remove('hidden');
};
window.editRole = function(id) {
    fetchWithLoader(`/ajustes/lib.php?a=get&id=${id}`, {}, function(data) {
            const datos= data.datos;
            editingId = datos.id_rol;
            document.getElementById('id_rol').value = datos.id_rol;
            document.getElementById('modulo').value = datos.modulo;
            document.getElementById('perfil').value = datos.perfil;
            document.getElementById('componente').value = datos.componente;
            document.getElementById('consultar').value = datos.consultar;
            document.getElementById('editar').value = datos.editar;
            document.getElementById('crear').value = datos.crear;
            document.getElementById('ajustar').value = datos.ajustar;
            document.getElementById('importar').value = datos.importar;
            document.getElementById('estado').value = datos.estado;
            document.getElementById('form-title').textContent = 'Editar Rol';
            document.getElementById('table-section').classList.add('hidden');
            document.getElementById('modal-bg').classList.remove('hidden');
        });
};
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
    fetchWithLoader(`/ajustes/lib.php?a=${editingId ? 'update' : 'create'}`, {
        method: 'POST',
        body: formData
    }, () => {
        document.getElementById('modal-bg').classList.add('hidden');
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
updateActiveFiltersChips();
loadAllRoleSelects();

    // Solo en móvil: mostrar botón de acciones al hacer doble clic en la fila
    function enableMobileRowActions() {
        if (window.innerWidth > 600) return; // Solo móvil
        document.querySelectorAll('#roles-table tbody tr').forEach(tr => {
            tr.addEventListener('dblclick', function() {
                document.querySelectorAll('#roles-table tbody tr').forEach(row => row.classList.remove('tr-show-actions'));
                tr.classList.add('tr-show-actions');
            });
            document.body.addEventListener('click', function(e) {
                if (!tr.contains(e.target)) tr.classList.remove('tr-show-actions');
            });
        });
    }

function loadAllRoleSelects(selected = {}) {
    fetchWithLoader('/ajustes/lib.php?a=opciones', {}, function(data) {
        if (data.opciones && data.opciones.estado)
            loadSelectChoices('fil-estado', data.opciones.estado, '-- Estado --', 'A');
        if (data.opciones && data.opciones.rta) {
            loadSelectChoices('consultar', data.opciones.rta, '-- Consultar --', 1);
            loadSelectChoices('editar', data.opciones.rta, '-- Editar --', 1);
            loadSelectChoices('crear', data.opciones.rta, '-- Crear --', 1);
            loadSelectChoices('ajustar', data.opciones.rta, '-- Ajustar --', 1);
            loadSelectChoices('importar', data.opciones.rta, '-- Importar --', 1);
        }
    });
}

});