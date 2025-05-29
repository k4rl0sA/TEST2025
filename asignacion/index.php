<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>	
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reasignar || SIGINF</title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='asigpred';	
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
	resetFrm();
}   

function resetFrm() {
	document.getElementById('fapp').reset();
}

function enabFielSele(a, b, c, d) {
	for (i = 0; i < c.length; i++) {
    	var ele = document.getElementById(c[i]);
    	enaFie(ele, !d.includes(a.value) || !b);
  	}
}

</script>
</head>
<body Onload="actualizar();">
<?php
require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='asigpred';
$perfi=datos_mysql("SELECT perfil as perfil FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}'");
$perfil = (!$perfi['responseResult']) ? '' : $perfi['responseResult'][0]['perfil'] ;
$import = ($perfil == 'GEO'||$perfil =='ADM'||$perfil =='SUPHOG') ? '<div class="campo"><div>Cargar Datos Geográficos</div></div><input class="button filtro" type="file" id="inputFile1" accept=".csv" name="inputFile1" style="width: 350px;"><br><button class="button campo" title="Cargar Archivo" id="btnLoad" type="button">IMPORTAR</button></div></div>':'';
?>
<form method='post' id='fapp'>

<div class="col-2 menu-filtro" id='<?php echo$mod; ?>-fil'>
	<?php echo $import; ?>
	</div>

<div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' >DERIVAR O REASIGNAR
		<nav class='menu left' >
			<li class='icono actualizar'    title='Actualizar'      Onclick="actualizar();">
			<li class='icono filtros'    title='Filtros'      Onclick="showFil(mod);">
			<li class='icono crear' title='Crear' onclick="mostrar(mod,'pro');"></li>
			<li class='icono lupa' title='Consultar Predio' Onclick="mostrar('predios','pro',event,'','../consultar/consulpred.php',7);">
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
</div>			
		
<div class='load' id='loader' z-index='0' ></div>
</form>
<script>
	let btnEnviar = document.querySelector("#btnEnviar"),btnLoad = document.querySelector("#btnLoad"),inputFile = document.querySelector("#inputFile");
	btnLoad.addEventListener("click", () => {
	uploadCsv(8, "geo_asig", inputFile1, "../libs/php/impgeo.php", mod);
  });
</script>	
<div class="overlay" id="overlay" onClick="closeModal();">
	<div class="popup" id="popup" z-index="0" onClick="closeModal();">
		<div class="btn-close-popup" id="closePopup" onClick="closeModal();">&times;</div>
		<h3><div class='image' id='<?php echo$mod; ?>-image'></div></h3>
		<h4><div class='message' id='<?php echo$mod; ?>-modal'></div></h4>
	</div>			
</div>
</body>