<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Talento Humano || <?php echo $APP; ?></title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<style>
.search-wrapper {
    position: relative;
    width: 300px;
}

.number-input-svg {
    width: 100%;
    padding: 12px 50px 12px 16px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    transition: border-color 0.3s;
}

.number-input-svg:focus {
    border-color: #007bff;
}

.search-btn-svg {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    border-radius: 4px;
    transition: background 0.3s;
}

.search-btn-svg:hover {
    background: #f8f9fa;
}

.search-icon-svg {
    width: 20px;
    height: 20px;
    fill: #666;
}
</style>
<script src="../libs/js/a.js?v=10"></script>
<script src="../libs/js/x.js?v=31.0"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='th';
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
  const rutaMap = {
  'contratos':'contratos.php'
 	};
   let ruta_app = rutaMap[tb] || 'lib.php';
	myFetch(ruta_app,"a=gra&tb="+tb,mod);
	/* if (document.getElementById(mod+'-modal').innerHTML.includes('Correctamente')){
		document.getElementById(mod+'-image').innerHTML='<svg class="icon-popup" ><use xlink:href="#ok"/></svg>';
	}else{
		document.getElementById(mod+'-image').innerHTML='<svg class="icon-popup" ><use xlink:href="#bad"/></svg>';
	} */
	setTimeout(actualizar, 1000);
	setTimeout(act_lista,1000,'gestion');
	openModal();
}   

</script>
</head>
<body Onload="actualizar();">
<?php
	require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='th';
$ya = new DateTime();
$grupos=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=191 and estado='A' order by 1",'');
// $fuentes=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=33 and estado='A' order by 1",'');
$localidad=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=2 and estado='A' and valor in(select subred from usuarios where id_usuario = '{$_SESSION['us_sds']}') order by 1",'');
$prioridad=opc_sql("select idcatadeta,descripcion from catadeta where idcatalogo=2 and estado='A' and valor in(select subred from usuarios where id_usuario = '{$_SESSION['us_sds']}') order by 1",'');
// $digitadores=opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` WHERE`perfil`='AUX' ORDER BY 1",$_SESSION['us_sds']);
?>
<form method='post' id='fapp' >
<div class="col-2 menu-filtro" id='<?php echo$mod; ?>-fil'>
	<!-- <div class="campo"><div>Fuentes</div>
		<select class="captura" id="ffuente" name="ffuente" OnChange="actualizar();">
			<?php /* echo $fuentes; */ ?>
		</select>
	</div> -->
	
	<div class="campo"><div>Documento Usuario</div><input class="captura" type="number" size=20 id="fusu" name="fusu" OnChange="actualizar();"></div>
</div>
<div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' > GESTION DEL TH MAS BIENESTAR EN TU HOGAR
		<nav class='menu left' >
			<!-- <li class='icono listado' title='Ver Listado' onclick="desplegar(mod+'-lis');" ></li> -->
			<li class='icono actualizar'    title='Actualizar'      Onclick="actualizar();">
			<li class='icono filtros'    title='Filtros'      Onclick="showFil(mod);">
			<li class='icono crear'       title='Crear Colaborador'     Onclick="mostrar('th','pro',event,'','lib.php',7,'th','0');">
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