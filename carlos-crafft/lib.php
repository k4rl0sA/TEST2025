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
	FROM hog_tam_valories A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"valories-lis",5);
}

function whe_tamcarlos() {
	$sql = '1';
    if (!empty($_POST['fidentificacion'])) {
        $sql .= " AND P.idpersona = '".$_POST['fidentificacion']."'";
    }
    if (!empty($_POST['ffam'])) {
        $sql .= " AND V.id_fam = '".$_POST['ffam']."'";
    }
    return $sql;
}

function cmp_tamcarlos(){
	$rta="<div class='encabezado valories'>TABLA valories</div><div class='contenido' id='valories-lis'>".lis_tamcarlos()."</div></div>";
	$t=['tam_valories'=>'','valories_tipodoc'=>'','valories_nombre'=>'','valories_idpersona'=>'','valories_fechanacimiento'=>'','valories_puntaje'=>'','valories_momento'=>'','valories_edad'=>'','valories_lugarnacimiento'=>'','valories_condicionsalud'=>'','valories_estadocivil'=>'','valories_escolaridad'=>'',
	 'valories_ocupacion'=>'','valories_rutina'=>'','valories_rol'=>'',	 'valories_actividad'=>'','valories_evento'=>'','valories_comportamiento'=>'','porcentaje_comprension'=>'','porcentaje_moverse'=>'','porcentaje_cuidado'=>'','porcentaje_relacionarce'=>'','porcentaje_actividades'=>'','porcentaje_participacion'=>'','porcentaje_total'=>'','valories_analisis'=>''];

	$w='tamcarlos';
	$d=get_tamcarlos(); 
	if ($d=="") {$d=$t;}
	$o='datos';
    $key='srch';
	$days=fechas_app('psicologia');
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('idvalories','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('valories_idpersona','t','20',$d['valories_idpersona'],$w.' '.$o.' '.$key,'N° Identificación','valories_idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('valories_tipodoc','s','3',$d['valories_tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-25','getDatForm(\'srch\',\'person\',[\'datos\']);');
	$c[]=new cmp('valories_nombre','t','50',$d['valories_nombre'],$w.' '.$o,'nombres','valories_nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('valories_fechanacimiento','d','10',$d['valories_fechanacimiento'],$w.' '.$o,'fecha nacimiento','valories_fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('valories_edad','n','3',$d['valories_edad'],$w.' '.$o,'edad','valories_edad',null,'',true,false,'','col-1');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
	
    
	$o='Tamizaje';
	$c[]=new cmp($o,'e',null,'Preguntas',$w);
	$c[]=new cmp('bebidas','s',3,'',$w.' '.$o,'¿Ha consumido bebidas alcohólicas (más de unos pocos sorbos)?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('sustancias','s',3,'',$w.' '.$o,'¿Ha usado algún otro tipo de sustancias que alteren su estado de ánimo o de conciencia? ','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('condualcoh','s',3,'',$w.' '.$o,'¿Alguna vez ha salido a la calle, o se ha subido a un carro conducido por  alguien (que podía ser usted mismo), estando bajo los efectos de alcohol?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('dismalcoh','s',3,'',$w.' '.$o,'¿Alguna vez su familia o sus amigos le han dicho que debería disminuir el consumo de alcohol o de','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('estadoanimo','s',3,'',$w.' '.$o,'¿Alguna vez ha usado alcohol o drogas para reconfortarse (para sentirse mejor, para socializar, para mejorar su estado de ánimo, para','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('lios','s',3,'',$w.' '.$o,'¿Alguna vez ha tenido líos o problemas (peleas físicas o verbales; suspensiones académicas; detención por la policía; accidentes) estando bajo los efectos de alcohol o de drogas?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('olvido','s',3,'',$w.' '.$o,'¿Alguna vez ha olvidado cosas que hizo estando bajo los efectos de alcohol o de drogas?','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('solo','s',3,'',$w.' '.$o,'¿Alguna vez ha consumido alcohol o drogas estando solo?','rta',null,null,true,true,'','col-10');
    
    $o='totalresul';
	$c[]=new cmp($o,'e',null,'TOTAL',$w);
    $c[]=new cmp('total','t',3,'',$w.' '.$o,'Puntaje','carlos_total',null,'',false,false,'','col-4');
    $c[]=new cmp('descripcion','t','3','',$w.' '.$o,'Descripcion','descripcion',null,null,false,false,'','col-5');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	
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
   
function gra_tamcarlos(){
	$id=divide($_POST['idvalories']);
	// print_r($_POST);
	if(count($id)!= "2"){
		return "No es posible actualizar el tamizaje";
	}else{
		$data=datos_mysql("select count(Z.momento) as moment from hog_tam_valories Z  where Z.idpeople='{$id[0]}'");
		$momen=$data['responseResult'][0]['moment'];
		if($momen=='0'){
			$idmomento = 1;
		}elseif($momen=='1'){
			$idmomento = 2;
		}else{
			return "Ya se realizo los dos momentos";
		}
	

	$suma_com = (
			$_POST['comprension1']+
			$_POST['comprension2']+
			$_POST['comprension3']+
			$_POST['comprension4']+
			$_POST['comprension5']+
			$_POST['comprension6']
		);

	$suma_mov = (
			$_POST['moverse1']+
			$_POST['moverse2']+
			$_POST['moverse3']+
			$_POST['moverse4']+
			$_POST['moverse5']
		);

	$suma_cui = (
			$_POST['cuidado1']+
			$_POST['cuidado2']+
			$_POST['cuidado3']+
			$_POST['cuidado4']
		);

	$suma_rel = (
			$_POST['relacionarce1']+
			$_POST['relacionarce2']+
			$_POST['relacionarce3']+
			$_POST['relacionarce4']+
			$_POST['relacionarce5']
		);

	$suma_act = (
			$_POST['actividades1']+
			$_POST['actividades2']+
			$_POST['actividades3']+
			$_POST['actividades4']+
			$_POST['actividades5']+
			$_POST['actividades6']+
			$_POST['actividades7']+
			$_POST['actividades8']
		);

	$suma_par = (
			$_POST['participacion1']+
			$_POST['participacion2']+
			$_POST['participacion3']+
			$_POST['participacion4']+
			$_POST['participacion5']+
			$_POST['participacion6']+
			$_POST['participacion7']+
			$_POST['participacion8']
		);

		$suma_valories = ($suma_com+$suma_mov+$suma_cui+$suma_rel+$suma_act+$suma_par);

	// var_dump($numero);
	switch ($suma_valories) {
		case ($suma_valories >= 1 && $suma_valories <= 36):
			$pnt=' Ninguna Discapacidad';
			break;
		case ($suma_valories >= 37 && $suma_valories <= 72):
			$pnt=' Discapacidad Leve';
			break;
		case ($suma_valories >= 73 && $suma_valories <= 108):
			$pnt=' Discapacidad Moderada';
			break;
		case ($suma_valories >= 109 && $suma_valories <= 144):
				$pnt=' Discapacidad Severa';
			break;
		case ($suma_valories >= 145 && $suma_valories <= 180):
				$pnt=' Discapacidad Severa';
			break;
		default:
			$pnt='Error en el rango, por favor valide';
			break;
	}

		$sql="INSERT INTO hog_tam_valories VALUES (null,
		$id[0],
		TRIM(UPPER('{$_POST['fecha_toma']}')),
		TRIM(UPPER('{$idmomento}')),
		TRIM(UPPER('{$_POST['comprension1']}')),
		TRIM(UPPER('{$_POST['comprension2']}')),
		TRIM(UPPER('{$_POST['comprension3']}')),
		TRIM(UPPER('{$_POST['comprension4']}')),
		TRIM(UPPER('{$_POST['comprension5']}')),
		TRIM(UPPER('{$_POST['comprension6']}')),
		TRIM(UPPER('{$_POST['moverse1']}')),
		TRIM(UPPER('{$_POST['moverse2']}')),
		TRIM(UPPER('{$_POST['moverse3']}')),
		TRIM(UPPER('{$_POST['moverse4']}')),
		TRIM(UPPER('{$_POST['moverse5']}')),
		TRIM(UPPER('{$_POST['cuidado1']}')),
		TRIM(UPPER('{$_POST['cuidado2']}')),
		TRIM(UPPER('{$_POST['cuidado3']}')),
		TRIM(UPPER('{$_POST['cuidado4']}')),
		TRIM(UPPER('{$_POST['relacionarce1']}')),
		TRIM(UPPER('{$_POST['relacionarce2']}')),
		TRIM(UPPER('{$_POST['relacionarce3']}')),
		TRIM(UPPER('{$_POST['relacionarce4']}')),
		TRIM(UPPER('{$_POST['relacionarce5']}')),
		TRIM(UPPER('{$_POST['actividades1']}')),
		TRIM(UPPER('{$_POST['actividades2']}')),
		TRIM(UPPER('{$_POST['actividades3']}')),
		TRIM(UPPER('{$_POST['actividades4']}')),
		TRIM(UPPER('{$_POST['actividades5']}')),
		TRIM(UPPER('{$_POST['actividades6']}')),
		TRIM(UPPER('{$_POST['actividades7']}')),
		TRIM(UPPER('{$_POST['actividades8']}')),
		TRIM(UPPER('{$_POST['participacion1']}')),
		TRIM(UPPER('{$_POST['participacion2']}')),
		TRIM(UPPER('{$_POST['participacion3']}')),
		TRIM(UPPER('{$_POST['participacion4']}')),
		TRIM(UPPER('{$_POST['participacion5']}')),
		TRIM(UPPER('{$_POST['participacion6']}')),
		TRIM(UPPER('{$_POST['participacion7']}')),
		TRIM(UPPER('{$_POST['participacion8']}')),
		TRIM(UPPER('{$_POST['dias1']}')),
		TRIM(UPPER('{$_POST['dias2']}')),
		TRIM(UPPER('{$_POST['dias3']}')),
		'{$suma_com}',
		'{$suma_mov}',
		'{$suma_cui}',
		'{$suma_rel}',
		'{$suma_act}',
		'{$suma_par}',
		$suma_valories,
		'$pnt',
		TRIM(UPPER('{$_SESSION['us_sds']}')),
		DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
		// echo $sql;
	}
	  $rta=dato_mysql($sql);
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
	