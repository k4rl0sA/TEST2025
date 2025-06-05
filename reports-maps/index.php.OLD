<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reportes || SIGINF</title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js"></script>
<script src="../libs/js/x.js"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
var mod='rptMap';
var ruta_app='lib.php';


function actualizar(){
	graficar();
}
google.charts.load("current", {"packages":["map"],"mapsApiKey": "AIzaSyAFb4phXctlfTOMwkvAWoCyWtykG_Cawoo"});
google.charts.setOnLoadCallback(graficar);

function graficar() {
    try {
        var tit = document.getElementById('indicador-indicador').options[document.getElementById('indicador-indicador').selectedIndex].text;
        const tb = document.getElementById('indicador-indicador').value;
        //const tv = document.getElementById('indi').value;
        const th = 900;
        const tg = 'BAR';

        let dato=myAjax(tb);
        let data = google.visualization.arrayToDataTable(dato);

        if (data.getNumberOfRows() === 0) {
          document.getElementById('chart_div').innerHTML = '<div class="maps-nodata">No hay datos disponibles relacionados para mostrar en el mapa.</div>';
          return;
        }

        var map = new google.visualization.Map(document.getElementById('chart_div'));
        map.draw(data, {
          showTooltip: true,
          showInfoWindow: true,
          icons: {
            blue: {normal:   'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',selected: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'},
            green: {normal:   'https://maps.google.com/mapfiles/ms/icons/green-dot.png',selected: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png'},
            yellow: {normal:   'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png',selected: 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png'},
            red: {normal:   'https://maps.google.com/mapfiles/ms/icons/red-dot.png',selected: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'},
            ltblue: {normal:   'https://maps.google.com/mapfiles/ms/icons/ltblue-dot.png',selected: 'https://maps.google.com/mapfiles/ms/icons/ltblue-dot.png'},
            purple: {normal:   'https://maps.google.com/mapfiles/ms/icons/purple-dot.png',selected: 'https://maps.google.com/mapfiles/ms/icons/purple-dot.png'},
            pink: {normal:   'https://maps.google.com/mapfiles/ms/icons/purple-dot.png',selected: 'https://maps.google.com/mapfiles/ms/icons/purple-dot.png'}
          }
        });
      }catch (error) {
        console.error("Error:", error);
    }
}

function myAjax(a){
	if (loader !== undefined) loader.style.display = 'block';
		if (window.XMLHttpRequest)
			xmlhttp = new XMLHttpRequest();
		else
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			xmlhttp.onreadystatechange = function () {
			if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)){
				data =xmlhttp.responseText;
				if (loader != undefined) loader.style.display = 'none';
					console.log(data)
			}}
			xmlhttp.open("POST",'lib.php',false);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send('a=opc&tb=3&'+ form_input('fapp'));
			return JSON.parse(data);
}


</script>
</head>
<body>
<?php

require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='rptMap';
$hoy = date("Y-m-d");
$ayer = date("Y-m-d",strtotime($hoy."- 2 days")); 
$reportes=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=229 and estado='A' order by 1",'');
$sizes=opc_sql("select valor,descripcion from catadeta where idcatalogo=228 and estado='A' order by 1",'');
$localidades=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=2 and estado='A' order by 1",'');
$territorios=opc_sql("SELECT idcatadeta,descripcion FROM `catadeta` WHERE idcatalogo=283 and estado='A' and valor=(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') ORDER BY CAST(idcatadeta AS UNSIGNED)",'');
$estados=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=44 and estado='A' order by 1",'');





?>


<form method='post' id='fapp'>
<div class="col-2 menu-filtro" id='<?php echo $mod; ?>-fil'>
	

<div class="campo"><div>Reporte</div>
	<select class="captura" id="indicador-indicador" name="indicador-indicador" onChange="graficar();">'.<?php echo $reportes; ?></select>
</div>

<div class="campo"><div>Localidad</div>
	<select class="captura" id="floc" name="floc" onChange="graficar();selectDepend('floc','fter','lib.php');">'.<?php echo $localidades; ?></select>
</div>

<div class="campo"><div>Territorio</div>
	<select class="captura" id="fter" name="fter" onChange="graficar();">'.<?php echo $territorios; ?></select>
</div>

<div class="campo"><div>Estado</div>
	<select class="captura" id="fest" name="fest" onChange="graficar();">'.<?php echo $estados; ?></select>
</div>

	
</div>
<div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo'> REPORTE PREDIOS MAPA POR ESTADOS
		<nav class='menu left'>
    <li class='icono actualizar'    title='Actualizar'      Onclick="graficar();">
    <li class='icono filtros'    title='Filtros'      Onclick="showFil(mod);">
    </nav>
		<nav class='menu right' >
			<li class='icono ayuda'      title='Necesitas Ayuda'            Onclick=" window.open('https://drive.google.com/drive/folders/1JGd31V_12mh8-l2HkXKcKVlfhxYEkXpA', '_blank');"></li>
            <li class='icono cancelar'      title='Salir'            Onclick="location.href='../main/'"></li>
        </nav>               
      </div>
  </form>
		<span class='mensaje' id='<?php echo $mod; ?>-msj' ></span>
     <div class='contenido' id='chart_div' ></div>
	 <div class='contenido' id='cmprstss' ></div>
</div>			
		
<div class='load' id='loader' z-index='0' ></div>

<div class="overlay" id="overlay" onClick="closeModal();">
	<div class="popup" id="popup" z-index="0" onClick="closeModal();">
		<div class="btn-close-popup" id="closePopup" onClick="closeModal();">&times;</div>
		<h3><div class='image' id='<?php echo$mod; ?>-image'></div></h3>
		<h4><div class='message' id='<?php echo$mod; ?>-modal'></div></h4>
	</div>
</div>
</body>
