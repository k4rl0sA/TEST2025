<?php
require_once '../libs/gestion.php';
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

function lis_sesiones(){
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['idpsi']) ? divide($_POST['idpsi']) : null);

	$info=datos_mysql("SELECT COUNT(*) total 
	FROM `psi_sesiones` P
		left JOIN usuarios U ON P.usu_creo=U.id_usuario 
	WHERE id_people='{$id[0]}'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;

	$pag=(isset($_POST['pag-sesiones']))? ($_POST['pag-sesiones']-1)* $regxPag:0;

	$sql="SELECT idsesipsi ACCIONES,psi_fecha_sesion Fecha,FN_CATALOGODESC(125,psi_sesion) Sesión,P.fecha_create Creado,U.nombre Creó,
	P.estado
	FROM `psi_sesiones` P
	left JOIN usuarios U ON P.usu_creo=U.id_usuario 
	WHERE id_people='{$id[0]}'";
		$sql.=" ORDER BY P.fecha_create";
		$sql.=' LIMIT '.$pag.','.$regxPag;
		//echo $sql;
			$datos=datos_mysql($sql);
			return create_table($total,$datos["responseResult"],"sesiones",$regxPag,'sesiones.php');

		if($_POST['id']=='0'){
			return "";
		}else{
			$id=divide($_POST['id']);
			$sql="SELECT psi_tipo_doc,psi_documento,psi_fecha_sesion,psi_sesion,cod_admin4,psi_validacion1,psi_validacion2,psi_validacion3,psi_validacion4,psi_validacion5,psi_validacion6,psi_validacion7,psi_validacion8,psi_validacion9,psi_validacion10,psi_validacion11,psi_validacion12,psi_validacion13,psi_validacion14,psi_validacion15,psi_validacion16,psi_validacion17,estado
			FROM `psi_sesiones` WHERE psi_tipo_doc='{$id[0]}' AND psi_documento='{$id[1]}'";
			$info=datos_mysql($sql);
			if (isset($info['responseResult'][0])){
				return $info['responseResult'][0];
			} else {
				return "";
			}
		} 
}

function cmp_sesiones_psi() {
	$rta="";
	$w='sesiones_psi';
	$j='';
	$o='infgen';
	$rta .="<div class='encabezado'>TABLA DE INTEGRANTES FAMILIA</div>
	<div class='contenido' id='sesiones-lis'>".lis_sesiones()."</div></div>";

	$c[]=new cmp($o,'e',null,'Sesion 3, 4, 5, 6',$w);
	//$key=' srch';
	$key=divide($_POST['id']);
	$days=fechas_app('psicologia');
	$sql="SELECT TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) edad FROM person WHERE idpeople='{$key[0]}'";
		$info=datos_mysql($sql);
		$edad=$info['responseResult'][0]['edad'];
		$ed = ($edad<18) ? false :true;
	/* $data=datos_mysql("SELECT CASE WHEN COUNT(*) = 0 THEN +3 ELSE +1 END total FROM psi_sesiones WHERE psi_tipo_doc='{$key[0]}' and psi_documento='{$key[1]}';");
	$nse=$data['responseResult'][0]['total']; */
		// $blo = ($ed) ? '' : 'bloqueo' ;
	$c[]=new cmp('idpsi','h','20', $_POST['id'],$w.' '.$o,'','',null,null,false,false,'','col-1');
	$c[]=new cmp('psi_fecha_sesion','d','10',$j,$w.' '.$o,'Fecha de la Sesion','psi_fecha_sesion',null,null,true,true,'','col-3',"validDate(this,$days,0);");
	$c[]=new cmp('psi_sesion','s','3',$j,$w.' '.$o,'Sesion','psi_sesion',null,null,false,false,'','col-4');
	$c[]=new cmp('cod_admin4','s','12',$j,$w.' cA4 '.$o,'Codigo Admisión','cod_admin4',null,null,true,true,'','col-3');

	$o='infgen_2';
	$c[]=new cmp($o,'e',null,'RESULTADO  EVALUACION DURANTE EP+',$w);
	$c[]=new cmp('psi_validacion1','a','1500',$j,$w.' '.$o,'1. Éste es el problema que más le preocupa, según usted nos dijo cuando le preguntamos al principio.','psi_validacion1',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion2','s','3',$j,$w.' '.$o,'1,1. ¿Cuánto le ha afectado durante la última semana? (Por favor marque un recuadro de abajo.)','psi_validacion2',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion3','a','1500',$j,$w.' '.$o,'2. Éste es el otro problema que le preocupa, según usted nos dijo cuando le preguntamos al principio.','psi_validacion3',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion4','s','3',$j,$w.' '.$o,'2,2. ¿Cuánto le ha afectado durante la última semana? (Por favor marque un recuadro de abajo.)','psi_validacion4',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion5','a','1500',$j,$w.' '.$o,'3. Esto es lo que le ha costado hacer, según usted nos dijo cuando le preguntamos al principio.','psi_validacion5',null,null,$ed,$ed,'','col-10');

	$c[]=new cmp('difhacer','s','3',$j,$w.' '.$o,'3,1. ¿Cuán difícil le ha resultado hacer esto durante la última semana?','psi_validacion9',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion6','s','3',$j,$w.' '.$o,'4. ¿Cómo se ha sentido la última semana?','psi_validacion6',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion7','o','2',$j,$w.' '.$o,'5. Durante la última semana, ¿ha tenido usted pensamientos o un plan para terminar con su vida?','psi_validacion7',null,null,$ed,$ed,'','col-10','hidFieOpt(\'psi_validacion7\',\'sem_hide\');');
	$c[]=new cmp('psi_validacion8','a','1500',$j,$w.' sem_hide '.$o,'5,1. Describa sus pensamientos o planes','psi_validacion8',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion9','a','1500',$j,$w.' sem_hide '.$o,'6. ¿Qué acciones usted ha efectuado para tratar de terminar con su vida?','psi_validacion9',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion10','s','3',$j,$w.' '.$o,'7. ¿Tiene un plan para terminar con su vida dentro de las próximas dos semanas?','en_duda',null,null,$ed,$ed,'','col-10','hidPlan(\'psi_validacion10\',\'duda_com\');');
	$c[]=new cmp('psi_validacion11','a','1500',$j,$w.' duda_com '.$o,'7,1. Describa su plan','psi_validacion11',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion12','a','1500',$j,$w.' '.$o,'8. Ahora que usted está participando en esta intervención, tal vez ha descubierto que otros problemas se han vuelto importantes. Si es así, mencione el que más le preocupa, o dígame si ningún otro problema se ha vuelto importante.','psi_validacion12',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion13','s','3',$j,$w.' '.$o,'8,1. ¿Cuánto le han afectado estos otros problemas durante la última semana?','psi_validacion13',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion14','a','1500',$j,$w.' '.$o,'Actividad A Desarrollar 1','psi_validacion14',null,null,true,true,'','col-10');
	$c[]=new cmp('psi_validacion15','a','1500',$j,$w.' '.$o,'Actividad A Desarrollar 2','psi_validacion15',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion16','a','1500',$j,$w.' '.$o,'Actividad A Desarrollar 3','psi_validacion16',null,null,false,true,'','col-10');
	$c[]=new cmp('contin_caso','s','3',$j,$w.' '.$o,'Se mantiene el caso abierto','psi_validacion17',null,null,true,true,'','col-10',"enabDepeValu('contin_caso','cA4',['4','5'],false);");

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_sesiones_psi(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT idsesipsi,psi_fecha_sesion,psi_sesion,cod_admin4,psi_validacion1,psi_validacion2,psi_validacion3,psi_validacion4,psi_validacion5,difhacer,psi_validacion6,psi_validacion7,psi_validacion8,psi_validacion9,psi_validacion10,psi_validacion11,psi_validacion12,psi_validacion13,psi_validacion14,psi_validacion15,psi_validacion16,psi_validacion17
		FROM `psi_sesiones` WHERE idsesipsi='{$id[0]}'";
		// echo $sql;
		$info=datos_mysql($sql);
		return json_encode($info['responseResult'][0]);
	} 
}

function focus_sesiones_psi(){
	return 'sesiones_psi';
}

function men_sesiones_psi(){
 $rta=cap_menus('sesiones_psi','pro');
 return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='sesiones_psi' && isset($acc['crear']) && $acc['crear']=='SI'){  
		$rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	}
	return $rta;
  }

  function numSess($a){
	// var_dump($id);
	$sql="SELECT MAX(psi_sesion) as max_sesion FROM psi_sesiones WHERE id_people='$a'";
	$info=datos_mysql($sql);
	// var_dump($info);
	 if (isset($info['responseResult'][0]['max_sesion']) && $info['responseResult'][0]['max_sesion'] !== null) {
        return intval($info['responseResult'][0]['max_sesion']) + 1;
    } else {
        return 3;
    }
}

function gra_sesiones_psi(){
	$idpsi=divide($_POST['idpsi']);
	if(count($idpsi) ==0){

/* 	$sql="UPDATE psi_sesiones SET 
		psi_validacion1  = TRIM(upper('{$_POST['psi_validacion1']}')),
		psi_validacion3  = TRIM(upper('{$_POST['psi_validacion3']}')),
		psi_validacion8  = TRIM(upper('{$_POST['psi_validacion8']}')),
		psi_validacion9  = TRIM(upper('{$_POST['psi_validacion9']}')),
		psi_validacion11 = TRIM(upper('{$_POST['psi_validacion11']}')),
		psi_validacion12 = TRIM(upper('{$_POST['psi_validacion12']}')),
		psi_validacion14 = TRIM(upper('{$_POST['psi_validacion14']}')),
		psi_validacion15 = TRIM(upper('{$_POST['psi_validacion15']}')),
		psi_validacion16 = TRIM(upper('{$_POST['psi_validacion16']}')),
		psi_validacion17 = TRIM(upper('{$_POST['contin_caso']}')),
		`usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),
		`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
		WHERE idsesipsi='$idpsi[0]'";  */
	  //echo $x;
	//   echo $sql;

	 $sql = "UPDATE psi_sesiones SET psi_validacion1 = ?,psi_validacion3 = ?,psi_validacion8 = ?,psi_validacion9 = ?,psi_validacion11 = ?,psi_validacion12 = ?,psi_validacion14 = ?,psi_validacion15 = ?,psi_validacion16 = ?,psi_validacion17 = ?,usu_update = ?,fecha_update = DATE_SUB(NOW(), INTERVAL 5 HOUR)  
	 WHERE idsesipsi = ?";
    $params = [
      ['type' => 's', 'value' => $_POST['psi_validacion1']],
      ['type' => 's', 'value' => $_POST['psi_validacion3']],
      ['type' => 's', 'value' => $_POST['psi_validacion8']],
      ['type' => 's', 'value' => $_POST['psi_validacion9']],
	  ['type' => 's', 'value' => $_POST['psi_validacion11']],
	  ['type' => 's', 'value' => $_POST['psi_validacion12']],
	  ['type' => 's', 'value' => $_POST['psi_validacion14']],
	  ['type' => 's', 'value' => $_POST['psi_validacion15']],
	  ['type' => 's', 'value' => $_POST['psi_validacion16']],
	  ['type' => 's', 'value' => $_POST['contin_caso']],
      ['type' => 's', 'value' => $_SESSION['us_sds']],
      ['type' => 's', 'value' => $idpsi[0]],
    ];

	} elseif(count($idpsi) ==2){
		$nuse=numSess($idpsi[0]);

		$sql = "INSERT INTO psi_sesiones VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?,?)";
		$params = [
		['type' => 'i', 'value' => NULL],
		['type' => 's', 'value' => $idpsi[0]],
		['type' => 's', 'value' => $_POST['psi_fecha_sesion']],
		['type' => 'i', 'value' => $nuse],
		['type' => 's', 'value' => $_POST['cod_admin4']],
		['type' => 's', 'value' => $_POST['psi_validacion1']],
		['type' => 's', 'value' => $_POST['psi_validacion2']],
		['type' => 's', 'value' => $_POST['psi_validacion3']],
		['type' => 's', 'value' => $_POST['psi_validacion4']],
		['type' => 's', 'value' => $_POST['psi_validacion5']],
		['type' => 's', 'value' => $_POST['difhacer']],
		['type' => 's', 'value' => $_POST['psi_validacion6']],
		['type' => 's', 'value' => $_POST['psi_validacion7']],
		['type' => 's', 'value' => $_POST['psi_validacion8']],
		['type' => 's', 'value' => $_POST['psi_validacion9']],
		['type' => 's', 'value' => $_POST['psi_validacion10']],
		['type' => 's', 'value' => $_POST['psi_validacion11']],
		['type' => 's', 'value' => $_POST['psi_validacion12']],
		['type' => 's', 'value' => $_POST['psi_validacion13']],
		['type' => 's', 'value' => $_POST['psi_validacion14']],
		['type' => 's', 'value' => $_POST['psi_validacion15']],
		['type' => 's', 'value' => $_POST['psi_validacion16']],
		['type' => 's', 'value' => $_POST['contin_caso']],
		['type' => 'i', 'value' => $_SESSION['us_sds']],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => 'A']
		];
		return $rta = mysql_prepd($sql, $params);
	}
	$rta=dato_mysql($sql);
	//return "correctamente";
	return $rta; 
}

function opc_cod_admin4($id='') {
	// var_dump($_REQUEST['id']);
	$cod=divide($_REQUEST['id']);
	return opc_sql("SELECT cod_admin,CONCAT_WS(' - ',cod_admin,FN_CATALOGODESC(127,final_consul))  from adm_facturacion af WHERE af.idpeople='".$cod[0]."' AND cod_cups IN(9,16) AND final_consul IN(17,18,19,20,21,22,23,24) ORDER BY 1", $id);
}
function opc_psi_validacion9($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_sesion($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 125 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion2($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion4($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion5($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion6($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_en_duda($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=130 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion13($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion17($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 160 and estado='A' ORDER BY 1",$id);
}
function bgcolor($a,$c,$f='c'){
	$rta="";
	return $rta;
}
function formato_dato($a,$b,$c,$d){
	$b=strtolower($b);
	$rta=$c[$d];
	// $rta=iconv('UTF-8','ISO-8859-1',$rta);
	// var_dump($a);
	// var_dump($c);
	if ($a=='sesiones' && $b=='acciones'){
		$rta="<nav class='menu right'>";
			$rta.="<li class='icono editar ' title='Editar Sesiones' id='".$c['ACCIONES']."' Onclick=\"Color('sesiones-lis');setTimeout(getData,300,'sesiones_psi',event,this,['psi_fecha_sesion','psi_sesion','cod_admin4','contin_caso'],'../psicologia/sesiones.php');\"></li>";  //getData('plancon',event,this,'id');act_lista(f,this);
		}
return $rta;
}