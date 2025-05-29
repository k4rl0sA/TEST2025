<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relevo || SIGINF</title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='relevo';
var ruta_app='lib.php';
function csv(b){
	var myWindow = window.open("../libs/gestion.php?a=exportar&b="+b,"Descargar archivo");
}

document.onkeyup=function(ev) {
	ev=ev||window.event;
	if (ev.ctrlKey && ev.keyCode==46) ev.target.value='';
	if (ev.ctrlKey && ev.keyCode==45) ev.target.value=ev.target.placeholder;
};

document.onkeyup=function(ev) {
	ev=ev||window.event;
	if (ev.keyCode==124) return;
};


function actualizar(){
	act_lista(mod);
}


function grabar(tb='',ev) {
  if (tb=='' && ev.target.classList.contains(proc)) tb=proc;
  var f=document.getElementsByClassName('valido '+tb);

  for (i=0;i<f.length;i++) {
    if (!valido(f[i])) {f[i].focus(); return};
  }
/*   if(tb=='relevo'){
		// document.getElementById(mod+'-modal').innerHTML=ajax(ruta_app,"a=gra&tb="+tb,false);
		myFetch(ruta_app,"a=gra&tb="+tb,mod);
  }

  if(tb=='sesiones'){ */
	var rutaMap = {
	'sesiones':'sesiones.php',
	'vitals_signs':'sigvital.php'
 };
		var ruta_app = rutaMap[tb] || 'lib.php';
	myFetch(ruta_app,"a=gra&tb="+tb,mod);
//   }
	if (document.getElementById(mod+'-modal').innerHTML.includes('Correctamente')){
		document.getElementById(mod+'-image').innerHTML='<svg class="icon-popup" ><use xlink:href="#ok"/></svg>';
	}else{
		document.getElementById(mod+'-image').innerHTML='<svg class="icon-popup" ><use xlink:href="#bad"/></svg>';
	}
	openModal();
	// setTimeout(act_lista(tb), 1000);
}


function disaFielChec(a, b,c) {
	for (let i = 0; i < c.length; i++) {
		let ele = document.getElementById(c[i]);
		ele.disabled = a.checked ? false : true;
		ele.required = a.checked ? true : false;
		// ele.classList.toggle('valido', false);
		ele.classList.toggle('captura', a.checked ? true : false);
		ele.classList.toggle('bloqueo', a.checked ? false : true);
		a.checked ? ele.removeAttribute('readonly'):ele.setAttribute('readonly', true);
	}
}







function enabMod(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value!='3'){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}


</script>
</head>
<body Onload="actualizar();">
<?php
require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='relevo';
$ya = new DateTime();
/*$grupos=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=11 and estado='A' order by 1",'');*/
$estados=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=44 and estado='A' order by 1",'');
$localidades=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=2 and estado='A' order by 1",'');
/* $digitadores=opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` WHERE`perfil`='MED' ORDER BY 1",'');  */
?>
<form method='post' id='fapp' >
<div class="col-2 menu-filtro" id='<?php echo$mod; ?>-fil'>
	
	<div class="campo"><div>N° Identificación</div><input class="captura" size=10 id="fid" name="fid" OnChange="actualizar();"></div>
	<!--<div class="campo"><div>Localidad</div>
		<select class="captura" id="flocalidad" name="flocalidad" OnChange="actualizar();">
			<?php //echo $localidades; ?>
		</select>
	</div>-->
	
</div>
<div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' > RELEVO 
		<nav class='menu left' id='<?php echo $mod.'-acc'; ?>'>
			<li class='icono actualizar'    title='Actualizar'      Onclick="actualizar();">
			<li class='icono filtros'    title='Filtros'      Onclick="showFil(mod);">
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

<div class="overlay" id="overlay" onClick="closeModal();">
	<div class="popup" id="popup" z-index="0" onClick="closeModal();">
		<div class="btn-close-popup" id="closePopup" onClick="closeModal();">&times;</div>
		<h3><div class='image' id='<?php echo$mod; ?>-image'></div></h3>
		<h4><div class='message' id='<?php echo$mod; ?>-modal'></div></h4>
	</div>			
</div>
</body>