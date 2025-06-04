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
	$info = datos_mysql("SELECT perfil FROM usuarios  where id_usuario='".$_SESSION['us_sds']."';" );
	$perfil = $info['responseResult'][0]['perfil'];
// var_dump($perfil);
	// $jAproTerr = ($_SESSION['us_sds'] != '80811594') ? "LEFT JOIN apro_terr A ON G.territorio = A.territorio" : ""; // Condiciona el JOIN
	$jAproTerr = ($perfil != 'ADM') ? " LEFT JOIN apro_terr A ON G.territorio = A.territorio" : ""; // Condiciona el JOIN


    $info = datos_mysql("SELECT COUNT(*) total FROM eac_ruteo er 
	LEFT JOIN hog_geo G ON er.idgeo = G.idgeo 
        $jAproTerr " . whe_rute());
    $total = $info['responseResult'][0]['total'];
    $regxPag = 10;
    $pag = (isset($_POST['pag-rute'])) ? ($_POST['pag-rute'] - 1) * $regxPag : 0;

    $sql = "SELECT er.id_ruteo AS ACCIONES, er.idgeo AS Cod_Predio, 
                FN_CATALOGODESC(235, tipo_prior) AS Grupo_Poblacion_Priorizada, 
                er.documento AS Documento_Usuario, er.nombres AS Nombre_Usuario, 
                FN_CATALOGODESC(218, er.perfil1) AS Interviene, 
                FN_CATALOGODESC(269, er.actividad1) AS Realizar, 
                er.estado
            FROM eac_ruteo er  
            LEFT JOIN hog_geo G ON er.idgeo = G.idgeo 
            $jAproTerr " . whe_rute();
    
    $sql .= " ORDER BY er.fecha_create";
    $sql .= ' LIMIT ' . $pag . ',' . $regxPag;
	// var_dump($sql);

	$sql1="SELECT  
	R.id_ruteo AS Codigo_Registro, FN_CATALOGODESC(33,R.fuente) AS 'FUENTE O REMITENTE', R.fecha_asig AS 'FECHA ASIGNACIÓN', FN_CATALOGODESC(191,R.priorizacion) AS 'COHORTE DE RIESGO', FN_CATALOGODESC(235,R.tipo_prior) AS 'GRUPO DE POBLACION PRIORIZADA', 
    R.tipo_doc AS 'TIPO DE DOCUMENTO', R.documento AS 'NÚMERO DE DOCUMENTO', R.nombres AS 'NOMBRES Y APELLIDOS DEL USUARIO', FN_CATALOGODESC(21,R.sexo) AS 'Sexo',
	G.idgeo AS Cod_Predio, G.direccion AS Direccion, R.telefono1 AS Telefono_1, R.telefono2 AS Telefono_2, R.telefono3 AS Telefono_3,
    RG.fecha_llamada AS 'Fecha Contacto Telefonico', FN_CATALOGODESC(270,RG.estado_llamada) AS 'Estado Contacto Telefonico', FN_CATALOGODESC(271,RG.estado_agenda) AS 'Estado Gestion'
	FROM eac_ruteo R
	LEFT JOIN hog_geo G ON R.idgeo = G.idgeo
	LEFT JOIN apro_terr A ON G.territorio = A.territorio AND R.actividad1 = A.doc_asignado
	LEFT JOIN eac_ruteo_ges RG ON R.id_ruteo = RG.idruteo
	WHERE A.doc_asignado ='".$_SESSION['us_sds']."'";
		
		// $tot="SELECT  COUNT(*) as total	FROM eac_ruteo R LEFT JOIN hog_geo G ON R.idgeo = G.idgeo LEFT JOIN apro_terr A ON R.idgeo = A.idgeo AND R.actividad1 = A.doc_asignado	WHERE A.doc_asignado ='R LEFT JOIN hog_geo G ON R.idgeo = G.idgeo LEFT JOIN apro_terr A ON R.idgeo = A.idgeo AND R.actividad1 = A.doc_asignado	WHERE A.doc_asignado ='".$_SESSION['us_sds']."'";
		$tot="SELECT  COUNT(*) AS total FROM eac_ruteo R LEFT JOIN hog_geo G ON R.idgeo = G.idgeo LEFT JOIN apro_terr A ON G.territorio = A.territorio AND R.actividad1 = A.doc_asignado LEFT JOIN eac_ruteo_ges RG ON R.id_ruteo = RG.idruteo	WHERE A.doc_asignado ='".$_SESSION['us_sds']."';";
		// echo $sql;
		$_SESSION['sql_rute']=$sql1;
		$_SESSION['tot_rute']=$tot;
		// /* echo json_encode($rta); */
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"rute",$regxPag);
}

function whe_rute() {
	$us_sds = $_SESSION['us_sds'] ?? '';
    $doc_asignado = $_SESSION['us_sds'] ?? 0;
    $perfil = perfil1();
    $sql1 = " WHERE G.subred = (SELECT subred FROM usuarios WHERE id_usuario = '" . $us_sds . "')";
    // Agregar condición de apro_terr solo si el perfil no es 'ADM'
	$info = datos_mysql("SELECT perfil FROM usuarios  where id_usuario='".$_SESSION['us_sds']."';" );
	$perfil = $info['responseResult'][0]['perfil'];
    if ($perfil != 'ADM') {
        $sql1 .= " AND A.doc_asignado = " . intval($doc_asignado);
    }
    if ($_POST['frut']) {
        $sql1 .= " AND id_ruteo ='" . $_POST['frut'] . "'";
    } elseif ($_POST['fusu']) {
        $sql1 .= " AND documento ='" . $_POST['fusu'] . "'";
    } else {
        $sql1 .= " AND 0";
    }
	// var_dump($sql1);
	return $sql1;
}

	/* if ($_POST['flocalidad'])
		$sql .= " AND localidad = '".$_POST['flocalidad']."'";
	if ($_POST['fgrupo'])
		$sql .= " AND priorizacion = '".$_POST['fgrupo']."'";
	if ($_POST['fseca'])
		$sql .= " AND sector_catastral = '".$_POST['fseca']."'";
	if ($_POST['fmanz'])
		$sql .= " AND nummanzana ='".$_POST['fmanz']."' ";
	 */


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
	// $rta .= "<li class='icono $a actualizar'    title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
  }
  return $rta;
}

function cmp_rute(){
 $rta="";
	$rta .="<div class='encabezado vivienda'>TABLA DE LLAMADAS REALIZADAS</div>
	<div class='contenido' id='gestion-lis' >".lis_gestion()."</div></div>";

 $t=['id'=>'', 'idgeo'=>'', 'id_ruteo'=>'','fecha_asig'=>'','fuente'=>'','priorizacion'=>'','tipo_prior'=>'','tipo_doc'=>'','documento'=>'','nombres'=>'','sexo'=>'',
 'direccion'=>'','telefono1'=>'','telefono2'=>'','telefono3'=>'', 'subred'=>'','localidad'=>'','upz'=>'','barrio'=>'', 'sector_catastral'=>'','nummanzana'=>'',
 'predio_num'=>'','unidad_habit'=>'','cordx'=>'','cordy'=>''];
 
 /* 'fecha_consulta'=>'',
 'cod_cups'=>'','per_consul'=>'','subred_report'=>'',,'fecha_gestion'=>'','fecha_gest'=>'','estado_g'=>'',
 'motivo_estado'=>'','direccion_nueva'=>'','sect'=>'', 'manz'=>'','pred'=>'', 'obse'=>'','dir_new'=>'','sector'=>'', 'manzana'=>'','predio'=>'','usu_creo'=>'', 'fecha_create'=>'', 'usu_update'=>'', 
 'fecha_update'=>'', 'estado'=>'']; */

 //'nacionalidad'=>'','etnia'=>'','regimen'=>'','eapb'=>'','tipo_doc_acu'=>'','documento_acu'=>'','nombres_acu'=>'',
 $w='rute';
 $d=get_ruteo();
//  var_dump($d);
 if ($d=="") {$d=$t;}
 $days=fechas_app('agendamiento');
 $u=($d['idgeo']=='0')?true:false;
//  var_dump($d['estado_g']);
 $x=($d['idgeo']=='0')?true:false;
// var_dump($_REQUEST);
// var_dump($d);
 $o='segrep';
 $c[]=new cmp($o,'e',null,'CASO REPORTADO',$w);
 $c[]=new cmp('id','h','20',$d['id_ruteo'],$w.' '.$o,'','',null,null,true,false,'','col-1');
 $c[]=new cmp('fecha_asig','d','10',$d['fecha_asig'],$w.' '.$o,'FECHA ASIGNACIÓN','fecha_asig',null,null,false,false,'','col-15');
 $c[]=new cmp('fuente','s','3',$d['fuente'],$w.' '.$o,'FUENTE O REMITENTE','fuente',null,null,false,false,'','col-25');
 $c[]=new cmp('priorizacion','s','3',$d['priorizacion'],$w.' '.$o,'COHORTE DE RIESGO','priorizacion',null,null,false,false,'','col-3');
 $c[]=new cmp('tipo_prior','s','3',$d['tipo_prior'],$w.' '.$o,'GRUPO DE POBLACION PRIORIZADA','tipo_prior',null,null,false,false,'','col-3');

 $c[]=new cmp('tipo_doc','s','3',$d['tipo_doc'],$w.' '.$o,'TIPO DE DOCUMENTO','tipo_doc',null,null,false,false,'','col-2');
 $c[]=new cmp('documento','nu','999999999999999999',$d['documento'],$w.' '.$o,'NÚMERO DE DOCUMENTO','documento',null,null,false,false,'','col-2');
 $c[]=new cmp('nombres','t','80',$d['nombres'],$w.' '.$o,'NOMBRES Y APELLIDOS DEL USUARIO','nombres',null,null,false,false,'','col-4');
 $c[]=new cmp('sexo','s','3',$d['sexo'],$w.' '.$o,'SEXO','sexo',null,null,false,false,'','col-2');
 
 $o='datcon';
 //$c[]=new cmp($o,'e',null,'DATOS DE CONTACTO',$w);
 $c[]=new cmp('direccion','t','90',$d['direccion'],$w.' '.$o,'Direccion','direccion',null,null,false,false,'','col-4');
 $c[]=new cmp('telefono1','n','10',$d['telefono1'],$w.' '.$o,'Telefono 1','telefono1',null,null,false,false,'','col-2');
 $c[]=new cmp('telefono2','n','10',$d['telefono2'],$w.' '.$o,'Telefono 2','telefono2',null,null,false,false,'','col-2');
 $c[]=new cmp('telefono3','n','10',$d['telefono3'],$w.' '.$o,'Telefono 3','telefono3',null,null,false,false,'','col-2');

 $c[]=new cmp('subred_report','s','3',$d['subred'],$w.' '.$o,'Subred','subred_report',null,null,false,false,'','col-3');
 $c[]=new cmp('localidad','s','3',$d['localidad'],$w.' '.$o,'Localidad','localidad',null,null,false,false,'','col-2');
 $c[]=new cmp('upz','s','3',$d['upz'],$w.' '.$o,'Upz','upz',null,null,false,false,'','col-2');
 $c[]=new cmp('barrio','s','5',$d['barrio'],$w.' '.$o,'Barrio','barrio',null,null,false,false,'','col-3');
 $c[]=new cmp('sector_catastral','n','6',$d['sector_catastral'],$w.' '.$o,'Sector Catastral (6)','sector_catastral',null,null,false,false,'','col-25');
 $c[]=new cmp('nummanzana','n','3',$d['nummanzana'],$w.' '.$o,'Nummanzana (3)','nummanzana',null,null,false,false,'','col-25');
 $c[]=new cmp('predio_num','n','3',$d['predio_num'],$w.' '.$o,'Predio de Num (3)','predio_num',null,null,false,false,'','col-25');
 $c[]=new cmp('unidad_habit','n','4',$d['unidad_habit'],$w.' '.$o,'Unidad habitacional (3)','unidad_habit',null,null,false,false,'','col-25');
 $c[]=new cmp('cordx','t','15',$d['cordx'],$w.' '.$o,'Cordx','cordx',null,null,false,false,'','col-5');
 $c[]=new cmp('cordy','t','15',$d['cordy'],$w.' '.$o,'Cordy','cordy',null,null,false,false,'','col-5');
 
 $o='gesefc';
 $c[]=new cmp($o,'e',null,'CONTACTO TELEFONICO',$w);
 $c[]=new cmp('fecha_llamada','d','10','',$w.' pRe '.$o,'Fecha de Primer Contacto Telefonico','fecha_gestion',null,null,true,true,'','col-2',"validDate(this,$days,0);");
 $c[]=new cmp('estado_llamada','s',2,'',$w.' pRe '.$o,'Estado Primer Contacto','estado_g',null,null,true,true,'','col-4','enabRutGest();enabRutVisit();');//
 $c[]=new cmp('observacion','a',7000,'',$w.' '.$o,'Observacion','observacion',null,null,true,true,'','col-10');

/* $o='gesefc';
 $c[]=new cmp($o,'e',null,'PROCESO DE GESTIÓN',$w);
 $c[]=new cmp('estado_agenda','s',2,'',$w.' pRe '.$o,'Estado','estado_agenda',null,null,true,$x,'','col-4',"enabFielSele(this,['motivo_estado']);");//
 $c[]=new cmp('motivo_estado','s','3','',$w.' sTA '.$o,'motivo_estado','motivo_estado',null,null,false,false,'','col-4','validState(this);');
 */
 $o='gesefc';
 $c[]=new cmp($o,'e',null,'PROCESO DE GESTIÓN',$w);
 $c[]=new cmp('estado_agenda','s',2,'',$w.' sTA '.$o,'Estado','estado_agenda',null,null,true,false,'','col-4','enabRutAgen();enabRutOthSub();');//
 $c[]=new cmp('motivo_estado','s','3','',$w.' ReC '.$o,'Motivo del Rechazado','motivo_estado',null,null,false,false,'','col-4');//
 $c[]=new cmp('fecha_gestion','d','10','',$w.' AGe '.$o,'Fecha de Agenda','fecha_gestion',null,null,false,false,'','col-2',"validDate(this,$days,30);");
 $c[]=new cmp('docu_confirm','nu','999999999999999999','',$w.' AGe '.$o,'Documento Confirmado  del Usuario','docu_confirm',null,null,false,false,'','col-2');
  $c[]=new cmp('perfil_gest','s',3,'',$w.' AGe '.$o,'Perfil que Gestiona','perfil_gest',null,'',false,false,'','col-2',"selectDepend('perfil_gest','usuario_gest','lib.php');");
 $c[]=new cmp('usuario_gest','s','10','',$w.' AGe '.$o,'Usuario que Gestiona','usuario_gest',null,null,false,false,'','col-2');

 $o='gesgeo';
 $c[]=new cmp($o,'e',null,'DATOS GEOGRAFICOS',$w);
 $c[]=new cmp('direccion_nueva_v','t','90','',$w.' dir '.$o,'Direccion Nueva <a href="https://mapas.bogota.gov.co/#" target="_blank">Abrir MAPAS Bogotá</a>','direccion_nueva_v',null,null,false,false,'','col-25');
 $c[]=new cmp('sector_catastral_v','n','6','',$w.' dir '.$o,'Sector Catastral (6)','sector_catastral_v',null,null,false,false,'','col-25');
 $c[]=new cmp('nummanzana_v','n','3','',$w.' dir '.$o,'Nummanzana (3)','nummanzana_v',null,null,false,false,'','col-25');
 $c[]=new cmp('predio_num_v','n','3','',$w.' dir '.$o,'Predio de Num (3)','predio_num_v',null,null,false,false,'','col-25');
//  $c[]=new cmp('observacion','a',50,$d['obse'],$w.' '.$o,'Observacion','observacion',null,null,true,true,'','col-10');
  
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 return $rta;
}
function lis_gestion(){ //revisar
	// var_dump($_POST);
	$id=divide($_POST['id']);
	$info=datos_mysql("SELECT COUNT(*) total from eac_ruteo_ges 
	where idruteo=$id[0]");
	$total=$info['responseResult'][0]['total'];
	$regxPag=5;
	$pag=(isset($_POST['pag-gestion']))? ($_POST['pag-gestion']-1)* $regxPag:0;
		
		$sql="SELECT id_rutges ACCIONES,id_rutges 'Cod Registro',erg.fecha_llamada 'Fecha',FN_CATALOGODESC(270,estado_llamada) 'Estado de la LLamada',
		FN_CATALOGODESC(271,estado_agenda) 'Estado de la Agenda',erg.usuario_gest 'Asignado A', fecha_create 'Creó',erg.estado 'Estado'
 FROM eac_ruteo_ges erg 
 WHERE idruteo=$id[0] and estado='A' ";
		$sql.=" ORDER BY fecha_create";
		// echo $sql;
		$_SESSION['sql_person']=$sql;
			$datos=datos_mysql($sql);
		return panel_content($datos["responseResult"],"gestion-lis",10);
}

/* function lis_rute(){
	$info=datos_mysql("SELECT COUNT(*) total from eac_ruteo 
	where 1 ".whe_rute());
	$total=$info['responseResult'][0]['total'];
	$regxPag=5;
	$pag=(isset($_POST['pag-rute']))? ($_POST['pag-rute']-1)* $regxPag:0;
	$sql="SELECT er.id_ruteo AS ACCIONES, er.idgeo AS Cod_Predio, FN_CATALOGODESC(235,tipo_prior) AS Grupo_Poblacion_Priorizada, er.documento AS Documento_Usuario,er.nombres AS Nombre_Usuario,FN_CATALOGODESC(218,er.perfil1) AS Interviene, FN_CATALOGODESC(269,er.actividad1) AS Realizar ,er.estado
  FROM eac_ruteo er 
  WHERE 1 ".whe_rute();
	$sql.="ORDER BY fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	//echo($sql);
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"rute",$regxPag);
} */
  function opc_perfil_gest($id=''){
	return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=218 and estado="A" AND descripcion=(select perfil from usuarios where id_usuario='.$_SESSION['us_sds'].') ORDER BY 1',$id);
	}
  function opc_perfil_gestusuario_gest($id=''){
    if($_REQUEST['id']!=''){	
            $sql = "SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios right join apro_terr at ON id_usuario=at.doc_asignado WHERE 
            perfil=(select descripcion from catadeta c where idcatalogo=218 and idcatadeta='{$_REQUEST['id']}' and estado='A') 
            and id_usuario ='{$_SESSION['us_sds']}' ORDER BY nombre";
            $info = datos_mysql($sql);		
		 //return json_encode($sql);	
           return json_encode($info['responseResult']);	
        }
}
  function opc_clasificacion($id=''){
  	return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=1 and estado="A" ORDER BY 1',$id);
  }

/* function opc_perfil($id=''){
    return opc_sql("SELECT idcatadeta, descripcion FROM `catadeta` WHERE idcatalogo = 218 AND estado = 'A'",$id);
}
 */
function opc_pre_clasif($id=''){
	// return opc_sql("SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE estado = 'A'",$id);
}
function opc_usuario_gest($id=''){
	// return opc_sql("SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE estado = 'A'",$id);
}
function opc_gestion($id=''){
	return opc_sql("SELECT `idcatadeta`, descripcion FROM `catadeta` WHERE idcatalogo=222 AND estado='A' ORDER BY 1", $id);
}
function opc_estado_agenda($id=''){
	return opc_sql("SELECT `idcatadeta`, descripcion FROM `catadeta` WHERE idcatalogo=271 AND estado='A' ORDER BY 1", $id);
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
		$sql="SELECT id_fam 'id',concat(id_fam,' - ','FAMILIA ',numfam) FROM hog_fam hv where idpre={$id[0]} ORDER BY 1";
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



/* function opc_usuariocod_admin(){
	// var_dump($_REQUEST['id']);
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT f.cod_admin cod,concat_ws('-',f.cod_admin,FN_CATALOGODESC(127,f.final_consul)) FROM adm_facturacion f WHERE f.tipo_doc='{$id[0]}' AND f.documento='{$id[1]}' ORDER BY 1";
		$info=datos_mysql($sql);
		// print_r($sql);
		return json_encode($info['responseResult']);
	} 					
} */

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
/* function opc_estado_g($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=270 and estado='A' ORDER BY 1",$id);
} */
function opc_estado_g_filtrado($idruteo, $id = ''){
	global $con;
    $sqlEstados = "SELECT estado_llamada FROM eac_ruteo_ges WHERE idruteo = $idruteo";
    $estadosExistentes = [];
    if ($con->multi_query($sqlEstados)) {
        do {
            if ($con->errno == 0) {
                $rs = $con->store_result();
                if ($rs !== false) {
                    while ($r = $rs->fetch_array(MYSQLI_NUM)) {
                        $estadosExistentes[] = $r[0]; 
                    }
                }
                $rs->free();
            }
        } while ($con->more_results() && $con->next_result());
    }
    if (empty($estadosExistentes)) {
        $estadosPermitidos = [1, 2,6];
    } else {
        $estadosPermitidos = [1,6];
        if (in_array(2, $estadosExistentes)) {
            if (in_array(3, $estadosExistentes)) {
                if (in_array(4, $estadosExistentes)) {
                    $estadosPermitidos[] = 5;
                } else {
                    $estadosPermitidos[] = 4;
                }
            } else {
                $estadosPermitidos[] = 3;
            }
        } else {
            $estadosPermitidos[] = 2; // ningUn NO CONTACTADO Mostrar NO CONTACTADO 1
        }
    }
    $sqlEstadosDisponibles = "SELECT idcatadeta, descripcion FROM catadeta 
                              WHERE idcatalogo = 270 AND estado = 'A' 
                              AND idcatadeta IN (" . implode(',', $estadosPermitidos) . ") 
                              ORDER BY idcatadeta";
    return opc_sql($sqlEstadosDisponibles, $id);
}
function opc_estado_g($id='') {
	$idruteo = divide($_POST['id'])[0] ?? 0;
    return opc_estado_g_filtrado($idruteo, $id);
}

function opc_motivo_estado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=272 and estado='A' ORDER BY 1",$id);
}
function opc_perfil($id=''){
return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=218 and estado="A" ORDER BY 1',$id);
}
function opc_doc_asignado($id=''){
	$co=datos_mysql("select FN_USUARIO(".$_SESSION['us_sds'].") as co;");
	$com=divide($co['responseResult'][0]['co']);
	return opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` WHERE  subred='{$com[2]}' ORDER BY 1",$id);//`perfil` IN('MED','ENF')
}
function opc_familiusuario(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT idpeople, CONCAT(idpersona, ' - ', CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2)) 
              FROM hog_fam hf 
              LEFT JOIN person p ON hf.id_fam=p.vivipersona 
              WHERE hf.id_fam={$id[0]} 
              ORDER BY 1";
		$info=datos_mysql($sql);
		// return json_encode($sql);
		return json_encode($info['responseResult']);
	} 					
}
function get_ruteo(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT `id_ruteo`,R.`idgeo`,`fuente`,`fecha_asig`,`priorizacion`,tipo_prior, `tipo_doc`, `documento`, `nombres`, `sexo`, R.direccion,`telefono1`, `telefono2`, `telefono3`, G.`subred`, G.`localidad`, G.`upz`, G.`barrio`, G.sector_catastral, G.nummanzana, G.predio_num, G.unidad_habit, G.`cordx`, G.`cordy` 
 		FROM `eac_ruteo` R 
 		LEFT JOIN hog_geo G ON R.idgeo=G.idgeo 
 		WHERE id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
	} 
}

function get_gest(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT `id_ruteo`, 
		 FROM `eac_ruteo_ges` R
		 WHERE  id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
	} 
}

function get_rute(){
	if (empty($_REQUEST['id'])) {
        return "";
    }
    $id = divide($_REQUEST['id']);
    $sql = "SELECT id_ruteo, fecha_asig, fuente, priorizacion, tipo_prior, tipo_doc , documento ,nombres, sexo , er.direccion ,telefono1 ,telefono2 ,telefono3, hg.subred, localidad, upz , barrio, sector_catastral, nummanzana, predio_num, unidad_habit, cordx, cordy, fecha_llamada, estado_llamada, erg.observaciones, estado_agenda, motivo_estado, fecha_gestion, docu_confirm ,''perfil_gest,usuario_gest, direccion_n, sector_n, manzana_n,predio_n
	FROM eac_ruteo_ges erg 
	left join eac_ruteo er ON erg.idruteo = er.id_ruteo 
	LEFT JOIN hog_geo hg ON er.idgeo = hg.idgeo
	WHERE erg.id_rutges='{$id[0]}'";
    $info = datos_mysql($sql);
    $data = $info['responseResult'][0];
    return json_encode($data);
}

function gra_rute(){
	$id=divide($_POST['id'] ?? '');
	if (($rtaFec = validFecha('ruteo', $_POST['fecha_llamada'] ?? '')) !== true) {
		return $rtaFec;
	  }
	$usu = $_SESSION['us_sds'];
		// $equ=datos_mysql("select equipo from usuarios where id_usuario=".$_SESSION['us_sds']);
	 $bina = isset($_POST['fequi'])?(is_array($_POST['fequi'])?implode("-", $_POST['fequi']):implode("-",array_map('trim',explode(",",str_replace("'","",$_POST['fequi']))))):'';
		$sql = "INSERT INTO eac_ruteo_ges VALUES(null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,'A')";
		$params = [
	['type' => 'i', 'value' => $id[0]],
	['type' => 's', 'value' => $_POST['fecha_llamada'] ?? ''],
	['type' => 'i', 'value' => $_POST['estado_llamada']?? ''],
	['type' => 's', 'value' => $_POST['observacion']?? ''],
	['type' => 'i', 'value' => $_POST['estado_agenda']?? ''],
	['type' => 'i', 'value' => $_POST['motivo_estado']?? ''],
	['type' => 's', 'value' => $_POST['fecha_gestion']?? ''],
	['type' => 'i', 'value' => $_POST['docu_confirm']?? ''],
	['type' => 's', 'value' => $_POST['usuario_gest']?? ''],
	['type' => 's', 'value' => $_POST['direccion_nueva_v']?? ''],
	['type' => 'i', 'value' => $_POST['sector_catastral_v']?? ''],
	['type' => 'i', 'value' => $_POST['nummanzana_v']?? ''],
	['type' => 'i', 'value' => $_POST['predio_num_v']?? ''],
	['type' => 's', 'value' => $bina],
	['type' => 's', 'value' => $usu],
	['type' => 's', 'value' => NULL],
	['type' => 's', 'value' => NULL]
		];
		$rta = mysql_prepd($sql, $params);
	// $rta = show_sql($sql, $params);
	// var_dump($_POST);
if(!empty($_POST['fecha_gestion']) && !empty($_POST['usuario_gest'])){
	$sql1 = "INSERT INTO geo_asig VALUES(NULL,?,?,?,?,NULL,NULL,'A')";
	$sql="SELECT idgeo id from eac_ruteo where id_ruteo=".$_POST['frut']."";
	$info=datos_mysql($sql);
	$id=$info['responseResult'][0]['id'];
	$params1 = array(
	array('type' => 's', 'value' =>$id ),
	array('type' => 's', 'value' => $_POST['usuario_gest']),
	array('type' => 'i', 'value' => $_SESSION['us_sds']),
	array('type' => 's', 'value' => date("Y-m-d H:i:s"))
	);
	//show_sql($sql1, $params1);
	$rta1 = mysql_prepd($sql1, $params1);
	if (strpos($rta1, "correctamente") !== false) {
		$rta.= " Y Se ha asignado el predio";
	}elseif(strpos($rta1, "Duplicate")){
		$rta.= " Y El predio ya se encontraba asignado";
	}
}
	return $rta;
}

function agend($id) {
    $id = divide($id);
    $sql = "SELECT COUNT(*) AS agenda from eac_ruteo_ges g
LEFT JOIN eac_ruteo er ON g.idruteo=er.id_ruteo 
	LEFT JOIN usuarios u ON er.actividad1=u.id_usuario
	WHERE idruteo=$id[0] and (estado_agenda=1 or estado_agenda=6 or estado_agenda=9 or estado_agenda=11 ) and estado_llamada=1 and u.perfil IN ('AUXHOG','ADM');";
    $info = datos_mysql($sql);
	// var_dump($info);
	if(intval($info['responseResult'][0]["agenda"])>0){
		return true;
	}else{
		return false;
	}
}
function fin($id) {
    $id = divide($id);
    $sql = "SELECT COUNT(*) AS estado from eac_ruteo_ges g
LEFT JOIN eac_ruteo er ON g.idruteo=er.id_ruteo 
	WHERE idruteo=$id[0] and (g.estado_agenda=1 or g.estado_agenda=9)";
    $info = datos_mysql($sql);
	// var_dump($info);
	if(intval($info['responseResult'][0]["estado"])>0){
		return true;
	}else{
		return false;
	}
}
function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($c);
// var_dump($a);
if ($a=='gestion-lis' && $b=='acciones'){
	$rta="<nav class='menu right'>";
	$rta.="<li title='Ver Registro '><i class='fa-solid fa-eye ico' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getDataFetch,500,'rute',event,this,'lib.php',['fecha_llamada','estado_llamada','observacion']);\"></i></li>"; 
}
if ($a=='rute' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono mapa' title='Ruteo' id='".$c['ACCIONES']."' Onclick=\"mostrar('rute','pro',event,'','lib.php',7);\"></li>";
		if (agend($c['ACCIONES'])) {
			$rta.="<li class='icono  editarAgenda' title='CLASIFICACIÓN' id='".$c['ACCIONES']."' Onclick=\"mostrar('rutclasif','pro',event,'','clasifica.php',7,'clasifica');\"></li>";
		}
		if (fin($c['ACCIONES'])) {
			$rta.="<li class='icono efectividadAgenda' title='GESTIÓN FINAL' id='".$c['ACCIONES']."' Onclick=\"mostrar('ruteresol','pro',event,'','ruteoresolut.php',7,'ruteresol');\"></li>";
		}
		// $rta.="<li class='icono  editarAgenda' title='CLASIFICACIÓN' id='".$c['ACCIONES']."' Onclick=\"mostrar('rutclasif','pro',event,'','clasifica.php',7,'clasifica');\"></li>";
		//$rta.="<li class='icono efectividadAgenda' title='GESTIÓN' id='".$c['ACCIONES']."' Onclick=\"mostrar('ruteresol','pro',event,'','ruteoresolut.php',7,'ruteresol');\"></li>";
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