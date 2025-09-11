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


function lis_tamcarlos(){
	if (!empty($_POST['fidentificacion']) || !empty($_POST['ffam'])) {
		$info=datos_mysql("SELECT COUNT(*) total from hog_tam_valories O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		where ".whe_tamcarlos());
		$total=$info['responseResult'][0]['total'];
		$regxPag=12;
		$pag=(isset($_POST['pag-tamcarlos']))? (intval($_POST['pag-tamcarlos'])-1)* $regxPag:0;

		$sql="SELECT O.idpeople ACCIONES,idoms 'Cod Registro',V.id_fam 'Cod Familia',P.idpersona Documento,FN_CATALOGODESC(1,P.tipo_doc) 'Tipo de Documento',CONCAT_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) Nombres,`puntaje` Puntaje,`descripcion` Descripcion, U.nombre Creo,U.subred,U.perfil perfil
	FROM hog_tam_oms O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		WHERE ";
	$sql.=whe_tamcarlos();
	$sql.=" ORDER BY O.fecha_create DESC";
	//echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"tamcarlos",$regxPag);
	}else{
		return "";
	}
}

function lis_valories(){
	$id=divide($_POST['id']);
	$sql="SELECT id_valories 'Cod Registro',momento,porcentaje_total,analisis,`nombre` Creó,`fecha_create` 'fecha Creó'
	function gra_tamcarlosen() {
		// Validar que se recibe la edad por POST
		if (!isset($_POST['edad']) || !is_numeric($_POST['edad'])) {
			return "Edad no proporcionada o inválida.";
		}
		$edad = intval($_POST['edad']);
		$idpeople = isset($_POST['idpeople']) ? intval($_POST['idpeople']) : 0;
		if ($idpeople <= 0) {
			return "ID de persona no válido.";
		}
		if ($edad < 16 && empty($_POST['fecha_toma'])) {
			return "La fecha de toma es obligatoria para menores de 16 años.";
		}

		// Preguntas 2 a 8
		$campos = [
			'sustancias', 'condualcoh', 'dismalcoh', 'estadoanimo', 'lios', 'olvido', 'solo'
		];
		$total = 0;
		foreach ($campos as $campo) {
			$val = isset($_POST[$campo]) ? intval($_POST[$campo]) : 0;
			$total += $val;
		}

		// Clasificación
		if ($total <= 2) {
			$descripcion = 'Consumo funcional / No consumo';
		} else {
			$descripcion = 'Consumo disfuncional (mayor riesgo)';
		}

		// Preparar consulta y parámetros
		$sql = "INSERT INTO tam_carlos_crafft (
			idpeople, fecha_toma, bebidas, sustancias, condualcoh, dismalcoh, estadoanimo, lios, olvido, solo, total, descripcion, usu_creo, fecha_create, estado
		) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
		$params = [
			['type' => 'i', 'value' => $idpeople],
			['type' => 's', 'value' => $_POST['fecha_toma']],
			['type' => 's', 'value' => $_POST['bebidas']],
			['type' => 's', 'value' => $_POST['sustancias']],
			['type' => 's', 'value' => $_POST['condualcoh']],
			['type' => 's', 'value' => $_POST['dismalcoh']],
			['type' => 's', 'value' => $_POST['estadoanimo']],
			['type' => 's', 'value' => $_POST['lios']],
			['type' => 's', 'value' => $_POST['olvido']],
			['type' => 's', 'value' => $_POST['solo']],
			['type' => 'i', 'value' => $total],
			['type' => 's', 'value' => $descripcion],
			['type' => 's', 'value' => $_SESSION['us_sds']],
			['type' => 's', 'value' => 'A']
		];
		$rta = mysql_prepd($sql, $params);
		return $rta;
	}

	function get_tamcarlos(){
		if($_POST['id']==0){
			return "";
		}else{
			 $id=divide($_POST['id']);
			// print_r($_POST);
			$sql="SELECT P.idpeople,P.idpersona valories_idpersona,P.tipo_doc valories_tipodoc,
			concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) valories_nombre,P.fecha_nacimiento valories_fechanacimiento,
			TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS valories_edad
			FROM person P
			WHERE P.idpeople ='{$id[0]}'";
			// echo $sql; 
			$info=datos_mysql($sql);
					return $info['responseResult'][0];
			}
		} 

function focus_tamcarlos(){
	return 'tamcarlos';
   }
   
function men_tamcarlos(){
	$rta=cap_menus('tamcarlos','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='tamcarlos'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
	return $rta;
  }
   
function gra_tamcarlos() {
	// Validar que se recibe la edad por POST
	if (!isset($_POST['edad']) || !is_numeric($_POST['edad'])) {
		return "Edad no proporcionada o inválida.";
	}
	$edad = intval($_POST['edad']);
	$idpeople = isset($_POST['idpeople']) ? intval($_POST['idpeople']) : 0;
	if ($idpeople <= 0) {
		return "ID de persona no válido.";
	}
	// Si es menor de 16 años, la variable es obligatoria
	if ($edad < 16) {
		// Aquí podrías validar campos obligatorios adicionales si aplica
		if (empty($_POST['fecha_toma'])) {
			return "La fecha de toma es obligatoria para menores de 16 años.";
		}
	}

	// Preguntas 2 a 8
	$campos = [
		'sustancias', 'condualcoh', 'dismalcoh', 'estadoanimo', 'lios', 'olvido', 'solo'
	];
	$total = 0;
	foreach ($campos as $campo) {
		$val = isset($_POST[$campo]) ? intval($_POST[$campo]) : 0;
		$total += $val;
	}

	// Clasificación
	if ($total <= 2) {
		$descripcion = 'Consumo funcional / No consumo';
	} else {
		$descripcion = 'Consumo disfuncional (mayor riesgo)';
	}

	// Insertar en la tabla tam_carlos_crafft
	$sql = "INSERT INTO tam_carlos_crafft (
		idpeople, fecha_toma, bebidas, sustancias, condualcoh, dismalcoh, estadoanimo, lios, olvido, solo, total, descripcion, usu_creo, fecha_create, estado
	) VALUES (
		'{$idpeople}',
		'" . addslashes($_POST['fecha_toma']) . "',
		'" . addslashes($_POST['bebidas']) . "',
		'" . addslashes($_POST['sustancias']) . "',
		'" . addslashes($_POST['condualcoh']) . "',
		'" . addslashes($_POST['dismalcoh']) . "',
		'" . addslashes($_POST['estadoanimo']) . "',
		'" . addslashes($_POST['lios']) . "',
		'" . addslashes($_POST['olvido']) . "',
		'" . addslashes($_POST['solo']) . "',
		'{$total}',
		'" . addslashes($descripcion) . "',
		'" . addslashes($_SESSION['us_sds']) . "',
		NOW(),
		'A'
	)";
	$rta = dato_mysql($sql);
	return $rta;
}

function opc_rta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
	}

    function opc_tipodoc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
	}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
		   if ($a=='tamcarlos' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamcarlos','pro',event,'','lib.php',7,'tamcarlos');\"></li>";  //act_lista(f,this);
			}
		return $rta;
	   }
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }
	