<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Información Geografica || SIGINF</title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js"></script>
<script src="../libs/js/x.js"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='geoloc';	
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
	if (tb == 'geoloc') {
		var ndir = document.getElementById('direccion_nueva'),
			sec = document.getElementById('sector_catastral'),
			cox = document.getElementById('cordx'),
			ver = document.getElementById('vereda'),
			coy = document.getElementById('cordy');
		if (sec.value == 2  && ndir.value!=='') {
			var err='No se puede ingresar una nueva dirección ya que esta no aplica para el sector Catastral,por favor valide e intente nuevamente';		
			showErr(err,tb);
			return
		}else if(sec.value == 2  && (cox.value=='' || coy.value==''|| ver.value=='' )){
			var err='las Coordenadas ó la Vereda no pueden estar vacias, para el sector Catastral,por favor valide e intente nuevamente';
			showErr(err,tb);
			return
		}
	}
	myFetch(ruta_app,"a=gra&tb="+tb,mod);
	resetFrm();
	act_lista('predios');
}   

function resetFrm() {
	document.getElementById('fapp').reset();
}

function enableAddr(a,b,c,d){
	const eru= document.querySelectorAll('input.'+b);
	const eur= document.querySelectorAll('input.'+c);
	const zon=document.getElementById(d).value;
	if(zon==='1'){
		for (i=0; i<eru.length;i++) {
		if(a.value=='SI'){
			enaFie(eru[i],false);
  		}else{
			enaFie(eru[i],true);
		}
		}	
	}else{
		for (i=0; i<eur.length;i++) {
		if(a.value=='SI'){
			enaFie(eur[i],false);
  		}else{
			enaFie(eur[i],true);
		}
	}
	}
	
}

function enaFie(ele, flag) {
	if(ele.type==='checkbox' && ele.checked==true){
		ele.checked=false;
	}else{
		ele.value = '';
	}
    ele.disabled = flag;
    ele.required = !flag;
    ele.classList.toggle('valido', !flag);
    ele.classList.toggle('captura', !flag);
    ele.classList.toggle('bloqueo', flag);
    flag ? ele.setAttribute('readonly', true) : ele.removeAttribute('readonly');
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

$mod='geoloc';
?>
<form method='post' id='fapp' >
<!-- <div class="col-2 menu-filtro" id='<?php echo$mod; ?>-fil'>

	</div> -->
	
<div class='col-0 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' >ASIGNACIÓN GEOGRAFICA
		<nav class='menu left' >
			<li class='icono crear' title='Asignar Predio' onclick="mostrar('geoloc','pro',event,'','lib.php',7,'Asignar Predio');"></li>
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
