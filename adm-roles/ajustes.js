const path = '/adm-roles/';
// --- SPA de Ajustes de Roles ---
document.addEventListener('DOMContentLoaded', function() {
    // Variables de estado globales
    let editingId = null;
    let roles = [];
    let filters = { search:'', modulo:'', perfil:'', estado:'' };
    let sortField = 'id_rol', sortDir = 'asc', page = 1, pageSize = 10, totalPages = 1;

    /**
     * Callback para quitar un filtro tipo chip
     */
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

    /**
     * Renderiza la tabla de roles con menú contextual y acciones
     */
    function renderRolesTable(data) {
        roles = data.roles || [];
        totalPages = data.totalPages || 1;
        document.getElementById('total-roles').textContent = `Total: ${data.totalRows || 0}`;
        showRangeInfo(page, pageSize, data.totalRows || 0);
        const tbody = document.querySelector('#roles-table tbody');
        tbody.innerHTML = '';
        roles.forEach(role => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="checkbox" class="select-role" value="${role.token}"></td>
                <td style="position:relative;">
                    <button class="action-menu-btn" onclick="toggleActionMenu(event, '${role.token}')">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <div class="action-menu" id="action-menu-${role.token}">
                        <button class="menu-item edit-btn" onclick="editRole('${role.token}')">
                            <i class="fa fa-edit"></i>
                            <span class="label">Editar</span>
                        </button>
                        <button class="menu-item delete-btn" onclick="deleteRole('${role.token}')">
                            <i class="fa fa-trash"></i>
                            <span class="label">Inactivar</span>
                        </button>
                        <button class="menu-item" onclick="caracterizarRole('${role.token}')">
                            <i class="fa fa-home"></i>
                            <span class="label">Caracterizar</span>
                        </button>
                    </div>
                </td>
                
                <td class="col-modulo" data-label="Modulo">${role.modulo}</td>
                <td class="col-perfil" data-label="Perfil">${role.perfil}</td>
                <td class="col-componente" data-label="Componente">${role.componente}</td>
                <td class="col-consultar" data-label="Consultar">${role.consultar}</td>
                <td class="col-editar" data-label="Editar">${role.editar}</td>
                <td class="col-crear" data-label="Crear">${role.crear}</td>
                <td class="col-ajustar" data-label="Ajustar">${role.ajustar}</td>
                <td class="col-importar" data-label="Importar">${role.importar}</td>
                <td class="col-estado" data-label="Estado">${role.estado}</td>
            `;
            /* <td data-label="ID">${role.token}</td>//NO MOSTRAR ID  */
            tbody.appendChild(tr);
        });
        // Paginador y ordenamiento
        renderPaginator('#paginator', page, totalPages, function(newPage) {
            page = newPage;
            fetchRoles();
        });
        updateSortIcons('#roles-table', sortField, sortDir);
        enableMobileRowActions('#roles-table', '.action-menu-btn');
    }

    /**
     * Obtiene y renderiza los roles según filtros, paginador y orden
     */
    function fetchRoles() {
        showLoader();
        const params = new URLSearchParams({
            ...filters,
            sort: sortField,
            dir: sortDir,
            page,
            pageSize
        }).toString();
        fetchWithLoader(path + `lib.php?a=list&${params}`, {}, function(data) {
            if (data.redirect) {
                window.location.href = data.redirect;
            return;
            }
            renderRolesTable(data);
        });
    }

    /**
     * Carga las opciones de los selects dinámicos (Choices.js)
     */
    function loadAllRoleSelects(selected = {}) {
        fetchWithLoader(path+'lib.php?a=opciones', {}, function(data) {
            if (data.opciones && data.opciones.estado) {
                window.estadoOptions = data.opciones.estado;
                loadSelectChoices('fil-estado', data.opciones.estado, '-- Estado --', selected.estado || 'A');
            }
            if (data.opciones && data.opciones.perfil) {
                window.perfilOptions = data.opciones.perfil;
                loadSelectChoices('fil-perfil', data.opciones.perfil, '-- Perfil --', selected.perfil || '');
            }
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

    /**
     * Inicializa el módulo de ajustes
     */
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

    // --- Filtros tipo chips ---
    // Se actualizan al enviar el formulario de filtros
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
            { estado: window.estadoOptions, perfil: window.perfilOptions },
            'active-filters-chips',
            'active-filters-count',
            removeFilterCallback
        );
        document.getElementById('filters-panel').classList.add('hidden');
    });

    // Limpiar filtros
    document.getElementById('clear-filters').onclick = function() {
        clearFilters(filters, 'filter-form', () => {
            page = 1;
            fetchRoles();
            updateActiveFiltersChips(
                filters,
                { estado: window.estadoOptions, perfil: window.perfilOptions },
                'active-filters-chips',
                'active-filters-count',
                removeFilterCallback
            );
            loadAllRoleSelects();
        });
    };

    // --- Ordenamiento de tabla ---
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
    // Crear nuevo rol
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

    // Cancelar edición/creación
    document.getElementById('cancel-btn').onclick = function() {
        document.getElementById('form-ajustes').classList.add('hidden');
        document.getElementById('table-section').classList.remove('hidden');
    };
    document.getElementById('close-win').onclick = function() {
        document.getElementById('form-ajustes').classList.add('hidden');
        document.getElementById('table-section').classList.remove('hidden');
    };

    // Editar rol
    window.editRole = function(token) {
        document.getElementById('role-form').reset();
        fetchWithLoader(path+`lib.php?a=get&token=${token}`, {}, function(data) {
            const datos = data.datos;
            editingId = token;
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

    // Eliminar rol
    window.deleteRole = function(token) {
        if (!confirm('¿Eliminar este rol?')) {
            showToast('Acción cancelada por el usuario.', 'info');
            return;
        }
        const formData = new FormData();
        formData.append('csrf_token', window.CSRF_TOKEN);
        formData.append('token', token);
        fetchWithLoader(path+`lib.php?a=delete`, {
            method: 'POST',
            body: formData
        }, () => fetchRoles());
    };

    // Caracterizar rol (ejemplo de acción extra)
    window.caracterizarRole = function(id) {
        showToast('Funcionalidad de caracterizar pendiente de implementar.', 'info');
    };

    // Guardar (crear/editar) rol
    document.getElementById('role-form').addEventListener('submit', function(e) {
        e.preventDefault();
         // Define las reglas de validación
        const rules = [
            { field: 'modulo', validate: v => !!v, message: 'El campo Módulo es obligatorio.' },
            { field: 'perfil', validate: v => !!v, message: 'El campo Perfil es obligatorio.' },
            { field: 'componente', validate: v => !!v, message: 'El campo Componente es obligatorio.' },
            { field: 'consultar', validate: v => !!v, message: 'El campo Consultar es obligatorio.' },
            { field: 'editar', validate: v => !!v, message: 'El campo Editar es obligatorio.' },
            { field: 'crear', validate: v => !!v, message: 'El campo Crear es obligatorio.' },
            { field: 'ajustar', validate: v => !!v, message: 'El campo Ajustar es obligatorio.' },
            { field: 'importar', validate: v => !!v, message: 'El campo Importar es obligatorio.' },
            { field: 'estado', validate: v => !!v, message: 'El campo Estado es obligatorio.' },
            { field: 'perfil', validate: v => v.length <= 11, message: 'El campo Perfil no debe exceder 11 caracteres.' },
            { field: 'componente', validate: v => v.length <= 3, message: 'El campo Componente no debe exceder 3 caracteres.' },
            { field: 'modulo', validate: v => v.length <= 15, message: 'El campo Módulo no debe exceder 15 caracteres.' },
            { field: 'modulo', validate: v => /^[a-zA-Z0-9 _-]+$/.test(v), message: 'El campo Módulo contiene caracteres inválidos.' },
            { field: 'componente', validate: v => /^[A-Z]+$/.test(v), message: 'El campo Módulo contiene caracteres inválidos.' },
            { field: 'perfil', validate: v => /^[A-Z]+$/.test(v), message: 'El campo Módulo contiene caracteres inválidos.' }
        ];
        const formData = new FormData(this);
        if (editingId) formData.append('token', editingId);
        formData.append('csrf_token', window.CSRF_TOKEN);
        fetchWithLoader(path+`lib.php?a=${editingId ? 'update' : 'create'}`, {  
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

    // Inicializar módulo
    initAjustes();

    // Ayuda contextual
    document.getElementById('help-btn').onclick = function() {
        const helpContent = `
            <h4>Gestión de Roles</h4>
            <p>En esta sección puede gestionar los roles del sistema, incluyendo creación, edición, eliminación y asignación de permisos.</p>
            <ul>
            <li><strong>Crear Rol:</strong> Haga clic en "Nuevo Rol" para agregar un nuevo rol con permisos específicos.</li>
            <li><strong>Editar Rol:</strong> Use el menú de acciones (ícono de tres puntos) junto a cada rol para editar sus detalles.</li>
            <li><strong>Eliminar Rol:</strong> Desde el mismo menú de acciones, puede inactivar un rol existente.</li>
            <li><strong>Filtros y Búsqueda:</strong> Utilice los filtros en la parte superior para buscar roles por módulo, perfil o estado.</li>
            <li><strong>Paginación y Ordenamiento:</strong> Navegue entre páginas de roles y ordene la tabla haciendo clic en los encabezados de columna.</li>
            </ul>
            <p>Para más información, consulte el <a href="https://ejemplo.com/manual-roles" target="_blank">manual del usuario</a> 
            o vea el <a href="https://ejemplo.com/video-roles" target="_blank">video tutorial</a>,o contacte al administrador del sistema.
            </p>
        `;
        showHelpModal(helpContent);
    };

    // Seleccionar/deseleccionar todos
    document.getElementById('select-all-roles').onclick = function() {
        const checked = this.checked;
        document.querySelectorAll('#roles-table .select-role').forEach(cb => cb.checked = checked);
    };

    // Acción masiva: Activar
    document.getElementById('bulk-activate').onclick = function() {
        const tokens = getSelectedTokens('#roles-table');
        if (tokens.length === 0) return showToast('Seleccione al menos un rol.', 'warning');
        if (!confirm(`¿Activar ${tokens.length} roles seleccionados?`)) return;
        bulkAction('activate', tokens);
    };

    // Acción masiva: Inactivar
    document.getElementById('bulk-inactivate').onclick = function() {
        const tokens = getSelectedTokens('#roles-table');
        if (tokens.length === 0) return showToast('Seleccione al menos un rol.', 'warning');
        if (!confirm(`¿Inactivar ${tokens.length} roles seleccionados?`)) return;
        bulkAction('inactivate', tokens);
    };

    // Acción masiva: Eliminar
    document.getElementById('bulk-delete').onclick = function() {
        const tokens = getSelectedTokens('#roles-table');
        if (tokens.length === 0) return showToast('Seleccione al menos un rol.', 'warning');
        if (!confirm(`¿Eliminar ${tokens.length} roles seleccionados? Esta acción no se puede deshacer.`)) return;
        bulkAction('delete', tokens);
    };

    // Función para enviar la acción masiva al backend
    function bulkAction(action, tokens) {
        const formData = new FormData();
        formData.append('csrf_token', window.CSRF_TOKEN);
        formData.append('action', action);
        formData.append('tokens', JSON.stringify(tokens));
        fetchWithLoader(path + 'lib.php?a=bulk', {
            method: 'POST',
            body: formData
        }, (data) => {
            showToast(data.message || 'Acción masiva completada.', data.status || 'success');
            fetchRoles()});
    }
    // Mostrar/ocultar columnas
    document.getElementById('toggle-columns-btn').onclick = function() {
        document.getElementById('columns-panel').classList.toggle('hidden');
    };
    // 
    document.querySelectorAll('.col-toggle').forEach(cb => {
        cb.onchange = function() {
            toggleColumn(cb.dataset.col, cb.checked);
        };
    });
    //ocultar columnas por defecto
    const defaultHiddenCols = ['col-consultar', 'col-editar', 'col-crear', 'col-ajustar', 'col-importar'];
    defaultHiddenCols.forEach(col => {
        const checkbox = document.querySelector(`.col-toggle[data-col="${col}"]`);
        if (checkbox) {
            checkbox.checked = false;
            toggleColumn(col, false);
        }});

    document.getElementById('bulk-activate').onclick = function() {
        const tokens = getSelectedTokens('#roles-table');
        if (tokens.length === 0) return showToast('Seleccione al menos un rol.', 'warning');
        if (!confirm(`¿Activar ${tokens.length} roles seleccionados?`)) return;
        bulkAction(path + 'lib.php?a=bulk', 'activate', tokens, () => fetchRoles());
    };

    function updateBulkActionsBar() {
    const selected = getSelectedTokens('#roles-table');
    const bar = document.getElementById('bulk-actions-bar');
    const count = document.getElementById('bulk-count');
    if (selected.length > 0) {
        bar.classList.remove('hidden');
        count.textContent = `${selected.length} seleccionados`;
    } else {
        bar.classList.add('hidden');
        count.textContent = '0 seleccionados';
    }
}
document.querySelectorAll('#roles-table .select-role').forEach(cb => {
    cb.onchange = updateBulkActionsBar;
});
document.getElementById('select-all-roles').onchange = updateBulkActionsBar;
document.getElementById('bulk-close').onclick = function() {
    document.querySelectorAll('#roles-table .select-role').forEach(cb => cb.checked = false);
    updateBulkActionsBar();
};


    
});