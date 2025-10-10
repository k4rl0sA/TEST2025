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

function lis_carlos(){
	$id=divide($_POST['id']);
	$sql="SELECT id_carlcra 'Cod Registro',fecha_toma,total Puntaje,descripcion,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM tam_carlos_crafft A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"carlos-lis",5);
}

function cmp_tamcarlos(){
	$rta="<div class='encabezado carlos'>TABLA CARLOS CRAFFT</div><div class='contenido' id='carlos-lis'>".lis_carlos()."</div></div>";
	$t=['idpersona'=>'','tipodoc'=>'','nombre'=>'','fechanacimiento'=>'','edad'=>''];
	$w='tamcarlos';
	$d=get_tamcarlos(); 
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
	
	$o='Tamizaje';
	$c[]=new cmp($o,'e',null,'Preguntas',$w);
	$c[]=new cmp('bebidas','s',3,'',$w.' '.$o,'¿Ha consumido bebidas alcohólicas (más de unos pocos sorbos)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('sustancias','s',3,'',$w.' '.$o,'¿Ha usado algún otro tipo de sustancias que alteren su estado de ánimo o de conciencia?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('condualcoh','s',3,'',$w.' '.$o,'¿Alguna vez ha salido a la calle, o se ha subido a un carro conducido por  alguien (que podía ser usted mismo), estando bajo los efectos de alcohol?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('dismalcoh','s',3,'',$w.' '.$o,'¿Alguna vez su familia o sus amigos le han dicho que debería disminuir el consumo de alcohol o de','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('estadoanimo','s',3,'',$w.' '.$o,'¿Alguna vez ha usado alcohol o drogas para reconfortarse (para sentirse mejor, para socializar, para mejorar su estado de ánimo, para','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('lios','s',3,'',$w.' '.$o,'¿Alguna vez ha tenido líos o problemas (peleas físicas o verbales; suspensiones académicas; detención por la policía; accidentes) estando bajo los efectos de alcohol o de drogas?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('olvido','s',3,'',$w.' '.$o,'¿Alguna vez ha olvidado cosas que hizo estando bajo los efectos de alcohol o de drogas?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('solo','s',3,'',$w.' '.$o,'¿Alguna vez ha consumido alcohol o drogas estando solo?','rta',null,null,true,true,'','col-10');
    
  /*   $o='totalresul';
	$c[]=new cmp($o,'e',null,'Resultado',$w);
    $c[]=new cmp('total','t',3,'',$w.' '.$o,'Puntaje','total',null,'',false,false,'','col-4');
    $c[]=new cmp('descripcion','t','3','',$w.' '.$o,'Descripcion','descripcion',null,null,false,false,'','col-5'); */
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

	function get_tamcarlos(){
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
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamcarlos','pro',event,'','../tamizajes/carlos.php',7,'tamcarlos');\"></li>";  //act_lista(f,this);
			}
		return $rta;
	   }
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }