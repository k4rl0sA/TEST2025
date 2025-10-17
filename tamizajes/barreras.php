<?php
require_once "../libs/gestion.php";
ini_set('display_errors','1');
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
    if (is_array($rta)) json_encode($rta);
    else echo $rta;
  }   
}

function lis_barreras(){
    $id=divide($_POST['id']);
    $sql="SELECT id_barrera 'Cod Registro',fecha_toma,geo_centro_cercano,geo_dificultad_salir,geo_dificultad_acudir,fis_limitacion_movilidad,eco_limitacion_costovida,adm_estado_afiliacion,psi_trastorno_emocional,cul_discriminacion,usu_creo,fecha_create FROM barreras_acceso_salud WHERE idpeople='".$id[0]."' ORDER BY fecha_create DESC";
    $datos=datos_mysql($sql);
    return panel_content($datos["responseResult"],"barreras-lis",5);
}

function cmp_barreras(){
    $rta="<div class='encabezado barreras'>BARRERAS DE ACCESO A LA SALUD</div><div class='contenido' id='barreras-lis'>".lis_barreras()."</div></div>";
    $t=['idpersona'=>'','tipodoc'=>'','nombre'=>'','fechanacimiento'=>''];
    $w='barreras';
    $d=get_barreras(); 
    if ($d=="") {$d=$t;}
    $o='datos';
    $days=fechas_app('psicologia');
    $c=[];
    $c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
    $c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
    $c[]=new cmp('idpersona','n','20',$d['idpersona'],$w.' '.$o,'N° Identificación','idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('tipodoc','s','3',$d['tipodoc'],$w.' '.$o,'Tipo Identificación','tipodoc',null,'',false,false,'','col-2');
	$c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-3');
	$c[]=new cmp('fechanacimiento','d','10',$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-2');
    $c[]=new cmp('edad','n','3',$d['edad'],$w.' '.$o,'edad','edad',null,'',true,false,'','col-1');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");

    $o='geograficas';
    $c[]=new cmp($o,'e',null,'Barreras Geográficas',$w);
    $c[]=new cmp('geo_centro_cercano','s','3','',$w.' '.$o,'El centro de atención en salud está ubicado en la misma localidad de su vivienda?','rta',null,null,true,true,'','col-10');
    $c[]=new cmp('geo_dificultad_salir','s','3','',$w.' '.$o,'Tiene dificultades para salir de su vivienda para acudir a los servicios de salud?','rta',null,null,true,true,'','col-10');
    $c[]=new cmp('geo_dificultad_acudir','s','3','',$w.' '.$o,'Dificultades para acudir a los servicios de salud','geo_dificultad_acudir',null,null,true,true,'','col-10');

    $o='fisicas';
    $c[]=new cmp($o,'e',null,'Barreras Físicas',$w);
    $c[]=new cmp('fis_limitacion_movilidad','s','3','',$w.' '.$o,'Tiene alguna limitación Física para movilizarse?','rta',null,null,true,true,'','col-10');
    $c[]=new cmp('fis_usa_dispositivo_asistencia','s','3','',$w.' '.$o,'Utiliza algún dispositivo de asistencia para la movilidad?','rta',null,null,true,true,'','col-10');
    $c[]=new cmp('fis_dispositivo_asistencia_cual','s','3','',$w.' '.$o,'Cual','fis_dispositivo_asistencia_cual',null,null,true,true,'','col-10');

    $o='economicas';
    $c[]=new cmp($o,'e',null,'Barreras Económicas',$w);
    $c[]=new cmp('eco_limitacion_costovida','s','3','',$w.' '.$o,'Presenta limitaciones en el costo de vida?','rta',null,null,true,true,'','col-10');
    $c[]=new cmp('eco_limitacion_cual','s','3','',$w.' '.$o,'Cual limitación presenta','eco_limitacion_cual',null,null,true,true,'','col-10');
    $c[]=new cmp('eco_dependencia_terceros','s','3','',$w.' '.$o,'Depende económicamente de terceros?','rta',null,null,true,true,'','col-10');

    $o='administrativas';
    $c[]=new cmp($o,'e',null,'Barreras Administrativas',$w);
    $c[]=new cmp('adm_estado_afiliacion','s','3','',$w.' '.$o,'Estado de afiliación al SS en estos momentos','adm_estado_afiliacion',null,null,true,true,'','col-10');
    $c[]=new cmp('adm_demora_autorizacion','s','3','',$w.' '.$o,'Ha presentado demoras al tramitar autorizaciones','rta',null,null,true,true,'','col-10');
    $c[]=new cmp('adm_demora_autorizacion_desc','a','255','',$w.' '.$o,'Cual','adm_demora_autorizacion_desc',null,null,true,true,'','col-10');
    $c[]=new cmp('adm_dificultad_citas','s','3','',$w.' '.$o,'Ha presentado dificultades en la asignación de citas?','rta',null,null,true,true,'','col-10');
    $c[]=new cmp('adm_dificultad_citas_cual','s','3','',$w.' '.$o,'Cual','adm_dificultad_citas_cual',null,null,true,true,'','col-10');

    $o='psicosociales';
    $c[]=new cmp($o,'e',null,'Barreras Psicosociales',$w);
    $c[]=new cmp('psi_trastorno_emocional','s','3','',$w.' '.$o,'Ha presentado transtornos emocionales?','rta',null,null,true,true,'','col-10');
    $c[]=new cmp('psi_trastorno_emocional_tipo','t','255','',$w.' '.$o,'Tipo de transtorno','psi_trastorno_emocional_tipo',null,null,true,true,'','col-10');
    
    $o='culturales';
    $c[]=new cmp($o,'e',null,'Barreras Culturales',$w);
    $c[]=new cmp('cul_discriminacion','s','3','',$w.' '.$o,'Ha percibido discriminación - actitud negativa o desconfianza por parte de los demás frente a la enfermedad?','rta',null,null,true,true,'','col-10');
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    return $rta;
}

function get_barreras(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		// print_r($_POST);
		$sql="SELECT P.idpeople,P.idpersona idpersona,P.tipo_doc tipodoc,
        concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,P.fecha_nacimiento fechanacimiento,
        TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS edad
		FROM person P
		WHERE P.idpeople ='{$id[0]}'";
		// echo $sql; 
		$info=datos_mysql($sql);
				return $info['responseResult'][0];
		}
	} 

function gra_barreras(){
    $id=divide($_POST['id']);
    $sql = "INSERT INTO barreras_acceso_salud (
        idpeople, fecha_toma, geo_centro_cercano, geo_dificultad_salir, geo_dificultad_acudir, geo_dificultad_acudir_cual,
        fis_limitacion_movilidad, fis_usa_dispositivo_asistencia, fis_dispositivo_asistencia_cual,
        eco_limitacion_costovida, eco_limitacion_cual, eco_dependencia_terceros,
        adm_estado_afiliacion, adm_demora_autorizacion, adm_demora_autorizacion_desc,
        adm_dificultad_citas, adm_dificultad_citas_cual,
        psi_trastorno_emocional, psi_trastorno_emocional_tipo,
        cul_discriminacion, usu_creo, fecha_create, estado
    ) VALUES (
        ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
    )";
    $params = [
        ['type' => 'i', 'value' => $id[0]],
        ['type' => 's', 'value' => post_or_null('fecha_toma')],
        ['type' => 's', 'value' => post_or_null('geo_centro_cercano')],
        ['type' => 's', 'value' => post_or_null('geo_dificultad_salir')],
        ['type' => 's', 'value' => post_or_null('geo_dificultad_acudir')],
        ['type' => 's', 'value' => post_or_null('geo_dificultad_acudir_cual')],
        ['type' => 's', 'value' => post_or_null('fis_limitacion_movilidad')],
        ['type' => 's', 'value' => post_or_null('fis_usa_dispositivo_asistencia')],
        ['type' => 's', 'value' => post_or_null('fis_dispositivo_asistencia_cual')],
        ['type' => 's', 'value' => post_or_null('eco_limitacion_costovida')],
        ['type' => 's', 'value' => post_or_null('eco_limitacion_cual')],
        ['type' => 's', 'value' => post_or_null('eco_dependencia_terceros')],
        ['type' => 's', 'value' => post_or_null('adm_estado_afiliacion')],
        ['type' => 's', 'value' => post_or_null('adm_demora_autorizacion')],
        ['type' => 's', 'value' => post_or_null('adm_demora_autorizacion_desc')],
        ['type' => 's', 'value' => post_or_null('adm_dificultad_citas')],
        ['type' => 's', 'value' => post_or_null('adm_dificultad_citas_cual')],
        ['type' => 's', 'value' => post_or_null('psi_trastorno_emocional')],
        ['type' => 's', 'value' => post_or_null('psi_trastorno_emocional_tipo')],
        ['type' => 's', 'value' => post_or_null('cul_discriminacion')],
        ['type' => 's', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => date('Y-m-d H:i:s')],
        ['type' => 's', 'value' => 'A']
    ];
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

function post_or_null($key) {
  return isset($_POST[$key]) && $_POST[$key] !== '' ? $_POST[$key] : null;
}
// Opciones para selects
function opc_rta($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}
function opc_tipodoc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_sexo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}
function opc_geo_dificultad_acudir($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=309 and estado='A' ORDER BY 1",$id);
}
function opc_fis_dispositivo_asistencia_cual($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=302 and estado='A' ORDER BY 1",$id);
}
function opc_eco_limitacion_cual($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=303 and estado='A' ORDER BY 1",$id);
}
function opc_adm_estado_afiliacion($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=304 and estado='A' ORDER BY 1",$id);
}
function opc_adm_dificultad_citas_cual($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=305 and estado='A' ORDER BY 1",$id);
}
/* function opc_geo_dificultad_acudir_cual($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=301 and estado='A' ORDER BY 1",$id);
}




function opc_psi_trastorno_emocional_tipo($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=306 and estado='A' ORDER BY 1",$id);
}
 function opc_geo_centro_cercano($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=307 and estado='A' ORDER BY 1",$id);
}
function opc_geo_dificultad_salir($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=308 and estado='A' ORDER BY 1",$id);
}

function opc_fis_limitacion_movilidad($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=310 and estado='A' ORDER BY 1",$id);
}
function opc_fis_usa_dispositivo_asistencia($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=311 and estado='A' ORDER BY 1",$id);
}
function opc_fis_usa_dispositivo_asistencia($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=311 and estado='A' ORDER BY 1",$id);
} */ 
// Menú y encabezado
function focus_barreras(){
    return 'barreras';
}
function men_barreras(){
    $rta=cap_menus('barreras','pro');
    return $rta;
}
function cap_menus($a,$b='cap',$con='con') {
    $rta = "";
    $acc=rol($a);
    if ($a=='barreras' && isset($acc['crear']) && $acc['crear']=='SI'){  
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    return $rta;
}

// Formato para acciones en listado
function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
    $rta=$c[$d];
    if ($a=='barreras' && $b=='acciones'){
        $rta="<nav class='menu right'>";
        $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('barreras','pro',event,'','../tamizajes/barreras.php',7,'barreras');\"></li>";
        $rta.="</nav>";
    }
    return $rta;
}

function bgcolor($a,$c,$f='c'){
    $rta="";
    return $rta;
}
?>
