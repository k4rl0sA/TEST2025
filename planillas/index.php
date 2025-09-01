<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Planillas || SIGREV</title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js?v=1.5"></script>
<script src="../libs/js/x.js?v=1.5"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='planillas';
var ruta_app='lib.php';

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
	myFetch(ruta_app,"a=gra&tb="+tb,mod);
}

async function cargarResumenFamiliar() {
	const doc= document.getElementById('idpersona').value;
	const tip = document.getElementById('tipo_doc').value;
	const fec = document.getElementById('fecha_formato').value;
	const col = document.getElementById('colaborador').value;
	if (doc != '' || tip != ''|| fec != '' || col != '') {	
  		const data = await getJSON('family',mod,doc+'_'+tip+'_'+fec+'_'+col,'lib.php');
  		if (!data) return;
  		let html = "<table style='width:100%; border-collapse:collapse;'><tr><th>Validación</th><th>Estado</th><th>Fecha</th></tr>";
  		data.forEach(row => {
  		  let icono = row.estado === 'Completado' ? "<span style='color:green;'>✔</span>" : "<span style='color:red;'>✘</span>";
  		  html += `<tr><td>${row.nombre}</td><td>${icono} </td><td>${row.fecha_ultima}</td></tr>`;
  		});
  		html += "</table>";
  		document.getElementById('valida-family').innerHTML = html;
	}
}

async function cargarResumenIndivi() {
    const doc = document.getElementById('idpersona').value;
    const tip = document.getElementById('tipo_doc').value;
    const fec = document.getElementById('fecha_formato').value;
    const col = document.getElementById('colaborador').value;
    if (doc && tip && fec && col) {
        const data = await getJSON('indivi', 'planillas', doc + '_' + tip + '_' + fec + '_' + col, 'lib.php');
        if (!data) return;
        let html = "<table style='width:100%; border-collapse:collapse;'><tr><th>Cod Persona</th><th>Alertas</th><th>Fecha</th><th>Signos</th><th>Fecha</th></tr>";
        data.forEach(row => {
            let icono_alerta = row.estado_alerta === 'Completado' ? "<span style='color:green;'>✔</span>" : "<span style='color:red;'>✘</span>";
            let icono_signos = row.estado_signos === 'Completado' ? "<span style='color:green;'>✔</span>" : "<span style='color:red;'>✘</span>";
            html += `<tr><td>${row.idpeople}</td><td>${icono_alerta}</td><td>${row.fecha_ultima}</td><td>${icono_signos}</td><td>${row.fecha_ultima}</td></tr>`;
        });
        html += "</table>";
        document.getElementById('valida-indivi').innerHTML = html;
    }
}

</script>
</head>
<body Onload="actualizar();">
<?php
require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){
    die("<script>window.top.location.href = '/';</script>");
}
$mod='planillas';
$ya = new DateTime();
$rta=datos_mysql("select FN_USUARIO('".$_SESSION['us_sds']."') as usu;");
$usu=divide($rta["responseResult"][0]['usu']);
?>
<form method='post' id='fapp'>
<div class="col-2 menu-filtro" id='<?php echo $mod; ?>-fil'>
	<div class="campo"><div>ID Persona</div><input class="captura" size=6 id="fidpeople" name="fidpeople" OnChange="actualizar();"></div>
	<div class="campo"><div>Código Familia</div><input class="captura" size=6 id="fcod_fam" name="fcod_fam" OnChange="actualizar();"></div>
    <div class="campo"><div>Tipo Planilla</div>
		<select class="captura" id="ftipo" name="ftipo" OnChange="actualizar();">
			<option value="">Todos</option>
			<option value="1">Abordaje</option>
			<option value="2">Plan de Cuidado Familiar</option>
			<option value="3">Atenciones</option>
		</select>
	</div>
	<div class="campo"><div>Estado Planilla</div>
		<select class="captura" id="festado_planilla" name="festado_planilla" OnChange="actualizar();">
			<option value="">Todos</option>
			<option value="P">Pendiente</option>
			<option value="A">Activo</option>
			<option value="G">Archivado</option>
		</select>
	</div>
</div>
<div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo'> PLANILLAS
		<nav class='menu left' >
			<li class='icono listado' title='Ver Listado' onclick="desplegar(mod+'-lis');" ></li>
			<li class='icono actualizar'    title='Actualizar'      Onclick="actualizar();">
			<li class='icono filtros'    title='Filtros'      Onclick="showFil(mod);">
            <li class='icono crear'       title='Crear Planilla'    Onclick="mostrar(mod,'pro');"></li>
		</nav>
		<nav class='menu right' >
			<li class='icono ayuda'      title='Necesitas Ayuda'            Onclick=" window.open('https://drive.google.com/drive/folders/1JGd31V_12mh8-l2HkXKcKVlfhxYEkXpA', '_blank');"></li>
            <li class='icono cancelar'      title='Salir'            Onclick="location.href='../main/'"></li>

        </nav>               
      </div>
      <div>
		</div>
		<span class='mensaje' id='<?php echo $mod; ?>-msj' ></span>
     <div class='contenido' id='<?php echo $mod; ?>-lis' ></div>
	 <div class='contenido' id='cmprstss' ></div>
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
