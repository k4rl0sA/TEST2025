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

function lis_assist(){
	$id=divide($_POST['id']);
	$sql="SELECT id_assist 'Cod Registro',fecha_toma,total Puntaje,descripcion,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM tam_assist A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"valories-lis",5);
}

function cmp_tamassist(){
	$rta="<div class='encabezado valories'>TABLA valories</div><div class='contenido' id='valories-lis'>".lis_assist()."</div></div>";
	$t=['idpersona'=>'','tipodoc'=>'','nombre'=>'','fechanacimiento'=>'','edad'=>''];
	$w='tamassist';
	$d=get_tamassist(); 
	if ($d=="") {$d=$t;}
	$o='datos';
    $key='srch';
	$days=fechas_app('psicologia');
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('idpersona','t','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('tipodoc','s','3',$d['tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-25','getDatForm(\'srch\',\'person\',[\'datos\']);');
	$c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('fechanacimiento','d','10',$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('edad','n','3',$d['edad'],$w.' '.$o,'edad','edad',null,'',true,false,'','col-1');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
	
	$o='Tabaco';
	$c[]=new cmp($o,'e',null,'Tabaco',$w);
	$c[]=new cmp('tconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('tfrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('tdeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('tsalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('thabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('tpreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('tcontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','rta',null,null,true,true,'','col-10');
	
    $o='Bebidas';
	$c[]=new cmp($o,'e',null,'Bebidas alcohólicas',$w);
	$c[]=new cmp('bconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('bfrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('bdeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('bsalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('bhabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('bpreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('bcontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','rta',null,null,true,true,'','col-10');
    
    $o='Cannabis';
	$c[]=new cmp($o,'e',null,'Cannabis',$w);
	$c[]=new cmp('cconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('cfrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('cdeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('csalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('chabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('cpreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('ccontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','rta',null,null,true,true,'','col-10');

  
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

	function get_tamassist(){
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

function focus_tamassist(){
	return 'tamassist';
   }
   
function men_tamassist(){
	$rta=cap_menus('tamassist','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='tamassist'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
	return $rta;
  }
   
function gra_tamassist() {
	$id = divide($_POST['id']);
	$idpeople = isset($id[0]) ? intval($id[0]) : 0;
	// Validar que se recibe la edad por POST
	if (!isset($_POST['edad']) || !is_numeric($_POST['edad'])) {
		return "Edad no proporcionada o inválida.";
	}
	$edad = intval($_POST['edad']);
	if ($edad < 16 && empty($_POST['fecha_toma'])) {
		return "La fecha de toma es obligatoria para menores de 16 años.";
	}
	if ($idpeople <= 0) {
		return "ID de persona no válido.";
	}
	// Preguntas 2 a 8
	$campos = [
		'bebidas','sustancias', 'condualcoh', 'dismalcoh', 'estadoanimo', 'lios', 'olvido', 'solo'
	];
	$total = 0;
	foreach ($campos as $campo) {
		$valor = isset($_POST[$campo]) ? intval($_POST[$campo]) : 2; // Si no viene, se asume 2 (cuenta como 0)
		$val = ($valor == 1) ? 1 : 0;
		$total += $val;
	}
	// Clasificación
	if ($total <= 2) {
		$descripcion = 'Consumo funcional - No consumo';
	} else {
		$descripcion = 'Consumo disfuncional (mayor riesgo)';
	}

	// Preparar consulta y parámetros
	$sql = "INSERT INTO tam_assist (
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
		   if ($a=='tamassist' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamassist','pro',event,'','lib.php',7,'tamassist');\"></li>";  //act_lista(f,this);
			}
		return $rta;
	   }
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }