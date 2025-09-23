<?php
ini_set('display_errors','1');
require_once "../libs/gestion.php";
if ($_POST['a']!='opc') $perf=perfil($_POST['tb']);
if (!isset($_SESSION['us_sds'])) die("<script>window.top.location.href='/';</script>");
else {
  $rta="";
  switch ($_POST['a']){
  case 'csv': 
    header_csv ($_REQUEST['tb'].'.csv');
    $rs=array('','');    
    echo csv($rs,'');
    die;
    break;
  default:
    eval('$rta='.$_POST['a'].'_'.$_POST['tb'].'();');
    if (is_array($rta)) echo json_encode($rta);
	else echo $rta;
  }
}

function opc_usuario(){
	$id=$_REQUEST['id'];
	$sql="SELECT hg.idgeo,FN_CATALOGODESC(72,hg.subred) AS subred,
	FN_CATALOGODESC(42,hg.estrategia) AS estrategia,
	IFNULL(u.nombre,u1.nombre) asignado,
	IFNULL(u.perfil,u1.perfil) perfil,
	hg.territorio 
	FROM hog_carac hv 
	LEFT JOIN hog_geo hg ON hv.idpre=hg.idgeo
	LEFT JOIN person p ON hv.idviv=p.vivipersona
	LEFT JOIN usuarios u ON hg.asignado=u.id_usuario
	LEFT JOIN usuarios u1 ON hg.usu_creo=u1.id_usuario
	WHERE p.idpersona='".$id."' and hg.estado_v='7'";
 //echo $sql;
	$info=datos_mysql($sql);
	if(isset($info['responseResult'][0])){ 
		return json_encode($info['responseResult'][0]);
	}else{
		return "[]";
	}
}

function opc_menu(){
	/* $buttons = [
		["iconClass" => "fas fa-cog", "title" => "Plan de Cuidado", "shortcut" => "Ctrl+S"],
		["iconClass" => "fas fa-user", "title" => "Compromisos", "shortcut" => "Ctrl+P"],
		["iconClass" => "fas fa-search", "title" => "Riesgo Ambiental", "shortcut" => "Ctrl+F"],
		["iconClass" => "fas fa-home", "title" => "Apgar", "shortcut" => "Ctrl+H"],
		["iconClass" => "fas fa-bell", "title" => "Notificaciones", "shortcut" => "Ctrl+N"],
		["iconClass" => "fas fa-sign-out-alt", "title" => "Salir", "shortcut" => "Ctrl+Q"]
	]; */
	$buttons = [
		["iconClass" => "fas fa-house-user", "title" => "Caracterización del Hogar", "shortcut" => "Ctrl+S"],
		["iconClass" => "fas fa-user", "title" => "Alertas", "shortcut" => "Ctrl+P"],
		["iconClass" => "fas fa-search", "title" => "Solicitar Admisión", "shortcut" => "Ctrl+F"],
		["iconClass" => "fas fa-home", "title" => "Atención", "shortcut" => "Ctrl+H"],
		["iconClass" => "fas fa-bell", "title" => "Evento VSP", "shortcut" => "Ctrl+N"],
		["iconClass" => "fas fa-sign-out-alt", "title" => "Salir", "shortcut" => "Ctrl+Q"]
	];
	
	return json_encode($buttons);
}

function lis_homes(){
	$total="SELECT COUNT(*) AS total FROM (
		SELECT G.idgeo AS ACCIONES,G.idgeo AS Cod_Predio,H.direccion,H.sector_catastral Sector,H.nummanzana AS Manzana,H.predio_num AS predio,H.unidad_habit AS 'Unidad',FN_CATALOGODESC(2,H.localidad) AS 'Localidad',U1.nombre,G.fecha_create,FN_CATALOGODESC(44,G.estado_v) AS estado 
		FROM geo_gest G	LEFT JOIN hog_geo H ON G.idgeo = H.idgeo LEFT JOIN usuarios U ON H.subred = U.subred	LEFT JOIN usuarios U1 ON H.usu_creo = U1.id_usuario
			WHERE G.estado_v IN ('7') ".whe_homes()." AND U.id_usuario = '{$_SESSION['us_sds']}') AS Subquery";
	$info=datos_mysql($total);
	$total=$info['responseResult'][0]['total']; 
	$regxPag=5;
	$pag=(isset($_POST['pag-homes']))? ($_POST['pag-homes']-1)* $regxPag:0;

	
$sql="SELECT G.idgeo AS ACCIONES,
	G.idgeo AS Cod_Predio,
	H.direccion,
	H.sector_catastral Sector,
	H.nummanzana AS Manzana,
	H.predio_num AS predio,
	H.unidad_habit AS 'Unidad',
	FN_CATALOGODESC(2,H.localidad) AS 'Localidad',
	U1.nombre,
	G.fecha_create,
	FN_CATALOGODESC(44,G.estado_v) AS estado
	FROM geo_gest G
	LEFT JOIN hog_geo H ON G.idgeo = H.idgeo
	LEFT JOIN usuarios U ON H.subred = U.subred
	LEFT JOIN usuarios U1 ON H.usu_creo = U1.id_usuario 
WHERE G.estado_v in('7') ".whe_homes()." 
	AND U.id_usuario = '{$_SESSION['us_sds']}'
	ORDER BY nummanzana, predio_num
	LIMIT $pag, $regxPag";
// echo $sql;
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"homes",$regxPag);
}


function whe_homes() {
	$fefin=date('Y-m-d');
	$feini = date("Y-m-d",strtotime($fefin."- 2 days"));
	$sql = "";
	if (!empty($_POST['fpred']) && $_POST['fdigita']) {
		$sql .= " AND G.idgeo = '" . $_POST['fpred'] . "' AND G.usu_creo ='" . $_POST['fdigita'] . "' and G.estado='A'";
	}else{
		$sql .="AND G.idgeo ='0'";
	} 
	return $sql;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  $acc=rol($a);
  if ($a=='homes' && isset($acc['crear']) && $acc['crear']=='SI') {  
  $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
//   $rta .= "<li class='icono $a exportar'       title='Exportar'    Onclick=\"csv('$a');\"></li>"; 
  $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";  
   }
   if ($a=='person' && isset($acc['crear']) && $acc['crear']=='SI') {  

	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	// $rta .= "<li class='icono $a exportar'       title='Exportar'    Onclick=\"csv('$a');\"></li>"; 
	$rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";

	}
	if($a=='atencion' && isset($acc['crear']) && $acc['crear']=='SI'){
		$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
		$rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";

	}
		if($a=='eac_juventud' && isset($acc['crear']) && $acc['crear']=='SI'){
		$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
		$rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";

	}
	if($a=='eac_adultez' && isset($acc['crear']) && $acc['crear']=='SI'){
		$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
		$rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";

	}
	if($a=='eac_vejez' && isset($acc['crear']) && $acc['crear']=='SI'){
		$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
		$rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";

	}
	
  return $rta;
}

function lis_famili(){
	// $id=divide($_POST['id']);
	$sql="SELECT idpre ACCIONES,id_fam AS Cod_Familiar,numfam AS N°_FAMILIA,fecha,CONCAT_WS(' ',FN_CATALOGODESC(6,complemento1),nuc1,FN_CATALOGODESC(6,complemento2),nuc2,FN_CATALOGODESC(6,complemento3),nuc3) Complementos,
		V.fecha_create Creado,nombre Creó
		FROM `hog_fam` V 
			LEFT JOIN usuarios P ON V.usu_create=id_usuario
			LEFT JOIN hog_carac C ON V.id_fam=C.idfam AND C.fecha = (SELECT MAX(fecha) FROM hog_carac WHERE C.idfam = V.id_fam)
			
		WHERE idpre='".$_POST['id'];
		$sql.="' ORDER BY V.fecha_create";
		//  echo $sql;
			$datos=datos_mysql($sql);
		return panel_content($datos["responseResult"],"famili-lis",15);
}
	
function cmp_homes1(){
	$rta="";
	$rta.="<div class='encabezado vivienda'>TABLA DE FAMILIAS POR VIVIENDA</div>
	<div class='contenido' id='famili-lis' >".lis_famili()."</div></div>";
	return $rta;
}

function cmp_homes(){
	$rta="";
	$t=['complemento1'=>'','nuc1'=>'','complemento2'=>'','nuc2'=>'','complemento3'=>'','nuc3'=>'','telefono1'=>'','telefono2'=>'','telefono3'=>''];
	$d = get_homes();
	if ($d==""){$d=$t;}
	// var_dump($_POST);
	$w='homes';
	$o='inf';
	$c[]=new cmp($o,'e',null,'INFORMACIÓN COMPLEMENTARIA DE LA VIVIENDA',$w);
	$c[]=new cmp('idg','h',15,$_POST['id'],$w.' '.$o,'id','idg',null,'####',false,false);
	// $c[]=new cmp('numfam','s',3,$numf,$w.' '.$o,'Número de Familia','numfam',null,'',false,false,'','col-2');
	$c[]=new cmp('complemento1','s','3',$d['complemento1'],$w.' '.$o,'complemento1','complemento',null,'',true,true,'','col-2');
    $c[]=new cmp('nuc1','t','4',$d['nuc1'],$w.' '.$o,'nuc1','nuc1',null,'',true,true,'','col-1');
 	$c[]=new cmp('complemento2','s','3',$d['complemento2'],$w.' '.$o,'complemento2','complemento',null,'',false,true,'','col-2');
 	$c[]=new cmp('nuc2','t','4',$d['nuc2'],$w.' '.$o,'nuc2','nuc2',null,'',false,true,'','col-15');
 	$c[]=new cmp('complemento3','s','3',$d['complemento3'],$w.' '.$o,'complemento3','complemento',null,'',false,true,'','col-2');
 	$c[]=new cmp('nuc3','t','4',$d['nuc3'],$w.' '.$o,'nuc3','nuc3',null,'',false,true,'','col-15');
	$c[]=new cmp('telefono1','n','10',$d['telefono1'],$w.' '.$o,'telefono1','telefono1','rgxphone',NULL,true,true,'','col-3');
	$c[]=new cmp('telefono2','n','10',$d['telefono2'],$w.' '.$o,'telefono2','telefono2','rgxphone1',null,false,true,'','col-3');
	$c[]=new cmp('telefono3','n','10',$d['telefono3'],$w.' '.$o,'telefono3','telefono3','rgxphone1',null,false,true,'','col-4');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_homes(){
	$id=divide($_REQUEST['id']);
	if($_REQUEST['id']=='' || count($id)!=2){
		return "";
	}else{
		$sql="SELECT id_fam,complemento1,nuc1,complemento2,nuc2,complemento3,nuc3,telefono1,telefono2,telefono3
		FROM `hog_fam` 
		WHERE id_fam ='{$id[1]}'";
		// echo $sql;
		// print_r($id);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
	} 
}

function num_fam(){
	if($_POST['idg']==''){
		return "";
	}else{
		$id=$_POST['idg'];
		$sql="SELECT max(numfam) nfam
		FROM  hog_fam
		WHERE idpre=$id";
		// echo $sql;
		//print_r($id);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
		$nf = json_encode($info['responseResult'][0]['nfam']);
	if (is_null($nf)) {
		$numf = 1;
	} else {
		$nf_limpio = preg_replace('/\D/', '', $nf);
		if ($nf_limpio === '') {
			$n = 0;
		} else {
			$n = intval($nf_limpio);
		}
		$numf = $n + 1;
	}
	return $numf;
	} 
}

function namequipo(){
		$sql="SELECT equipo FROM  usuarios WHERE id_usuario='".$_SESSION['us_sds']."'";
		// echo $sql;
		//print_r($id);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
		return $info['responseResult'][0]['equipo'];
}

function opc_incluofici($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=179 and estado='A' ORDER BY 1",$id);
}
function opc_pobladifer($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=178 and estado='A' ORDER BY 1",$id);
}
function opc_tenDencia($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=8 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_vivienda($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=4 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_familia($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=10 and estado='A' ORDER BY 1",$id);
}
function opc_complemento($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=6 and estado='A' ORDER BY 1",$id);
}
function opc_vinculos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=12 and estado='A' ORDER BY 1",$id);
}
function opc_ingreso($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=13 and estado='A' ORDER BY 1",$id);
}
function opc_encuentra($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}


function focus_homes(){
	return 'homes';
   }
   
function focus_homes1(){
	return 'homes1';
}
function men_homes(){
	$rta=cap_menus('homes','pro');
	return $rta;
}
function men_homes1(){
	$rta=cap_menus('homes1','fix');
	return $rta;
}
   
function gra_homes(){
	// var_dump($_POST);
	$id = divide($_POST['idg']);

	$campos = array('complemento1','nuc1','complemento2','nuc2', 'complemento3','nuc3','telefono1','telefono2','telefono3');

	// Validar telefono1: solo 7 o 10 dígitos numéricos
	foreach (['telefono1' => false, 'telefono2' => true, 'telefono3' => true] as $campo => $permiteCero) {
		$tel = isset($_POST[$campo]) ? trim($_POST[$campo]) : '';
		if ($permiteCero) {
			if ($tel === '') $tel = '0';
			if (!preg_match('/^(0|\d{7}|\d{10})$/', $tel)) {
				return "msj['Error: El $campo debe tener exactamente 7 o 10 dígitos numéricos, o el valor 0.']";
			}
		} else {
			if (!preg_match('/^\d{7}$|^\d{10}$/', $tel)) {
				return "msj['Error: El $campo debe tener exactamente 7 o 10 dígitos numéricos.']";
			}
		}
		$_POST[$campo] = $tel;
	}

	if(count($id)==2){
		$sql = "UPDATE hog_fam SET " . implode(" = ?, ", $campos) . " = ?, usu_update = ?, fecha_update = ? WHERE id_fam = ?";
		$params = params($campos); // Para UPDATE, agregamos los valores dinámicos
		$params[] = array('type' => 's', 'value' => $_SESSION['us_sds']);
		$params[] = array('type' => 's', 'value' => date("Y-m-d H:i:s"));
		$params[] = array('type' => 'i', 'value' => $id[1]);
	}else{
		$id = $_POST['idg'];
		$holders = array_fill(0, count($campos), '?');// Crear placeholders para los valores
		$sql = "INSERT INTO hog_fam VALUES (?,?,?, " . implode(", ", $holders) . ",?,?,?,?,?,?)";
		$params = array(
			array('type' => 'i', 'value' => NULL),
			array('type' => 'i', 'value' => $id),
			array('type' => 'i', 'value' => num_fam())
		);
		$params = array_merge($params, params($campos));// Agregar los valores dinámicos
		$params[] = array('type' => 's', 'value' => namequipo());
		$params[] = array('type' => 'i', 'value' => $_SESSION['us_sds']);
		$params[] = array('type' => 's', 'value' => date("Y-m-d H:i:s"));
		$params[] = array('type' => 's', 'value' => NULL);
		$params[] = array('type' => 's', 'value' => NULL);
		$params[] = array('type' => 's', 'value' => 'A');
	}
	// var_dump($params);
	$rta = mysql_prepd($sql, $params);
	return $rta;
}



// INICIO FORMULARIO INTEGRANTES DE LA FAMILIA


function cmp_person1(){
	$rta="";
	$rta .="<div class='encabezado vivienda'>TABLA DE INTEGRANTES FAMILIA</div>
	<div class='contenido' id='datos-lis' >".lista_persons()."</div></div>";
	return $rta;
} 


function cmp_person(){
	$rta="";
	// $p=get_edad();
    $w='person';
	$o='infgen';
	$key='pEr';
	$t=['encuentra'=>'','idpersona'=>'','tipo_doc'=>'','nombre1'=>'','nombre2'=>'','apellido1'=>'','apellido2'=>'','fecha_nacimiento'=>'','sexo'=>'','genero'=>'','oriensexual'=>'','nacionalidad'=>'','estado_civil'=>'','niveduca'=>'','abanesc'=>'','ocupacion'=>'','tiemdesem'=>'','vinculo_jefe'=>'','etnia'=>'','pueblo'=>'','idioma'=>'','discapacidad'=>'','regimen'=>'','eapb'=>'','afiliaoficio'=>'','sisben'=>'','catgosisb'=>'','pobladifer'=>'','incluofici'=>'','cuidador'=>'','perscuidada'=>'','tiempo_cuidador'=>'','cuidador_unidad'=>'','vinculo'=>'','tiempo_descanso'=>'','descanso_unidad'=>'','reside_localidad'=>'','localidad_vive'=>'','transporta'=>'','telefono1'=>'','telefono2'=>'0','correo'=>''];
	// print_r($_POST);
	if (count(divide($_POST['id']))==2){
		$edit=false;
		$d = get_person();
		if ($d==""){$d=$t;}
	}else{
		$edit=true;
		$d='';
		if ($d==""){$d=$t;}
	}
	// var_dump($d);
	$c[]=new cmp($o,'e',null,'INFORMACIÓN GENERAL',$w);
	$c[]=new cmp('idp','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
	$c[]=new cmp('encuentra','s','2',$d['encuentra'],$w.' '.$o,'El usuario se encuentra','encuentra',null,null,true,true,'','col-2');
	$c[]=new cmp('idpersona','nu','9999999999999999',$d['idpersona'],$w.' '.$key.' '.$o,'Identificación <a href="https://www.adres.gov.co/consulte-su-eps" target="_blank">     Abrir ADRES</a>','idpersona',null,null,true,$edit,'','col-4');
	$c[]=new cmp('tipo_doc','s','3',$d['tipo_doc'],$w.' '.$key.' '.$o,'Tipo documento','tipo_doc',null,null,true,$edit,'','col-4',"getDatForm('pEr','personOld',['infgen'],this);");
	$c[]=new cmp('nombre1','t','30',$d['nombre1'],$w.' '.$o,'Primer Nombre','nombre1',null,null,true,true,'','col-2');
	$c[]=new cmp('nombre2','t','30',$d['nombre2'],$w.' '.$o,'Segundo Nombre','nombre2',null,null,false,true,'','col-2');
	$c[]=new cmp('apellido1','t','30',$d['apellido1'],$w.' '.$o,'Primer Apellido','apellido1',null,null,true,true,'','col-2');
	$c[]=new cmp('apellido2','t','30',$d['apellido2'],$w.' '.$o,'Segundo Apellido','apellido2',null,null,false,true,'','col-2');
	$c[]=new cmp('fecha_nacimiento','d','',$d['fecha_nacimiento'],$w.' '.$o,'Fecha de nacimiento','fecha_nacimiento',null,null,true,$edit,'','col-2',"validDate(this,-43800,0);",[],"child14('fecha_nacimiento','osx');Ocup5('fecha_nacimiento','OcU');");
	$c[]=new cmp('sexo','s','3',$d['sexo'],$w.' '.$o,'Sexo','sexo',null,null,true,$edit,'','col-2');
	$c[]=new cmp('genero','s','3',$d['genero'],$w.' '.$o,'Genero','genero',null,null,true,true,'','col-2');
	$c[]=new cmp('oriensexual','s','3',$d['oriensexual'],$w.' osx '.$o,'Orientacion Sexual','oriensexual',null,null,true,true,'','col-2');
	$c[]=new cmp('nacionalidad','s','3',$d['nacionalidad'],$w.' '.$o,'nacionalidad','nacionalidad',null,null,true,true,'','col-2');
	$c[]=new cmp('estado_civil','s','3',$d['estado_civil'],$w.' '.$o,'Estado Civil','estado_civil',null,null,true,true,'','col-2');
	$c[]=new cmp('niveduca','s','3',$d['niveduca'],$w.' '.$o,'Nivel Educativo','niveduca',null,'',true,true,'','col-25',"enabDesEsc('niveduca','aE',fecha_nacimiento);");//true
	$c[]=new cmp('abanesc','s','3',$d['abanesc'],$w.' aE '.$o,'Razón del abandono Escolar','abanesc',null,'',false,false,'','col-25');
	$c[]=new cmp('ocupacion','s','3',$d['ocupacion'],$w.' OcU '.$o,'Ocupacion','ocupacion',null,'',false,true,'','col-25',"timeDesem(this,'des');");//true
	$c[]=new cmp('tiemdesem','n','3',$d['tiemdesem'],$w.' des '.$o,'Tiempo de desempleo (Meses)','tiemdesem',null,'',false,false,'','col-25');
	$c[]=new cmp('vinculo_jefe','s','3',$d['vinculo_jefe'],$w.' '.$o,'Vinculo con el jefe del Hogar','vinculo_jefe',null,null,true,true,'','col-2');
	$c[]=new cmp('etnia','s','3',$d['etnia'],$w.' '.$o,'Pertenencia Etnica','etnia',null,null,true,true,'','col-2',"enabEtni('etnia','ETn','idi');");
	$c[]=new cmp('pueblo','s','50',$d['pueblo'],$w.' ETn cmhi '.$o,'pueblo','pueblo',null,null,false,true,'','col-2');
	$c[]=new cmp('idioma','o','2',$d['idioma'],$w.' ETn cmhi idi '.$o,'Habla Español','idioma',null,null,false,true,'','col-2');
	$c[]=new cmp('discapacidad','s','3',$d['discapacidad'],$w.' '.$o,'discapacidad','discapacidad',null,null,true,true,'','col-2');
	$c[]=new cmp('regimen','s','3',$d['regimen'],$w.' '.$o,'regimen','regimen',null,null,true,true,'','col-2',"enabAfil('regimen','eaf');enabEapb('regimen','rgm');");
	$c[]=new cmp('eapb','s','3',$d['eapb'],$w.' rgm '.$o,'eapb','eapb',null,null,true,true,'','col-2');
	$c[]=new cmp('afiliacion','o','2',$d['afiliaoficio'],$w.' eaf cmhi '.$o,'¿Esta interesado en afiliación por oficio?','afiliacion',null,null,false,true,'','col-2');
	$c[]=new cmp('sisben','s','3',$d['sisben'],$w.' '.$o,'Grupo Sisben <a href="https://www.sisben.gov.co/paginas/consulta-tu-grupo.html" target="_blank">     Abrir SISBEN</a>','sisben',null,null,true,true,'','col-2');
	$c[]=new cmp('catgosisb','n','2',$d['catgosisb'],$w.' '.$o,'Categoria Sisben','catgosisb','rgxsisben',null,true,true,'','col-2');
	$c[]=new cmp('pobladifer','s','3',$d['pobladifer'],$w.' '.$o,'Poblacion Direferencial y de Inclusión','pobladifer',null,'',true,true,'','col-2');
	$c[]=new cmp('incluofici','s','3',$d['incluofici'],$w.' '.$o,'Población Inclusion por Oficio','incluofici',null,'',true,true,'','col-2');

	$o='relevo';
	$c[]=new cmp('cuidador','o','2',$d['cuidador'],$w.' '.$o,'¿Es cuidador de una persona residente en la vivienda?','cuidador',null,null,true,true,'','col-25',"hideCuida('cuidador','cUi');");
	$c[]=new cmp('perscuidada','s','3',$d['perscuidada'],$w.' cUi '.$o,'N° de identificacion y Nombres','cuida',null,null,false,false,'','col-35');
	$c[]=new cmp('tiempo_cuidador','n','20',$d['tiempo_cuidador'],$w.' cUi '.$o,'¿Por cuánto tiempo ha sido cuidador?','tiempo_cuidador',null,null,false,false,'','col-2');
	$c[]=new cmp('cuidador_unidad','s','3',$d['cuidador_unidad'],$w.' cUi '.$o,'Unidad de medida tiempo cuidador','cuidador_unidad',null,null,false,false,'','col-2');
	$c[]=new cmp('vinculo_cuida','s','3',$d['vinculo'],$w.' cUi '.$o,'Vinculo con la persona cuidada','vinculo_cuida',null,null,false,false,'','col-2');
	$c[]=new cmp('tiempo_descanso','n','20',$d['tiempo_descanso'],$w.' cUi '.$o,'¿Cada cuánto descansa?','tiempo_descanso',null,null,false,false,'','col-2');
	$c[]=new cmp('descanso_unidad','s','3',$d['descanso_unidad'],$w.' cUi '.$o,'Unidad de medida tiempo descanso','descanso_unidad',null,null,false,false,'','col-2');
	$c[]=new cmp('reside_localidad','o','2',$d['reside_localidad'],$w.' cUi '.$o,'Reside en la localidad','reside_localidad',null,null,false,false,'','col-3',"enabLoca('reside_localidad','lochi');");
	$c[]=new cmp('localidad_vive','s','3',$d['localidad_vive'],$w.' lochi cUi '.$o,'¿En qué localidad vive?','localidad_vive',null,null,false,false,'','col-3');
	$c[]=new cmp('transporta','s','3',$d['transporta'],$w.' lochi cUi  '.$o,'¿En que se transporta?','transporta',null,null,false,false,'','col-4');
	
	$o='hab';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN DE CONTACTO',$w);
	$c[]=new cmp('telefono1','nu','9999999999',$d['telefono1'],$w.' '.$o,'telefono1','telefono1','rgxphone',NULL,true,true,'','col-3');
	$c[]=new cmp('telefono2','nu','9999999999',$d['telefono2'],$w.' '.$o,'telefono2','telefono2','rgxphone1',null,false,true,'','col-3');
	$c[]=new cmp('correo','em','80',$d['correo'],$w.' '.$o,'Correo','correo','rgxmail',null,true,true,'','col-4');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}
      
function lista_persons(){ //revisar
	// var_dump($_POST);
	$id=divide($_POST['id']);
		$sql="SELECT concat_ws('_',idpeople,$id[0]) ACCIONES,idpeople 'Cod Persona',concat_ws(' ',nombre1,nombre2,apellido1,apellido2) 'Nombre Usuario',FN_CATALOGODESC(17,regimen) Regimen,FN_CATALOGODESC(18,eapb) EAPB, fecha_create 'Fecha Creación',fecha_nacimiento 'Fecha Nacimiento'
		FROM `person` 
			WHERE vivipersona='".$id[0]."'";
		$sql.=" ORDER BY fecha_create";
		// echo $sql;
		// $_SESSION['sql_person']=$sql;
			$datos=datos_mysql($sql);
		return panel_content($datos["responseResult"],"datos-lis",15, array('R', 'Fecha Nacimiento'));
}

function focus_person(){
	return 'person';
}
   
function men_person(){
	$rta=cap_menus('person','pro');
	return $rta;
}

function get_person(){
	//  print_r($_REQUEST);
	 $id=divide($_REQUEST['id']);
	if($_REQUEST['id']=='' || count($id)!=2){
		return "";
	}else{
		$sql="SELECT concat_ws('_',idpeople,vivipersona),encuentra,idpersona,tipo_doc,nombre1,nombre2,
		apellido1,apellido2,fecha_nacimiento,sexo,genero,oriensexual,nacionalidad,estado_civil,
		niveduca,abanesc,ocupacion,tiemdesem,vinculo_jefe,etnia,pueblo,idioma,discapacidad,regimen,eapb,
		afiliaoficio,sisben,catgosisb,pobladifer,incluofici,cuidador,perscuidada,tiempo_cuidador,
		cuidador_unidad,vinculo,tiempo_descanso,descanso_unidad,reside_localidad,localidad_vive,
		transporta,telefono1,telefono2,correo
		FROM `person`
		WHERE idpeople ='{$id[0]}'" ;
		// echo $sql;
		// print_r($id);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
	} 
}

 function get_personOld(){
	// print_r($_REQUEST);
	$id=divide($_POST['id']);
	$info=datos_mysql("select idpersona from person where idpersona ='".$id[0]."'");
	if (!$info['responseResult']) {
		$sql="SELECT encuentra,idpersona,tipo_doc,nombre1,nombre2,apellido1,apellido2,fecha_nacimiento,
		sexo,genero,oriensexual,nacionalidad,estado_civil,niveduca,abanesc,ocupacion,tiemdesem,vinculo_jefe,etnia,pueblo,idioma,discapacidad,regimen,eapb,
		afiliaoficio,sisben,catgosisb,pobladifer,incluofici,cuidador,perscuidada,tiempo_cuidador,cuidador_unidad,vinculo,tiempo_descanso,
		descanso_unidad,reside_localidad,localidad_vive,transporta
		FROM `personas` 
   	WHERE idpersona ='".$id[0]."' AND tipo_doc='".$id[1]."'";
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode (new stdClass);
	}
	return json_encode($info['responseResult'][0]);
	}else{
		// return json_encode (new stdClass);
		return $rta="Error: El usuario con este número de documento ya se encuentra registrado.";

	}
} 

function gra_person(){
	// print_r($_POST);
	$id=divide($_POST['idp']);
	// print_r(count($id));
	if (($rtaN = validNum($_POST['telefono1'], [7, 10])) !== true) {
		return $rtaN;
	}
	if (($rtaN = validNum($_POST['telefono2'], [7, 10], ['0'])) !== true) {
		return $rtaN;
	}
	if(count($id)!=1){
		$sql="UPDATE `person` SET 
		encuentra=TRIM(UPPER('{$_POST['encuentra']}')),
		`nombre1`=TRIM(UPPER('{$_POST['nombre1']}')),
		`nombre2`=TRIM(UPPER('{$_POST['nombre2']}')),
		`apellido1`=TRIM(UPPER('{$_POST['apellido1']}')),
		`apellido2`=TRIM(UPPER('{$_POST['apellido2']}')),
		`genero`=TRIM(UPPER('{$_POST['genero']}')),
		`oriensexual`=TRIM(UPPER('{$_POST['oriensexual']}')),
		`nacionalidad`=TRIM(UPPER('{$_POST['nacionalidad']}')),
		`estado_civil`=TRIM(UPPER('{$_POST['estado_civil']}')),
		niveduca=TRIM(UPPER('{$_POST['niveduca']}')),
		abanesc=TRIM(UPPER('{$_POST['abanesc']}')),
		ocupacion=TRIM(UPPER('{$_POST['ocupacion']}')),
		tiemdesem=TRIM(UPPER('{$_POST['tiemdesem']}')),
		`vinculo_jefe`=TRIM(UPPER('{$_POST['vinculo_jefe']}')),
		`etnia`=TRIM(UPPER('{$_POST['etnia']}')),
		`pueblo`=TRIM(UPPER('{$_POST['pueblo']}')),
		`idioma`=TRIM(UPPER('{$_POST['idioma']}')),
		`discapacidad`=TRIM(UPPER('{$_POST['discapacidad']}')),
		`regimen`=TRIM(UPPER('{$_POST['regimen']}')),
		`eapb`=TRIM(UPPER('{$_POST['eapb']}')),
		`afiliaoficio`=TRIM(UPPER('{$_POST['afiliacion']}')),
		`sisben`=TRIM(UPPER('{$_POST['sisben']}')),
		`catgosisb`=TRIM(UPPER('{$_POST['catgosisb']}')),
		`pobladifer`=TRIM(UPPER('{$_POST['pobladifer']}')),
		`incluofici`=TRIM(UPPER('{$_POST['incluofici']}')),
		`cuidador`=TRIM(UPPER('{$_POST['cuidador']}')),
		`perscuidada`=TRIM(UPPER('{$_POST['perscuidada']}')),
		`tiempo_cuidador`=TRIM(UPPER('{$_POST['tiempo_cuidador']}')),
		`cuidador_unidad`=TRIM(UPPER('{$_POST['cuidador_unidad']}')),
		`vinculo`=TRIM(UPPER('{$_POST['vinculo_cuida']}')),
		`tiempo_descanso`=TRIM(UPPER('{$_POST['tiempo_descanso']}')),
		`descanso_unidad`=TRIM(UPPER('{$_POST['descanso_unidad']}')),
		`reside_localidad`=TRIM(UPPER('{$_POST['reside_localidad']}')),
		`localidad_vive`=TRIM(UPPER('{$_POST['localidad_vive']}')),
		`transporta`=TRIM(UPPER('{$_POST['transporta']}')),
		`telefono1`=TRIM(UPPER('{$_POST['telefono1']}')),
		`telefono2`=TRIM(UPPER('{$_POST['telefono2']}')),
		`correo`=TRIM(UPPER('{$_POST['correo']}')),
		`usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),
		`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
		WHERE idpeople =TRIM(UPPER('{$id[0]}'))";
		$rta=dato_mysql($sql);
		//    echo $sql."    ".$rta;
	}else{
		/* $sql1="INSERT INTO `personas_datocomp` VALUES (TRIM(UPPER('{$_POST['tipo_doc']}')),TRIM(UPPER('{$_POST['idpersona']}')),TRIM(UPPER('{$_POST['fpe']}')),TRIM(UPPER('{$_POST['fta']}')),TRIM(UPPER('{$_POST['imc']}')),TRIM(UPPER('{$_POST['tas']}')),TRIM(UPPER('{$_POST['tad']}')),TRIM(UPPER('{$_POST['glu']}')),TRIM(UPPER('{$_POST['bra']}')),TRIM(UPPER('{$_POST['abd']}')),TRIM(UPPER('{$_POST['pef']}')),TRIM(UPPER('{$_POST['des']}')),TRIM(UPPER('{$_POST['fin']}')),TRIM(UPPER('{$_POST['oms']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR),TRIM(UPPER('{$_SESSION['us_sds']}')),null,null,'A')";
		$rta1=dato_mysql($sql1); */

		$idp=cleanTx($_POST['idpersona']);

		$sql="INSERT INTO person VALUES (NULL,
		TRIM(UPPER('{$_POST['encuentra']}')),
		TRIM(UPPER('$idp')),$id[0],
		TRIM(UPPER('{$_POST['tipo_doc']}')),
		TRIM(UPPER('{$_POST['nombre1']}')),
		TRIM(UPPER('{$_POST['nombre2']}')),
		TRIM(UPPER('{$_POST['apellido1']}')),
		TRIM(UPPER('{$_POST['apellido2']}')),
		TRIM(UPPER('{$_POST['fecha_nacimiento']}')),
		TRIM(UPPER('{$_POST['sexo']}')),
		TRIM(UPPER('{$_POST['genero']}')),
		TRIM(UPPER('{$_POST['oriensexual']}')),
		TRIM(UPPER('{$_POST['nacionalidad']}')),
		TRIM(UPPER('{$_POST['estado_civil']}')),
		TRIM(UPPER('{$_POST['niveduca']}')),
		TRIM(UPPER('{$_POST['abanesc']}')),
		TRIM(UPPER('{$_POST['ocupacion']}')),
		TRIM(UPPER('{$_POST['tiemdesem']}')),
		TRIM(UPPER('{$_POST['vinculo_jefe']}')),
		TRIM(UPPER('{$_POST['etnia']}')),
		TRIM(UPPER('{$_POST['pueblo']}')),
		TRIM(UPPER('{$_POST['idioma']}')),
		TRIM(UPPER('{$_POST['discapacidad']}')),
		TRIM(UPPER('{$_POST['regimen']}')),
		TRIM(UPPER('{$_POST['eapb']}')),
		TRIM(UPPER('{$_POST['afiliacion']}')),
		TRIM(UPPER('{$_POST['sisben']}')),
		TRIM(UPPER('{$_POST['catgosisb']}')),
		TRIM(UPPER('{$_POST['pobladifer']}')),
		TRIM(UPPER('{$_POST['incluofici']}')),
		TRIM(UPPER('{$_POST['cuidador']}')),
		TRIM(UPPER('{$_POST['perscuidada']}')),
		TRIM(UPPER('{$_POST['tiempo_cuidador']}')),
		TRIM(UPPER('{$_POST['cuidador_unidad']}')),
		TRIM(UPPER('{$_POST['vinculo_cuida']}')),
		TRIM(UPPER('{$_POST['tiempo_descanso']}')),
		TRIM(UPPER('{$_POST['descanso_unidad']}')),
		TRIM(UPPER('{$_POST['reside_localidad']}')),
		TRIM(UPPER('{$_POST['localidad_vive']}')),
		TRIM(UPPER('{$_POST['transporta']}')),
		TRIM(UPPER('{$_POST['telefono1']}')),
		TRIM(UPPER('{$_POST['telefono2']}')),
		TRIM(UPPER('{$_POST['correo']}')),
		TRIM(UPPER('{$_SESSION['us_sds']}')),
		DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";	
		$rta=dato_mysql($sql);
		
	}
		//echo $sql;
		
		return $rta;
	}
	
	function opc_abanesc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=181 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_niveduca($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=180 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_ocupacion($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=175 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_numfam($id=''){
		return opc_sql("SELECT `idcatadeta`,concat(descripcion,' - ',idcatadeta) FROM `catadeta` WHERE idcatalogo=172 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_tipo_doc($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 2",$id);
    }
    function opc_sexo($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
    function opc_genero($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=19 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_oriensexual($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=49 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_nacionalidad($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=30 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
    }
    function opc_etnia($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=16 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
    }
	function opc_regimen($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=17 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
    }
    function opc_eapb($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=18 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
    }
	function opc_sisben($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=48 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_estado_civil($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=47 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_vinculo_jefe($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=54 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	} 
	function opc_cuidador_unidad($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=67 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_vinculo_cuida($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=54 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_localidad_vive($id=''){
		return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=2  ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
    }
	function opc_transporta($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=25 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_descanso_unidad($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=67 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_pueblo($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=15 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}
	function opc_discapacidad($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=14 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}

function opc_cuida(){
	$id=divide($_REQUEST['id']);
	if(count($id)==1){
		$sql="SELECT idpeople,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) 'Nombres' from person where vivipersona='$id[0]'";
	}else if(count($id)==2){
		$sql="SELECT idpeople,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) 'Nombres' from person where vivipersona='$id[1]' and idpeople<>'$id[0]'";
	}
	// var_dump($id);
		return opc_sql($sql,'');
}

function get_persona(){
	$id=divide($_REQUEST['id']);
	if($_REQUEST['id']==''){
		return "";
	}else{
		$sql="SELECT id_fam,complemento1,nuc1,complemento2,nuc2,complemento3,nuc3,telefono1,telefono2,telefono3
		FROM `hog_fam` 
		WHERE id_fam ='{$id[1]}'";
		// echo $sql;
		// print_r($id);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];			
	}
}

function opc_aux() {
    $rta = [
        ["icon" => 'fas fa-plus', "text" => 'Nuevo', "color" => 'white', "short" => 'Ctrl N'],
        ["icon" => 'fas fa-trash', "text" => 'Eliminar', "color" => 'white', "short" => 'Ctrl Supr'],
        ["icon" => 'fas fa-file-pdf', "text" => 'PDF', "color" => 'white', "short" => 'Ctrl P'],
        ["icon" => 'fas fa-eye', "text" => 'Ver', "color" => 'white', "short" => 'Ctrl E'],
        ["icon" => 'fas fa-pregnant', "text" => 'Gestantes', "color" => 'white', "short" => 'Ctrl G'],
        ["icon" => 'fas fa-vial', "text" => 'Caracterización', "color" => 'white', "short" => 'Ctrl R'],
        ["icon" => 'fas fa-medkit', "text" => 'Atención Médica', "color" => 'white', "short" => 'Ctrl A'],
    ];
    return json_encode($rta);
}

function plan($id){
	$sql="select id FROM hog_plancuid where idviv='".$id."'";
	$info=datos_mysql($sql);
	if(isset($info['responseResult'][0])){
		return true;
	}else{
		return false;
	}
}

function apg($id) {
    $id = divide($id);
    $sql = "SELECT id_apgar FROM hog_tam_apgar WHERE idpeople='" . $id[0] . "'";
    $info = datos_mysql($sql);
    return isset($info['responseResult'][0]);
}



function eventAsign($key) {
    $id = divide($key);
    $sql = "SELECT evento as eve
            FROM vspeve
            WHERE idpeople ='{$id[0]}'";
    // echo $sql;
    // print_r($id);
    $info = datos_mysql($sql);
    $rta = $info['responseResult'];
    $eventos = array(
      1 => ['icono' => 'siges1', 'titulo' => 'SIFILIS GESTACIONAL', 'modulo' => 'sifigest'],
	  2 => ['icono' => 'hbges1', 'titulo' => 'HB GESTACIONAL', 'modulo' => 'hbgest'],
	  3 => ['icono' => 'vihge1', 'titulo' => 'VIH GESTACIONAL', 'modulo' => 'vihgest'],
	  4 => ['icono' => 'gesta1', 'titulo' => 'BAJO PESO GESTACIONAL', 'modulo' => 'gestantes'],
	  5 => ['icono' => 'gesta1', 'titulo' => 'FAMILIAS CON GESTANTES', 'modulo' => 'gestantes'],
	  6 => ['icono' => 'gesta3', 'titulo' => 'MORBILIDAD MATERNA EXTREMA', 'modulo' => 'mme'],
	  7 => ['icono' => 'condu1', 'titulo' => 'CONDUCTA SUICIDA (CONSUMADO)', 'modulo' => 'condsuic'],
	  8 => ['icono' => 'siges1', 'titulo' => 'VIOLENCIA EN GESTANTES', 'modulo' => 'violgest'],
	  9 => ['icono' => 'desnu1', 'titulo' => 'DNT AGUDA MODERADA O SEVERA', 'modulo' => 'dntsevymod'],
	  10 => ['icono' => 'desnu1', 'titulo' => 'MENORES CON EXCESO DE PESO', 'modulo' => 'dntsevymod'],
	  11 => ['icono' => 'aterm1', 'titulo' => 'BPN A TÉRMNO', 'modulo' => 'bpnterm'],
	  12 => ['icono' => 'prete1', 'titulo' => 'BPN PRETERMINO', 'modulo' => 'bpnpret'],
	  13 => ['icono' => 'hipot1', 'titulo' => 'MNE HIPOTIRODISMO', 'modulo' => 'mnehosp'],
	  14 => ['icono' => 'siges1', 'titulo' => 'ERA - IRA', 'modulo' => 'eraira'],
	  15 => ['icono' => 'desnu1', 'titulo' => 'FAMILIAS CON MENORES DE 5 AÑOS', 'modulo' => 'dntsevymod'],
	  16 => ['icono' => 'canin1', 'titulo' => 'CANCER INFANTIL', 'modulo' => 'cancinfa'],
	  17 => ['icono' => 'sifil1', 'titulo' => 'SIFILIS CONGENITA', 'modulo' => 'sificong'],
	  18 => ['icono' => 'psico1', 'titulo' => 'ACOMPAÑAMIENTO PSICOSOCIAL', 'modulo' => 'acompsic'],
	  19 => ['icono' => 'duelo1', 'titulo' => 'APOYO PSICOLOGICO EN DUELO', 'modulo' => 'apopsicduel'],
	  20 => ['icono' => 'croni2', 'titulo' => 'CRONICOS', 'modulo' => 'cronicos'],
	  21 => ['icono' => 'viole1', 'titulo' => 'VIOLENCIA REITERADA', 'modulo' => 'violreite'],
	  22 => ['icono' => 'salor1', 'titulo' => 'SALUD ORAL', 'modulo' => 'saludoral'],
	  23 => ['icono' => 'otrca1', 'titulo' => 'OTROS CASOS PRIORIZADOS', 'modulo' => 'otroprio'],
	  24 => ['icono' => 'gesta1', 'titulo' => 'OBESIDAD GESTACIONAL', 'modulo' => 'gestantes'],
	  25 => ['icono' => 'gesta1', 'titulo' => 'MATERNAS ADOLESCENTES', 'modulo' => 'gestantes'],
	  26 => ['icono' => 'condu1', 'titulo' => 'CONDUCTA SUICIDA (AMENAZA)', 'modulo' => 'condsuic'],
	  27 => ['icono' => 'condu1', 'titulo' => 'CONDUCTA SUICIDA (IDEACIÓN)', 'modulo' => 'condsuic'],
	  28 => ['icono' => 'condu1', 'titulo' => 'CONDUCTA SUICIDA (INTENTO)', 'modulo' => 'condsuic']
    );
    $eve = '';
    foreach ($rta as $evento) {
		// print_r($_POST);
        $id = $evento['eve'];
        if (isset($eventos[$id])) {
            $icono = $eventos[$id]['icono'];
            $titulo = $eventos[$id]['titulo'];
            $modulo = $eventos[$id]['modulo'];
            $eve .= acceso($modulo)? "<li class='icono $icono' title='$titulo' id='".$key."_".$id."' Onclick=\"mostrar('$modulo','pro',event,'','../vsp/$modulo.php',7,'$modulo');Color('datos-lis');\"></li>":"";
        } else {
            $eve .= "ERROR EN EL ID DEL EVENTO";
        }
    }
    return $eve;
}

function get_Tamiz($fec) {
	$info = datos_mysql("select TIMESTAMPDIFF(YEAR,'$fec',CURDATE()) AS ano");
	$edad = isset($info['responseResult'][0]['ano']) ? intval($info['responseResult'][0]['ano']) : 0;
	$tamiz = [];
	// Apgar: menores de 6 años
	if ($edad >= 0 && $edad <= 6) {
		$tamiz[] = 'tamApgar';
	}
	// RQC: 5 a 15 años
	if ($edad >= 5 && $edad <= 15) {
		$tamiz[] = 'tamrqc';
	}
	// Carlos Crafft: 12 a 15 años
	if ($edad >= 12 && $edad <= 15) {
		$tamiz[] = 'tamcarlos';
	}
	// Valoración del Riesgo: 12 a 17 años
	if ($edad >= 12 && $edad <= 17) {
		$tamiz[] = 'tamvalories';
	}
	// Assist y Riesgo Mental: 16 a 17 años
	if ($edad >= 16 && $edad <= 17) {
		$tamiz[] = 'tamassist';
		$tamiz[] = 'riesgomental';
	}
	// SRQ: 16 años en adelante
	if ($edad >= 16) {
		$tamiz[] = 'tamsrq';
		$tamiz[] = 'tamassist';
		$tamiz[] = 'riesgomental';
	}
	// COPE: 10 años en adelante
	if ($edad >= 10) {
		$tamiz[] = 'tamcope';
	}
	// Findrisc y OMS: 18 años en adelante
	if ($edad >= 18) {
		$tamiz[] = 'tamfindrisc';
		$tamiz[] = 'tamoms';
	}
	// EPOC: 40 años en adelante
	if ($edad >= 40) {
		$tamiz[] = 'tamepoc';
	}
	return $tamiz;
}

function sessions($id) {
    $id = divide($id);
    $sql = "SELECT idrelevo FROM rel_relevo WHERE id_people='" . $id[0] . "' AND acep_rbc ='SI'";
    $info = datos_mysql($sql);
	// var_dump($sql);
	if(isset($info['responseResult'][0])){
		return true;
	}else{
		return false;
	}
}

function psiSesi2($id) {
    $id = divide($id);
    $sql = "SELECT idpsi FROM psi_psicologia WHERE id_people='".$id[0]."' AND psi_validacion13 = 'SI'";
    $info = datos_mysql($sql);
	// var_dump($sql);
	if(isset($info['responseResult'][0])){
		return true;
	}else{
		return false;
	}
}

function psiSessi($id) {
    $id = divide($id);
    $sql = "SELECT id_sesion2 FROM psi_sesion2 WHERE id_people='".$id[0]."' AND contin_caso='4'";
    $info = datos_mysql($sql);
	// var_dump($sql);
	if(isset($info['responseResult'][0])){
		return true;
	}else{
		return false;
	}
}

function psiSesFin($id) {
    $id = divide($id);
    $sql = "SELECT COUNT(*) AS totSes,
		(SELECT COUNT(id_people) from `psi_sesiones`  
		WHERE id_people=$id[0] AND psi_validacion17=5) as cierre
		FROM `psi_sesiones` p WHERE id_people=$id[0]";
		// var_dump($sql);
    $info = datos_mysql($sql);
	// var_dump($info);
	if(intval($info['responseResult'][0]["totSes"]>=1) && 
		intval($info['responseResult'][0]["cierre"]=1)){
		return true;
	}else{
		return false;
	}
}

function ember($id) {
    $id = divide($id);
    $sql = "SELECT COUNT(*) AS Embera from acc_indigenas WHERE idpeople=$id[0] and accion=1";
    $info = datos_mysql($sql);
	// var_dump($info);
	if(intval($info['responseResult'][0]["Embera"])==1){
		return true;
	}else{
		return false;
	}
}

function uaic($id) {
	$id = divide($id);
    $sql = "SELECT COUNT(*) AS uaic from acc_indigenas WHERE idpeople=$id[0] and accion=2";
    $info = datos_mysql($sql);
	// var_dump($info);
	if(intval($info['responseResult'][0]["uaic"])==1){
		return true;
	}else{
		return false;
	}
}

function validUser($id) {
	$id = divide($id);
	$sql = "SELECT COUNT(*) AS total FROM validuser WHERE idpeople=$id[0]";
	$info = datos_mysql($sql);
	if(isset($info['responseResult'][0]['total']) && intval($info['responseResult'][0]['total']) > 0) {
		return true;
	} else {
		return false;
	}
	return ;
}


function medicamAtenci($id) {
    $id = divide($id);
    $sql = "SELECT COUNT(*) AS total FROM eac_atencion WHERE idpeople='".$id[0]."' AND medicamentos = '1'";
    $info = datos_mysql($sql);
	//var_dump($sql);
	if(isset($info['responseResult'][0]['total']) && intval($info['responseResult'][0]['total']) > 0) {
		return true;
	} else {
		return false;
	}
	return ;
}

function laboratorios($id) {
    $id = divide($id);
    $sql = "SELECT COUNT(*) AS total FROM eac_atencion WHERE idpeople='".$id[0]."' AND laboratorios = '1'";
    $info = datos_mysql($sql);
	//var_dump($sql);
	if(isset($info['responseResult'][0]['total']) && intval($info['responseResult'][0]['total']) > 0) {
		return true;
	} else {
		return false;
	}
	return ;
}

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// print_r($c);
// var_dump($c);
	if ($a=='homes' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li title='Caracterización del Hogar'  Onclick=\"mostrar('homes1','fix',event,'','lib.php',0,'homes1');hideFix('person1','fix');Color('homes-lis');\"><i class='fa-solid fa-house-user ico' id='".$c['ACCIONES']."'></i></li>";//setTimeout(mostrar('person1','fix',event,'','lib.php',0,'person1'),500);
		$rta.="<li title='Crear Ubicación de la Familia'  Onclick=\"mostrar('homes','pro',event,'','lib.php',7,'homes');Color('homes-lis');\"><i class='fa-solid fa-circle-plus ico' id='".$c['ACCIONES']."'></i></li>";//setTimeout(DisableUpdate,300,'fechaupd','hid');
		$rta .= acceso('unidadeshs') ? "<li title='Unidades Habitacionales'  Onclick=\"mostrar('unidadeshs','pro',event,'','../soporte/unidades.php',3,'unidadeshs');Color('homes-lis');\"><i class='fa-solid fa-building-un color-soporte ico' id='".$c['ACCIONES']."'></i></li>" : "";
	}
 	if ($a=='famili-lis' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li title='Editar Familia' Onclick=\"mostrar('homes','pro',event,'','lib.php',7,'homes');Color('famili-lis');\"><i class='fa-solid fa-pen-to-square ico' id='".$c['ACCIONES']."_".$c['Cod_Familiar']."'></i></li>";
		$rta.="<li title='Crear Integrante Familia' Onclick=\"mostrar('person','pro',event,'','lib.php',7,'person');Color('famili-lis');\"><i class='fa-solid fa-person-circle-plus ico' id='".$c['Cod_Familiar']."'></i></li>";
		$rta.="<li title='Mostrar Integrantes' Onclick=\"mostrar('person1','fix',event,'','lib.php',0,'person1');Color('famili-lis');\"><i class='fa-solid fa-people-group ico' id='".$c['Cod_Familiar']."'></i></li>";
		$rta .= acceso('caract') ? "<li title='Crear Caracterización Familiar' onclick=\"mostrar('caract','pro',event,'','../crea-caract/lib.php',7,'caract');Color('famili-lis');\"><i class='fa-solid fa-file-circle-plus ico' id='{$c['Cod_Familiar']}'></i></li>" : "";
		$rta .= acceso('planDcui') ? "<li title='Planes de Cuidado Familiar' onclick=\"mostrar('planDcui','pro',event,'','plancui.php',7);Color('famili-lis');\"><i class='fa-solid fa-file-contract ico' id='{$c['Cod_Familiar']}'></i></li>" : "";
		if(plan($c['Cod_Familiar'])===true){
			$rta .= "<li title='Compromisos Concertados' onclick=\"mostrar('compConc','pro',event,'','plncon.php',7);Color('famili-lis');\"><i class='fa-solid fa-handshake-angle ico' id='{$c['Cod_Familiar']}'></i></li>";
		}
		$rta.=(acceso('ambient')) ? "<li title='Riesgo Ambiental' Onclick=\"mostrar('ambient','pro',event,'','amb.php',7);Color('famili-lis');\"><i class='fa-solid fa-tree-city ico' id='".$c['Cod_Familiar']."' ></i></li>":'';

		$rta.="<li title='Interlocales' Onclick=\"mostrar('trasladint','pro',event,'','../soporte/interloc.php',4,'Interlocales');Color('datos-lis');\"><i class='fa-solid fa-reply-all color-soporte ico' id='".$c['Cod_Familiar']."'></i> </li>";//soporte traslados
	}
	if ($a=='datos-lis' && $b=='acciones'){

		$rta="<div class='scroll-menu'><nav class='menu'>";
		/* $rta.="<li class='icono menubtn' id='menuToggle_".$c['ACCIONES']."'></li><div id='menuContainer_".$c['ACCIONES']."'></div>"; */
		
		
		//mostrar('person','pro',event,'','lib.php',7,'person');Color('datos-lis');setTimeout(enabAfil,1000,'regimen','eaf');setTimeout(enabEtni,1000,'etnia','ocu','idi');setTimeout(enabLoca,1000,'reside_localidad','lochi');setTimeout(EditOcup,1000,'ocupacion','true');setTimeout(hideCuida,1000,'cuidador','cUi')
		if(!validUser($c['ACCIONES'])){
		$rta.="<li title='Validar Usuario' Onclick=\"mostrar('validPerson','pro',event,'','../soporte/valperson.php',7,'person');Color('datos-lis');\"><i class='fas fa-user-check color-soporte ico' id='".$c['ACCIONES']."'></i> </li>";
		}
		if(validUser($c['ACCIONES'])){
			$rta.="<li title='Editar Usuario' Onclick=\"mostrar('person','pro',event,'','lib.php',7,'person');Color('datos-lis');\"><i class='fa-solid fa-pen-to-square ico' id='".$c['ACCIONES']."'></i> </li>";//setTimeout(enabEapb,700,'regimen','rgm');setTimeout(getData,600,'person',event,this,['idpersona','tipo_doc','fecha_nacimiento','sexo']);
			$rta .= acceso('signos') ? "<li title='Signos' onclick=\"mostrar('signos','pro',event,'','signos.php',7,'signos');Color('datos-lis');\"><i class='fa-solid fa-stethoscope ico' id='{$c['ACCIONES']}'></i></li>" : "";
			$rta .= acceso('alertas') ? "<li title='Alertas' onclick=\"mostrar('alertas','pro',event,'','alertas.php',7,'alertas');Color('datos-lis');\"><i class='fa-solid fa-person-circle-exclamation ico' id='{$c['ACCIONES']}'></i></li>" : "";
			
			
			/**********************TAMIZAJES*************************/
			$tamiz= get_Tamiz($c['Fecha Nacimiento']);
			if (is_array($tamiz) && in_array('tamApgar', $tamiz)) {
				if (apg($c['ACCIONES'])) {
					$rta .= acceso('tamApgar') ? "<li title='Tamizaje Apgar' onclick=\"mostrar('tamApgar','pro',event,'','../tamizajes/apgar.php',7);Color('datos-lis');\"><i class='fa-solid fa-people-roof ico naranja' id='".$c['ACCIONES']."'></i></li>": '';
				} else {
					$rta .= acceso('tamApgar') ? "<li title='Tamizaje Apgar' onclick=\"mostrar('tamApgar','pro',event,'','../tamizajes/apgar.php',7);Color('datos-lis');\"><i class='fa-solid fa-people-roof ico' id='".$c['ACCIONES']."'></i></li>": '';
				}
			}
			if (is_array($tamiz) && in_array('tamfindrisc', $tamiz)) {
				$rta .= acceso('tamfindrisc') ? "<li title='Tamizaje Findrisc' onclick=\"mostrar('tamfindrisc','pro',event,'','../tamizajes/findrisc.php',7);Color('datos-lis');\"><i class='fa-solid fa-hospital-user ico' id='".$c['ACCIONES']."'></i></li>": '';
			}
			if (is_array($tamiz) && in_array('tamoms', $tamiz)) {
				$rta .= acceso('tamoms') ? "<li title='Tamizaje OMS' onclick=\"mostrar('tamoms','pro',event,'','../tamizajes/oms.php',7);Color('datos-lis');\"><i class=' fa-solid fa-heart-circle-bolt ico' id='".$c['ACCIONES']."'></i></li>": '';
			}
			if (is_array($tamiz) && in_array('tamepoc', $tamiz)) {
				$rta .= acceso('tamepoc') ? "<li title='Tamizaje EPOC' onclick=\"mostrar('tamepoc','pro',event,'','../tamizajes/epoc.php',7);Color('datos-lis');\"><i class=' fa-solid fa-head-side-cough ico' id='".$c['ACCIONES']."'></i></li>": '';
			}
			if (is_array($tamiz) && in_array('tamcope', $tamiz)) {
				$rta .= acceso('tamcope') ? "<li title='Tamizaje COPE' onclick=\"mostrar('tamcope','pro',event,'','../tamizajes/cope.php',7);Color('datos-lis');\"><i class=' fa-solid fa-head-side-virus ico' id='".$c['ACCIONES']."'></i></li>": '';
			}
				$rta .= acceso('tamzung') ? "<li title='Tamizaje ZUNG' onclick=\"mostrar('tamzung','pro',event,'','../tamizajes/zung.php',7);Color('datos-lis');\"><i class=' fa-solid fa-face-sad-tear ico' id='".$c['ACCIONES']."'></i></li>": '';
				$rta .= acceso('tamhamilton') ? "<li title='Tamizaje HAMILTON' onclick=\"mostrar('tamhamilton','pro',event,'','../tamizajes/hamilton.php',7);Color('datos-lis');\"><i class=' fa-solid fa-face-sad-cry ico' id='".$c['ACCIONES']."'></i></li>": '';
				$rta .= acceso('tamWhodas') ? "<li title='Tamizaje WHODAS' onclick=\"mostrar('tamWhodas','pro',event,'','../tamizajes/whodas.php',7);Color('datos-lis');\"><i class=' fa-solid fa-bed-pulse ico' id='".$c['ACCIONES']."'></i></li>": '';
		
				$rta .= acceso('tamzarit') ? "<li title='Tamizaje ZARIT ' onclick=\"mostrar('tamzarit','pro',event,'','../tamizajes/zarit.php',7);Color('datos-lis');\"><i class=' fa-solid fa-face-rolling-eyes ico' id='".$c['ACCIONES']."'></i></li>": '';
				$rta .= acceso('tamBarthel') ? "<li title='Tamizaje BARTHEL' onclick=\"mostrar('tamBarthel','pro',event,'','../tamizajes/barthel.php',7);Color('datos-lis');\"><i class=' fa-solid fa-people-carry-box ico' id='".$c['ACCIONES']."'></i></li>": '';
			/**********************TAMIZAJES*************************/
		
			$rta .= acceso('admision') ? "<li title='Solicitar Admisión' onclick=\"mostrar('admision','pro',event,'','admision.php',7,'admision');Color('datos-lis');\"><i class='fa-solid fa-tty ico' id='{$c['ACCIONES']}'></i></li>" : "";
			$rta .= acceso('atencion') ? "<li title='Crear Atención' onclick=\"mostrar('atencion','pro',event,'','atencion.php',7,'atencion');Color('datos-lis')\"><i class='fa-solid fa-user-doctor ico' id='{$c['ACCIONES']}'></i></li>" : "";
			
			 if (medicamAtenci($c['ACCIONES'])) {
				$rta .= acceso('medicamentctrl') ? "<li title='Entrega Medicamentos' onclick=\"mostrar('medicamentctrl','pro',event,'','../servicios_complem/medicamentos.php',7,'Control Medicamentos');Color('datos-lis')\"><i class='fa-solid fa-capsules ico' id='{$c['ACCIONES']}'></i></li>" : "";	
			}
			if (laboratorios($c['ACCIONES'])) {
				$rta .= acceso('laboratorios') ? "<li title='Laboratorios' onclick=\"mostrar('laboratorios','pro',event,'','../servicios_complem/laboratorios.php',7,'Control Laboratorios');Color('datos-lis')\"><i class='fa-solid fa-flask-vial ico' id='{$c['ACCIONES']}'></i></li>" : "";
			}
			$rta .= acceso('vspeve') ? "<li class='icono admsi1' title='Validar Evento' id='{$c['ACCIONES']}' onclick=\"mostrar('vspeve','pro',event,'','vspeve.php',7,'vspeve');Color('datos-lis');\"></li>" : "";
			$rta.=eventAsign($c['ACCIONES']);
			$rta .= acceso('relevo') ? "<li title='Relevo' onclick=\"mostrar('relevo','pro',event,'','../relevo/lib.php',7,'relevo');Color('datos-lis');\"><i class=' fa-solid fa-person-cane ico' id='{$c['ACCIONES']}'></i></li>":"";
			if (sessions($c['ACCIONES'])) {
				$perfiles = ['ADM','LARREL', 'FISREL', 'LEFREL', 'TSOREL','PROFAM','TERAPEUTA'];
				$rta .= acceso('sesiones') ? "<li title='Sesiónes' onclick=\"mostrar('sesiones','pro',event,'','../relevo/sesiones.php',7);Color('datos-lis');\"> <i class=' fa-solid fa-address-book ico' id='{$c['ACCIONES']}' onclick=\"setTimeout(chanActi,300,'rel_validacion3','act',['" . implode("','", $perfiles) . "']);\"></i></li>" : "";
			}
			
	    	if (is_array($tamiz) && in_array('tamrqc', $tamiz)) {
				$rta .= acceso('psicologia') ? "<li title='RQC' onclick=\"mostrar('tamrqc','pro',event,'','../tamizajes/rqc.php',7,'rqc');Color('datos-lis');\"><i class='fas fa-notes-medical ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
			if (is_array($tamiz) && in_array('tamsrq', $tamiz)) {
				$rta .= acceso('psicologia') ? "<li title='SRQ' onclick=\"mostrar('tamsrq','pro',event,'','../tamizajes/srq.php',7,'srq');Color('datos-lis');\"><i class='fas fa-lightbulb ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
			
			$rta .= acceso('psicologia') ? "<li title='Psicologia Sesión 1' onclick=\"mostrar('psicologia','pro',event,'','../psicologia/lib.php',7,'psicologia');Color('datos-lis');\"><i class=' fa-solid fa-person-circle-question ico' id='{$c['ACCIONES']}'></i></li>":"";
						if (psiSesi2($c['ACCIONES'])) {
				$rta .= acceso('psicologia') ? "<li title='Psicologia Sesión 2' onclick=\"mostrar('sesion2','pro',event,'','../psicologia/lib.php',7,'sesion2');Color('datos-lis');\"><i class=' fa-solid fa-person-circle-question ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
			if (psiSessi($c['ACCIONES'])) {
				$rta .= acceso('psicologia') ? "<li title='Sesión 3, 4, 5, 6' onclick=\"mostrar('sesiones_psi','pro',event,'','../psicologia/sesiones.php',7,'sesiones_psi');setTimeout(hidPlan,700,'psi_validacion10','duda_com',false);setTimeout(hidFieOpt,700,'psi_validacion7','sem_hide',false);Color('datos-lis');\"><i class=' fa-solid fa-person-circle-question ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
			if (psiSesFin($c['ACCIONES'])) {
				$rta .= acceso('psicologia') ? "<li title='Sesión final' onclick=\"mostrar('sesion_fin','pro',event,'','../psicologia/lib.php',7,'sesion_fin');Color('datos-lis');\"><i class=' fa-solid fa-person-circle-question ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
			$rta.=(acceso('etnias')) ? "<li title='Etnias' Onclick=\"mostrar('ethnicity','pro',event,'','../etnias/tipoetn.php',7);Color('famili-lis');\"><i class='fa-solid fa-people-arrows ico' id='".$c['ACCIONES']."' ></i></li>":'';
			if (ember($c['ACCIONES'])) {
				$rta .= acceso('ember') ? "<li title='Identificación Embera' onclick=\"mostrar('emb_Id','pro',event,'','../etnias/embid.php',7,'Identificación Embera');Color('datos-lis');\"><i class=' fa-solid fa-users-line ico' id='{$c['ACCIONES']}'></i></li>":"";
				$rta .= acceso('ember') ? "<li title='Embera Seguimiento Rutinario' onclick=\"mostrar('segnoreg','pro',event,'','../etnias/embsegnoreg.php',7,'Seguimiento No Regular');Color('datos-lis');\"><i class=' fa-solid fa-list-check ico' id='{$c['ACCIONES']}'></i></li>":"";
				$rta .= acceso('ember') ? "<li title='Seguimientos Hospitalarios' onclick=\"mostrar('seguim','pro',event,'','../etnias/embsegui.php',7,'Seguimientos');Color('datos-lis');\"><i class=' fa-solid fa-rectangle-list ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
			if (uaic($c['ACCIONES'])) {
				$rta .= acceso('uaic') ? "<li title='Identificación UAIC' onclick=\"mostrar('uaic_id','pro',event,'','../etnias/uaicid.php',7,'Identificación');Color('datos-lis');setTimeout(enabEmbPare,1000,'parentesco');\"><i class=' fa-solid fa-id-badge ico' id='{$c['ACCIONES']}'></i></li>":"";
				$rta .= acceso('uaic') ? "<li title='Seguimientos UAIC' onclick=\"mostrar('uaic_seg','pro',event,'','../etnias/uaicseg.php',7,'Seguimientos');Color('datos-lis');\"><i class=' fa-solid fa-list-ol ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
			$rta .= acceso('servagen') ? "<li title='Servicio de Agendamiento' onclick=\"mostrar('servagen','pro',event,'','../agendamient/serage.php',7,'Servicio de Agendamiento');Color('datos-lis');\"><i class=' fa-solid fa-square-phone ico' id='{$c['ACCIONES']}'></i></li>":"";

			$rta.="<li title='Trasladar Usuario' Onclick=\"mostrar('traslados','pro',event,'','../soporte/trasladloc.php',4,'traslados');Color('datos-lis');\"><i class='fa-solid fa-reply-all color-color-soporte ico' id='".$c['ACCIONES']."'></i> </li>";

			if (is_array($tamiz) && in_array('tamvalories', $tamiz)) {
				$rta .= acceso('tamvalories') ? "<li title='Valoración del Riesgo' onclick=\"mostrar('tamvalories','pro',event,'','../tamizajes/valoriesg.php',7,'Valoración del Riesgo');Color('datos-lis');\"><i class='fa-solid fa-skull-crossbones ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
			if (is_array($tamiz) && in_array('tamcarlos', $tamiz)) {
				$rta .= acceso('tamcarlos') ? "<li title='Tamizaje Carlos Crafft' onclick=\"mostrar('tamcarlos','pro',event,'','../tamizajes/carlos.php',7,'Carlos Crafft');Color('datos-lis');\"><i class='fa-solid fa-cannabis ico' id='{$c['ACCIONES']}'></i></li>":""; //fa-clipboard-list
			}
			if (is_array($tamiz) && in_array('tamassist', $tamiz)) {
				$rta .= acceso('tamassist') ? "<li title='Tamizaje assist' onclick=\"mostrar('tamassist','pro',event,'','../tamizajes/assist.php',7,'Assist');Color('datos-lis');\"><i class='fa-solid fa-wine-bottle ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
			if (is_array($tamiz) && in_array('riesgomental', $tamiz)) {
				$rta .= acceso('riesgomental') ? "<li title='Tamizaje Riesgo Mental' onclick=\"mostrar('riesgomental','pro',event,'','../tamizajes/riesgomental.php',7,'Riesgo Mental');Color('datos-lis');\"><i class='fas fa-brain ico' id='{$c['ACCIONES']}'></i></li>":"";
			}
		}
	}
		if($a=='atencion' && $b=='acciones'){
			$rta="<nav class='menu right'>";
			$rta.="<li class='icono editar ' title='Editar Atención' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,1000,'atencion',event,this,['idpersona','tipo_doc']);setTimeout(getData,1300,'atencion',event,this,['idpersona','tipo_doc']);setTimeout(getData,1500,'atencion',event,this,['idpersona','tipo_doc']);setTimeout(changeSelect,1100,'letra1','rango1');setTimeout(changeSelect,1150,'letra2','rango2');setTimeout(changeSelect,1280,'letra3','rango3');setTimeout(changeSelect,1385,'rango1','diagnostico1');setTimeout(changeSelect,1385,'rango2','diagnostico2');setTimeout(changeSelect,1385,'rango3','diagnostico3');Color('datos-lis');\"></li>";
		}
		if ($a=='planc-lis' && $b=='acciones'){
			$rta="<nav class='menu right'>";
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,1000,'planDcui',event,this,['id','fecha_caracteriza']);\"></li>";  //   act_lista(f,this);
		} 
return $rta;
}

function bgcolor($a,$c,$f='c'){
	$rta = '';
	return $rta;
}