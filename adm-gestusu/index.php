<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Usuarios || <?php echo $APP; ?></title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js?v=1"></script>
<script src="../libs/js/x.js"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='gestuser';	
var ruta_app='lib.php';


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

</script>
</head>
<body Onload="actualizar();">
<?php

require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='gestuser';
$hoy = date("Y-m-d");
$ayer = date("Y-m-d",strtotime($hoy."- 2 days")); 
$estados=array('A'=>'Activo','I'=>'Inactivo');
?>


<form method='post' id='fapp'>
<div class="col-2 menu-filtro" id='<?php echo $mod; ?>-fil'>
	

<div class="campo"><div>Caso</div><input class="captura" type="number" size=20 id="fcaso" name="fcaso" onChange="actualizar();"></div>
<div class="campo"><div>Documento Colaborador</div><input class="captura" type="number" size=10 id="fdoc" name="fdoc" onChange="actualizar();"></div>

<!-- <div class="campo"><div>Estado</div>
	<select class="captura" id="festado" name="festado" onChange="actualizar();">'.<?php /* echo $estados; */?></select>
</div> -->
	

</div>
<div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' > ZONA DE ADMINISTRACIÓN DE USUARIOS
		<nav class='menu left' >
    <li class='icono actualizar'    title='Actualizar'      Onclick="actualizar();">
    <li class='icono crear'      title='Administración de Usuarios' onclick="mostrar('gestuser','pro',event,'','lib.php','7','Creación de Usuarios');"></li>
    </nav>
		<nav class='menu right' >
			<li class='icono ayuda'      title='Necesitas Ayuda'            Onclick=" window.open('https://drive.google.com/drive/folders/1JGd31V_12mh8-l2HkXKcKVlfhxYEkXpA', '_blank');"></li>
            <li class='icono cancelar'      title='Salir'            Onclick="location.href='../main/'"></li>
        </nav>               
      </div>
  </form>
		<span class='mensaje' id='<?php echo $mod; ?>-msj' ></span>
     <div class='contenido' id='<?php echo $mod; ?>-lis' ></div>
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
