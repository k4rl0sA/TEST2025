<?php
ini_set('display_errors', '1');
$mod = 'descargas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../libs/css/stylePop.css" rel="stylesheet">
    <script src="../libs/js/a.js?v=1.0"></script>
    <script src="../libs/js/popup.js?v=1.0"></script>
    <title>Generar Archivo Consolidado</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 0.5rem;
        }
        input[type="date"],select {
            /* width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 1rem;
            margin-bottom: 1rem;
            box-sizing: border-box;
            text-align: justify;
            font-size: large;
            border-color: blue;
            border: groove; */

            width: 100%;
    padding: 0.5rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    box-sizing: border-box;
    text-align: justify;
    font-size: large;
    border: groove;
    border-color: cornflowerblue;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .progress-container {
            margin-top: 1.5rem;
        }
        .progress-bar {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            height: 10px;
            margin-bottom: 0.5rem;
        }
        .progress-bar-fill {
            height: 100%;
            background-color: #007bff;
            width: 0;
            transition: width 0.3s ease;
        }
        .progress-text {
            font-size: 0.9rem;
            color: #555;
        }
        /* Estilos para el spinner */
    .spinner {
        margin-top: 1rem;
    }
    .spinner-border {
        width: 2rem;
        height: 2rem;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border 0.75s linear infinite;
    }
    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }
    .text-primary {
        color: #007bff;
    }
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }
    </style>
</head>
<body>
<div class="container">
    <h1>Generar Archivo Excel</h1>
    <form id="generarForm">
    <label for="fecha">Seleccione El tipo de archivo a descargar:</label>
        <select id="tipo" name="tipo">
            <option value="1">SIN Validaciones</option>
            <option value="2">CON Validaciones</option>
            <option value="3">Fechas</option>
            <option value="4">Alertas</option>
            <option value="5">Caracteriz_OK</option>
            <option value="6">Signos</option>
            <option value="7">Tamizajes</option>
            <option value="8">Validar Fechas Atenciones Individuales</option>
        </select>

        <label for="fecha_inicio">Fecha de inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" required>

        <label for="fecha_fin">Fecha de fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" required>

        <button type="button" onclick="generarArchivo()">Generar Archivo</button>
    </form>
    <div class="progress-container">
        <div class="progress-bar">
            <div class="progress-bar-fill" id="progressBarFill"></div>
        </div>
        <div class="progress-text" id="progressText">0%</div>
    </div>
    <!-- Spinner de carga -->
    <div class="spinner" id="spinner" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>
</div>
    <script>
        var mod = 'descargas';
    function generarArchivo() {
        const tipo = document.getElementById('tipo').value;
        const fecha_inicio = document.getElementById('fecha_inicio').value;
        const fecha_fin = document.getElementById('fecha_fin').value;
    if (!fecha_inicio || !fecha_fin) {
        inform('Por favor, seleccione ambas fechas.');
        return;
    }
    // Mostrar el spinner
    document.getElementById('spinner').style.display = 'block';
    const xhr = new XMLHttpRequest();
    xhr.open('POST','lib.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
         if (xhr.readyState === 4 && xhr.status === 200) {
        try {
            const response = JSON.parse(xhr.responseText);
            
            if (response.success) {
                document.getElementById('spinner').style.display = 'none';
                // Crear enlace temporal para descarga
                const link = document.createElement('a');
                link.href = response.file;
                link.download = response.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                inform('Archivo descargado con éxito.');
            } else {
                warnin(response.message);
            }
        } catch (e) {
            console.error('Error al procesar la respuesta:', e);
            warnin('Error al procesar la respuesta');
        }
    }
    };
    xhr.send(`tipo=${tipo}&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`);
}
    </script>
    <div class="overlay" id="overlay" onClick="closeModal();">
		<div class="popup" id="popup" z-index="0" onClick="closeModal();">
			<div class="btn-close-popup" id="closePopup" onClick="closeModal();">&times;</div>
			<h3>
				<div class='image' id='<?php echo $mod; ?>-image'></div>
			</h3>
			<h4>
				<div class='message' id='<?php echo $mod; ?>-modal'></div>
			</h4>
		</div>
	</div>
</body>
</html>