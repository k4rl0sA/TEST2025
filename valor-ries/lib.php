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


function lis_tamvalories(){
	if (!empty($_POST['fidentificacion']) || !empty($_POST['ffam'])) {
		$info=datos_mysql("SELECT COUNT(*) total from hog_tam_valories O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		where ".whe_tamvalories());
		$total=$info['responseResult'][0]['total'];
		$regxPag=12;
		$pag=(isset($_POST['pag-tamvalories']))? (intval($_POST['pag-tamvalories'])-1)* $regxPag:0;

		$sql="SELECT O.idpeople ACCIONES,idoms 'Cod Registro',V.id_fam 'Cod Familia',P.idpersona Documento,FN_CATALOGODESC(1,P.tipo_doc) 'Tipo de Documento',CONCAT_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) Nombres,`puntaje` Puntaje,`descripcion` Descripcion, U.nombre Creo,U.subred,U.perfil perfil
	FROM hog_tam_oms O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		WHERE ";
	$sql.=whe_tamvalories();
	$sql.=" ORDER BY O.fecha_create DESC";
	//echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"tamvalories",$regxPag);
	}else{
		return "<div class='error' style='padding: 12px; background-color:#00a3ffa6;color: white; border-radius: 25px; z-index:100; top:0;text-transform:none'>
                <strong style='text-transform:uppercase'>NOTA:</strong>Por favor Ingrese el numero de documento ó familia a Consultar
                <span style='margin-left: 15px; color: white; font-weight: bold; float: right; font-size: 22px; line-height: 20px; cursor: pointer; transition: 0.3s;' onclick=\"this.parentElement.style.display='none';\">&times;</span>
            </div>";
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

function whe_tamvalories() {
	$sql = '1';
    if (!empty($_POST['fidentificacion'])) {
        $sql .= " AND P.idpersona = '".$_POST['fidentificacion']."'";
    }
    if (!empty($_POST['ffam'])) {
        $sql .= " AND V.id_fam = '".$_POST['ffam']."'";
    }
    return $sql;
}

function cmp_tamvalories(){
	$rta="<div class='encabezado valories'>TABLA valories</div><div class='contenido' id='valories-lis'>".lis_tamvalories()."</div></div>";
	$t=['tam_valories'=>'','valories_tipodoc'=>'','valories_nombre'=>'','valories_idpersona'=>'','valories_fechanacimiento'=>'','valories_puntaje'=>'','valories_momento'=>'','valories_edad'=>'','valories_lugarnacimiento'=>'','valories_condicionsalud'=>'','valories_estadocivil'=>'','valories_escolaridad'=>'',
	 'valories_ocupacion'=>'','valories_rutina'=>'','valories_rol'=>'',	 'valories_actividad'=>'','valories_evento'=>'','valories_comportamiento'=>'','porcentaje_comprension'=>'','porcentaje_moverse'=>'','porcentaje_cuidado'=>'','porcentaje_relacionarce'=>'','porcentaje_actividades'=>'','porcentaje_participacion'=>'','porcentaje_total'=>'','valories_analisis'=>''];

	$w='tamvalories';
	$d=get_tamvalories(); 
	if ($d=="") {$d=$t;}
	$o='datos';
    $key='srch';
	$days=fechas_app('psicologia');
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('idvalories','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('valories_idpersona','t','20',$d['valories_idpersona'],$w.' '.$o.' '.$key,'N° Identificación','valories_idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('valories_tipodoc','s','3',$d['valories_tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','valories_tipodoc',null,'',false,false,'','col-25','getDatForm(\'srch\',\'person\',[\'datos\']);');
	$c[]=new cmp('valories_nombre','t','50',$d['valories_nombre'],$w.' '.$o,'nombres','valories_nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('valories_fechanacimiento','d','10',$d['valories_fechanacimiento'],$w.' '.$o,'fecha nacimiento','valories_fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('valories_edad','n','3',$d['valories_edad'],$w.' '.$o,'edad','valories_edad',null,'',true,false,'','col-1');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('tipoaccion','s',3,'',$w.' '.$o,'Tipo de Accion','tipoaccion',null,null,true,true,'','col-3');
	
	$o='comprencion';
	$c[]=new cmp($o,'e',null,'Vinculación Actividad Estructurada',$w);
	$c[]=new cmp('actest','s',3,'',$w.' '.$o,'Se ha incorporado actividad estructurada','opcion',null,null,true,true,'','col-10');
	

	$o='capacidad';
	$c[]=new cmp($o,'e',null,'2. Capacidad para moverse en su alrededor (entorno)',$w);
	$c[]=new cmp('moverse1','s',3,'',$w.' '.$o,'Estar de pie durante largos períodos de tiempo, como por ejemplo, 30 minutos','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('moverse2','s',3,'',$w.' '.$o,'Ponerse de pie cuando estaba sentado','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('moverse3','s',3,'',$w.' '.$o,'Moverse dentro de su casa','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('moverse4','s',3,'',$w.' '.$o,'Salir de su casa','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('moverse5','s',3,'',$w.' '.$o,'Andar largas distancias como un kilómetro (o algo equivalente)','nivel',null,null,true,true,'','col-10');

	$o='cuidado';
	$c[]=new cmp($o,'e',null,'3. Cuidado personal ',$w);
	$c[]=new cmp('cuidado1','s',3,'',$w.' '.$o,'Lavarse todo el cuerpo (bañarse)','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('cuidado2','s',3,'',$w.' '.$o,'Vestirse','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('cuidado3','s',3,'',$w.' '.$o,'Comer','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('cuidado4','s',3,'',$w.' '.$o,'Estar solo/a durante unos día','nivel',null,null,true,true,'','col-10');

	$o='relacion';
	$c[]=new cmp($o,'e',null,'4. Relacionarse con otras personas ',$w);
	$c[]=new cmp('relacionarce1','s',3,'',$w.' '.$o,'Relacionarse con personas que no conoce','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('relacionarce2','s',3,'',$w.' '.$o,'Mantener una amistad','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('relacionarce3','s',3,'',$w.' '.$o,'Llevar bien con personas cercanas a usted','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('relacionarce4','s',3,'',$w.' '.$o,'Hacer nuevos amigos','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('relacionarce5','s',3,'',$w.' '.$o,'Tener relaciones sexuales','nivel',null,null,true,true,'','col-10');

	$o='actividad';
	$c[]=new cmp($o,'e',null,'5. Actividades de la vida diaria',$w);
	$c[]=new cmp('actividades1','s',3,'',$w.' '.$o,'Cumplir con sus quehaceres de la casa','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('actividades2','s',3,'',$w.' '.$o,'Realizar bien los quehaceres más importantes de la casa','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('actividades3','s',3,'',$w.' '.$o,'Acabar todos los quehaceres que tenía que hacer en la casa','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('actividades4','s',3,'',$w.' '.$o,'Acabar sus quehaceres de la casa tan rápido como era necesario','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('actividades5','s',3,'',$w.' '.$o,'Llevar a cabo su trabajo diario o las actividades escolares','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('actividades6','s',3,'',$w.' '.$o,'Realizar bien las tareas más importantes de su trabajo o de la escuela','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('actividades7','s',3,'',$w.' '.$o,'Acabar todo el trabajo que necesitaba hacer','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('actividades8','s',3,'',$w.' '.$o,'Acabar su trabajo tan rápido como era necesario','nivel',null,null,true,true,'','col-10');

	$o='participacion';
	$c[]=new cmp($o,'e',null,'6. Participación en sociedad',$w);
	$c[]=new cmp('participacion1','s',3,'',$w.' '.$o,'Dificultad para participar, al mismo nivel que el resto de las personas, en actividades de la comunidad (p.e. fiestas, actividades religiosas u otras)','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('participacion2','s',3,'',$w.' '.$o,'Dificultades debido a barreras u obstáculos existentes en su alrededor (entorno)','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('participacion3','s',3,'',$w.' '.$o,'Dificultad para vivir con dignidad o respeto debido a las actitudes y acciones de otras personas','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('participacion4','s',3,'',$w.' '.$o,'Cantidad de tiempo que ha dedicado a su "condición de salud" o a las consecuencias de la misma','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('participacion5','s',3,'',$w.' '.$o,'Qué impacto emocional (qué tanto le ha afectado) su "condición de salud"','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('participacion6','s',3,'',$w.' '.$o,'Qué impacto económico ha tenido usted o su familia debido a su "condición de salud"','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('participacion7','s',3,'',$w.' '.$o,'Dificultad que ha tenido usted y/o su familia debido a su "condición de salud"','nivel',null,null,true,true,'','col-10');
	$c[]=new cmp('participacion8','s',3,'',$w.' '.$o,'Dificultad que ha tenido para realizar cosas que le ayuden a relajarse o disfrutar','nivel',null,null,true,true,'','col-10');

	$o='dias';
	$c[]=new cmp($o,'e',null,'Días',$w);
	$c[]=new cmp('dias1','n',2,'',$w.' '.$o,'En los últimos 30 días, cuántos días ha tenido estas dificultades','nivel',null,null,true,true,'','col-10','validardias');
	$c[]=new cmp('dias2','n',2,'',$w.' '.$o,'En los últimos 30 días, cuántos días no pudo realizar ninguna de sus actividades habituales (nada) o del trabajo debido a su "condición de salud"','nivel',null,null,true,true,'','col-10','validardias');
	$c[]=new cmp('dias3','n',2,'',$w.' '.$o,'En los últimos 30 días, sin contar los días en que no pudo realizar "ninguna de sus actividades", cuántos días tuvo que recortar o reducir sus actividades habituales o del trabajo debido a su "condición de salud"','nivel',null,null,true,true,'','col-10','validardias');
	
	$o='analisis';
	$c[]=new cmp($o,'e',null,'Analisis ',$w);
  $c[]=new cmp('porcentaje_comprension','t',20,'',$w.' '.$o,'Comprensión','porcentaje_comprension',null,'',false,false,'','col-15');
  $c[]=new cmp('porcentaje_moverse','t',20,'',$w.' '.$o,'Moverse','porcentaje_moverse',null,'',false,false,'','col-15');
  $c[]=new cmp('porcentaje_cuidado','t',20,'',$w.' '.$o,'Cuidado','porcentaje_cuidado',null,'',false,false,'','col-15');
  $c[]=new cmp('porcentaje_relacionarce','t',20,'',$w.' '.$o,'Relacionarce','porcentaje_relacionarce',null,'',false,false,'','col-15');
  $c[]=new cmp('porcentaje_actividades','t',20,'',$w.' '.$o,'Actividades','porcentaje_actividades',null,'',false,false,'','col-15');
  $c[]=new cmp('porcentaje_participacion','t',20,'',$w.' '.$o,'Participacion','porcentaje_participacion',null,'',false,false,'','col-25');
  $c[]=new cmp('porcentaje_total','t',20,'',$w.' '.$o,'Puntaje Total','porcentaje_total',null,'',false,false,'','col-3');
  $c[]=new cmp('analisis','t',20,'',$w.' '.$o,'Clasificación','porcentaje_total',null,'',false,false,'','col-4');
  $c[]=new cmp('momento','t',20,'',$w.' '.$o,'Momento','valories_momento',null,'',false,false,'','col-3');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	
	return $rta;
   }

	function get_tamvalories(){
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
   
function gra_tamvalories(){
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


	function opc_valories_tipodoc($id=''){
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
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=162 and estado='A'  ORDER BY 1 ",$id);
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
	