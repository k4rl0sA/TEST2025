<?php
 require_once '../libs/gestion.php';
ini_set('display_errors','1');
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



function lis_sesigcole(){
	if (!empty($_POST['fpred'])) {
		$total = "SELECT COUNT(DISTINCT sc.id_cole) AS total  FROM hog_sescole sc
          LEFT JOIN usuarios u ON sc.usu_create = u.id_usuario
            LEFT JOIN hog_geo hg ON sc.idpre = hg.idgeo 
          WHERE hg.subred = (SELECT subred FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}') 
              " . whe_sesigcole();
        $info = datos_mysql($total);
        $total = $info['responseResult'][0]['total'];
        $regxPag = 5;
        $pag = (isset($_POST['pag-sesigcole'])) ? ($_POST['pag-sesigcole'] - 1) * $regxPag : 0;

  $sql="SELECT DISTINCT
            sc.id_cole AS 'ACCIONES',
            sc.fecha,
            FN_CATALOGODESC(239, sc.tipo_activ),
            sc.lugar,
            sc.equipo,
            
            u.nombre AS Creo,
            sc.fecha_create AS Creado,
            sc.estado
        FROM hog_sescole sc
        LEFT JOIN usuarios u ON sc.usu_create = u.id_usuario
      LEFT JOIN hog_geo hg ON sc.idpre = hg.idgeo 
        WHERE hg.subred = (SELECT subred FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}')
          " . whe_sesigcole() . " 
        GROUP BY sc.id_cole, sc.fecha, sc.tipo_activ, sc.lugar, sc.equipo,  u.nombre, sc.fecha_create, sc.estado 
        LIMIT $pag, $regxPag";
		// var_dump($total);
//var_dump($sql);
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"sesigcole",$regxPag);
	
}else{
	return "<div class='error' style='padding: 12px; background-color:#00a3ffa6;color: white; border-radius: 25px; z-index:100; top:0;text-transform:none'>
			<strong style='text-transform:uppercase'>NOTA:</strong>Por favor Ingrese el número del Predio a Consultar
			<span style='margin-left: 15px; color: white; font-weight: bold; float: right; font-size: 22px; line-height: 20px; cursor: pointer; transition: 0.3s;' onclick=\"this.parentElement.style.display='none';\">&times;</span>
		</div>";
}
}		

function whe_sesigcole() {
	$sql = "";
	if (!empty($_POST['fpred']) && $_POST['fdigita']) {
		$sql .= " AND sc.idpre = '" . $_POST['fpred'] . "' AND sc.usu_create ='" . $_POST['fdigita'] . "'";
	}else{
		$sql .="AND sc.idpre ='0'";
	} 
	return $sql;
}

function focus_sesigcole(){
 return 'sesigcole';
}

function men_sesigcole(){
 $rta=cap_menus('sesigcole','pro');
 return $rta;
} 

function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  $acc=rol($a);
  if ($a=='sesigcole' && isset($acc['crear']) && $acc['crear']=='SI'){  
	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
  	$rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
  }
  return $rta;
}

function cmp_sesigcole(){
	$rta="";
	$t=['id'=>'','fecha'=>'','tipo_activ'=>'','lugar'=>'','jorna'=>'','equi'=>'','tematica1'=>'','des_temati1'=>'','tematica2'=>'','des_temati2'=>'','tematica3'=>'','des_temati3'=>'','tematica4'=>'','des_temati4'=>'','tematica5'=>'','des_temati5'=>'','tematica6'=>'','des_temati6'=>'','tematica7'=>'','des_temati7'=>'','tematica8'=>'','des_temati8'=>''];
	$d=get_sesigcole();
	if ($d==""){$d=$t;}
	//var_dump($_POST);
	$id=divide($_POST['id']);
    $w="sesigcole";
	$o='infbas';
	// var_dump($d['equi']);
	$equi = $d['equi'];
	$eq = str_replace("-", ",", $equi);
	// var_dump($eq);
	$days=fechas_app('vivienda');
	$o='Secgi';
	$enb = ($_POST['id']=='0') ? true : false ;
	$c[]=new cmp($o,'e',null,'SESIONES GRUPALES Y COLECTIVAS',$w);
	$c[]=new cmp('id','h','20',$_POST['fpred'].'_'.$_POST['id'],$w.' '.$o,'','',null,null,true,false,'','col-1');
	$c[]=new cmp('fecha_int','d','10',$d['fecha'],$w.' '.$o,'fecha_Intervencion','fecha_int',null,null,true,$enb,'','col-15',"validDate(this,$days,0);");
	$c[]=new cmp('activi','s','15',$d['tipo_activ'],$w.' '.$o,'Tipo de Actividad','fm1',null,null,true,$enb,'','col-25');
	$c[]=new cmp('luga','t','100',$d['lugar'],$w.' '.$o,'Lugar','rta',null,null,true,true,'','col-6',"fieldsValue('agen_intra','aIM','1',true);");
	$c[]=new cmp('jorna','s','3',$d['jorna'],$w.' '.$o,'jornada','jorna',null,null,true,true,'','col-5',"fieldsValue('agen_intra','aIM','1',true);");
	$c[]=new cmp('equi','m',3,$eq,$w.' '.$o,'Equipo','equi',null,null,true,true,'','col-5',"fieldsValue('agen_intra','aIM','1',true);");
	$c[]=new cmp('temati1','s',3,$d['tematica1'],$w.' '.$o,'tematica 1','temati1',null,null,true,true,'','col-15',"selectDepend('temati1','desc_temati1');");
	$c[]=new cmp('desc_temati1','s','3',$d['des_temati1'],$w.' '.$o,'Descripcion tematica 1','desc_temati1',null,null,true,true,'','col-35');
    $c[]=new cmp('temati2','s','3',$d['tematica2'],$w.' '.$o,'tematica 2','temati2',null,null,false,true,'','col-15',"selectDepend('temati2','desc_temati2');");
    $c[]=new cmp('desc_temati2','s','3',$d['des_temati2'],$w.' '.$o,'Descripcion tematica 2','desc_temati2',null,null,false,true,'','col-35');
    $c[]=new cmp('temati3','s','3',$d['tematica3'],$w.' '.$o,'tematica 3','temati3',null,null,false,true,'','col-15',"selectDepend('temati3','desc_temati3');");
    $c[]=new cmp('desc_temati3','s','3',$d['des_temati3'],$w.' '.$o,'Descripcion tematica 3','desc_temati3',null,null,false,true,'','col-35');
    $c[]=new cmp('temati4','s','3',$d['tematica4'],$w.' '.$o,'tematica 4','temati4',null,null,false,true,'','col-15',"selectDepend('temati4','desc_temati4');");
    $c[]=new cmp('desc_temati4','s','3',$d['des_temati4'],$w.' '.$o,'Descripcion tematica 4','desc_temati4',null,null,false,true,'','col-35');
	$c[]=new cmp('temati5','s','3',$d['tematica5'],$w.' '.$o,'tematica 5','temati5',null,null,false,true,'','col-15',"selectDepend('temati5','desc_temati5');");
    $c[]=new cmp('desc_temati5','s','3',$d['des_temati5'],$w.' '.$o,'Descripcion tematica 5','desc_temati5',null,null,false,true,'','col-35');
	$c[]=new cmp('temati6','s','3',$d['tematica6'],$w.' '.$o,'tematica 6','temati6',null,null,false,true,'','col-15',"selectDepend('temati6','desc_temati6');");
    $c[]=new cmp('desc_temati6','s','3',$d['des_temati6'],$w.' '.$o,'Descripcion tematica 6','desc_temati6',null,null,false,true,'','col-35');
	$c[]=new cmp('temati7','s','3',$d['tematica7'],$w.' '.$o,'tematica 7','temati7',null,null,false,true,'','col-15',"selectDepend('temati7','desc_temati7');");
    $c[]=new cmp('desc_temati7','s','3',$d['des_temati7'],$w.' '.$o,'Descripcion tematica 7','desc_temati7',null,null,false,true,'','col-35');
	$c[]=new cmp('temati8','s','3',$d['tematica8'],$w.' '.$o,'tematica 8','temati8',null,null,false,true,'','col-15',"selectDepend('temati8','desc_temati8');");
    $c[]=new cmp('desc_temati8','s','3',$d['des_temati8'],$w.' '.$o,'Descripcion tematica 8','desc_temati8',null,null,false,true,'','col-35');

	// $c[]=new cmp('medico','s',15,$d,$w.' der '.$o,'Asignado','medico',null,null,false,false,'','col-5');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_sesigcole(){
	$id=divide($_REQUEST['id']);
	if($_REQUEST['id']==''){
		return "";
	}else{
		$sql = "select id_cole id,idpre,fecha,tipo_activ,lugar,jornada jorna,equipo equi,tematica1,des_temati1,tematica2,des_temati2,tematica3,des_temati3,tematica4,des_temati4,tematica5,des_temati5,tematica6,des_temati6,tematica7,des_temati7,tematica8,des_temati8,usu_create,fecha_create,usu_update,fecha_update,estado 
		from hog_sescole
		WHERE id_cole ='{$id[0]}'";
		// var_dump($sql);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
		return $info['responseResult'][0];
	}
}

function gra_sesigcole(){
	$id=divide($_POST['id']);
	// var_dump($_POST['fequi']);
	$equi = isset($_POST['fequi'])?(is_array($_POST['fequi'])?implode("-", $_POST['fequi']):implode("-",array_map('trim',explode(",",str_replace("'","",$_POST['fequi']))))):'';
	if ($id[0]!='' && $id[1]==0) {
		$sql = "INSERT INTO hog_sescole VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";
		$params = [
			['type' => 'i', 'value' => NULL],
			['type' => 'i', 'value' => $id[0]],
			['type' => 's', 'value' => $_POST['fecha_int']],
			['type' => 'i', 'value' => $_POST['activi']],
			['type' => 's', 'value' => $_POST['luga']],
			['type' => 's', 'value' => $_POST['jorna']],
			['type' => 's', 'value' => $equi],
			['type' => 'i', 'value' => $_POST['temati1']],
			['type' => 'i', 'value' => $_POST['desc_temati1']],
			['type' => 'i', 'value' => $_POST['temati2']],
			['type' => 'i', 'value' => $_POST['desc_temati2']],
			['type' => 'i', 'value' => $_POST['temati3']],
			['type' => 'i', 'value' => $_POST['desc_temati3']],
			['type' => 'i', 'value' => $_POST['temati4']],
			['type' => 'i', 'value' => $_POST['desc_temati4']],
			['type' => 'i', 'value' => $_POST['temati5']],
			['type' => 'i', 'value' => $_POST['desc_temati5']],
			['type' => 'i', 'value' => $_POST['temati6']],
			['type' => 'i', 'value' => $_POST['desc_temati6']],
			['type' => 'i', 'value' => $_POST['temati7']],
			['type' => 'i', 'value' => $_POST['desc_temati7']],
			['type' => 'i', 'value' => $_POST['temati8']],
			['type' => 'i', 'value' => $_POST['desc_temati8']],
			['type' => 'i', 'value' => $_SESSION['us_sds']],
			['type' => 's', 'value' => NULL],
			['type' => 's', 'value' => NULL],
			['type' => 's', 'value' => 'A']
		];
		// var_dump($sql);
		return mysql_prepd($sql, $params);
	}else if(count($id)==2&& $id[1]!=0){
		$sql = "UPDATE hog_sescole SET lugar=?,equipo=?,tematica1=?,des_temati1=?,tematica2=?,des_temati2=?,tematica3=?,des_temati3=?,tematica4=?,des_temati4=?,tematica5=?,des_temati5=?,tematica6=?,
		des_temati6=?,tematica7=?,des_temati7=?,tematica8=?,des_temati8=?,usu_update=?,fecha_update=DATE_SUB(NOW(),INTERVAL 5 HOUR) WHERE id_cole=?";
		$params = [
			['type' => 's', 'value' => $_POST['luga']],
			['type' => 's', 'value' => $equi],
			['type' => 'i', 'value' => $_POST['temati1']],
			['type' => 'i', 'value' => $_POST['desc_temati1']],
			['type' => 'i', 'value' => $_POST['temati2']],
			['type' => 'i', 'value' => $_POST['desc_temati2']],
			['type' => 'i', 'value' => $_POST['temati3']],
			['type' => 'i', 'value' => $_POST['desc_temati3']],
			['type' => 'i', 'value' => $_POST['temati4']],
			['type' => 'i', 'value' => $_POST['desc_temati4']],
			['type' => 'i', 'value' => $_POST['temati5']],
			['type' => 'i', 'value' => $_POST['desc_temati5']],
			['type' => 'i', 'value' => $_POST['temati6']],
			['type' => 'i', 'value' => $_POST['desc_temati6']],
			['type' => 'i', 'value' => $_POST['temati7']],
			['type' => 'i', 'value' => $_POST['desc_temati7']],
			['type' => 'i', 'value' => $_POST['temati8']],
			['type' => 'i', 'value' => $_POST['desc_temati8']],
			['type' => 'i', 'value' => $_SESSION['us_sds']],
			['type' => 'i', 'value' => $id[1]]
		];
		// var_dump($sql);
		return mysql_prepd($sql, $params);
	}else{
		return "Error : Debe ingresar el codigo del predio en la zona de filtros para poder generara la Sesión";
	}
	// return $rta;
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

/* function opc_equi($id=''){
	return opc_sql("SELECT id_usuario,nombre FROM usuarios u where equipo=(select equipo where id_usuario=$_SESSION['us_sds']) AND estado='A'", $id);
}
 */

 function opc_equi($id=''){
	return opc_sql("SELECT id_usuario, nombre FROM usuarios WHERE subred=(select subred from usuarios where id_usuario=".$_SESSION['us_sds'].") AND estado='A' ORDER BY LPAD(nombre, 2, '0')", $id);
}

function opc_jorna($id=''){
	return opc_sql("SELECT idcatadeta, descripcion FROM `catadeta` WHERE idcatalogo=242 AND estado='A' ORDER BY LPAD(idcatadeta, 2, '0')", $id);
}

function opc_temati1desc_temati1($id=''){
	if($_REQUEST['id']!=''){
				$id=divide($_REQUEST['id']);
				$sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='238' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
				$info=datos_mysql($sql);
				return json_encode($info['responseResult']);
		}
}
function opc_temati2desc_temati2($id=''){
	if($_REQUEST['id']!=''){
				$id=divide($_REQUEST['id']);
				$sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='238' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
				$info=datos_mysql($sql);
				return json_encode($info['responseResult']);
		}
}function opc_temati3desc_temati3($id=''){
	if($_REQUEST['id']!=''){
				$id=divide($_REQUEST['id']);
				$sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='238' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
				$info=datos_mysql($sql);
				return json_encode($info['responseResult']);
		}
}function opc_temati4desc_temati4($id=''){
	if($_REQUEST['id']!=''){
				$id=divide($_REQUEST['id']);
				$sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='238' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
				$info=datos_mysql($sql);
				return json_encode($info['responseResult']);
		}
}function opc_temati5desc_temati5($id=''){
	if($_REQUEST['id']!=''){
				$id=divide($_REQUEST['id']);
				$sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='238' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
				$info=datos_mysql($sql);
				return json_encode($info['responseResult']);
		}
}function opc_temati6desc_temati6($id=''){
	if($_REQUEST['id']!=''){
				$id=divide($_REQUEST['id']);
				$sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='238' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
				$info=datos_mysql($sql);
				return json_encode($info['responseResult']);
		}
}function opc_temati7desc_temati7($id=''){
	if($_REQUEST['id']!=''){
				$id=divide($_REQUEST['id']);
				$sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='238' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
				$info=datos_mysql($sql);
				return json_encode($info['responseResult']);
		}
}function opc_temati8desc_temati8($id=''){
	if($_REQUEST['id']!=''){
				$id=divide($_REQUEST['id']);
				$sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='238' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
				$info=datos_mysql($sql);
				return json_encode($info['responseResult']);
		}
}

function opc_tipose($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function opc_fm1($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=239 and estado='A' ORDER BY 1",$id);
}

function opc_temati1($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=237 and estado='A' ORDER BY 1",$id);
}

function opc_desc_temati1($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=238 and estado='A' ORDER BY 1",$id);
}

function opc_temati2($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=237 and estado='A' ORDER BY 1",$id);
}

function opc_desc_temati2($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=238 and estado='A' ORDER BY 1",$id);
}

function opc_temati3($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=237 and estado='A' ORDER BY 1",$id);
}

function opc_desc_temati3($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=238 and estado='A' ORDER BY 1",$id);
}

function opc_temati4($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=237 and estado='A' ORDER BY 1",$id);
}

function opc_desc_temati4($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=238 and estado='A' ORDER BY 1",$id);
}

function opc_temati5($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=237 and estado='A' ORDER BY 1",$id);
}

function opc_desc_temati5($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=238 and estado='A' ORDER BY 1",$id);
}

function opc_temati6($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=237 and estado='A' ORDER BY 1",$id);
}

function opc_desc_temati6($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=238 and estado='A' ORDER BY 1",$id);
}

function opc_temati7($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=237 and estado='A' ORDER BY 1",$id);
}

function opc_desc_temati7($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=238 and estado='A' ORDER BY 1",$id);
}

function opc_temati8($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=237 and estado='A' ORDER BY 1",$id);
}

function opc_desc_temati8($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=238 and estado='A' ORDER BY 1",$id);
}

function opc_rta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=4 and estado='A' ORDER BY 1",$id);
}

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);			
 $rta=$c[$d];
// $rta=iconv('U	TF-8','ISO-8859-1',$rta);
// var_dump($c);
	if ($a=='sesigcole' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class='menu right'>";	
		$rta.="<li class='icono asigna1' title='Crear Sesion' id='".$c['ACCIONES']."' Onclick=\"mostrar('sesigcole','pro',event,'','lib.php',7);\"></li>";	
		$rta.="<li title='Crear Integrantes de la Actividad' Onclick=\"mostrar('sespers','pro',event,'','sesiperson.php',7,'sespers');\"><i class='fa-solid fa-person-circle-plus ico' id='".$c['ACCIONES']."'></i></li>";
	}
	
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
