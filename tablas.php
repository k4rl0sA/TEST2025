<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla y Gráficos Dinámicos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #e3f2fd; font-family: 'Poppins', Arial, sans-serif; margin: 0; }
        .container { max-width: 1100px; margin: 30px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 32px #1976d220; padding: 32px 24px; }
        h2 { color: #1976d2; text-align: center; margin-bottom: 24px; }
        .controls { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; justify-content: center; }
        .controls input, .controls select, .controls button { padding: 8px 12px; border-radius: 6px; border: 1px solid #90caf9; font-size: 1em; }
        .controls button { background: linear-gradient(90deg, #1976d2 60%, #42a5f5 100%); color: #fff; border: none; font-weight: 600; cursor: pointer; }
        .controls button:hover { background: #1565c0; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; background: #fff; }
        th, td { padding: 12px 10px; border-bottom: 1.5px solid #e3f2fd; text-align: left; }
        th { background: #1976d2; color: #fff; cursor: pointer; position: relative; }
        th.sort-asc::after { content: "▲"; position: absolute; right: 10px; }
        th.sort-desc::after { content: "▼"; position: absolute; right: 10px; }
        tr:hover { background: #e3f2fd; }
        .filter-input { width: 90%; padding: 4px 8px; border-radius: 4px; border: 1px solid #bbdefb; margin-bottom: 4px; }
        .totals-row { background: #bbdefb; font-weight: bold; }
        .chart-container { background: #f5faff; border-radius: 12px; padding: 18px; margin-top: 24px; }
        @media (max-width: 700px) {
            .container { padding: 10px 2vw; }
            th, td { padding: 8px 4px; font-size: 0.98em; }
            .controls { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Tabla y Gráficos Dinámicos</h2>
    <div class="controls">
        <input type="text" id="headers" placeholder="Encabezados (ej: Nombre,Edad,Nota)">
        <input type="text" id="totals" placeholder="Totales (ej: Edad,Nota) - opcional">
        <input type="text" id="row" placeholder="Fila (ej: Ana,23,4.5)">
        <button onclick="addRow()">Agregar Fila</button>
        <button onclick="clearTable()">Limpiar</button>
        <select id="chartType" onchange="drawChart()">
            <option value="bar">Barras</option>
            <option value="line">Líneas</option>
            <option value="pie">Torta</option>
            <option value="doughnut">Dona</option>
        </select>
        <button onclick="downloadJSON()">Descargar JSON</button>
    </div>
    <div class="table-responsive">
        <table id="tabla">
            <thead></thead>
            <tbody></tbody>
            <tfoot></tfoot>
        </table>
    </div>
    <div class="chart-container">
        <canvas id="chart"></canvas>
    </div>
</div>
<script>
let data = {
    headers: [],
    totals: [],
    rows: []
};
let sortCol = null, sortAsc = true;

function renderTable() {
    const thead = document.querySelector('#tabla thead');
    const tbody = document.querySelector('#tabla tbody');
    const tfoot = document.querySelector('#tabla tfoot');
    // Encabezados
    thead.innerHTML = '';
    if (data.headers.length) {
        let tr = document.createElement('tr');
        data.headers.forEach((h, i) => {
            let th = document.createElement('th');
            th.textContent = h;
            th.onclick = () => sortTable(i);
            if (sortCol === i) th.classList.add(sortAsc ? 'sort-asc' : 'sort-desc');
            // Filtro
            let input = document.createElement('input');
            input.className = 'filter-input';
            input.placeholder = 'Filtrar...';
            input.oninput = () => filterTable(i, input.value);
            th.appendChild(document.createElement('br'));
            th.appendChild(input);
            tr.appendChild(th);
        });
        thead.appendChild(tr);
    }
    // Filas
    tbody.innerHTML = '';
    let filteredRows = data.rows.filter(row => row._visible !== false);
    filteredRows.forEach(row => {
        let tr = document.createElement('tr');
        data.headers.forEach((h, i) => {
            let td = document.createElement('td');
            td.textContent = row[i] ?? '';
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
    // Totales
    tfoot.innerHTML = '';
    if (data.totals.length) {
        let tr = document.createElement('tr');
        tr.className = 'totals-row';
        data.headers.forEach((h, i) => {
            let td = document.createElement('td');
            if (data.totals.includes(h)) {
                let sum = filteredRows.reduce((acc, row) => acc + (parseFloat(row[i]) || 0), 0);
                td.textContent = sum.toLocaleString('es-ES', {maximumFractionDigits: 2});
            }
            tr.appendChild(td);
        });
        tfoot.appendChild(tr);
    }
    drawChart();
}

function addRow() {
    let headers = document.getElementById('headers').value.split(',').map(s => s.trim()).filter(Boolean);
    let totals = document.getElementById('totals').value.split(',').map(s => s.trim()).filter(Boolean);
    let row = document.getElementById('row').value.split(',').map(s => s.trim());
    if (!headers.length || !row.length) {
        alert('Debes ingresar encabezados y una fila de datos.');
        return;
    }
    if (!data.headers.length) {
        data.headers = headers;
        data.totals = totals;
    }
    if (headers.join() !== data.headers.join()) {
        alert('Los encabezados deben coincidir con los iniciales.');
        return;
    }
    while (row.length < data.headers.length) row.push('');
    data.rows.push(row);
    data.rows.forEach(r => r._visible = true);
    renderTable();
    document.getElementById('row').value = '';
}

function clearTable() {
    data = { headers: [], totals: [], rows: [] };
    sortCol = null;
    renderTable();
    document.getElementById('headers').value = '';
    document.getElementById('totals').value = '';
    document.getElementById('row').value = '';
    if (window.chartInstance) window.chartInstance.destroy();
}

function sortTable(col) {
    if (sortCol === col) sortAsc = !sortAsc;
    else { sortCol = col; sortAsc = true; }
    data.rows.sort((a, b) => {
        let va = a[col] ?? '', vb = b[col] ?? '';
        let na = parseFloat(va), nb = parseFloat(vb);
        if (!isNaN(na) && !isNaN(nb)) return sortAsc ? na - nb : nb - na;
        return sortAsc ? va.localeCompare(vb) : vb.localeCompare(va);
    });
    renderTable();
}

function filterTable(col, val) {
    val = val.toLowerCase();
    data.rows.forEach(row => {
        row._visible = !val || (row[col] ?? '').toString().toLowerCase().includes(val);
    });
    renderTable();
}

function downloadJSON() {
    let json = JSON.stringify({
        headers: data.headers,
        totals: data.totals,
        rows: data.rows.map(r => r.slice(0, data.headers.length))
    }, null, 2);
    let blob = new Blob([json], {type: "application/json"});
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = "tabla.json";
    a.click();
    URL.revokeObjectURL(url);
}

// Gráficos
function drawChart() {
    if (!data.headers.length || !data.rows.length) return;
    let chartType = document.getElementById('chartType').value;
    let ctx = document.getElementById('chart').getContext('2d');
    if (window.chartInstance) window.chartInstance.destroy();
    // Por defecto, usa la primera columna como etiquetas y la(s) siguiente(s) como valores
    let labels = data.rows.filter(r => r._visible !== false).map(r => r[0]);
    let datasets = [];
    for (let i = 1; i < data.headers.length; i++) {
        // Solo numéricos
        let values = data.rows.filter(r => r._visible !== false).map(r => parseFloat(r[i]) || 0);
        if (values.some(v => v !== 0)) {
            datasets.push({
                label: data.headers[i],
                data: values,
                backgroundColor: chartType === 'pie' || chartType === 'doughnut'
                    ? values.map((_, idx) => `hsl(${(idx*50)%360},70%,60%)`)
                    : `rgba(25, 118, 210, 0.7)`,
                borderColor: '#1976d2',
                borderWidth: 2
            });
        }
    }
    if (!datasets.length) return;
    window.chartInstance = new Chart(ctx, {
        type: chartType,
        data: { labels, datasets },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' },
                title: { display: false }
            }
        }
    });
}
</script>
</body>
</html>