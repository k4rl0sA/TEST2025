// Funciones JavaScript para el módulo TH (Talento Humano)

/**
 * Función personalizada para cargar datos por nombre de campo en lugar de por índice
 * Compatible con la respuesta JSON del backend PHP
 */
function getDataByName(a, ev, i, blo, path = ruta_app) {
    if (ev.type !== 'click') return;
    
    const c = document.getElementById(`${a}-pro-con`);
    const cmp = c.querySelectorAll('.captura, .bloqueo');
    let loader = document.getElementById('loader');
    if (loader) loader.style.display = 'block';
    
    fetch(path, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `a=get&tb=${a}&id=${i.id}`,
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.statusText}`);
        }
        return response.json();
    })
    .then((rta) => {
        if (loader) loader.style.display = 'none';
        
        if (!rta || Object.keys(rta).length === 0) {
            console.warn('No se encontraron datos.');
            return;
        }
        
        if (rta.error) {
            console.error('Error desde el backend:', rta.error);
            return;
        }
        
        // Mapear datos por nombre de campo en lugar de por índice
        cmp.forEach((element) => {
            const fieldName = element.name || element.id;
            
            if (rta[fieldName] !== undefined) {
                if (element.type === 'checkbox') {
                    element.checked = rta[fieldName] === 'SI';
                    element.value = element.checked ? 'SI' : 'NO';
                } else {
                    element.value = rta[fieldName];
                }
                
                // Deshabilitar campos especificados en el arreglo `blo`.
                if (blo.includes(fieldName)) {
                    element.disabled = true;
                }
            }
        });
        
        // Ejecutar calcularTotales si existe para recalcular valores
        if (typeof calcularTotales === 'function') {
            calcularTotales();
        }
    })
    .catch((error) => {
        if (loader) loader.style.display = 'none';
        console.error('Error:', error);
        if (typeof errors === 'function') {
            errors('Error al cargar los datos: ' + error.message);
        }
    });
}

/**
 * Función para calcular totales automáticamente en actividades/adicionales
 */
function calcularTotales() {
    const horaAct = document.getElementById('hora_act');
    const canAct = document.getElementById('can_act');
    const horaTh = document.getElementById('hora_th');
    const totalHoras = document.getElementById('total_horas');
    const totalValor = document.getElementById('total_valor');
    
    if (horaAct && canAct && totalHoras) {
        const horas = parseFloat(horaAct.value) || 0;
        const cantidad = parseFloat(canAct.value) || 0;
        const resultHoras = horas * cantidad;
        totalHoras.value = resultHoras.toFixed(1);
    }
    
    if (totalHoras && horaTh && totalValor) {
        const horas = parseFloat(totalHoras.value) || 0;
        const valorHora = parseInt(horaTh.value) || 0;
        const resultValor = horas * valorHora;
        totalValor.value = Math.round(resultValor);
    }
}