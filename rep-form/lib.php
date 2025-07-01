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


function lis_rute(){
	$info=datos_mysql("SELECT COUNT(*) total from eac_ruteo 
	where subred_report in(select subred from usuarios where id_usuario = '".$_SESSION['us_sds']."') ".whe_rute());
	$total=$info['responseResult'][0]['total'];
	$regxPag=5;
	$pag=(isset($_POST['pag-rute']))? ($_POST['pag-rute']-1)* $regxPag:0;
	$sql="SELECT ROW_NUMBER() OVER (ORDER BY 1) R,`id_ruteo` ACCIONES, id_ruteo AS Cod_Registro ,idgeo AS Cod_Predio, nombres, `fecha_asig` AS Asignado, FN_CATALOGODESC(235,tipo_prior) AS Grupo_Poblacion_Priorizada, intervencion AS Intervencion_A_realizar, caracteriza AS Usuario_Caracterizado, estado
  FROM `eac_ruteo` 
  WHERE subred_report in(select subred from usuarios where id_usuario = '".$_SESSION['us_sds']."') ".whe_rute();
	$sql.="ORDER BY fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	//echo($sql);
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"rute",$regxPag);
	}

function whe_rute() {
	$sql = " AND estado='A' ";
	 if ($_POST['fcod'])
		$sql .= " AND id_ruteo = '".$_POST['fcod']."'";
	/*if ($_POST['fgrupo'])
		$sql .= " AND priorizacion = '".$_POST['fgrupo']."'";
	if ($_POST['fseca'])
		$sql .= " AND sector_catastral = '".$_POST['fseca']."'";
	if ($_POST['fmanz'])
		$sql .= " AND nummanzana ='".$_POST['fmanz']."' ";
	if ($_POST['fpred'])
		$sql .= " AND predio_num ='".$_POST['fpred']."' "; */
	return $sql;
}


function focus_rute(){
 return 'rute';
}


function men_rute(){
 $rta=cap_menus('rute','pro');
 return $rta;
}


function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  if ($a=='rute'){  
	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
  }
  return $rta;
}


function cmp_rute(){
 $rta="";
$rta .="<div class='encabezado vivienda'>TABLA DE GESTIONES REALIZADAS</div>
	<div class='contenido' id='datos-lis' >".lista_gestion()."</div></div>";
 $t=['id'=>'','fecha_asig'=>'','fuente'=>'','priorizacion'=>'','tipo_prior'=>'','tipo_doc'=>'','documento'=>'','nombres'=>'','fecha_nac'=>'','sexo'=>'',
 'nacionalidad'=>'','etnia'=>'','regimen'=>'','eapb'=>'','tipo_doc_acu'=>'','documento_acu'=>'','nombres_acu'=>'','direccion'=>'','telefono1'=>'','telefono2'=>'','telefono3'=>'',
 'intervencion'=>'','caracteriza'=>'','per_consul'=>'','fecha_consulta'=>'','cod_cups'=>'',
 'subred_report'=>'','localidad'=>'','upz'=>'','barrio'=>'','cordx'=>'','cordy'=>'','fecha_gestion'=>'','fecha_gest'=>'','estado_g'=>'',
 'motivo_estado'=>'','direccion_nueva'=>'','sect'=>'', 'manz'=>'','pred'=>'', 'obse'=>'','dir_new'=>'','sector'=>'', 'manzana'=>'','predio'=>'','usu_creo'=>'', 'fecha_create'=>'', 'usu_update'=>'', 
 'fecha_update'=>'', 'estado'=>''];
 $w='rute';
$d=get_rute(); 
 if ($d=="") {$d=$t;}
 $u=($d['idgeo']=='0')?true:false;
//  var_dump($d['estado_g']);
 $x=($d['idgeo']=='0')?false:true;
 
// var_dump($_POST);
// var_dump($d);
 $o='segrep';
 $c[]=new cmp($o,'e',null,'SEGUIMIENTO REPORTE',$w);
 $c[]=new cmp('id','h','20',$d['id_ruteo'],$w.' '.$o,'','',null,null,true,false,'','col-1');
 $c[]=new cmp('fecha_asig','d','10',$d['fecha_asig'],$w.' '.$o,'FECHA ASINACION','fecha_asig',null,null,false,false,'','col-15');
 $c[]=new cmp('fuente','s','3',$d['fuente'],$w.' '.$o,'FUENTE O REMITENTE','fuente',null,null,false,false,'','col-25');
 $c[]=new cmp('priorizacion','s','3',$d['priorizacion'],$w.' '.$o,'COHORTE DE RIESGO','priorizacion',null,null,false,false,'','col-3');
 $c[]=new cmp('tipo_prior','s','3',$d['tipo_prior'],$w.' '.$o,'GRUPO DE POBLACION PRIORIZADA','tipo_prior',null,null,false,false,'','col-3');

 $o='atemed';
 $c[]=new cmp($o,'e',null,'DATOS DEL USUARIO QUE REQUIERE LA ATENCIÓN MEDICA',$w);
 $c[]=new cmp('tipo_doc','s','3',$d['tipo_doc'],$w.' '.$o,'TIPO DE DOCUMENTO','tipo_doc',null,null,false,false,'','col-2');
 $c[]=new cmp('documento','t','20',$d['documento'],$w.' '.$o,'NUMERO DE DOCUMENTO','documento',null,null,false,false,'','col-2');
 $c[]=new cmp('nombres','t','80',$d['nombres'],$w.' '.$o,'NOMBRES Y APELLIDOS DEL USUARIO','nombres',null,null,false,false,'','col-4');
 $c[]=new cmp('fecha_nac','d','10',$d['fecha_nac'],$w.' '.$o,'FECHA DE NACIMIENTO','fecha_nac',null,null,false,false,'','col-2');
 $c[]=new cmp('sexo','s','3',$d['sexo'],$w.' '.$o,'SEXO','sexo',null,null,false,false,'','col-2');
 $c[]=new cmp('nacionalidad','s','3',$d['nacionalidad'],$w.' '.$o,'NACIONALIDAD','nacionalidad',null,null,false,false,'','col-2');
 $c[]=new cmp('etnia','s','3',$d['etnia'],$w.' '.$o,'Pertenencia Etnica','etnia',null,null,false,false,'','col-2');
 $c[]=new cmp('regimen','s','3',$d['regimen'],$w.' '.$o,'regimen','regimen',null,null,false,false,'','col-2');
 $c[]=new cmp('eapb','s','3',$d['eapb'],$w.'  '.$o,'eapb','eapb',null,null,false,false,'','col-2');
 
 $o='datacu';
 //$c[]=new cmp($o,'e',null,'DATOS DEL ACUDIENTE (Estas variables se diligencian para los menores de edad), o datos del usuario quien coloco la solicitud  PQR',$w);
 $c[]=new cmp('tipo_doc_acu','s','3',$d['tipo_doc_acu'],$w.' '.$o,'TIPO DE DOCUMENTO ACUDIENTE','tipo_doc_acu',null,null,false,false,'','col-2');
 $c[]=new cmp('documento_acu','t','20',$d['documento_acu'],$w.' '.$o,'DOCUMENTO ACUDIENTE','documento_acu',null,null,false,false,'','col-2');
 $c[]=new cmp('nombres_acu','t','80',$d['nombres_acu'],$w.' '.$o,'NOMBRES Y APELLIDOS DEL ACUDIENTE','nombres_acu',null,null,false,false,'','col-6');

 $o='datcon';
 //$c[]=new cmp($o,'e',null,'DATOS DE CONTACTO',$w);
 $c[]=new cmp('direccion','t','90',$d['direccion'],$w.' '.$o,'Direccion','direccion',null,null,false,false,'','col-4');
 $c[]=new cmp('telefono1','n','10',$d['telefono1'],$w.' '.$o,'Telefono 1','telefono1',null,null,false,false,'','col-2');
 $c[]=new cmp('telefono2','n','10',$d['telefono2'],$w.' '.$o,'Telefono 2','telefono2',null,null,false,false,'','col-2');
 $c[]=new cmp('telefono3','n','10',$d['telefono3'],$w.' '.$o,'Telefono 3','telefono3',null,null,false,false,'','col-2');

 $o='datcup';
 $c[]=new cmp($o,'e',null,'CONSULTA AUTORIZADA',$w);
 $c[]=new cmp('intervencion','t','3',$d['intervencion'],$w.' '.$o,'Intervencion a Realizar','intervencion',null,null,false,false,'','col-4');
 $c[]=new cmp('caracteriza','t','3',$d['caracteriza'],$w.' '.$o,'Usuario Caracterizado','caracteriza',null,null,false,false,'','col-2');
 $c[]=new cmp('per_consul','n','10',$d['per_consul'],$w.' '.$o,'Perfil de la Intervencion','per_consul',null,null,false,false,'','col-4'); 
 $c[]=new cmp('fecha_consulta','d','90',$d['direccion'],$w.' '.$o,'Fecha Ultima Consulta','fecha_asig',null,null,false,false,'','col-3');
 $c[]=new cmp('cod_cups','n','10',$d['cod_cups'],$w.' '.$o,'Cups Autorizado por EAPB','cod_cups',null,null,false,false,'','col-3');
 
 
 $o='datesp';
 $c[]=new cmp($o,'e',null,'DATOS DE UBUCACIÓN',$w);
 
 $c[]=new cmp('subred_report','s','3',$d['subred_report'],$w.' '.$o,'Subred','subred_report',null,null,false,false,'','col-2');
 $c[]=new cmp('localidad','s','3',$d['localidad'],$w.' '.$o,'Localidad','localidad',null,null,false,false,'','col-2');
 $c[]=new cmp('upz','s','3',$d['upz'],$w.' '.$o,'Upz','upz',null,null,false,false,'','col-2');
 $c[]=new cmp('barrio','s','5',$d['barrio'],$w.' '.$o,'Barrio','barrio',null,null,false,false,'','col-4');
 $c[]=new cmp('sector_catastral','n','6',$d['sector_catastral'],$w.' '.$o,'Sector Catastral (6)','sector_catastral',null,null,false,false,'','col-25');
 $c[]=new cmp('nummanzana','n','3',$d['nummanzana'],$w.' '.$o,'Nummanzana (3)','nummanzana',null,null,false,false,'','col-25');
 $c[]=new cmp('predio_num','n','3',$d['predio_num'],$w.' '.$o,'Predio de Num (3)','predio_num',null,null,false,false,'','col-25');
 $c[]=new cmp('unidad_habit','n','4',$d['unidad_habit'],$w.' '.$o,'Unidad habitacional (3)','unidad_habit',null,null,false,false,'','col-25');
 $c[]=new cmp('cordx','t','15',$d['cordx'],$w.' '.$o,'Cordx','cordx',null,null,false,false,'','col-5');
 $c[]=new cmp('cordy','t','15',$d['cordy'],$w.' '.$o,'Cordy','cordy',null,null,false,false,'','col-5');
    
  if($d['idgeo']==='0'){
	$o='gesefc';
 $c[]=new cmp($o,'e',null,'PROCESO VALIDACION',$w);
 $c[]=new cmp('fecha_gestion','d','10',$d['fecha_gestion'],$w.' pRe '.$o,'Fecha de Gestion','fecha_gestion',null,null,true,$u,'','col-2','validDate(this,-2,0);');
 $c[]=new cmp('direccion_nueva_v','t','90',$d['dir_new'],$w.' dir '.$o,'Direccion Nueva','direccion_nueva_v',null,null,true,$u,'','col-25');
 $c[]=new cmp('sector_catastral_v','n','6',$d['sector'],$w.' '.$o,'Sector Catastral (6)','sector_catastral_v',null,null,true,$u,'','col-25');
 $c[]=new cmp('nummanzana_v','n','3',$d['manzana'],$w.' '.$o,'Nummanzana (3)','nummanzana_v',null,null,true,$u,'','col-25');
 $c[]=new cmp('predio_num_v','n','3',$d['predio'],$w.' '.$o,'Predio de Num (3)','predio_num_v',null,null,true,$u,'','col-25');
 $c[]=new cmp('telefono1_v','n','10',$d['tel_1'],$w.' '.$o,'Telefono 1','telefono1_v',null,null,true,$u,'','col-2');
 $c[]=new cmp('telefono2_v','n','10',$d['tel_2'],$w.' '.$o,'Telefono 2','telefono2_v',null,null,false,$u,'','col-2');
 $c[]=new cmp('telefono3_v','n','10',$d['tel_3'],$w.' '.$o,'Telefono 3','telefono3_v',null,null,false,$u,'','col-2');
 }else{
	$o='gesefc';
 $c[]=new cmp($o,'e',null,'PROCESO GESTIÓN ',$w);
 $c[]=new cmp('fecha_gest','d','10','',$w.' pRe '.$o,'Fecha de Gestion','fecha_gestion',null,null,true,$x,'','col-2','validDate(this,-2,0);');
 $c[]=new cmp('estado_g','s',2,'',$w.' pRe '.$o,'estado','estado_g',null,null,true,$x,'','col-4',"enabFielSele(this,['motivo_estado']);");//
 $c[]=new cmp('motivo_estado','s','3','',$w.' sTA '.$o,'motivo_estado','motivo_estado',null,null,false,false,'','col-4','validState(this);');
 $c[]=new cmp('direccion_nueva','t','90','',$w.' dir '.$o,'Direccion Nueva','direccion_nueva',null,null,false,$x,'','col-25');
 $c[]=new cmp('sector_catastral_n','n','6','',$w.' dir '.$o,'Sector Catastral (6)','sector_catastral_n',null,null,false,$x,'','col-25');
 $c[]=new cmp('nummanzana_n','n','3','',$w.' dir '.$o,'Nummanzana (3)','nummanzana_n',null,null,false,$x,'','col-25');
 $c[]=new cmp('predio_num_n','n','3','',$w.' dir '.$o,'Predio de Num (3)','predio_num_n',null,null,false,$x,'','col-25');
 $c[]=new cmp('observacion','a',50,'',$w.' '.$o,'Observacion','observacion',null,null,true,true,'','col-10');
 }
 
 
 
 
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 return $rta;
}
function lista_gestion(){ //revisar
	//var_dump($_POST);
	$id=divide($_POST['id']);
	// var_dump($id);
	$sql="SELECT RG.fecha_gest Fecha,FN_CATALOGODESC(35,RG.estado_g) Estado,RG.fecha_create 'Fecha de Creación',U.nombre Creo,U.perfil perfil
		FROM eac_ruteo_ges RG
		LEFT JOIN usuarios U ON RG.usu_creo=U.id_usuario 
			WHERE  idruteo='$id[0]'";
		$sql.=" ORDER BY fecha_create";
		// echo $sql;
		//$_SESSION['sql_person']=$sql;
			$datos=datos_mysql($sql);
		return panel_content($datos["responseResult"],"datos-lis",10);
}



function opc_gestion($id=''){
	return opc_sql("SELECT `idcatadeta`, descripcion FROM `catadeta` WHERE idcatalogo=222 AND estado='A' ORDER BY 1", $id);
}

function opc_idgeo($a){
	$id=divide($a);
	$sql="SELECT concat_ws('_',sector_catastral,nummanzana,predio_num,unidad_habit) cod
		 FROM `eac_ruteo` WHERE  id_ruteo='{$id[0]}'";
		 $info=datos_mysql($sql);
		 $cod= $info['responseResult'][0]['cod'];
		 return $cod;
		 /* return	opc_sql("SELECT CONCAT_WS('_',idgeo,estado_v),FN_CATALOGODESC(44,estado_v)
			from hog_geo where 
			sector_catastral='$co[0]' AND nummanzana='$co[1]' AND predio_num='$co[2]' AND unidad_habit='$co[3]' AND estado_v>3",$id);  */
}

function opc_estado($id=''){
	$id=opc_idgeo($_REQUEST['id']);
		$co=divide($id);
		return	opc_sql("SELECT idgeo,FN_CATALOGODESC(44,estado_v)
			from hog_geo where 
			sector_catastral='$co[0]' AND nummanzana='$co[1]' AND predio_num='$co[2]' AND unidad_habit='$co[3]' AND estado_v>3",$id); 
			// var_dump($id);
}



function opc_estadofamili(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT idviv 'id',concat(idviv,' - ','FAMILIA ',numfam) FROM hog_viv hv where idpre={$id[0]} ORDER BY 1";
		$info=datos_mysql($sql);
		// print_r($sql);
		return json_encode($info['responseResult']);
	} 
}
function opc_famili($id=''){
	// return opc_sql("SELECT `idcatadeta`, descripcion FROM `catadeta` WHERE idcatalogo=0 AND estado='A' ORDER BY 1", $id);
}
function opc_usuario($id=''){
	// return opc_sql("SELECT `idcatadeta`, descripcion FROM `catadeta` WHERE idcatalogo=0 AND estado='A' ORDER BY 1", $id);
}

function opc_familiusuario(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT CONCAT_WS('_',tipo_doc,idpersona),CONCAT_WS('-',idpersona,tipo_doc,CONCAT_WS(' ',nombre1,apellido1)) FROM personas p WHERE vivipersona={$id[0]} ORDER BY 1";
		$info=datos_mysql($sql);
		// print_r($sql);
		return json_encode($info['responseResult']);
	} 					
}

function opc_usuariocod_admin(){
	// var_dump($_REQUEST['id']);
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT f.cod_admin cod,concat_ws('-',f.cod_admin,FN_CATALOGODESC(127,f.final_consul)) FROM adm_facturacion f WHERE f.tipo_doc='{$id[0]}' AND f.documento='{$id[1]}' ORDER BY 1";
		$info=datos_mysql($sql);
		// print_r($sql);
		return json_encode($info['responseResult']);
	} 					
}

function opc_cod_admin($id=''){
	// return opc_sql("SELECT `idcatadeta`, descripcion FROM `catadeta` WHERE idcatalogo=0 AND estado='A' ORDER BY 1", $id);
}
function opc_fuente($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
}
function opc_subred_report($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=72 and estado='A' ORDER BY 1",$id);
}
function opc_priorizacion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=191 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_prior($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=235 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_doc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_doc_acu($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_sexo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}
function opc_nacionalidad($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=30 and estado='A' ORDER BY 1",$id);
}
function opc_etnia($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=16 and estado='A' ORDER BY 1",$id);
}
function opc_regimen($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=17 and estado='A' ORDER BY 1",$id);
}
function opc_eapb($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=18 and estado='A' ORDER BY 1",$id);
}
function opc_perfil_asignado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=3 and estado='A' ORDER BY 1",$id);
}

function opc_localidad($id=''){
	return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=2 ORDER BY cast(idcatadeta as signed)",$id);
}
function opc_upz($id=''){
	return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=7 and estado='A' ORDER BY 1",$id);
}
function opc_barrio($id=''){
	return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=20 and estado='A' ORDER BY 1",$id);
}
function opc_estado_g($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=35 and estado='A' ORDER BY 1",$id);
}
function opc_motivo_estado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=36 and estado='A' ORDER BY 1",$id);
}
/* function opc_asignado($id=''){
	$co=datos_mysql("select FN_USUARIO(".$_SESSION['us_sds'].") as co;");
	$com=divide($co['responseResult'][0]['co']);
	return opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` WHERE `perfil` IN('MED','ENF') AND componente='EAC' and subred='{$com[2]}' ORDER BY 1",$id);
} */


function get_rute(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT `id_ruteo`, R.`idgeo`,`fuente`, `fecha_asig`, `priorizacion`,tipo_prior, `tipo_doc`, `documento`, `nombres`, `fecha_nac`, `sexo`, `nacionalidad`, `etnia`,`regimen`,`eapb`, 
		`tipo_doc_acu`, `documento_acu`, `nombres_acu`, R.direccion,`telefono1`, `telefono2`, `telefono3`, R.`intervencion`,R.`caracteriza`,`per_consul`,`fecha_consulta`,`cod_cups`,R.`subred_report`, G.`localidad`, G.`upz`, G.`barrio`, 
		G.sector_catastral, G.nummanzana, G.predio_num, G.unidad_habit, G.`cordx`, G.`cordy`, RG.fecha_gest 'fecha_gest',RV.fecha_gestion, RG.estado_g, RG.motivo_estado, RG.direccion_nueva, RG.sector_catastral AS sect, RG.nummanzana AS manz, RG.predio_num AS pred, RG.observaciones AS obse, RV.direccion_nueva AS dir_new, RV.sector_catastral AS sector, RV.nummanzana AS manzana, RV.predio_num AS predio, RV.telefono_1 AS tel_1, RV.telefono_2 AS tel_2, RV.telefono_3 AS tel_3
		 FROM `eac_ruteo` R
		 LEFT JOIN hog_geo G ON R.idgeo=G.idgeo
		 LEFT JOIN eac_ruteo_ges RG ON R.id_ruteo=RG.idruteo
         LEFT JOIN eac_ruteo_val RV ON R.id_ruteo=RV.idruteo
		 WHERE  id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
	} 
}

 
function gra_rute(){
	// var_dump($_POST);
	$id=divide($_POST['id']);
	$sql="SELECT idgeo FROM `eac_ruteo` WHERE  id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
		if ($info['responseResult'][0]['idgeo']=='0') {
			$sql = "INSERT INTO eac_ruteo_val VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$params = array(
			array('type' => 'i', 'value' => NULL),
			array('type' => 'i', 'value' => $id[0]),
			array('type' => 's', 'value' => $_POST['fecha_gestion']),
			array('type' => 's', 'value' => $_POST['direccion_nueva_v']),
			array('type' => 's', 'value' => $_POST['sector_catastral_v']),
			array('type' => 's', 'value' => $_POST['nummanzana_v']),
			array('type' => 's', 'value' => $_POST['predio_num_v']),
			array('type' => 's', 'value' => $_POST['telefono1_v']),
			array('type' => 's', 'value' => $_POST['telefono2_v']),
			array('type' => 's', 'value' => $_POST['telefono3_v']),
			array('type' => 'i', 'value' => $_SESSION['us_sds']),
			array('type' => 's', 'value' => date("Y-m-d H:i:s")),
			array('type' => 's', 'value' => NULL),
			array('type' => 's', 'value' => NULL),
			array('type' => 's', 'value' => 'A')
			);
			// var_dump($params);
			return $rta = mysql_prepd($sql, $params);			
	}else{
		$sql = "INSERT INTO eac_ruteo_ges VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$params = array(
		array('type' => 'i', 'value' => NULL),
		array('type' => 'i', 'value' => $id[0]),
		array('type' => 's', 'value' => $_POST['fecha_gest']),
		array('type' => 's', 'value' => $_POST['estado_g']),
		array('type' => 's', 'value' => $_POST['motivo_estado']),
		array('type' => 's', 'value' => $_POST['direccion_nueva']),
		array('type' => 's', 'value' => $_POST['sector_catastral_n']),
		array('type' => 's', 'value' => $_POST['nummanzana_n']),
		array('type' => 's', 'value' => $_POST['predio_num_n']),
		array('type' => 's', 'value' => $_POST['observacion']),
		array('type' => 'i', 'value' => $_SESSION['us_sds']),
		array('type' => 's', 'value' => date("Y-m-d H:i:s")),
		array('type' => 's', 'value' => NULL),
		array('type' => 's', 'value' => NULL),
		array('type' => 's', 'value' => 'A')
		);
		// var_dump($params);
		return	$rta = mysql_prepd($sql, $params);
	}
}


/**********************************INICIO ASIGNACIÓN*************************** */

FUNCTION lis_predios(){
	// var_dump($_POST);
	$id =isset($_POST['cod_pre']) ? divide($_POST['cod_pre']) : json_encode (new stdClass);
$info=datos_mysql("SELECT COUNT(*) total FROM geo_asig WHERE idgeo='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-predios']))? ($_POST['pag-predios']-1)* $regxPag:0;

  
	$sql="SELECT  A.id_asig 'Cod Registro',A.idgeo 'Cod Predio', A.fecha_create 'Fecha de Asignación', U.nombre 'Colaborador Asignado', concat_ws(' - ',U1.perfil,U1.nombre) 'Quien Asignó'
	FROM geo_asig A
	LEFT JOIN  usuarios U ON A.doc_asignado=U.id_usuario
	LEFT JOIN  usuarios U1 ON A.usu_create=U1.id_usuario ";
$sql.="WHERE idgeo='".$id[0];
$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"predios",$regxPag);
   }

   function get_predio(){
	// print_r($_POST);
	$id=divide($_POST['id']);
	$sql="SELECT G.idgeo,G.zona, G.localidad, G.upz, G.barrio, G.sector_catastral, G.nummanzana, G.predio_num, G.unidad_habit, G.direccion, G.vereda, G.cordx, G.cordy, G.manz_cuidado 
 	FROM `hog_geo` G 
  		WHERE G.idgeo ='".$id[0]."'";
	// var_dump($sql);
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode (new stdClass);
	}
return json_encode($info['responseResult'][0]);
}

function opc_rolasignado(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT id_usuario,CONCAT(id_usuario,'-',nombre) FROM usuarios WHERE subred in(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and componente IN(SELECT componente FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}')  AND estado='A' and perfil='".$id[0]."' ORDER BY 1 ASC";
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	} 
}
function gra_asigpred(){
	$id=divide($_POST['cod_pre']);
	$sql = "INSERT INTO geo_asig VALUES(?,?,?,?,?,?,?,?)";
	$params = array(
	array('type' => 'i', 'value' => NULL),
	array('type' => 's', 'value' => $id[0]),
	array('type' => 's', 'value' => $_POST['asignado']),
	array('type' => 'i', 'value' => $_SESSION['us_sds']),
	array('type' => 's', 'value' => date("Y-m-d H:i:s")),
	array('type' => 's', 'value' => NULL),
	array('type' => 's', 'value' => NULL),
	array('type' => 's', 'value' => 'A')
	);
	$rta = mysql_prepd($sql, $params);
	return $rta;
}

/**********************************FIN ASIGNACIÓN*************************** */

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($c);
// var_dump($a);
	if ($a=='rute' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono mapa' title='Ruteo' id='".$c['ACCIONES']."' Onclick=\"mostrar('rute','pro',event,'','lib.php',7);\"></li>";
		$rta.="<li class='icono crear' title='Asignación Predios' id='".$c['ACCIONES']."' Onclick=\"mostrar('asigpred','pro',event,'','../asignacion/lib.php',7);\"></li>";
		// $rta.="<li class='icono canin1' title='GESTIÓN' id='".$c['ACCIONES']."' Onclick=\"mostrar('ruteresol','pro',event,'','ruteoresolut.php',7,'ruteresol');\"></li>";
		// if($c['Gestionado']== '1' || $c['Gestionado']=='2'){
	
		// }
	}
	
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
