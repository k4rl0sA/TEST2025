<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SOPORTE || <?php echo $APP; ?></title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<!-- <link href="../libs/css/s.css" rel="stylesheet"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js?v=1"></script>
<script src="../libs/js/x.js"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='soporte';	
var ruta_app='lib.php';
function csv(b){
		var myWindow = window.open("../libs/gestion.php?a=exportar&b="+b,"Descargar archivo");
}

document.onkeyup=function(ev) {
	ev=ev||window.event;
	if (ev.ctrlKey && ev.keyCode==46) ev.target.value='';
	if (ev.ctrlKey && ev.keyCode==45) ev.target.value=ev.target.placeholder;
};


function actualizar(){
	act_lista(mod);
}

function grabar(tb='',ev){
        if (tb=='' && ev.target.classList.contains(proc)) tb=proc;
        var f=document.getElementsByClassName('valido '+tb);
        for (i=0;i<f.length;i++) {
            if (!valido(f[i])) {f[i].focus(); return};
        }
        var res = confirm("¿Desea guardar la información? Recuerda que no se podrá editar posteriormente.");
        if(res==true){
            myFetch(ruta_app,"a=gra&tb="+tb,mod);
            setTimeout(actualizar, 1000);
        }
}



/* document.addEventListener('DOMContentLoaded', function() {
    const soporteLis = document.getElementById('soporte-lis');
    if (soporteLis) {
        soporteLis.addEventListener('click', function(event) {
            const icon = event.target.closest('i.fa-thumbs-up.ico');
            if (icon) {
                const id = icon.id;
                if (!id) return;
                if (confirm("¿Desea aprobar la interlocal del ticket : " + id + " ?")) {
                    if (typeof loader !== "undefined") loader.style.display = 'block';
                    fetch(ruta_app, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: 'a=approve_interl&tb=soporte&id=' + encodeURIComponent(id)
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (typeof loader !== "undefined") loader.style.display = 'none';
                        if (data.includes('Se ha') || data.includes('Correctamente')) {
                            inform('Se ha aprobado la interlocal con ticket ' + id);
                            actualizar();
                        } else {
                            warnin('No se pudo aprobar la ficha. ' + data);
                        }
                    })
                    .catch(error => {
                        if (typeof loader !== "undefined") loader.style.display = 'none';
                        errors('Error: ' + error);
                    });
                }
            }
        });
    }
}); */

function addDynamicListAction(options) {
    document.addEventListener('DOMContentLoaded', function() {
        const contenedor = document.getElementById(options.containerId);
        if (!contenedor) return;
        contenedor.addEventListener('click', async function(event) {
            const icon = event.target.closest(options.selector);
            if (!icon) return;
            const id = icon.id;
            const accion = icon.dataset.acc || '';
            if (!id) return;
            let preData = {};
            // Si se define una función preFetch, ejecútala y espera el resultado
            if (typeof options.preFetch === 'function') {
                preData = await options.preFetch(id, accion) || {};
            }
            // Reemplaza {id}, {doc}, {otro} en los mensajes
            let confirmMsg = options.confirmMsg || '';
            Object.keys(preData).forEach(k => {
                confirmMsg = confirmMsg.replace(`{${k}}`, preData[k]);
            });
            confirmMsg = confirmMsg.replace('{id}', id);
            if (options.confirmMsg && !confirm(confirmMsg)) return;
            if (typeof loader !== "undefined") loader.style.display = 'block';
            fetch(ruta_app, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: 'a=' + options.action + '&tb=' + options.tb + '&id=' + encodeURIComponent(id) + '&accion=' + encodeURIComponent(accion)
            })
            .then(response => response.text())
            .then(data => {
                if (typeof loader !== "undefined") loader.style.display = 'none';
                let msg = options.successMsg || '';
                Object.keys(preData).forEach(k => {
                    msg = msg.replace(`{${k}}`, preData[k]);
                });
                msg = msg.replace('{id}', id);
                if (data.includes('Se ha') || data.includes('Correctamente')) {
                    if (options.successMsg) inform(msg);
                    if (typeof options.onSuccess === 'function') options.onSuccess(id, data, preData);
                    if (typeof actualizar === 'function') actualizar();
                } else {
                    let errMsg = options.errorMsg || '';
                    Object.keys(preData).forEach(k => {
                        errMsg = errMsg.replace(`{${k}}`, preData[k]);
                    });
                    errMsg = errMsg.replace('{id}', id);
                    if (options.errorMsg) warnin(errMsg + ' ' + data);
                }
            })
            .catch(error => {
                if (typeof loader !== "undefined") loader.style.display = 'none';
                errors('Error: ' + error);
            });
        });
    });
}

addDynamicListAction({
    containerId: 'soporte-lis',
    selector: 'i.fa-thumbs-up.ico',
    action: 'approve_interl',
    tb: 'soporte',
    confirmMsg: "¿Desea aprobar la interlocal para el usuario {doc}?",
    successMsg: "Se ha aprobado la interlocal",
    errorMsg: "No se pudo aprobar la interlocal para el usuario {doc}.",
    preFetch: async function(id) {
        // Puedes consultar cualquier cosa aquí
        const res = await fetch(ruta_app, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: 'a=get_documento&tb=soporte&id=' + encodeURIComponent(id)+ '&accion=' + encodeURIComponent('INTERLOCAL')
        });
        const data = await res.json();
        return { doc: data.doc || '' };
    }
});

addDynamicListAction({
    containerId: 'soporte-lis',
    selector: 'i.fa-trash.ico',
    action: 'eliminar_soporte',
    tb: 'soporte',
    confirmMsg: "¿Desea eliminar el registro {id}?",
    successMsg: "Registro eliminado correctamente.",
    errorMsg: "No se pudo eliminar el registro {id}."
    // No defines preFetch, así que no consulta nada antes
});


</script>
</head>
<body Onload="actualizar();">
<?php
require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='soporte';
$ya = new DateTime();
// $localidades=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=2 and estado='A' order by 1",'');
$estados=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=285 and estado='A' order by 1",'');
$digitadores=opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` ORDER BY 2 ASC",$_SESSION["us_sds"]);
$sql="SELECT  subred FROM usuarios WHERE id_usuario=".$_SESSION['us_sds'];
$info = datos_mysql($sql);
$subredActual=$info['responseResult'][0]['subred'];
$subredes=opc_sql("SELECT numero,CASE numero WHEN 1 THEN 'Norte' WHEN 2 THEN 'Sur' WHEN 3 THEN 'Centro Oriente' WHEN 4 THEN 'Sur Occidente' END AS direccion FROM (VALUES (1), (2), (3), (4)) AS T(numero);",$subredActual);
$acciones=opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=286 AND estado='A' ORDER BY 1",'');
?>
<form method='post' id='fapp' >
<div class="col-2 menu-filtro" id='<?php echo$mod; ?>-fil'>


<div class="campo">
	<div>Cod. Ticket</div>
	<input class="captura" type="number" id="ftic" name="ftic" OnChange="actualizar();">
</div>
<div class="campo">
	<div>Cod. Predio</div>
	<input class="captura" type="number" id="fpredio" name="fpredio" OnChange="actualizar();">
</div>

<div class="campo">
	<div>Cod. Persona</div>
	<input class="captura" type="number" id="fuser" name="fuser" OnChange="actualizar();">
</div>

<div class="campo"><div>Estado</div>
		<select class="captura" id="fest" name="fest" OnChange="actualizar();">
			<?php echo $estados; ?>
		</select>
</div>
	
	<div class="campo"><div>Colaborador</div>
		<select class="captura" id="fdigita" name="fdigita" OnChange="actualizar();">
			<?php echo $digitadores; ?>
		</select>
	</div>

<div class="campo"><div>Acciones</div>
    <select class="captura" id="facc" name="facc" OnChange="actualizar();">
        <?php echo $acciones; ?>
    </select>
</div>

<div class="campo"><div>Subred</div>
    <select class="captura" id="fsubred" name="fsubred" OnChange="actualizar();">
        <?php echo $subredes; ?>
    </select>
</div>
	
</div>
<div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' >SOPORTE
		<nav class='menu left' >
			<li class='icono actualizar'    title='Actualizar'      Onclick="actualizar();">
			<li class='icono filtros'    title='Filtros'      Onclick="showFil(mod);">
			
			<!-- <li class='icono casa'       title='Interlocales'     Onclick="mostrar('trasladint','pro',event,'','interloc.php',7,'Interlocales');"></li> -->
			<li class='icono lupa' title='Consultar Predio' Onclick="mostrar('predios','pro',event,'','../consultar/consulpred.php',7);">
            <li class='icono crear'       title='Crear Solicitud Aplicativo'     Onclick="mostrar('solicitudes','pro',event,'','../soporte/solicitud.php',7);">
		</nav>
		<nav class='menu right' >
			<li class='icono ayuda'      title='Necesitas Ayuda'            Onclick=" window.open('https://sites.google.com/', '_blank');"></li>
            <li class='icono cancelar'      title='Salir'            Onclick="location.href='../main/'"></li>
        </nav>               
      </div>
      <div>
		</div>
		<span class='mensaje' id='<?php echo $mod; ?>-msj' ></span>
     <div class='contenido' id='<?php echo $mod; ?>-lis' ></div>
</div>			
		
<div class='load' id='loader' z-index='0' ></div>
</form>	
<div class="overlay" id="overlay" onClick="closeModal();">
	<div class="popup" id="popup" z-index="0" onClick="closeModal();">
		<div class="btn-close-popup" id="closePopup" onClick="closeModal();">&times;</div>
		<h3><div class='image' id='<?php echo$mod; ?>-image'></div></h3>
		<h4><div class='message' id='<?php echo$mod; ?>-modal'></div></h4>
	</div>			
</div>
</body>