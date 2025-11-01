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

function lis_tamhamilton(){
	if (!empty($_POST['fidentificacion']) || !empty($_POST['ffam'])) {
		$info=datos_mysql("SELECT COUNT(*) total from hog_tam_hamilton O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		where ".whe_tamhamilton());
		$total=$info['responseResult'][0]['total'];
		$regxPag=12;
		$pag=(isset($_POST['pag-tamhamilton']))? (intval($_POST['pag-tamhamilton'])-1)* $regxPag:0;

		$sql="SELECT O.idpeople ACCIONES,id_hamilton 'Cod Registro',V.id_fam 'Cod Familia',P.idpersona Documento,FN_CATALOGODESC(1,P.tipo_doc) 'Tipo de Documento',CONCAT_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) Nombres,total Puntaje,psiquica,somatica, U.nombre Creo,U.subred,U.perfil perfil
	FROM hog_tam_hamilton O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		WHERE ";
	$sql.=whe_tamhamilton();
	$sql.=" ORDER BY O.fecha_create DESC";
	//echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"tamhamilton",$regxPag);
	}else{
		return "<div class='error' style='padding: 12px; background-color:#00a3ffa6;color: white; border-radius: 25px; z-index:100; top:0;text-transform:none'>
                <strong style='text-transform:uppercase'>NOTA:</strong>Por favor Ingrese el numero de documento ó familia a Consultar
                <span style='margin-left: 15px; color: white; font-weight: bold; float: right; font-size: 22px; line-height: 20px; cursor: pointer; transition: 0.3s;' onclick=\"this.parentElement.style.display='none';\">&times;</span>
            </div>";
	}
}

function lis_hamilton(){
	$id=divide($_POST['id']);//id_hamilton ACCIONES,
	$sql="SELECT id_hamilton 'Cod Registro',momento,analisis,psiquica,somatica,total,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM hog_tam_hamilton A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"hamilton-lis",5);
}

function whe_tamhamilton() {
	$sql = '1';
    if (!empty($_POST['fidentificacion'])) {
        $sql .= " AND P.idpersona = '".$_POST['fidentificacion']."'";
    }
    if (!empty($_POST['ffam'])) {
        $sql .= " AND V.id_fam = '".$_POST['ffam']."'";
    }
    return $sql;
}


function cmp_tamhamilton(){
	$rta="<div class='encabezado hamilton'>TABLA HAMILTON</div><div class='contenido' id='hamilton-lis'>".lis_hamilton()."</div></div>";
	$t=['tam_hamilton'=>'','hamilton_tipodoc'=>'','hamilton_nombre'=>'','hamilton_idpersona'=>'','hamilton_fechanacimiento'=>'','hamilton_total'=>'','hamilton_momento'=>'','hamilton_edad'=>'','hamilton_sintoma1'=>'','hamilton_sintoma2'=>'','hamilton_sintoma3'=>'','hamilton_sintoma4'=>'','hamilton_sintoma5'=>'','hamilton_sintoma6'=>'','hamilton_sintoma7'=>'','hamilton_sintoma8'=>'','hamilton_sintoma9'=>'','hamilton_sintoma10'=>'','hamilton_sintoma11'=>'','hamilton_sintoma12'=>'','hamilton_sintoma13'=>'','hamilton_sintoma14'=>'','hamilton_psiquica'=>'','hamilton_somatica'=>'','hamilton_analisis'=>'']; 
	$w='tamhamilton';
	$d=get_tamhamilton(); 
	if ($d=="") {$d=$t;}
	$o='datos';
    $key='srch';
	$days=fechas_app('psicologia');
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('idhamilton','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('hamilton_idpersona','t','20',$d['hamilton_idpersona'],$w.' '.$o.' '.$key,'N° Identificación','hamilton_idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('hamilton_tipodoc','s','3',$d['hamilton_tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','hamilton_tipodoc',null,'',false,false,'','col-25');//,'getDatForm(\'srch\',\'person\',[\'datos\']);'
	$c[]=new cmp('hamilton_nombre','t','50',$d['hamilton_nombre'],$w.' '.$o,'nombres','hamilton_nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('hamilton_fechanacimiento','d','10',$d['hamilton_fechanacimiento'],$w.' '.$o,'fecha nacimiento','hamilton_fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('hamilton_edad','n','3',$d['hamilton_edad'],$w.' '.$o,'edad','hamilton_edad',null,'',true,false,'','col-1');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
    
	$o='actv';
	$c[]=new cmp($o,'e',null,'SÍNTOMAS DE LOS ESTADOS DE ANSIEDAD',$w);
	$c[]=new cmp('sintoma1','s',3,'',$w.''.$o,'1. Estado de ánimo ansioso.Preocupaciones,anticipación de lo peor, aprensión (anticipación temerosa), irritabilidad ','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma2','s',3,'',$w.' '.$o,'2. Tensión.Sensación de tensión, imposibilidad de relajarse, reacciones con sobresalto, llanto fácil, temblores, sensación de inquietud.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma3','s',3,'',$w.' '.$o,'3. Temores.A la oscuridad, a los desconocidos, a quedarse solo, a los animales grandes, al tráfico, a las multitudes.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma4','s',3,'',$w.' '.$o,'4. Insomnio.Dificultad para dormirse, sueño interrumpido, sueño insatisfactorio y cansancio al despertar.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma5','s',3,'',$w.' '.$o,'5. Intelectual (cognitivo) Dificultad para concentrarse, mala memoria.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma6','s',3,'',$w.' '.$o,'6. Estado de ánimo deprimido. Pérdida de interés, insatisfacción en las diversiones, depresión, despertar prematuro, cambios de humor durante el día.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma7','s',3,'',$w.' '.$o,'7. Síntomas somáticos generales (musculares) Dolores y molestias musculares, rigidez muscular, contracciones musculares, sacudidas clónicas, crujir de	dientes, voz temblorosa.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma8','s',3,'',$w.' '.$o,'8. Síntomas somáticos generales (sensoriales) Zumbidos de oídos, visión borrosa, sofocos y escalofríos, sensación de debilidad, sensación de hormigueo.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma9','s',3,'',$w.' '.$o,'9. Síntomas cardiovasculares. Taquicardia, palpitaciones, dolor en el pecho, latidos vasculares, sensación de desmayo, extrasístole.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma10','s',3,'',$w.' '.$o,'10. Síntomas respiratorios. Opresión o constricción en el pecho, sensación de ahogo, suspiros, disnea.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma11','s',3,'',$w.' '.$o,'11. Síntomas gastrointestinales. Dificultad para tragar, gases, dispepsia: dolor antes y después de comer, sensación de ardor, sensación de estómago lleno, vómitos acuosos, vómitos, sensación de estómago vacío, digestión lenta, borborigmos (ruido intestinal), diarrea, pérdida de peso, estreñimiento.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma12','s',3,'',$w.' '.$o,'12. Síntomas genitourinarios. Micción frecuente, micción urgente, amenorrea, menorragia, aparición de la frigidez, eyaculación precoz, ausencia de erección, impotencia.','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma13','s',3,'',$w.' '.$o,'13. Síntomas autónomos. Boca seca, rubor, palidez, tendencia a sudar, vértigos, cefaleas de tensión, piloerección (pelos de punta)','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('sintoma14','s',3,'',$w.' '.$o,'14. Comportamiento en la entrevista (general y fisiológico) Tenso, no relajado, agitación nerviosa: manos, dedos cogidos, apretados, tics, enrollar un pañuelo; inquietud; pasearse de un lado a otro, temblor de manos, ceño fruncido, cara tirante, aumento del tono muscular, suspiros, palidez facial. Tragar saliva, eructar, taquicardia de reposo, frecuencia respiratoria por encima de 20 res/min, sacudidas enérgicas de tendones, temblor, pupilas dilatadas, exoftalmos (proyección anormal del globo del ojo), sudor, tics en los párpados.','nivel',null,null,true,true,'','col-10');

	$o='inter';
	$c[]=new cmp($o,'e',null,'INTERPRETACIÓN ',$w);
    $c[]=new cmp('psiquica','t','3','',$w.' '.$o,'Ansiedad psíquica','hamilton_psiquica',null,'',false,false,'','col-3');
    $c[]=new cmp('somatica','t','3','',$w.' '.$o,'Ansiedad somática','hamilton_somatica',null,'',false,false,'','col-3');
    $c[]=new cmp('total','t','3','',$w.' '.$o,'PUNTUACIÓN TOTAL','hamilton_total',null,'',false,false,'','col-4');
    $c[]=new cmp('analisis','t','100','',$w.' '.$o,'Analisis','hamilton_analisis',null,'',false,false,'','col-6');
    $c[]=new cmp('momento','t','20','',$w.' '.$o,'Momento','hamilton_momento',null,'',false,false,'','col-4');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	
	return $rta;
   }

	function get_tamhamilton(){
		if($_POST['id']==0){
			return "";
		}else{
			 $id=divide($_POST['id']);
			// print_r($_POST);
			$sql="SELECT P.idpeople,P.idpersona hamilton_idpersona,P.tipo_doc hamilton_tipodoc,
			concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) hamilton_nombre,P.fecha_nacimiento hamilton_fechanacimiento,
			TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS hamilton_edad
			FROM person P
			WHERE P.idpeople ='{$id[0]}'";
			// echo $sql; 
			$info=datos_mysql($sql);
					return $info['responseResult'][0];
			}
		}


/* function get_person(){
	// print_r($_POST);
	$id=divide($_POST['id']);
$sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,fecha_nacimiento,YEAR(CURDATE())-YEAR(fecha_nacimiento) Edad
FROM personas 
	WHERE idpersona='".$id[0]."' AND tipo_doc=upper('".$id[1]."')";
	// echo $sql;
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return '';
	}
return json_encode($info['responseResult'][0]);
} */

function focus_tamhamilton(){
	return 'tamhamilton';
   }
   
function men_tamhamilton(){
	$rta=cap_menus('tamhamilton','pro');
	return $rta;
   }

function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='tamhamilton' && isset($acc['crear']) && $acc['crear']=='SI'){  
		$rta .= "<li class='icono $a  grabar' title='Grabar' Onclick=\"grabar('$a',this);\" ></li>";
	}
	return $rta;
}
   
function gra_tamhamilton(){
	$id=divide($_POST['idhamilton']);
	// print_r($_POST);
	if(count($id)!= "2"){
		return "No es posible actualizar el tamizaje";
	}else{
		$data=datos_mysql("select count(Z.momento) as moment from hog_tam_hamilton Z  where Z.idpeople='{$id[0]}'");
		$momen=$data['responseResult'][0]['moment'];
		if($momen=='0'){
			$idmomento = 1;
		}elseif($momen=='1'){
			$idmomento = 2;
		}else{
			return "Ya se realizo los dos momentos";
		}

	
	$suma_psiquica = (
		$_POST['sintoma1']+
		$_POST['sintoma2']+
		$_POST['sintoma3']+
		$_POST['sintoma4']+
		$_POST['sintoma5']+
		$_POST['sintoma6']+
		$_POST['sintoma14']
	);

	$suma_somatica = (
		$_POST['sintoma7']+
		$_POST['sintoma8']+
		$_POST['sintoma9']+
		$_POST['sintoma10']+
		$_POST['sintoma11']+
		$_POST['sintoma12']+
		$_POST['sintoma13']
	);

	$suma_hamilton = ($suma_psiquica+$suma_somatica);

	if($suma_hamilton <= 6){
		$escala_hamilton = 'Ausencia de Ansiedad ';
	}else if($suma_hamilton >= 7 && $suma_hamilton <= 14){
		$escala_hamilton = 'Ansiedad Leve';
	}else if($suma_hamilton >= 15 && $suma_hamilton <= 28){
		$escala_hamilton = 'Ansiedad Moderada';
	}else if($suma_hamilton >= 29 && $suma_hamilton <= 42){
		$escala_hamilton = 'Ansiedad severa';
	}else if($suma_hamilton >= 43 && $suma_hamilton <= 56){
		$escala_hamilton = 'Ansiedad muy severa';
	}else{
		$escala_hamilton = 'Fuera de Rango';
	}


		$sql="INSERT INTO hog_tam_hamilton VALUES (null,
		$id[0],
		TRIM(UPPER('{$_POST['fecha_toma']}')),
		TRIM(UPPER('{$idmomento}')),
		TRIM(UPPER('{$_POST['sintoma1']}')),
		TRIM(UPPER('{$_POST['sintoma2']}')),
		TRIM(UPPER('{$_POST['sintoma3']}')),
		TRIM(UPPER('{$_POST['sintoma4']}')),
		TRIM(UPPER('{$_POST['sintoma5']}')),
		TRIM(UPPER('{$_POST['sintoma6']}')),
		TRIM(UPPER('{$_POST['sintoma7']}')),
		TRIM(UPPER('{$_POST['sintoma8']}')),
		TRIM(UPPER('{$_POST['sintoma9']}')),
		TRIM(UPPER('{$_POST['sintoma10']}')),
		TRIM(UPPER('{$_POST['sintoma11']}')),
		TRIM(UPPER('{$_POST['sintoma12']}')),
		TRIM(UPPER('{$_POST['sintoma13']}')),
		TRIM(UPPER('{$_POST['sintoma14']}')),
		TRIM(UPPER('{$escala_hamilton}')),
		TRIM(UPPER('{$suma_psiquica}')),
		TRIM(UPPER('{$suma_somatica}')),
		TRIM(UPPER('{$suma_hamilton}')),
		TRIM(UPPER('{$_SESSION['us_sds']}')),
		DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
		//echo $sql;
	}
	  $rta=dato_mysql($sql);
	//   return "correctamente";
	  return $rta;
	}



	function opc_hamilton_tipodoc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
	}
	function opc_sexo($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
	}
	function opc_momento($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=116 and estado='A'  ORDER BY 1 ",$id);
	}
	function opc_departamento($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=105 and estado='A' ORDER BY 1",$id);
	}
	function opc_salud_mental($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=104 and estado='A' ORDER BY 1",$id);
	}
	function opc_estado_civil($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=47 and estado='A' ORDER BY 1",$id);
	}
	function opc_niv_educativo($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=52 and estado='A' ORDER BY 1",$id);
	}
	function opc_nivel($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=117 and estado='A'  ORDER BY 1 ",$id);
	}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
		   if ($a=='tamhamilton' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamhamilton','pro',event,'','../tamizajes/hamilton.php',7,'tamhamilton');\"></li>";  //act_lista(f,this);
			}
		return $rta;
	   }
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }
	