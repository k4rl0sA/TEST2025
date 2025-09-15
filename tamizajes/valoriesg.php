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


function lis_valories(){
	$id=divide($_POST['id']);
	$sql="SELECT id_valrie 'Cod Registro',fecha_toma,tipo,puntaje,descripcion,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM tam_valo_ries A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"valories-lis",5);
}

function cmp_tamvalories(){
	$rta="<div class='encabezado valories'>TABLA valories</div><div class='contenido' id='valories-lis'>".lis_valories()."</div></div>";
	$t=['idpersona'=>'','tipodoc'=>'','nombre'=>'','fechanacimiento'=>'','edad'=>''];
	$w='tamvalories';
	$d=get_tamvalories(); 
	if ($d=="") {$d=$t;}
	$o='datos';
    $key='srch';
	$days=fechas_app('psicologia');
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('idvalories','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('idpersona','t','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('tipodoc','s','3',$d['tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-25','getDatForm(\'srch\',\'person\',[\'datos\']);');
	$c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('fechanacimiento','d','10',$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('edad','n','3',$d['edad'],$w.' '.$o,'edad','edad',null,'',true,false,'','col-1');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('tipo','s',3,'',$w.' '.$o,'Tipo de Accion','tipoaccion',null,null,true,true,'','col-3');
	
	$o='vinculacion';
	$c[]=new cmp($o,'e',null,'Vinculación Actividad Estructurada',$w);
	$c[]=new cmp('actest','s',3,'',$w.' '.$o,'Se ha incorporado actividad estructurada','opcion',null,null,true,true,'','col-10');
	

	$o='relaciones';
	$c[]=new cmp($o,'e',null,'Relaciones Interpersonales',$w);
	$c[]=new cmp('redsoc','s',3,'',$w.' '.$o,'Mejores relaciones  con sus redes sociales de apoyo','opcion',null,null,true,true,'','col-10');
	

	$o='patron';
	$c[]=new cmp($o,'e',null,'Patrón de consumo',$w);
	$c[]=new cmp('aumsus','s',3,'',$w.' '.$o,'Aumentó la cantidad de sustancia consumida.','opcion',null,null,true,true,'','col-10');
	$c[]=new cmp('mantsus','s',3,'',$w.' '.$o,'Mantiene la cantidad de sustancia consumida.','opcion',null,null,true,true,'','col-10');
	$c[]=new cmp('dismsus','s',3,'',$w.' '.$o,'Disminuyó  la cantidad de sustancia consumida.','opcion',null,null,true,true,'','col-10');
	$c[]=new cmp('elimsus','s',3,'',$w.' '.$o,'Eliminó el consumo de sustancias psicoactivas','opcion',null,null,true,true,'','col-10');

	$o='percepcion';
	$c[]=new cmp($o,'e',null,'Percepción de riesgo',$w);
	$c[]=new cmp('consfisc','s',3,'',$w.' '.$o,'Relacionarse con personas que no conoce','opcion',null,null,true,true,'','col-10');
	$c[]=new cmp('pelicons','s',3,'',$w.' '.$o,'Mantener una amistad','opcion',null,null,true,true,'','col-10');
	

	
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	
	return $rta;
   }

	function get_tamvalories(){
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

function focus_tamvalories(){
	return 'tamvalories';
   }
   
function men_tamvalories(){
	$rta=cap_menus('tamvalories','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='tamvalories'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
	return $rta;
  }
   
function gra_tamvalories() {
    $id = divide($_POST['idvalories']);
    $idpeople = isset($id[0]) ? intval($id[0]) : 0;
    if ($idpeople <= 0) {
        return "ID de persona no válido.";
    }

  /*   // Validar que solo se puedan hacer 2 registros (uno PRE y uno POS) por persona
    $sql_check = "SELECT COUNT(*) AS total FROM tam_valo_ries WHERE idpeople=? AND tipo=? AND estado='A'";
    $params_check = [
        ['type' => 'i', 'value' => $idpeople],
        ['type' => 's', 'value' => $_POST['tipo']]
    ];
    $res_check = mysql_prepd($sql_check, $params_check);
    if (isset($res_check[0]['total']) && $res_check[0]['total'] > 0) {
        return "Ya existe un registro de tipo {$_POST['tipo']} para esta persona.";
    } */
    // Sumar puntaje de preguntas 1 a 8
    $campos = ['preg1','preg2','preg3','preg4','preg5','preg6','preg7','preg8'];
    $puntaje = 0;
    foreach ($campos as $campo) {
        $valor = isset($_POST[$campo]) ? intval($_POST[$campo]) : 0;
        $puntaje += $valor;
    }
    // Descripción según puntaje (puedes ajustar la lógica si se requiere)
    if ($puntaje >= 7) {
        $descripcion = 'Bajo riesgo';
    } elseif ($puntaje >= 4) {
        $descripcion = 'Riesgo moderado';
    } else {
        $descripcion = 'Alto riesgo';
    }
    $sql = "INSERT INTO tam_valo_ries (
        idpeople, fecha_toma, tipo, actest,redsoc ,aumsus, mantsus,dismsus, elimsus,consfisc, pelicons,puntaje, descripcion, usu_creo, fecha_create, estado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(),INTERVAL 5 HOUR), ?)";
    $params = [
       ['type' => 'i', 'value' => $idpeople],
        ['type' => 's', 'value' => $_POST['fecha_toma']],
        ['type' => 's', 'value' => $_POST['tipo']],
        ['type' => 's', 'value' => $_POST['actest']],
        ['type' => 's', 'value' => $_POST['redsoc']],
        ['type' => 's', 'value' => $_POST['aumsus']],
		['type' => 's', 'value' => $_POST['mantsus']],
		['type' => 's', 'value' => $_POST['dismsus']],
		['type' => 's', 'value' => $_POST['elimsus']],
		['type' => 's', 'value' => $_POST['consfisc']],
		['type' => 's', 'value' => $_POST['pelicons']],
        ['type' => 'i', 'value' => $puntaje],
        ['type' => 's', 'value' => $descripcion],
        ['type' => 's', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => 'A']
    ];
    $rta = mysql_prepd($sql, $params);
    return $rta;
}


function opc_tipodoc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function opc_tipoaccion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=290 and estado='A'  ORDER BY 1 ",$id);
}

function opc_opcion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A'  ORDER BY 1 ",$id);
}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
		   if ($a=='tamvalories' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamvalories','pro',event,'','lib.php',7,'tamvalories');\"></li>";  //act_lista(f,this);
			}
		return $rta;
	   }
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }
	