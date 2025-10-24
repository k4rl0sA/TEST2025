<?php
require_once "../libs/gestion.php";
ini_set('display_errors','0');
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

function lis_th(){
$info = datos_mysql("SELECT COUNT(*) total FROM th T WHERE " . whe_th());
    $total = $info['responseResult'][0]['total'];
    $regxPag = 20;
    $pag = (isset($_POST['pag-th'])) ? ($_POST['pag-th'] - 1) * $regxPag : 0;

    $sql = "SELECT T.tipo_doc AS 'Tipo Documento', T.n_documento AS 'N° Documento', concat (T.nombre1, ' ', T.nombre2, ' ', T.apellido1, ' ', T.apellido2) AS 'Nombres y Apellidos del Colaborador', T.n_contacto AS 'N° Contacto', T.estado AS 'Estado Usuario' 
	        FROM th T  
            WHERE " . whe_th();    
    $sql .= " ORDER BY T.fecha_create";
    $sql .= ' LIMIT ' . $pag . ',' . $regxPag;
	/* var_dump($sql); */
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"th",$regxPag);
}

function whe_th() {
    $sql2 = " SELECT subred FROM usuarios where id_usuario='" . $_SESSION['us_sds'] . "'";
	$info=datos_mysql($sql2);
    $subred = $info['responseResult'][0]['subred'];
	//var_dump($sql2);
    $sql1 .= " T.subred = " . intval($subred);
    if ($_POST['fusu']) {
        $sql1 .= " AND n_documento ='" . $_POST['fusu'] . "'";
    }
	//var_dump($sql1);
	return $sql1;
}


function focus_th(){
 return 'th';
}

function men_th(){
 $rta=cap_menus('th','pro');
 return $rta;
}


function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  if ($a=='th'){  
	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	// $rta .= "<li class='icono $a actualizar'    title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
  }
  if ($a=='reasignar'){  
	$rta .= "<li class='icono $a grabar'      title='Reasignar'       OnClick=\"grabar('$a',this);\"></li>";
  }
  return $rta;
}

function cmp_th(){
 $rta =" ";
 $w='th';
 $o='segrep';
 $c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
 $c[]=new cmp('tipo_doc','s',3,$d['tipo_doc'],$w.' '.$o,'Tipo documento','tipo_doc',null,null,true,false,'','col-4');
 
//  $c[]=new cmp('tipo_doc','s','3','',$w.' '.$o,'TIPO DE DOCUMENTO','tipo_doc',null,null,false,false,'','col-2');
 /* $c[]=new cmp('documento','nu','999999999999999999','',$w.' '.$o,'NÚMERO DE DOCUMENTO','documento',null,null,false,false,'','col-2');
 $c[]=new cmp('nombre1','t','30','',$w.' '.$o,'Primer Nombre','nombre1',null,null,true,true,'','col-2');
 $c[]=new cmp('nombre2','t','30','',$w.' '.$o,'Segundo Nombre','nombre2',null,null,false,true,'','col-2');
 $c[]=new cmp('apellido1','t','30','',$w.' '.$o,'Primer Apellido','apellido1',null,null,true,true,'','col-2');
 $c[]=new cmp('apellido2','t','30','',$w.' '.$o,'Segundo Apellido','apellido2',null,null,false,true,'','col-2');
 $c[]=new cmp('fecha_nacimiento','d','','',$w.' '.$o,'Fecha de nacimiento','fecha_nacimiento',null,null,true,false,'','col-2');
 $c[]=new cmp('sexo','s','3','',$w.' '.$o,'Sexo','sexo',null,null,true,false,'','col-2');
 $c[]=new cmp('contacto','n','10','',$w.' '.$o,'N° Contacto','contacto',null,null,false,false,'','col-2');
 $c[]=new cmp('email','n','10','',$w.' '.$o,'Correo Electronico','email',null,null,false,false,'','col-2'); */
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 return $rta;
}


/* function get_th(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT `id_ruteo`,R.`idgeo`,`fuente`,`fecha_asig`,`priorizacion`,tipo_prior, `tipo_doc`, `documento`, `nombres`, `sexo`, R.direccion,`telefono1`, `telefono2`, `telefono3`, G.`subred`, G.`localidad`, G.`upz`, G.`barrio`, G.sector_catastral, G.nummanzana, G.predio_num, G.unidad_habit, G.`cordx`, G.`cordy` 
 		FROM `eac_ruteo` R 
 		LEFT JOIN hog_geo G ON R.idgeo=G.idgeo 
 		WHERE id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
	} 
}

function gra_th(){
	$id=divide($_POST['id'] ?? '');
	if (($rtaFec = validFecha('ruteo', $_POST['fecha_llamada'] ?? '')) !== true) {
		return $rtaFec;
	  }
	$usu = $_SESSION['us_sds'];
		// $equ=datos_mysql("select equipo from usuarios where id_usuario=".$_SESSION['us_sds']);
	 $bina = isset($_POST['fequi'])?(is_array($_POST['fequi'])?implode("-", $_POST['fequi']):implode("-",array_map('trim',explode(",",str_replace("'","",$_POST['fequi']))))):'';
		$sql = "INSERT INTO eac_ruteo_ges VALUES(null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),NULL,NULL,'A')";
	$params = [
		['type' => 'i', 'value' => $id[0]],
		['type' => 's', 'value' => $_POST['fecha_llamada'] ?? ''],
		['type' => 'i', 'value' => $_POST['estado_llamada']?? ''],
		['type' => 's', 'value' => $_POST['observacion']?? ''],
		['type' => 'i', 'value' => $_POST['estado_agenda']?? ''],
		['type' => empty($_POST['motivo_estado']) ? 'z' : 'i', 'value' => empty($_POST['motivo_estado']) ? '' : $_POST['motivo_estado']],
		['type' => empty($_POST['fecha_gestion']) ? 'z' : 's', 'value' => empty($_POST['fecha_gestion']) ? null : $_POST['fecha_gestion']],
		['type' => empty($_POST['docu_confirm']) ? 'z' : 'i', 'value' => empty($_POST['docu_confirm']) ? null : $_POST['docu_confirm']],
		['type' => empty($_POST['usuario_gest']) ? 'z' : 'i', 'value' => empty($_POST['usuario_gest']) ? null : $_POST['usuario_gest']],
		['type' => empty($_POST['direccion_nueva_v']) ? 'z' : 's', 'value' => empty($_POST['direccion_nueva_v']) ? null : $_POST['direccion_nueva_v']],
		['type' => empty($_POST['sector_catastral_v']) ? 'z' : 'i', 'value' => empty($_POST['sector_catastral_v']) ? null : $_POST['sector_catastral_v']],
		['type' => empty($_POST['nummanzana_v']) ? 'z' : 'i', 'value' => empty($_POST['nummanzana_v']) ? null : $_POST['nummanzana_v']],
		['type' => empty($_POST['predio_num_v']) ? 'z' : 'i', 'value' => empty($_POST['predio_num_v']) ? null : $_POST['predio_num_v']],
		['type' => 's', 'value' => $bina],
		['type' => 's', 'value' => $usu]
		];
	$rta = mysql_prepd($sql, $params);
	// $rta = show_sql($sql, $params);
	// var_dump($_POST);
	if(!empty($_POST['fecha_gestion']) && !empty($_POST['usuario_gest'])){
		$sql1 = "INSERT INTO geo_asig VALUES(NULL,?,?,?,?,NULL,NULL,'A')";
		$sql="SELECT idgeo id from eac_ruteo where id_ruteo=".$_POST['frut']."";
		$info=datos_mysql($sql);
		$id=$info['responseResult'][0]['id'];
		$params1 = array(
		array('type' => 'i', 'value' =>$id ),
		array('type' => 'i', 'value' => $_POST['usuario_gest']),
		array('type' => 'i', 'value' => $_SESSION['us_sds']),
		array('type' => 's', 'value' => date("Y-m-d H:i:s"))
		);
		// $rta3=show_sql($sql1, $params1);
		$rta1 = mysql_prepd($sql1, $params1);
		if (strpos($rta1, "correctamente") !== false){
			$rta.= " Y Se ha asignado el predio";
		}elseif(strpos($rta1, "Duplicate")){
			$rta.= " Y El predio ya se encontraba asignado";
		}
	}
	// return $rta3;
	return $rta;
}
 */
/***************************************************************************/
function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($c);
// var_dump($a);
if ($a=='gestion-lis' && $b=='acciones'){
	$rta="<nav class='menu right'>";
	$rta.="<li title='Ver Registro '><i class='fa-solid fa-eye ico' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getDataFetch,500,'rute',event,this,'lib.php',['fecha_llamada','estado_llamada','observacion']);\"></i></li>"; 
}
if ($a=='rute' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono mapa' title='Ruteo' id='".$c['ACCIONES']."' Onclick=\"mostrar('rute','pro',event,'','lib.php',7);\"></li>";
		if (agend($c['ACCIONES'])) {
			$rta.="<li class='icono  editarAgenda' title='CLASIFICACIÓN' id='".$c['ACCIONES']."' Onclick=\"mostrar('rutclasif','pro',event,'','clasifica.php',7,'clasifica');\"></li>";
		}
		if (fin($c['ACCIONES'])) {
			$rta.="<li class='icono efectividadAgenda' title='GESTIÓN FINAL' id='".$c['ACCIONES']."' Onclick=\"mostrar('ruteresol','pro',event,'','ruteoresolut.php',7,'ruteresol');\"></li>";
		}
		
		if (EnabRemot($c['ACCIONES'])) {
			$rta .= acceso('seguiRemoto') ? "<li title='Seguimiento remoto' onclick=\"mostrar('seguiremoto','pro',event,'','seguiRemoto.php',7,'Seguimiento Remoto');\"><i class='fa-solid fa-house-laptop ico' id='{$c['ACCIONES']}'></i></li>" : "";
			
		}
		if (EnabFin($c['ACCIONES'])) {
			$rta .= acceso('reasigRut') ? "<li title='Reasignar Ruteo' onclick=\"mostrar('reasignar','pro',event,'','lib.php',7,'Reasignar Ruteo');\"><i class='fa-solid fa-route ico' id='{$c['ACCIONES']}'></i></li>" : "";
			
		}
		// $rta.="<li title='Reasignar Ruteo' onclick=\"mostrar('reasignar','pro',event,'','lib.php',7,'Reasignar Ruteo');\"><i class='fa-solid fa-route ico' id='{$c['ACCIONES']}'></i></li>"; 
		
		// $rta.="<li class='icono  editarAgenda' title='CLASIFICACIÓN' id='".$c['ACCIONES']."' Onclick=\"mostrar('rutclasif','pro',event,'','clasifica.php',7,'clasifica');\"></li>";
		//$rta.="<li class='icono efectividadAgenda' title='GESTIÓN' id='".$c['ACCIONES']."' Onclick=\"mostrar('ruteresol','pro',event,'','ruteoresolut.php',7,'ruteresol');\"></li>";
		// if($c['Gestionado']== '1' || $c['Gestionado']=='2'){
		// }
	}
	log_error($rta);
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>