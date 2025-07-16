<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validador de caracteres permitidos</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; }
        .container { background: #fff; padding: 24px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 2px 12px #0001;}
        textarea { width: 100%; height: 120px; font-size: 1em; margin-bottom: 12px; }
        .result { margin-top: 18px; }
        .bad { color: #d32f2f; font-weight: bold; }
        .good { color: #388e3c; font-weight: bold; }
        .label { font-weight: bold; }
        pre { background: #f0f0f0; padding: 8px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Validador de caracteres permitidos</h2>
    <label for="texto">Ingrese texto:</label>
    <textarea id="texto"></textarea>
    <button onclick="validar()">Validar</button>
    <div class="result" id="resultado"></div>
</div>
<script>
function cleanTx(val) {
    val = val.trim();
    const pattern = /[^\w\s.\-@:,()+\u00C0-\u00FF%=]/gu;
    val = val.replace(/\s+/g, ' ');
    val = val.replace(pattern, '');
    val = val.replace(/[\n\r\t]/g, '');
    val = val.toLocaleUpperCase('es-ES');
    return val;
}

function caracteresNoPermitidos(val) {
    // Mismo patrón que en cleanTx
    const pattern = /[^\w\s.\-@:,()+\u00C0-\u00FF%=]/gu;
    let encontrados = val.match(pattern);
    // También saltos de línea y tabulaciones
    let especiales = val.match(/[\n\r\t]/g);
    if (especiales) {
        if (!encontrados) encontrados = [];
        encontrados = encontrados.concat(especiales);
    }
    // Quitar duplicados
    return encontrados ? [...new Set(encontrados)] : [];
}

function validar() {
    const texto = document.getElementById('texto').value;
    const noPermitidos = caracteresNoPermitidos(texto);
    const limpio = cleanTx(texto);
    let html = '';
    if (noPermitidos.length > 0) {
        html += `<div class="bad">Caracteres no permitidos encontrados:</div>`;
        html += `<pre>${noPermitidos.join(' ')}</pre>`;
    } else {
        html += `<div class="good">No se encontraron caracteres no permitidos.</div>`;
    }
    html += `<div class="label">Texto limpio:</div><pre>${limpio}</pre>`;
    document.getElementById('resultado').innerHTML = html;
}
</script>
</body>
</html>