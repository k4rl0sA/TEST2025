<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>gestión de Usuarios || SIGINF</title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js"></script>
<script src="../libs/js/x.js"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='adm_usuarios';	
var ruta_app='lib.php';

function csv(b){
  // const b=document.getElementById(b).value;
   var myWindow = window.open("../libs/gestion.php?a=exportar&b="+b,"Descargar archivo");
}

function actualizar(){
	act_lista(mod);
}

/* function actualizar(){
	mostrar(mod);
} */

function grabar(tb='',ev){
  if (tb=='' && ev.target.classList.contains(proc)) tb=proc;
  var f=document.getElementsByClassName('valido '+tb);
   for (i=0;i<f.length;i++) {
     if (!valido(f[i])) {f[i].focus(); return};
  }
    myFetch(ruta_app,"a=gra&tb="+tb,mod);  
    // DownloadCsv('lis','planos','fapp');
}   

</script>
<style> 
        

        .container {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            width: 400px;
        }

        .input-container {
            margin-bottom: 20px;
        }

        .input-label {
            font-weight: bold;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .submit-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body Onload="actualizar();">
<?php

require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='adm_usuarios';
$hoy = date("Y-m-d");
$ayer = date("Y-m-d",strtotime($hoy."- 2 days")); 
?>


<form method='post' id='fapp'>
<!-- <div class="col-2 menu-filtro" id='<?php echo $mod; ?>-fil'>
	

<div class="campo"><div>Caso</div><input class="captura" type="number" size=20 id="fcaso" name="fcaso" ></div>

	
	<div class="campo">
		<div>Fecha Asignado Desde</div>
		<input type="date" class="captura" size=10 id="fdes" name="fdes" value='<?php echo $ayer; ?>' OnChange="actualizar();" disabled="true">
		
	</div>
	<div class="campo">
		<div>Fecha Asignado Hasta</div>
		<input type="date" class="captura" size=10 id="fhas" name="fhas" value='<?php echo $hoy; ?>' OnChange="actualizar();" disabled="true">
	</div>
</div> -->
<div class='col-0 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' > ZONA DE GESTIÓN DE USUARIOS
		<nav class='menu left' >
    <li class='icono actualizar'    title='Actualizar'      Onclick="actualizar();">
    <!-- <li class='icono gestusu'      title='Gestión de Usuarios' onclick="mostrar('gestionusu','pro',event,'','lib.php','7','Gestión de Usuarios');"></li> -->
    <li class='icono exportar'      title='Exportar Información'    Onclick="mostrar('planos','pro');"></li>
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
