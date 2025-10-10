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

function lis_tamzung(){
	if (!empty($_POST['fidentificacion']) || !empty($_POST['ffam'])) {
		$info=datos_mysql("SELECT COUNT(*) total from hog_tam_zung O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		where ".whe_tamzung());
		$total=$info['responseResult'][0]['total'];
		$regxPag=12;
		$pag=(isset($_POST['pag-tamzung']))? (intval($_POST['pag-tamzung'])-1)* $regxPag:0;

		$sql="SELECT O.idpeople ACCIONES,id_zung 'Cod Registro',V.id_fam 'Cod Familia',P.idpersona Documento,FN_CATALOGODESC(1,P.tipo_doc) 'Tipo de Documento',CONCAT_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) Nombres,`puntaje` Puntaje,`analisis` Descripcion, U.nombre Creo,U.subred,U.perfil perfil
	FROM hog_tam_zung O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		WHERE ";
	$sql.=whe_tamzung();
	$sql.=" ORDER BY O.fecha_create DESC";
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"tamzung",$regxPag);
	}else{
		return "<div class='error' style='padding: 12px; background-color:#00a3ffa6;color: white; border-radius: 25px; z-index:100; top:0;text-transform:none'>
                <strong style='text-transform:uppercase'>NOTA:</strong>Por favor Ingrese el numero de documento ó familia a Consultar
                <span style='margin-left: 15px; color: white; font-weight: bold; float: right; font-size: 22px; line-height: 20px; cursor: pointer; transition: 0.3s;' onclick=\"this.parentElement.style.display='none';\">&times;</span>
            </div>";
	}
}

function lis_zung(){
	$id=divide($_POST['id']);//id_zung ACCIONES,
	$sql="SELECT id_zung 'Cod Registro',momento,analisis,puntaje,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM hog_tam_zung A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"zung-lis",5);
}

function whe_tamzung() {
	$sql = '1';
    if (!empty($_POST['fidentificacion'])) {
        $sql .= " AND P.idpersona = '".$_POST['fidentificacion']."'";
    }
    if (!empty($_POST['ffam'])) {
        $sql .= " AND V.id_fam = '".$_POST['ffam']."'";
    }
    return $sql;
}

function cmp_tamzung(){
	$rta="<div class='encabezado zung'>TABLA ZUNG</div><div class='contenido' id='zung-lis'>".lis_zung()."</div></div>";
	$t=['tam_zung'=>'','zung_tipodoc'=>'','zung_nombre'=>'','zung_idpersona'=>'','zung_fechanacimiento'=>'','zung_puntaje'=>'','zung_momento'=>'','zung_analisis'=>'','zung_edad'=>'','zung_anuncio1'=>'','zung_anuncio2'=>'','zung_anuncio3'=>'','zung_anuncio4'=>'','zung_anuncio5'=>'','zung_anuncio6'=>'','zung_anuncio7'=>'','zung_anuncio8'=>'','zung_anuncio9'=>'','zung_anuncio10'=>'','zung_anuncio11'=>'','zung_anuncio12'=>'','zung_anuncio13'=>'','zung_anuncio14'=>'','zung_anuncio15'=>'','zung_anuncio16'=>'','zung_anuncio17'=>'','zung_anuncio18'=>'','zung_anuncio19'=>'','zung_anuncio20'=>'']; 
	$w='tamzung';
	$d=get_tamzung();
	if ($d=="") {$d=$t;}
	$o='datos';
    $key='srch';
	$days=fechas_app('psicologia');
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('idzung','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('zung_idpersona','n','20',$d['zung_idpersona'],$w.' '.$o.' '.$key,'N° Identificación','zung_idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('zung_tipodoc','s','3',$d['zung_tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','zung_tipodoc',null,'',false,false,'','col-25');//,'getDatForm(\'srch\',\'person\',[\'datos\']);
	$c[]=new cmp('zung_nombre','t','50',$d['zung_nombre'],$w.' '.$o,'nombres','zung_nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('zung_fechanacimiento','d','10',$d['zung_fechanacimiento'],$w.' '.$o,'fecha nacimiento','zung_fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('zung_edad','n','3',$d['zung_edad'],$w.' '.$o,'edad','zung_edad',null,'',true,false,'','col-1');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
    
    
	$o='actv';
	$c[]=new cmp($o,'e',null,'Escala',$w);
	$c[]=new cmp('anuncio1','s',3,'',$w.' '.$o,'1. Me siento triste y deprimido.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio2','s',3,'',$w.' '.$o,'2. Por las mañanas me siento mejor que por las tardes.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio3','s',3,'',$w.' '.$o,'3. Frecuentemente tengo ganas de llorar y a veces lloro.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio4','s',3,'',$w.' '.$o,'4. Me cuesta mucho dormir o duermo mal por las noches.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio5','s',3,'',$w.' '.$o,'5. Ahora tengo tanto apetito como antes.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio6','s',3,'',$w.' '.$o,'6. Todavía me siento atraído por el sexo opuesto.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio7','s',3,'',$w.' '.$o,'7. Creo que estoy adelgazando.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio8','s',3,'',$w.' '.$o,'8. Estoy estreñido.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio9','s',3,'',$w.' '.$o,'9. Tengo palpitaciones.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio10','s',3,'',$w.' '.$o,'10. Me canso por cualquier cosa.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio11','s',3,'',$w.' '.$o,'11. Mi cabeza está tan despejada como antes.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio12','s',3,'',$w.' '.$o,'12. Hago las cosas con la misma facilidad que antes.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio13','s',3,'',$w.' '.$o,'13. Me siento agitado e intranquilo y no puedo estar quieto.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio14','s',3,'',$w.' '.$o,'14. Tengo esperanza y confío en el futuro.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio15','s',3,'',$w.' '.$o,'15. Me siento más irritable que habitualmente.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio16','s',3,'',$w.' '.$o,'16. Encuentro fácil tomar decisiones.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio17','s',3,'',$w.' '.$o,'17. Me creo útil y necesario para la gente.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio18','s',3,'',$w.' '.$o,'18. Encuentro agradable vivir, mi vida es plena.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio19','s',3,'',$w.' '.$o,'19. Creo que sería mejor para los demás si me muriera.','escala',null,null,true,true,'','col-10');
	$c[]=new cmp('anuncio20','s',3,'',$w.' '.$o,'20. Me gustan las mismas cosas que solían agradarme.','escala',null,null,true,true,'','col-10');

	$o='inter';
	$c[]=new cmp($o,'e',null,'INTERPRETACIÓN ',$w);
    $c[]=new cmp('zung_puntaje','n','3','',$w.' '.$o,'Total','zung_puntaje',null,'',false,false,'','col-1');
	$c[]=new cmp('zung_momento','t','20','',$w.' '.$o,'Momento','zung_momento',null,'',false,false,'','col-3');
    $c[]=new cmp('zung_analisis','t','100','',$w.' '.$o,'Analisis','zung_analisis',null,'',false,false,'','col-6');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
   }


   function get_tamzung(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		// print_r($_POST);
		$sql="SELECT P.idpeople,P.idpersona zung_idpersona,P.tipo_doc zung_tipodoc,
        concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) zung_nombre,P.fecha_nacimiento zung_fechanacimiento,
        TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS zung_edad
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
    $sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,fecha_nacimiento,
    TIMESTAMPDIFF(YEAR, fecha_nacimiento,CURDATE()) AS edad;
    FROM personas 
    left JOIN personas_datocomp ON idpersona=dc_documento and tipo_doc=dc_tipo_doc
        WHERE idpersona='".$id[0]."' AND tipo_doc=upper('".$id[1]."')";
        // return print_r(json_encode($sql));
        $info=datos_mysql($sql);
        if (!$info['responseResult']) {
            return json_encode (new stdClass);
        }
    return json_encode($info['responseResult'][0]);
    } */

function focus_tamzung(){
	return 'tamzung';
   }
   
function men_tamzung(){
	$rta=cap_menus('tamzung','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
  if ($a=='tamzung'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
	return $rta;
  }
   
function gra_tamzung(){
	$id=divide($_POST['idzung']);
	// print_r($_POST);
	if(count($id)!= "2"){
		return "No es posible actualizar el tamizaje";
	}else{
		$data=datos_mysql("select count(Z.momento) as moment from hog_tam_zung Z  where Z.idpeople='{$id[0]}' and Z.estado='A'");
		$momen=$data['responseResult'][0]['moment'];
		if($momen=='0'){
			$idmomento = 1;
		}elseif($momen=='1'){
			$idmomento = 2;
		}else{
			return "Ya se realizo los dos momentos";
		}

	$suma_zung = (
		$_POST['anuncio1']+
		$_POST['anuncio2']+
		$_POST['anuncio3']+
		$_POST['anuncio4']+
		$_POST['anuncio5']+
		$_POST['anuncio6']+
		$_POST['anuncio7']+
		$_POST['anuncio8']+
		$_POST['anuncio9']+
		$_POST['anuncio10']+
		$_POST['anuncio11']+
		$_POST['anuncio12']+
		$_POST['anuncio13']+
		$_POST['anuncio14']+
		$_POST['anuncio15']+
		$_POST['anuncio16']+
		$_POST['anuncio17']+
		$_POST['anuncio18']+
		$_POST['anuncio19']+
		$_POST['anuncio20']
	);

	if($suma_zung <= 28){
		$escala_zung = 'Ausencia de depresión';
	}else if($suma_zung >= 29 && $suma_zung <= 41){
		$escala_zung = 'Depresión leve';
	}else if($suma_zung >= 42 && $suma_zung <= 53){
		$escala_zung = 'Depresión moderada';
	}else{
		$escala_zung = 'Depresión grave';
	}


		$sql="INSERT INTO hog_tam_zung VALUES (null,
		$id[0],
		TRIM(UPPER('{$_POST['fecha_toma']}')),
		TRIM(UPPER('{$idmomento}')),
		TRIM(UPPER('{$_POST['anuncio1']}')),
		TRIM(UPPER('{$_POST['anuncio2']}')),
		TRIM(UPPER('{$_POST['anuncio3']}')),
		TRIM(UPPER('{$_POST['anuncio4']}')),
		TRIM(UPPER('{$_POST['anuncio5']}')),
		TRIM(UPPER('{$_POST['anuncio6']}')),
		TRIM(UPPER('{$_POST['anuncio7']}')),
		TRIM(UPPER('{$_POST['anuncio8']}')),
		TRIM(UPPER('{$_POST['anuncio9']}')),
		TRIM(UPPER('{$_POST['anuncio10']}')),
		TRIM(UPPER('{$_POST['anuncio11']}')),
		TRIM(UPPER('{$_POST['anuncio12']}')),
		TRIM(UPPER('{$_POST['anuncio13']}')),
		TRIM(UPPER('{$_POST['anuncio14']}')),
		TRIM(UPPER('{$_POST['anuncio15']}')),
		TRIM(UPPER('{$_POST['anuncio16']}')),
		TRIM(UPPER('{$_POST['anuncio17']}')),
		TRIM(UPPER('{$_POST['anuncio18']}')),
		TRIM(UPPER('{$_POST['anuncio19']}')),
		TRIM(UPPER('{$_POST['anuncio20']}')),
		TRIM(UPPER('{$escala_zung}')),
		TRIM(UPPER('{$suma_zung}')),
		TRIM(UPPER('{$_SESSION['us_sds']}')),
		DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A');";
		// echo $sql;
	}
	  $rta=dato_mysql($sql);
	//   return "correctamente";
	  return $rta;
	}

	function opc_zung_tipodoc($id=''){
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
	function opc_escala($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=119 and estado='A'  ORDER BY 1 ",$id);
	}

function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
	if ($a=='tamzung' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamzung','pro',event,'','../tamizajes/zung.php',7,'tamzung');\"></li>";  //act_lista(f,this);
		}
	return $rta;
}
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }