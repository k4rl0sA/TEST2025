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
    if (is_array($rta)) json_encode($rta);
	else echo $rta;
  }
}

function lis_tamoms(){
	if (!empty($_POST['fidentificacion']) || !empty($_POST['ffam'])) {
		$info=datos_mysql("SELECT COUNT(*) total from hog_tam_oms O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		where ".whe_tamoms());
		$total=$info['responseResult'][0]['total'];
		$regxPag=12;
		$pag=(isset($_POST['pag-tamoms']))? (intval($_POST['pag-tamoms'])-1)* $regxPag:0;

		$sql="SELECT O.idpeople ACCIONES,idoms 'Cod Registro',V.id_fam 'Cod Familia',P.idpersona Documento,FN_CATALOGODESC(1,P.tipo_doc) 'Tipo de Documento',CONCAT_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) Nombres,`puntaje` Puntaje,`descripcion` Descripcion, U.nombre Creo,U.subred,U.perfil perfil
	FROM hog_tam_oms O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		WHERE ";
	$sql.=whe_tamoms();
	$sql.=" ORDER BY O.fecha_create DESC";
	//echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"tamoms",$regxPag);
	}else{
		return "<div class='error' style='padding: 12px; background-color:#00a3ffa6;color: white; border-radius: 25px; z-index:100; top:0;text-transform:none'>
                <strong style='text-transform:uppercase'>NOTA:</strong>Por favor Ingrese el numero de documento ó familia a Consultar
                <span style='margin-left: 15px; color: white; font-weight: bold; float: right; font-size: 22px; line-height: 20px; cursor: pointer; transition: 0.3s;' onclick=\"this.parentElement.style.display='none';\">&times;</span>
            </div>";
	}
}

function lis_oms(){
	$id=divide($_POST['id']);
	$sql="SELECT idoms ACCIONES,
	idoms 'Cod Registro',fecha_toma,descripcion,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM hog_tam_oms A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"oms-lis",5);

}

function whe_tamoms() {
	$sql = '1';
    if (!empty($_POST['fidentificacion'])) {
        $sql .= " AND P.idpersona = '".$_POST['fidentificacion']."'";
    }
    if (!empty($_POST['ffam'])) {
        $sql .= " AND V.id_fam = '".$_POST['ffam']."'";
    }
    return $sql;
}


function cmp_tamoms(){
	$rta="<div class='encabezado oms'>TABLA oms</div><div class='contenido' id='oms-lis'>".lis_oms()."</div></div>";
	$a=['idoms'=>'','diabetes'=>'','fuma'=>'','tas'=>'','puntaje'=>'','descripcion'=>''];//,'nombre'=>'','fechanacimiento'=>'','edad'=>''
	$p=['idoms'=>'','idpersona'=>'','tipo_doc'=>'','nombre'=>'','sexo'=>'','fechanacimiento'=>'','edad'=>''];
	$w='tamoms';
	$d=get_toms();
	// var_dump($d);
	if (!isset($d['idoms'])) {
		$d = array_merge($d,$a);
	}
	$o='datos';
    $key='oms';
	$days=fechas_app('vivienda');//CAMBIO DE ADD ESTA LINEA
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('idpersona','t','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','idpersona',null,'',false,false,'','col-3');
	$c[]=new cmp('tipodoc','s','3',$d['tipo_doc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-3',"getDatForm('oms','person','datos');setTimeout(function() {hiddxTamiz('edad', 'pruoms',17);}, 1000);");
	$c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('sexo','s','3',$d['sexo'],$w.' '.$o,'Sexo','sexo',null,'',false,false,'','col-2');
	$c[]=new cmp('fechanacimiento','d','10',$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-3');
    $c[]=new cmp('edad','n','3',$d['edad'],$w.' '.$o,'edad en Años','edad',null,'',true,false,'','col-2');

	$o='pruoms';
 	$c[]=new cmp($o,'e',null,'PRUEBA OMS Riesgo Cardiovascular',$w);
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
 	$c[]=new cmp('fuma','s',2,'',$w.' '.$o,'Fuma','fuma',null,null,true,true,'','col-25');
	$c[]=new cmp('diabetes','s',3,'',$w.' '.$o,'Tiene Diabetes','diabetes',null,null,true,true,'','col-3');
	$c[]=new cmp('tas','n',3,'',$w.' '.$o,'Presión Sistólica (mmHg)','tas',null,null,true,true,'','col-2');

	$o='totalresul';
	$c[]=new cmp($o,'e',null,'TOTAL',$w);
	$c[]=new cmp('puntaje','t','10','',$w.' '.$o,'Puntaje','puntaje',null,null,false,false,'','col-5');
	$c[]=new cmp('descripcion','t','50','',$w.' '.$o,'Descripcion','descripcion',null,null,false,false,'','col-5');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();

	return $rta;

   }

   function get_toms(){//CAMBIO function nueva
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		// print_r($_POST);
		$sql="SELECT idoms,O.idpeople,diabetes,fuma,tas,puntaje,descripcion,
		O.estado,P.idpersona,P.tipo_doc,P.sexo,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,P.fecha_nacimiento fechanacimiento,YEAR(CURDATE())-YEAR(P.fecha_nacimiento) edad
		FROM `hog_tam_oms` O
		LEFT JOIN person P ON O.idpeople = P.idpeople
			WHERE P.idpeople ='{$id[0]}'";
		// echo $sql;
		$info=datos_mysql($sql);
			if (!$info['responseResult']) {
				$sql="SELECT P.idpersona,P.tipo_doc,P.sexo,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,
				P.fecha_nacimiento fechanacimiento,
				YEAR(CURDATE())-YEAR(P.fecha_nacimiento) edad
				FROM person P
				WHERE P.idpeople ='{$id[0]}'";
				// echo $sql;
				$info=datos_mysql($sql);
			return $info['responseResult'][0];
			}
		return $info['responseResult'][0];
	}
}


function get_tamoms() { // NUEVA FUNCIÓN ADAPTADA AL TAMIZAJE
    if (empty($_REQUEST['id'])) {
        return "";
    }

    $id = divide($_REQUEST['id']);
    $sql = "SELECT A.idoms, P.idpersona, P.tipo_doc,
            concat_ws(' ', P.nombre1, P.nombre2, P.apellido1, P.apellido2) AS nombre,
            P.fecha_nacimiento AS fechanacimiento,
            YEAR(CURDATE()) - YEAR(P.fecha_nacimiento) AS edad,
            A.fecha_toma, A.diabetes, A.fuma, A.tas, A.puntaje, A.descripcion
            FROM hog_tam_oms A
            LEFT JOIN person P ON A.idpeople = P.idpeople
            WHERE A.idoms = '{$id[0]}'";

    $info = datos_mysql($sql);
    $data = $info['responseResult'][0];

    // Datos básicos
    $baseData = [
        'idoms' => $data['idoms'],
        'idpersona' => $data['idpersona'],
        'tipo_doc' => $data['tipo_doc'],
        'nombre' => $data['nombre'],
        'fechanacimiento' => $data['fechanacimiento'],
        'edad' => $data['edad'],
        'fecha_toma' => $data['fecha_toma'] ?? null, // Valor por defecto null si no está definido
    ];
    // Campos adicionales específicos del tamizaje 
    $edadCampos = [
        'diabetes', 'fuma', 'tas', 'puntaje', 'descripcion'
    ];
    foreach ($edadCampos as $campo) {
        $baseData[$campo] = $data[$campo];
    }
    return json_encode($baseData);
}

function get_person(){
	// print_r($_POST);
	$id=divide($_POST['id']);
	$sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,sexo ,fecha_nacimiento,TIMESTAMPDIFF(YEAR,fecha_nacimiento, CURDATE()) edad
from person P
WHERE idpersona='".$id[0]."' AND tipo_doc=upper('".$id[1]."');";
	// return json_encode($sql);
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode (new stdClass);
	}
return json_encode($info['responseResult'][0]);
}

function focus_tamoms(){
	return 'tamoms';
   }

function men_tamoms(){
	$rta=cap_menus('tamoms','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = "";
	$acc=rol($a);
	if ($a=='tamoms'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
	return $rta;
  }

function gra_tamoms(){
	$id=divide($_POST['id']);
if(count($id)!==2){
	return "No es posible actualizar el tamizaje";
}else{
// var_dump($_POST);

$diab = ($_POST['diabetes']==1) ? 'SI' : 'NO';
$fuma = ($_POST['fuma']==1) ? 'SI' : 'NO';
$sql2="SELECT CASE
	WHEN {$_POST['edad']} < 50 THEN 40
	WHEN {$_POST['edad']} >= 50 AND {$_POST['edad']} < 60 THEN 50
	WHEN {$_POST['edad']} >= 60 AND {$_POST['edad']} < 70 THEN 60
	ELSE 70
END anios,
 CASE
	WHEN  {$_POST['tas']}< 140 THEN 120
	WHEN  {$_POST['tas']}>= 140 AND {$_POST['tas']} < 160 THEN 140
	WHEN  {$_POST['tas']}>= 160 AND {$_POST['tas']} < 180 THEN 160
	ELSE 180
END ten;";
$info=datos_mysql($sql2);
$año=$info['responseResult'][0]['anios'];
$ten=$info['responseResult'][0]['ten'];


$sql1="SELECT puntaje,clasificacion from oms
where diabetes='{$diab}' AND sexo='{$_POST['sexo']}' AND fuma='{$fuma}'
AND edad=$año AND tas=$ten;";

$info=datos_mysql($sql1);
$suma_oms=$info['responseResult'][0]['puntaje'];
$des=$info['responseResult'][0]['clasificacion'];



	
		$sql = "INSERT INTO hog_tam_oms VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";

		$params = [
			['type' => 'i', 'value' => NULL],
			['type' => 'i', 'value' => $id[0]],
			['type' => 's', 'value' => $_POST['fecha_toma']],
			['type' => 's', 'value' => $_POST['diabetes']],
			['type' => 's', 'value' => $_POST['fuma']],
			['type' => 's', 'value' => $_POST['tas']],
			['type' => 's', 'value' => $suma_oms],
			['type' => 's', 'value' => $des],
			['type' => 's', 'value' => $_SESSION['us_sds']],
			['type' => 's', 'value' => NULL],
			['type' => 's', 'value' => NULL],
			['type' => 's', 'value' => 'A']
		];
		echo($suma_oms.' ');
		 return $rta = mysql_prepd($sql, $params);

	}

}


	function opc_tipodoc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
	}
	function opc_sexo($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
	}
	function opc_fuma($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
	}
	function opc_diabetes($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
	}



	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
		   if ($a=='tamoms' && $b=='acciones'){
			$rta="<nav class='menu right'>";
			$rta.="<li title='Ver'><i class='fa-solid fa-eye ico' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getDataFetch,500,'find',,'person','datos',event,this,'../findrisc/lib.php',['puntaje','descripcion']);\"></i></li>";
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamoms','pro',event,'','lib.php',7,'tamoms');setTimeout(hiddxedad,300,'edad','prufin');\"></li>";  //act_lista(f,this);
			}
		return $rta;
	   }

	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }
