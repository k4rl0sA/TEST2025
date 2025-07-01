<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ruteo || SIGINF</title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js"></script>
<script src="../libs/js/x.js?v=5.0"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='rute';
var ruta_app='lib.php';
function csv(b){
		var myWindow = window.open("../../libs/gestion.php?a=exportar&b="+b,"Descargar archivo");
}

document.onkeyup=function(ev) {
	ev=ev||window.event;
	if (ev.ctrlKey && ev.keyCode==46) ev.target.value='';
	if (ev.ctrlKey && ev.keyCode==45) ev.target.value=ev.target.placeholder;
};


function actualizar(){
	act_lista(mod);
}

function getData(a, ev,i,blo) {
	if (ev.type == 'click') {
		var c = document.getElementById(a+'-pro-con');
		var cmp=c.querySelectorAll('.captura,.bloqueo')
		if (loader != undefined) loader.style.display = 'block';
			if (window.XMLHttpRequest)
				xmlhttp = new XMLHttpRequest();
			else
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				xmlhttp.onreadystatechange = function () {
				if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)){
					data =JSON.parse(xmlhttp.responseText);
					if (loader != undefined) loader.style.display = 'none';
						console.log(data)
					}
				}
				xmlhttp.open("POST", ruta_app,false);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send('a=get&tb='+a+'&id=' + i.id);
				var rta =data;
				var data=Object.values(rta);
				for (i=0;i<cmp.length;i++) {
					//~ if cmp[i]==27{
						cmp[i].value=data[i];
						if(cmp[i].type==='checkbox')cmp[i].checked=false;
							if (cmp[i].value=='SI' && cmp[i].type==='checkbox'){
								cmp[i].checked=true;
							}else if(cmp[i].value!='SI' && cmp[i].type==='checkbox'){
								cmp[i].value='NO';
							}
							for (x=0;x<blo.length;x++) {
								if(cmp[i].name==blo[x]) cmp[i].disabled = true;
							}
				} 
	}
}

function grabar(tb='',ev){
  if (tb=='' && ev.target.classList.contains(proc)) tb=proc;
  var f=document.getElementsByClassName('valido '+tb);
   for (i=0;i<f.length;i++) {
     if (!valido(f[i])) {f[i].focus(); return};
  }
  var rutaMap = {
			'ruteresol':'ruteoresolut.php'
		};
		var ruta_app = rutaMap[tb] || 'lib.php';
	myFetch(ruta_app,"a=gra&tb="+tb,mod);
	if (document.getElementById(mod+'-modal').innerHTML.includes('Correctamente')){
		document.getElementById(mod+'-image').innerHTML='<svg class="icon-popup" ><use xlink:href="#ok"/></svg>';
	}else{
		document.getElementById(mod+'-image').innerHTML='<svg class="icon-popup" ><use xlink:href="#bad"/></svg>';
	}
	setTimeout(actualizar, 1000);
	openModal();
}   


function enabFielSele(a, b) {
	var ele = document.getElementById(b);
	if(a.value==3 || a.value==6){
		enaFie(ele,false);
	}else{
		enaFie(ele,true);
	}
	if(a.value==6){
		const c = document.querySelectorAll('.dir input');
		for (i = 0; i < c.length; i++) {
			var el = document.getElementById(c[i].id);
			enaFie(el, false);		
		}
	}else{
		const d = document.querySelectorAll('.dir input');
		for (i = 0; i < d.length; i++) {
			var e = document.getElementById(d[i].id);
			enaFie(e, true);		
		}
	}
}

</script>
</head>
<body Onload="actualizar();">
<?php
	require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='rute';
$ya = new DateTime();
$grupos=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=191 and estado='A' order by 1",'');
// $fuentes=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=33 and estado='A' order by 1",'');
$localidad=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=2 and estado='A' and valor in(select subred from usuarios where id_usuario = '{$_SESSION['us_sds']}') order by 1",'');
// $digitadores=opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` WHERE`perfil`='AUX' ORDER BY 1",$_SESSION['us_sds']);
?>
<form method='post' id='fapp' >
<div class="col-2 menu-filtro" id='<?php echo$mod; ?>-fil'>
<div class="campo"><div>Codigo Registro</div><input class="captura" size=6 id="fcod" name="fcod" OnChange="actualizar();"></div>	
<!-- <div class="campo"><div>Fuentes</div>
		<select class="captura" id="ffuente" name="ffuente" OnChange="actualizar();">
			<?php /* echo $fuentes; */ ?>
		</select>
	</div> -->
	<!--<div class="campo"><div>Grupo Priorizado</div>
		<select class="captura" id="fcod" name="fcod" OnChange="actualizar();">
			<?php /*echo $grupos; */?>
		</select>-->
	</div>
	<!--	<div class="campo"><div>Localidad</div>
		<select class="captura" id="flocalidad" name="flocalidad" OnChange="actualizar();">
			<?php /*echo $localidad; */?>
		</select>
	</div>
	<div class="campo"><div>Sector Catastral</div><input class="captura" size=6 id="fseca" name="fseca" OnChange="actualizar();"></div>
	<div class="campo"><div>Manzana</div><input class="captura" size=3 id="fmanz" name="fmanz" OnChange="actualizar();"></div>
	<div class="campo"><div>Predio</div><input class="captura" size=3 id="fpred" name="fpred" OnChange="actualizar();"></div>
</div>-->
<div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' > RUTEO
		<nav class='menu left' >
			<li class='icono listado' title='Ver Listado' onclick="desplegar(mod+'-lis');" ></li>
			<li class='icono exportar'      title='Exportar Información General'    Onclick="csv(mod);"></li>
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


	
