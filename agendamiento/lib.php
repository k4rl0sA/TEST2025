<?php
require_once "../libs/gestion.php";
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

function whe_agendamiento() {
	$sql = "";
	if ($_POST['fidpersona'])
		$sql .= " AND P.idpersona like '%".$_POST['fidpersona']."%'";
	if ($_POST['fdigita'])
		$sql .= " AND A.usu_creo ='".$_POST['fdigita']."' ";
	if ($_POST['festado'])
		$sql .= " AND A.estado = '".$_POST['festado']."' ";
	if ($_POST['fdes']) {
		if ($_POST['fhas']) {
			$sql .= " AND fecha_cita >='".$_POST['fdes']."' AND fecha_cita <='".$_POST['fhas']."'";
		} else {
			$sql .= " AND fecha_cita >='".$_POST['fdes']."' AND fecha_cita <='". $_POST['fdes']."'";
		}
	}
	return $sql;
}

/*function lis_agendamiento(){
	//~ estado=1 or estado=2 or  or estado=5 
    $info=datos_mysql("SELECT COUNT(*) total FROM `frecuenciauso` A LEFT JOIN person P ON A.idpeople=P.idpeople left JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}') ".whe_agendamiento());
	$total=$info['responseResult'][0]['total'];
	$regxPag=5;
	$pag=(isset($_POST['pag-frecuenciauso']))? ($_POST['pag-frecuenciauso']-1)* $regxPag:0;


	$sql="SELECT idagendamiento ACCIONES,
idpersona Documento,FN_CATALOGODESC(1,tipo_doc) 'Tipo Documento',FN_CATALOGODESC(274,`punto_atencion`) Punto,
FN_CATALOGODESC(275,tipo_cita) 'Tipo Cita',`fecha_cita`,`hora_cita`,fecha_llamada 'Recordación Cita',FN_CATALOGODESC(276,A.estado) Estado
from agendamiento A LEFT JOIN person P ON A.idpeople=P.idpeople left JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE A.estado not in (1,2,3,5) AND U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}')";
	$sql.=whe_agendamiento();
	$sql.=" ORDER BY 6 ASC,7 ASC";
//~ echo $sql;   
	$sql1="SELECT IFNULL(T4.fecha,T2.fecha_envio) 'Fecha Caracterizacion',IFNULL(T3.apellido1,T2.apellido1) 'Primer Apellido',IFNULL(T3.apellido2,T2.apellido2) 'Segundo Apellido',IFNULL(T3.nombre1,T2.nombre1) 'Primer Nombre',
	IFNULL(T3.nombre2,T2.nombre2) 'Segundo Nombre',IFNULL(T3.idpersona,T2.idpersona) 'N° Documento',FN_CATALOGODESC(1,IFNULL(T3.tipo_doc,T2.tipo_doc)) 'Tipo Documento',IFNULL(T3.fecha_nacimiento,T2.fecha_nacimiento) 'Fecha de Nacimiento',
	FN_CATALOGODESC(21,IFNULL(T3.genero,T2.genero)) 'Genero',FN_CATALOGODESC(16,IFNULL(T3.etnia,T2.etnia)) 'Etnia',FN_CATALOGODESC(30,IFNULL(T3.nacionalidad,T2.nacionalidad)) 'Nacionalidad',FN_CATALOGODESC(2,IFNULL(T4.localidad,T2.localidad)) 'Localidad',IFNULL(T4.upz,T2.upz) 'UPZ',IFNULL(T4.direccion,T2.direccion) 'Dirección',IFNULL(T4.telefono1,T2.telefono1) 'Teléfono1',IFNULL(T4.telefono2,T2.telefono2) 'Teléfono2',FN_CATALOGODESC(274,T1.punto_atencion) 'PUnto de Atención',FN_CATALOGODESC(39,T1.tipo_cita) 'Tipo de Cita',
	T1.fecha_create 'Fecha de Asignación',T1.fecha_cita 'Fecha de la Cita',T1.hora_cita 'Hora de la Cita',T1.nombre_atendio 'Nombre quien Atendió Llamada',
	FN_CATALOGODESC(276,T1.estado) 'Estado',T1.usu_creo 'Digitador',T1.observac_cita 'Observación Cita',IFNULL(T1.fecha_llamada,'00-00-0000') 'Fecha Recordación',
	ifnull(T1.nombre_llamada,'-') 'Nombre quien Recibió Llamada' ,ifnull(T1.confirma_cita,'-') 'Confirmo Cita',ifnull(T1.msjtxt,'-') 'Desea Envio de Msj',
	ifnull(T1.usu_update,'-') 'Digitador1',ifnull(T1.observac_llamadas,'-') 'Observaciones de Recordación',ifnull(T1.fecha_llamada2,'-') 'Fecha Llamada por Efectividad',ifnull(T1.nombre_llamada2,'-') 'Nombre quien Contesto Llamada',ifnull(FN_CATALOGODESC(41,T1.motivo_inasistencia),'-') 'Motivo de la Inasistencia',ifnull(T1.reasigno,'-') 'Se reasigno la Cita',ifnull(T1.usu_update,'-') 'Digitador2',ifnull(T1.observac_llamada2,'-') 'Observaciones de Inasistencia' 
FROM agendamiento A
LEFT JOIN person P ON A.idpeople=P.idpeople 
left JOIN usuarios U ON A.usu_creo = U.id_usuario
	WHERE U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}')";
	$sql1.=whe_agendamiento();
	$sql1.="ORDER BY `fecha_cita` ASC,`hora_cita` ASC";
	//~ echo $sql1;
	$_SESSION['sql_agendamiento']=$sql1;
	$datos=datos_mysql($sql);
return panel_content($datos["responseResult"],"agendamiento",19);
}*/

function lis_agendamiento(){
	//~ estado=1 or estado=2 or  or estado=5 
    $info=datos_mysql("SELECT COUNT(*) total from agendamiento A LEFT JOIN person P ON A.idpeople=P.idpeople left JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE A.estado not in (1,2,3,5) AND U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}') ".whe_agendamiento());
	$total=$info['responseResult'][0]['total'];
	$regxPag=15;
	$pag=(isset($_POST['pag-agendamiento']))? ($_POST['pag-agendamiento']-1)* $regxPag:0;

	$sql="SELECT idagendamiento ACCIONES,
idpersona Documento,FN_CATALOGODESC(1,tipo_doc) 'Tipo Documento',
FN_CATALOGODESC(275,tipo_cita) 'Tipo Cita',`fecha_cita`,`hora_cita`,fecha_llamada 'Recordación Cita',FN_CATALOGODESC(276,A.estado) Estado
from agendamiento A LEFT JOIN person P ON A.idpeople=P.idpeople left JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE A.estado not in (1,2,3,5) AND U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}')";
	$sql.=whe_agendamiento();
	$sql.="ORDER BY 6 ASC,7 ASC";
	$sql.=' LIMIT '.$pag.','.$regxPag;
//~ echo $sql;   
	$datos=datos_mysql($sql);
return create_table($total,$datos["responseResult"],"agendamiento",$regxPag);
}

function focus_agendamiento(){
 return 'agendamiento';
}

function men_agendamiento(){
 $rta=cap_menus('agendamiento','pro');
 return $rta;
}

function get_agendamiento(){
    // var_dump($_POST['id']);
    $id=divide($_POST['id']);
if($_POST['id']){
    $sql="SELECT T2.idpersona,T2.tipo_doc,T2.nombre1 nombre1,T2.nombre2 nombre2,T2.apellido1 apellido1,T2.apellido2 apellido2,
    T2.fecha_nacimiento,
     concat('Años= ',timestampdiff(YEAR,T2.fecha_nacimiento,curdate()),
    ' Meses= ',MONTH(CURDATE()) - MONTH(T2.fecha_nacimiento) + 12 * IF( MONTH(CURDATE()) < MONTH(T2.fecha_nacimiento),1, IF(MONTH(CURDATE())=MONTH(T2.fecha_nacimiento),IF (DAY(CURDATE()) < DAY(T2.fecha_nacimiento),1,0),0)) - IF(MONTH(CURDATE())<>MONTH(T2.fecha_nacimiento), (DAY(CURDATE()) < DAY(T2.fecha_nacimiento)), IF (DAY(CURDATE()) < DAY(T2.fecha_nacimiento),1,0 ) ), ' Días= ',DAY(CURDATE())-DAY(T2.fecha_nacimiento)+30*(DAY(CURDATE()) < DAY(T2.fecha_nacimiento))) edad,
    T2.genero genero, T2.eapb eapb,T3.telefono1,T3.telefono2,T3.telefono3,
    punto_atencion,tipo_cita,fecha_cita,hora_cita,nombre_atendio,observac_cita 
		FROM agendamiento T1 
		left join person T2 ON T1.idpeople=T2.idpeople
        LEFT JOIN hog_fam T3 ON T2.vivipersona=T3.id_fam
	WHERE T1.idagendamiento='".$id[0]."'";//AND T1.tipodoc=upper('".$id[2]."') AND fecha_cita='".$id[3]."' AND hora_cita='".$id[4]."'
		$info=datos_mysql($sql);
		return $info['responseResult'][0];
}
} 
//concat('Años= ',timestampdiff(YEAR,T2.fecha_nacimiento,curdate()),' Meses= ',MONTH(CURDATE()) - MONTH(T2.fecha_nacimiento) + 12 * IF( MONTH(CURDATE()) < MONTH(T2.fecha_nacimiento),1, IF(MONTH(CURDATE())=MONTH(T2.fecha_nacimiento),IF (DAY(CURDATE()) < DAY(T2.fecha_nacimiento),1,0),0)) - IF(MONTH(CURDATE())<>MONTH(T2.fecha_nacimiento), (DAY(CURDATE()) < DAY(T2.fecha_nacimiento)), IF (DAY(CURDATE()) < DAY(T2.fecha_nacimiento),1,0 ) ), ' Días= ',DAY(CURDATE())-DAY(T2.fecha_nacimiento)+30*(DAY(CURDATE()) < DAY(T2.fecha_nacimiento))) edad,
/* function get_persona(){
		$id=divide($_REQUEST['id']);
		$sql="SELECT 
T1.idpersona,T1.tipo_doc,T1.nombre1,T1.nombre2,T1.apellido1,T1.apellido2,T1.fecha_nacimiento,T1.genero,T1.eapb,T2.telefono1,T2.telefono2,T3.tipo_consulta,T3.punto_atencion,T3.tipo_cita,T3.fecha_cita,T3.hora_cita,T3.nombre_atendio,T3.observac_cita
FROM personas T1
RIGHT join caracterizacion T2 ON T1.ficha=T2.idficha
LEFT join agendamiento T3 ON T1.idpersona=T3.id_persona
WHERE T1.idpersona='".$id[0]."' AND T1.tipo_doc=upper('".$id[1]."')";
		$info=datos_mysql($sql);
		return json_encode($info['responseResult'][0]); 
} */
function get_persona(){
	if ($_REQUEST['id']){
		$id = divide($_REQUEST['id']);
		$sql = "SELECT T1.idpersona, T1.tipo_doc, T1.nombre1, T1.nombre2, T1.apellido1, T1.apellido2, T1.fecha_nacimiento,
				timestampdiff(YEAR, T1.fecha_nacimiento, curdate()) edad,
				T1.sexo, T1.eapb, T3.telefono1, T3.telefono2, T3.telefono3, T4.punto_atencion, T4.tipo_cita, T4.fecha_cita, T4.hora_cita, T4.nombre_atendio, T4.observac_cita
				FROM person T1
				RIGHT JOIN hog_agen T2 ON T1.idpeople = T2.idpeople
				LEFT JOIN hog_fam T3 ON T1.vivipersona = T3.id_fam
				LEFT JOIN agendamiento T4 ON T1.idpeople = T4.idpeople
				WHERE T1.idpersona = '".$id[0]."' AND T1.tipo_doc = UPPER('".$id[1]."')";
		$info = datos_mysql($sql);
		if (!$info['responseResult']) {
			$sql1 = "SELECT T1.idpersona, T1.tipo_doc, T1.nombre1, T1.nombre2, T1.apellido1, T1.apellido2, T1.fecha_nacimiento,
					 timestampdiff(YEAR, T1.fecha_nacimiento, curdate()) edad,
					 T1.sexo, T1.eapb, T3.telefono1, T3.telefono2, T3.telefono3, T4.punto_atencion, T4.tipo_cita, T4.fecha_cita, T4.hora_cita, T4.nombre_atendio, T4.observac_cita
					 FROM person T1
					 LEFT JOIN hog_fam T3 ON T1.vivipersona = T3.id_fam
					 LEFT JOIN agendamiento T4 ON T1.idpeople = T4.idpeople
					 WHERE T1.idpersona = '".$id[0]."' AND T1.tipo_doc = UPPER('".$id[1]."')";
			$info1 = datos_mysql($sql1);
			if (!$info1['responseResult']) {
				return json_encode(new stdClass());
			} else {
				return json_encode($info1['responseResult'][0]);
			}
		} else {
			return json_encode($info['responseResult'][0]);
		}
	}
	return json_encode(new stdClass());
}
function lis_consulta(){
    $info=datos_mysql("SELECT COUNT(*) total FROM agendamiento A LEFT JOIN person P ON A.idpeople=P.idpeople left JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}') ".whe_agendamiento());
	$total=$info['responseResult'][0]['total'];
	$regxPag=5;
	$pag=(isset($_POST['pag-agendamiento']))? ($_POST['pag-agendamiento']-1)* $regxPag:0;

	$id=divide($_POST['id']);
	$sql="SELECT Concat_WS(' ',T2.nombre1,T2.nombre2,T2.apellido1,T2.apellido2) NOMBRES,
	T2.fecha_nacimiento Nacio,FN_CATALOGODESC(21,T2.sexo) Sexo,FN_CATALOGODESC(18,T2.eapb) Eapb,
	T3.telefono1,T3.telefono2,T3.telefono3
	 FROM agendamiento T1
		left join person T2 ON T1.idpeople=T2.idpeople 
        LEFT JOIN hog_fam T3 ON T2.vivipersona=T3.id_fam
		WHERE T1.idagendamiento='{$id[0]}'";
//~ echo $sql;
	$datos=datos_mysql($sql);
return panel_content($datos["responseResult"],"consulta",1);
}
 
 function lis_consulta1(){
	 $id=divide($_POST['id']);
	$sql="SELECT FN_CATALOGODESC(274,punto_atencion) 'Punto de Atención',FN_CATALOGODESC(275,tipo_cita) 'Cita Tipo',fecha_cita Fecha,
	hora_cita Hora 
	FROM agendamiento T1
	left join personas T2 ON T1.idpeople=T2.idpeople 
	WHERE T1.idagendamiento='{$id[0]}'";
//~ echo $sql;
	$datos=datos_mysql($sql);
return panel_content($datos["responseResult"],"consulta",3);
} 

function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  if($a=='agendamiento' && isset($acc['crear']) && $acc['crear']=='SI'){
  	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
  }
  $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
  return $rta;
}

function cmp_agendamiento(){
 $rta="";
 $t=['idpersona'=>'','tipodoc'=>'','nombre1'=>'','nombre2'=>'','apellido1'=>'','apellido2'=>'','tipo_doc'=>'',
 'fecha_nacimiento'=>'','edad'=>'','genero'=>'','eapb'=>'','telefono1'=>'','telefono2'=>'','telefono3'=>'','nombre_atendio'=>'',
 'observac_cita'=>'','tipo_consulta'=>'','punto_atencion'=>'','tipo_cita'=>'','fecha_cita'=>'','hora_cita'=>''];
 $w='agendamiento';
 $d=get_agendamiento(); 
  //~ echo(json_encode($d));
 if ($d=="") {$d=$t;}
 $u=($d['idpersona']=='')?true:false;
 $o='percit';
 $key='find';
$days=fechas_app('agendamiento');
 $c[]=new cmp($o,'e',null,'AGENDAMIENTO DE USUARIOS',$w);
 $c[]=new cmp('ipe','h',50,$_POST['id'],$w,'','idp',null,'','','');  
 //~ $c[]=new cmp('fcr','h',18,$d['fecha_create'],$w.' '.$o,'',0,'','','',false,'','col-4');
 $c[]=new cmp('idp','nu',99999999999999999,$d['idpersona'],$w.' '.$key.' '.$o,'N° Identificación',0,'rgxdfnum','#################',true,$u,'','col-4');
 $c[]=new cmp('tdo','s',3,$d['tipo_doc'],$w.' '.$key.' '.$o,'Tipo Documento','tipo_doc',null,null,true,$u,'','col-3','getPerson');
 $c[]=new cmp('no1','t',50,$d['nombre1'],$w.' '.$o,'Primer Nombre','nombre1',null,null,false,false,'','col-3');
 $c[]=new cmp('no2','t',50,$d['nombre2'],$w.' '.$o,'Segundo Nombre','nombre2',null,null,false,false,'','col-3');
 $c[]=new cmp('ap1','t',50,$d['apellido1'],$w.' '.$o,'Primer Apellido','apellido1',null,null,false,false,'','col-4');
 $c[]=new cmp('ap2','t',50,$d['apellido2'],$w.' '.$o,'Segundo Apellido','apellido2',null,null,false,false,'','col-3');
 $c[]=new cmp('fen','d',10,$d['fecha_nacimiento'],$w.' '.$o,'Fecha de Nacimiento','fecha_nacimiento',null,null,false,false,'','col-3');
 $c[]=new cmp('eda','t',100,$d['edad'],$w.' '.$o,'Edad','edad',null,null,false,false,'','col-4');
 $c[]=new cmp('gen','s',3,$d['genero'],$w.' '.$o,'Sexo','genero',null,null,false,false,'','col-3');
 $c[]=new cmp('eap','s',3,$d['eapb'],$w.' '.$o,'Eapb','eapb',null,null,false,false,'','col-4');
 $c[]=new cmp('te1','na',10,$d['telefono1'],$w.' '.$o,'Telefono 1','telefono1',null,null,false,false,'','col-2');
 $c[]=new cmp('te2','na',10,$d['telefono2'],$w.' '.$o,'Telefono 2','telefono2',null,null,false,false,'','col-2'); 
 $c[]=new cmp('te3','na',10,$d['telefono3'],$w.' '.$o,'Telefono 3','telefono3',null,null,false,false,'','col-2'); 
 $c[]=new cmp('pun','s',3,$d['punto_atencion'],$w.' '.$o,'Punto de Atención','punto_atenc',null,null,true,true,'','col-5'); 
 $c[]=new cmp('cit','s',3,$d['tipo_cita'],$w.' '.$o,'Tipo de Cita','tipo_cita',null,null,true,$u,'','col-5'); 
 $c[]=new cmp('fci','d',10,$d['fecha_cita'],$w.' '.$o,'Fecha','fecha',null,null,true,true,'','col-3',"validDate(this,$days,30);"); 
 $c[]=new cmp('hci','c',10,$d['hora_cita'],$w.' '.$o,'Hora','hora',null,null,true,true,'','col-2','validTime');
 $c[]=new cmp('nom','t',100,$d['nombre_atendio'],$w,'Persona que Atendio','nombre_atendio',null,null,true,true,'','col-5');
 $c[]=new cmp('obc','a',1000,$d['observac_cita'],$w.' '.$o,'Observaciones','observacion',null,null,false,true,'','col-10s'); 
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 $rta.="<br>";
 $rta.="</div>";
 return $rta;
}
/* function cmp_observaciones(){
 $rta="";
 $t=['id_persona'=>'','tipodoc'=>'','nombre1'=>'','nombre2'=>'','apellido1'=>'','apellido2'=>'','tipo_doc'=>'',
 'fecha_nacimiento'=>'','edad'=>'','genero'=>'','eapb'=>'','telefono1'=>'','telefono2'=>'','estado'=>''];
 $w='observaciones';
  $d=get_observ(); 
 if ($d=="") {$d=$t;}
 $u=($d['id_persona']=='')?true:false;
 $o='percit';
 $c[]=new cmp($o,'e',null,'OBSERVACIONES DEL NO AGENDAMIENTO ',$w);
 $c[]=new cmp('ipe','h',50,$_POST['id'],$w,'','idp',null,'','','');  
 //~ $c[]=new cmp('fcr','h',18,$d['fecha_create'],$w.' '.$o,'',0,'','','',false,'','col-4');
 $c[]=new cmp('idp','n',18,$d['id_persona'],$w.' '.$o,'N° Identificación',0,'rgxdfnum','#################',true,$u,'','col-3');
 $c[]=new cmp('tdo','s',3,$d['tipodoc'],$w.' '.$o,'Tipo Documento','tipo_doc',null,null,true,$u,'','col-4','getPerson');
 $c[]=new cmp('no1','t',50,$d['nombre1'],$w.' '.$o,'Primer Nombre','nombre1',null,null,false,false,'','col-3');
 $c[]=new cmp('no2','t',50,$d['nombre2'],$w.' '.$o,'Segundo Nombre','nombre2',null,null,false,false,'','col-3');
 $c[]=new cmp('ap1','t',50,$d['apellido1'],$w.' '.$o,'Primer Apellido','apellido1',null,null,false,false,'','col-4');
 $c[]=new cmp('ap2','t',50,$d['apellido2'],$w.' '.$o,'Segundo Apellido','apellido2',null,null,false,false,'','col-3');
 $c[]=new cmp('fen','d',10,$d['fecha_nacimiento'],$w.' '.$o,'Fecha de Nacimiento','fecha_nacimiento',null,null,false,false,'','col-3');
 $c[]=new cmp('eda','t',50,$d['edad'],$w.' '.$o,'Edad','edad',null,null,false,false,'','col-4');
 $c[]=new cmp('gen','s',3,$d['genero'],$w.' '.$o,'Sexo','genero',null,null,false,false,'','col-3');
 $c[]=new cmp('eap','s',3,$d['eapb'],$w.' '.$o,'Eapb','eapb',null,null,false,false,'','col-3');
 $c[]=new cmp('te1','t',10,$d['telefono1'],$w.' '.$o,'Telefono 1','telefono1',null,null,false,false,'','col-2');
 $c[]=new cmp('te2','t',10,$d['telefono2'],$w.' '.$o,'Telefono 2','telefono2',null,null,false,false,'','col-2'); 
 $c[]=new cmp('cit','s',3,$d['tipo_cita'],$w.' '.$o,'Tipo de Cita','tipo_cita',null,null,true,$u,'','col-5'); 
 $c[]=new cmp('est','s',3,$d['estado'],$w.' '.$o,'Estado','estado',null,null,true,$u,'','col-5'); 
 $c[]=new cmp('obc','a',1000,$d['observac_cita'],$w.' '.$o,'Observaciones','observacion',null,null,false,true,'','col-10s'); 
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 $rta.="<br>";
 $rta.="</div>";
 return $rta;
} 

function focus_observaciones(){
 return 'observaciones';
}

function men_observaciones(){
 $rta=cap_menus('observaciones','pro');
 return $rta;
}

function gra_observaciones(){
	$sql="UPDATE frecuenciauso SET `realizada`='OB'
	WHERE id_persona='{$_POST['idp']}' AND tipo_doc=UPPER('{$_POST['tdo']}') AND tipo_cita='{$_POST['cit']}' AND realizada='NO';";
	  $rta=dato_mysql($sql);
	echo $sql;
	//~ $obs = trim(preg_replace('/\s+/', ' ',$_POST['obc']));
	$obs= trim(preg_replace("/[\r\n|\n|\r]+/",PHP_EOL,$_POST['obc']));
 $sql="INSERT INTO observagendamiento VALUES ({$_POST['idp']},UPPER('{$_POST['tdo']}'),'{$_POST['cit']}','{$_POST['est']}',UPPER('{$obs}'),
 '{$_SESSION['us_sds']}', NULL, NULL, 'A');";	
	//~ echo $sql;
  $rta=dato_mysql($sql);
  return $rta;	
}

function get_observ(){
	$id=divide($_POST['id']);			
	$sql="SELECT T1.id_persona,T1.tipodoc,T2.nombre1,T2.nombre2,T2.apellido1,T2.apellido2,T2.fecha_nacimiento,
	concat('Años= ',
	timestampdiff(YEAR,T2.fecha_nacimiento,curdate()),
	', Meses= ',
	MONTH(CURDATE()) - MONTH(T2.fecha_nacimiento) + 12 *IF( MONTH(CURDATE())<MONTH(T2.fecha_nacimiento), 1,IF(MONTH(CURDATE())=MONTH(T2.fecha_nacimiento),IF (DAY(CURDATE())<DAY(T2.fecha_nacimiento),1,0),0)
) - IF(MONTH(CURDATE())<>MONTH(T2.fecha_nacimiento),(DAY(CURDATE())<DAY(T2.fecha_nacimiento)), IF (DAY(CURDATE())<DAY(T2.fecha_nacimiento),1,0 ) ),
	', Días= ',
	DAY(CURDATE())-DAY(T2.fecha_nacimiento)+30*(DAY(CURDATE())< DAY(T2.fecha_nacimiento))) edad,T2.genero,
	T2.eapb,T3.telefono1,T3.telefono2,tipo_consulta
	FROM agendamiento T1 
	left join personas	T2 ON T1.id_persona=T2.idpersona 
	left join personas1 T4 ON T1.id_persona=T4.idpersona 
	left join caracterizacion T3 ON T1.id_persona=T3.id_persona 
	WHERE T1.id_persona='".$id[1]."' AND T1.tipodoc=upper('".$id[2]."')";
		$info=datos_mysql($sql);
		//~ echo($sql);
		return $info['responseResult'][0];
}

 function lis_observaciones(){
	$sql="SELECT `id_persona`,`tipodoc`,`tipo_cita`,estados,`observac_cita`,`usu_creo`,`usu_update`,`fecha_update`,`estado` FROM `observagendamiento`";
		$_SESSION['sql_observaciones']=$sql;
}
*/

function gra_agendamiento(){
	$obs= trim(preg_replace("/[\r\n|\n|\r]+/",PHP_EOL,$_POST['obc']));
 if ($_POST['ipe']){
  $id=divide($_POST['ipe']);
	$sql="UPDATE agendamiento SET punto_atencion=?,fecha_cita=?,
	hora_cita=?,nombre_atendio=?,observac_cita=?,
	usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR)  
	WHERE idagendamiento= ?;";
$params=[
	['type' => 's', 'value' => $_POST['pun']],
	['type' => 's', 'value' => $_POST['fci']],
	['type' => 's', 'value' => $_POST['hci']],
	['type' => 's', 'value' => $_POST['nom']],
	['type' => 's', 'value' => $obs],
	['type' => 's', 'value' => $_SESSION['us_sds']],
	['type' => 's', 'value' => $id[0]]
];

 //~ echo $sql;
 $rta = mysql_prepd($sql, $params);
//  $rta=dato_mysql($sql);
	return $rta;
 }else{
    $sql="SELECT idpeople from person where idpersona='".$_POST['idp']."' AND tipo_doc='".$_POST['tdo']."'";
	$id=datos_mysql($sql);
	$id=$id['responseResult'][0]['idpeople'];
	$sql="INSERT INTO agendamiento VALUES (?,?,?,?,DATE_SUB(NOW(), INTERVAL 5 HOUR),
	?,?,?,?,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,?, NULL, NULL,?);";
	$params=[
		['type' => 'i','value' => NULL],
		['type' => 'i', 'value' => $id],
		['type' => 'i', 'value' => $_POST['pun']],
		['type' => 'i', 'value' => $_POST['cit']],
		['type' => 's', 'value' => $_POST['fci']],
		['type' => 's', 'value' => $_POST['hci']],
		['type' => 's', 'value' => $_POST['nom']],
		['type' => 's', 'value' => $obs],
		['type' => 's', 'value' => $_SESSION['us_sds']],
		['type' => 'i', 'value' => 4],
	];
	//~ echo $sql;";
	$rta=mysql_prepd($sql, $params);
/* 	$sql="INSERT INTO agendamiento VALUES (NULL,$id,'{$_POST['pun']}','{$_POST['cit']}',DATE_SUB(NOW(), INTERVAL 5 HOUR),
    '{$_POST['fci']}','{$_POST['hci']}','{$_POST['nom']}',trim('{$obs}'),NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,'{$_SESSION['us_sds']}', NULL, NULL, '4');"; 
    echo $sql;
	$rta=datos_mysql($sql);
    var_dump($rta['responseResult'][0]["affected_rows"]);
	*/
    $rta1=$rta['responseResult'][0]["affected_rows"];
	if (strpos($rta1,1) === false) {
		$rta='Ouch!, No se realizo la creación de la cita (Posiblemente este usuario ya tiene una cita agendada en esta misma fecha), compruebe la información del usuario e intente nuevamente.';
	}else{
        $rta="Se ha Insertado : " .$rta1. " Registro Correctamente.";
		 $sql="SELECT MAX(idagendamiento) AS id FROM agendamiento;";
		 $info=datos_mysql($sql);
		 $id=$info['responseResult'][0]["id"]; 
//~ echo " El id = ".$id." ";
		$upfr=gra_finalizado($id);
        // var_dump($upfr);
		if (strpos($upfr, 'correctamente') === false) {
			$upfr=', Sin embargo, No se pudo realizar la actualización de la cita, en el campo realizado en la tabla frecuencia de uso.';
		}else{$upfr='';}
	}
	return $rta." ".$upfr;
 }
}

function gra_finalizado($a=''){
	$sql="SELECT T1.idpeople id,T1.tipo_cita
	FROM agendamiento T1
	left join person T2 ON T1.idpeople=T2.idpeople 
	WHERE T1.idagendamiento='{$a}'";
	$info=datos_mysql($sql);
	$id=$info['responseResult'][0]["id"]; 
	$cita=$info['responseResult'][0]["tipo_cita"]; 

    $sql1 = "UPDATE frecuenciauso SET `realizada`='SI' 
    WHERE idpeople=? AND tipo_cita=? AND realizada='NO';";
	$params1 = array(
	array('type' => 'i', 'value' => $id),
	array('type' => 's', 'value' => $cita)
	);
    // $rta1 = show_sql($sql1, $params1);
	$rta1 = mysql_prepd($sql1, $params1);
  return $rta1;
}

function opc_idptdo(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		// var_dump($_REQUEST);
		/* $sql1="SELECT DISTINCT(t1.tipo_cita) id,FN_CATALOGODESC(39,t1.tipo_cita) tcita 
		FROM frecuenciauso t1
        LEFT JOIN agendamiento t2 ON t1.id_persona=t2.id_persona 
		WHERE t1.idpeople='1023904500' AND t1.tipo_doc=UPPER('CC') AND t1.realizada='NO' 
        AND t1.tipo_cita NOT IN( SELECT tipo_cita FROM agendamiento WHERE `idpeople`='1023904500' AND `tipodoc`=UPPER('CC') AND `estado` IN (4,6)) ;"; */
		$sql="SELECT idpeople from person where idpersona='".$id[0]."' AND tipo_doc='".$id[1]."'";
	    $id=datos_mysql($sql);
	    $id=$id['responseResult'][0]['idpeople'];
        
		$sql="SELECT tipo_cita id,FN_CATALOGODESC(275,tipo_cita) tcita 
		FROM frecuenciauso 
		WHERE idpeople='".$id."' AND realizada='NO' AND estado='A' ;";
		//~ var_dump($sql);
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	}  
}

function opc_tipo_doc($id=''){
	//~ var_dump($id);
	//~ var_dump(opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",""));
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function opc_genero($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}
function opc_eapb($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=18 and estado='A' ORDER BY 1",$id);
}
function opc_etnia($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=16 and estado='A' ORDER BY 1",$id);
}
function opc_nacionalidad($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=30 and estado='A' ORDER BY 1",$id);
}
function opc_punto_atenc($id=''){
	return opc_sql("SELECT `idcatadeta`,concat(idcatadeta,' - ',descripcion) FROM `catadeta` WHERE idcatalogo=274 and estado='A' and (valor=(select subred from usuarios where id_usuario='{$_SESSION['us_sds']}') OR valor=0) ORDER BY LENGTH(idcatadeta), idcatadeta",$id);
}
function opc_tipo_cita($id=''){
    return opc_sql("SELECT `idcatadeta`,concat(idcatadeta,' - ',descripcion) FROM `catadeta` WHERE idcatalogo=275 and estado='A' ORDER BY LENGTH(idcatadeta), idcatadeta",$id);
 /*    if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);

        $sql="SELECT idpeople from person where idpersona='".$_REQUEST['idp']."' AND tipo_doc='".$_REQUEST['tdo']."'";
        $id=datos_mysql($sql);
        $id=$id['responseResult'][0]['idpeople'];

	$sql="SELECT tipo_cita id,FN_CATALOGODESC(275,tipo_cita) tcita 
		FROM frecuenciauso 
		WHERE idpeople='".$id."' AND realizada='NO' AND observaciones=1 AND estado='A' ;";
		var_dump($sql);
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
    } */
}
function opc_tip_inasis($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=282 and estado='A' ORDER BY 1",$id);
}
function opc_estados($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=276 and estado='A' ORDER BY 1",$id);
}
function opc_estado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=280 and estado='A' ORDER BY 1",$id);
}
function opc_est($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=276 and idcatadeta IN(2,3,5) and estado='A' ORDER BY 1",$id);
}


 /***********************INICIO RECORDAR ASISTENCIA********************************/


 function cmp_confirma_asistencia(){
 $rta="";
 $w='confirma_asistencia';
 $d="";
 $rta=" <span class='mensaje' id='".$w."-msj' ></span>";
 $rta.="<div id='tblConsulta'>".lis_consulta()."</div>";
 $rta.="<div id='tblConsulta1'>".lis_consulta1()."</div>";
  $c[]=new cmp('ipe','h',10,$_POST['id'],$w,'','idp',null,'','','');  
 //~ $c[]=new cmp('idp','h',10,$_POST['id'],$w,'','idp',null,'','','');  
 $c[]=new cmp(null,'e',null,'CONFIRMACIÓN DE ASISTENCIA',$w);
 $c[]=new cmp('nom','t',100,$d,$w,'Persona que Atendio','nombre',null,null,true,true,'','col-5');
 $c[]=new cmp('con','o',2,$d,$w,'Confirma Asistencia','con',null,null,true,true,'','col-2');
 $c[]=new cmp('msj','o',2,$d,$w,'Desea envio de Mensaje de Texto','msj',null,null,true,true,'','col-3');
 $c[]=new cmp('obl','a',1000,$d,$w,'Observaciones','observacion',null,null,false,true,'','col-10s'); 
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 $rta.="</div>";
 return $rta;
}

function focus_confirma_asistencia(){
 return 'confirma_asistencia';
}

function men_confirma_asistencia(){
 $rta=cap_menus('confirma_asistencia','pro');
 return $rta;
}

function gra_confirma_asistencia(){
	$id=divide($_POST['ipe']);
	/* $sql="UPDATE agendamiento SET fecha_llamada=DATE_SUB(NOW(), INTERVAL 5 HOUR),
	nombre_llamada=UPPER('{$_POST['nom']}'),confirma_cita='{$_POST['con']}',
	msjtxt='{$_POST['msj']}',observac_llamadas=trim(UPPER('{$_POST['obl']}')),
	usu_update='".$_SESSION['us_sds']."',fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR),estado='6' 
 WHERE idagendamiento='{$id[0]}';";
	//~ echo $sql;
  $rta=dato_mysql($sql); */
  $sql="UPDATE agendamiento SET fecha_llamada=DATE_SUB(NOW(), INTERVAL 5 HOUR),
	nombre_llamada=?,confirma_cita=?,msjtxt=?,observac_llamadas=?,
	usu_update=?,fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR),estado=? 
 WHERE idagendamiento=?;";
  $params=[
		['type' => 's', 'value' => $_POST['nom']],
		['type' => 's', 'value' => $_POST['con']],
		['type' => 's', 'value' => $_POST['msj']],
		['type' => 's', 'value' => $_POST['obl']],
		['type' => 's', 'value' => $_SESSION['us_sds']],
		['type' => 'i', 'value' => 6],
		['type' => 'i', 'value' => $id[0]]
	];
	$rta = mysql_prepd($sql, $params);
  return $rta;	
}

  /***********************FIN RECORDAR ASISTENCIA********************************/

 /***********************INICIO SEGUIMIENTO********************************/


 function cmp_seguimiento(){
 $rta="";
 $w='seguimiento';
 $d="";
 $rta=" <span class='mensaje' id='".$w."-msj' ></span>";
 $rta.="<div id='tblConsulta'>".lis_consulta()."</div>";
 $rta.="<div id='tblConsulta1'>".lis_consulta1()."</div>";
 $c[]=new cmp('idp','h',10,$_POST['id'],$w,'','idp',null,'','','');  
 $c[]=new cmp(null,'e',null,'SEGUIMIENTO DE LA CITA',$w);
 $c[]=new cmp('asi','o',2,$d,$w,'Asistio a la Cita','asi',null,null,true,true,'','col-2','asist(this)');
 $c[]=new cmp('nom','t',100,$d,$w,'Persona que Atendio','nombre',null,null,true,true,'','col-5');
 $c[]=new cmp('tin','s',3,$d,$w,'Motivo de la Inasistencia','tip_inasis',null,null,true,true,'','col-3'); 
 $c[]=new cmp('rea','o',2,$d,$w,'Reasignar Cita','rea',null,null,true,true,'','col-3');
 $c[]=new cmp('est','s',2,$d,$w,'Estado','est',null,null,true,true,'','col-4');
 $c[]=new cmp('obi','a',1000,$d,$w,'Observaciones','observacion',null,null,false,true,'','col-10s'); 
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 $rta.="</div>";
 return $rta;
}

function focus_seguimiento(){
 return 'seguimiento';
}

function men_seguimiento(){
 $rta=cap_menus('seguimiento','pro');
 return $rta;
}

function gra_seguimiento(){
	$id=divide($_POST['idp']);
	if($_POST['asi']=='SI'){
		$est='1';
	}else{
		$est=$_POST['est'];
	}
	/* $sql="UPDATE agendamiento SET `fecha_llamada2`=DATE_SUB(NOW(), INTERVAL 5 HOUR), `nombre_llamada2`=UPPER('{$_POST['nom']}'),`motivo_inasistencia`='{$_POST['tin']}', `reasigno`='{$_POST['rea']}',
	`observac_llamada2`=trim(UPPER('{$_POST['obi']}')),`usu_update`='".$_SESSION['us_sds']."', `fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR), `estado`='{$est}'
	WHERE idagendamiento='{$id[0]}'";// AND tipodoc=UPPER('{$id[2]}') AND fecha_cita='{$id[3]}' AND hora_cita='{$id[4]}';
	//~ echo $sql;
  $rta=dato_mysql($sql); */
  $sql="UPDATE agendamiento SET `fecha_llamada2`=DATE_SUB(NOW(), INTERVAL 5 HOUR), `nombre_llamada2`=?,`motivo_inasistencia`=?, `reasigno`=?,
	`observac_llamada2`=?,`usu_update`=?, `fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR), `estado`=?
	WHERE idagendamiento=?";
	$params=[
		['type' => 's', 'value' => $_POST['nom']],
		['type' => (empty($_POST['tin']) ? 'z' : 's'), 'value' => (empty($_POST['tin']) ? null : $_POST['tin'])],
		['type' => 's', 'value' => $_POST['rea']],
		['type' => 's', 'value' => $_POST['obi']],
		['type' => 's', 'value' => $_SESSION['us_sds']],
		['type' => 'i', 'value' => $est],
		['type' => 'i', 'value' => $id[0]]
	];
	$rta = mysql_prepd($sql, $params);
  return $rta;
}
  /***********************FIN SEGUIMIENTO********************************/

  
  
function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
  if ($a=='agendamiento'&& $b=='id'){$rta= "<div class='txt-center'>".$c['ID']."</div>";}
  //~ var_dump($c);
 if (($a=='agendamiento') && ($b=='acciones'))    {
		$rta="<nav class='menu right'>";
		$rta.="<li class='icono editarAgenda' title='Editar Cita OK' id='".$c['ACCIONES']."' Onclick=\"mostrar('agendamiento','pro',event,'','lib.php',4);\"></li>";
		if ($c['Estado']=='PENDIENTE'){
			$rta.="<li class='icono confirmaAgenda' title='Confirmar Asistencia' id='".$c['ACCIONES']."' OnClick=\"mostrar('confirma_asistencia','pro',event,'','lib.php',9,'confirma_asistencia');\" ></li>";
		}
		if ($c['Estado']=='RECORDADA'){
			$rta.="<li class='icono efectividadAgenda' title='Realizar Efectividad' id='".$c['ACCIONES']."' OnClick=\"mostrar('seguimiento','pro',event,'','lib.php',9,'seguimiento');\" ></li>";
		}
		$rta.="</nav>";
	}
	
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}

/*
SELECT T2.fecha 'Fecha Caracterizacion',T1.apellido1 'Primer Apellido',T1.apellido2 'Segundo Apellido',T1.nombre1 'Primer Nombre',
	T1.nombre2 'Segundo Nombre',T1.idpersona 'N° Documento',FN_CATALOGODESC(1,T1.tipo_doc) 'Tipo Documento',T1.fecha_nacimiento 'Fecha de Nacimiento',
	FN_CATALOGODESC(21,T1.genero) 'Genero',FN_CATALOGODESC(16,T1.etnia) 'Etnia',FN_CATALOGODESC(30,T1.nacionalidad) 'Nacionalidad',
	FN_CATALOGODESC(2,T2.localidad) 'Localidad',	T2.upz 'UPZ',T2.direccion 'Dirección',T2.telefono1 'Teléfono1',T2.telefono2 'Teléfono2',
	FN_CATALOGODESC(37,T3.tipo_consulta) 'Tipo de Consulta',FN_CATALOGODESC(38,T3.punto_atencion) 'PUnto de Atención',FN_CATALOGODESC(39,T3.tipo_cita) 'Tipo de Cita',
	T3.fecha_create 'Fecha de Asignación',T3.fecha_cita 'Fecha de la Cita',T3.hora_cita 'Hora de la Cita',T3.nombre_atendio 'Nombre quien Atendió Llamada',
	FN_CATALOGODESC(40,T3.estado) 'Estado',T3.usu_creo 'Digitador',T3.observac_cita 'Observación Cita',IFNULL(T3.fecha_llamada,'00-00-0000') 'Fecha Recordación',
	ifnull(T3.nombre_llamada,'-') 'Nombre quien Recibió Llamada' ,ifnull(T3.confirma_cita,'-') 'Confirmo Cita',ifnull(T3.msjtxt,'-') 'Desea Envio de Msj',
	ifnull(T3.usu_update,'-') 'Digitador1',ifnull(T3.observac_llamadas,'-') 'Observaciones de Recordación',ifnull(T3.fecha_llamada2,'-') 'Fecha Llamada por Efectividad',ifnull(T3.nombre_llamada2,'-') 'Nombre quien Contesto Llamada',ifnull(FN_CATALOGODESC(41,T3.motivo_inasistencia),'-') 'Motivo de la Inasistencia',ifnull(T3.reasigno,'-') 'Se reasigno la Cita',ifnull(T3.usu_update,'-') 'Digitador2',ifnull(T3.observac_llamada2,'-') 'Observaciones de Inasistencia'



SELECT FN_CATALOGODESC(37,T1.tipo_consulta) 'Tipo de Consulta',FN_CATALOGODESC(38,T1.punto_atencion) 'PUnto de Atención',FN_CATALOGODESC(39,T1.tipo_cita) 'Tipo de Cita',
	T1.fecha_create 'Fecha de Asignación',T1.fecha_cita 'Fecha de la Cita',T1.hora_cita 'Hora de la Cita',T1.nombre_atendio 'Nombre quien Atendió Llamada',
	FN_CATALOGODESC(40,T1.estado) 'Estado',T1.usu_creo 'Digitador',T1.observac_cita 'Observación Cita',IFNULL(T1.fecha_llamada,'00-00-0000') 'Fecha Recordación',
	ifnull(T1.nombre_llamada,'-') 'Nombre quien Recibió Llamada' ,ifnull(T1.confirma_cita,'-') 'Confirmo Cita',ifnull(T1.msjtxt,'-') 'Desea Envio de Msj',
	ifnull(T1.usu_update,'-') 'Digitador1',ifnull(T1.observac_llamadas,'-') 'Observaciones de Recordación',ifnull(T1.fecha_llamada2,'-') 'Fecha Llamada por Efectividad',ifnull(T1.nombre_llamada2,'-') 'Nombre quien Contesto Llamada',ifnull(FN_CATALOGODESC(41,T1.motivo_inasistencia),'-') 'Motivo de la Inasistencia',ifnull(T1.reasigno,'-') 'Se reasigno la Cita',ifnull(T1.usu_update,'-') 'Digitador2',ifnull(T1.observac_llamada2,'-') 'Observaciones de Inasistencia',T4.fecha 'Fecha Caracterizacion',FN_CATALOGODESC(2,T4.localidad) 'Localidad',	T4.upz 'UPZ',T4.direccion 'Dirección',T4.telefono1 'Teléfono1',T4.telefono2 'Teléfono2' FROM agendamiento T1
	LEFT join personas1 T2 ON T1.id_persona=T2.idpersona AND T1.tipodoc=T2.tipo_doc
    LEFT join personas T3 ON T1.id_persona=T3.idpersona AND T1.tipodoc=T3.tipo_doc 
    LEFT join caracterizacion T4 ON T3.idpersona=T4.id_persona
	WHERE '1'='1' ORDER BY `fecha_cita` ASC,`hora_cita` ASC
*/
?>



