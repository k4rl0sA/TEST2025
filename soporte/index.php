<?php
ini_set('display_errors','1');
// include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SOPORTE || <?php echo $APP; ?></title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="../libs/css/app.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<script src="../libs/js/a.js"></script>
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
    if(document.getElementById('id').value=='0'){
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
    }else{
        const message = `La función de Editar no está habilitada en este momento`;
        document.getElementById(mod+'-modal').innerHTML = message;
        document.getElementById(mod+'-image').innerHTML = '<svg class="icon-popup" ><use xlink:href="#bad"/></svg>';
        openModal();
    }
}

</script>
</head>
<body Onload="actualizar();">
<?php
require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='soporte';
$ya = new DateTime();
// $localidades=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=2 and estado='A' order by 1",'');
$estados=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=284 and estado='A' order by 1",'');
$digitadores=opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` ORDER BY 2 ASC",$_SESSION["us_sds"]);
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
	
</div>
<div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' >SOPORTE
		<nav class='menu left' >
			<li class='icono actualizar'    title='Actualizar'      Onclick="actualizar();">
			<li class='icono filtros'    title='Filtros'      Onclick="showFil(mod);">
			<li class='icono crear'       title='Crear'     Onclick="mostrar(mod,'pro');"></li> <!--setTimeout(load,500);-->
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