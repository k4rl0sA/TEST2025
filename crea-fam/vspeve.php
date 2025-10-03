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

function focus_vspeve(){
  return 'vspeve';
 }
 
 function men_vspeve(){
  $rta=cap_menus('vspeve','pro');
  return $rta;
 }
 
 
 function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  $acc=rol($a);
  if ($a=='vspeve' && isset($acc['crear']) && $acc['crear']=='SI') {  
   $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
    }
  $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";  
  return $rta;
}


function lis_eventos(){
    // var_dump($_POST['id']);
    $id=divide($_POST['id']);

    $total="SELECT COUNT(*) AS total FROM (
      SELECT id_eve 'Cod Registro',idpeople,FN_CATALOGODESC(87,evento),fecha_even
    FROM vspeve E 
    WHERE E.idpeople='{$id[0]}') AS Subquery";
    $info=datos_mysql($total);
    $total=$info['responseResult'][0]['total']; 
    $regxPag=5;
    $pag=(isset($_POST['pag-eventos']))? ($_POST['pag-eventos']-1)* $regxPag:0;



    $sql="SELECT id_eve 'Cod Registro',idpeople,FN_CATALOGODESC(87,evento),fecha_even
    FROM vspeve E 
    WHERE E.idpeople='{$id[0]}'";  
    $sql.=" ORDER BY 4 desc LIMIT $pag, $regxPag";
    // echo $sql;
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"eventos",$regxPag,'vspeve.php');
}

function cmp_vspeve(){
	$rta="<div class='encabezado medid'>TABLA DE EVENTOS POR USUARIO</div>
	<div class='contenido' id='eventos-lis'>".lis_eventos()."</div></div>";
	$t=['id_eve'=>'','tipodoc'=>'','idpersona'=>'','nombre'=>'','fechanacimiento'=>'','edad'=>'','sexo'=>'','docum_base'=>'','evento'=>'','fecha_even'=>''];
	$d=get_persona();
	if ($d==""){$d=$t;}
	$e="";
	$w='vspeve';
	$o='datos';
  $key='eve';
  $edad='AÑOS= '.$d['anos'].' MESES= '.$d['meses'].' DIAS= '.$d['dias'];
  $days=fechas_app('vsp');
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('idpersona','n','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','idpersona',null,'',false,false,'','col-3');
	$c[]=new cmp('tipodoc','s','3',$d['tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-3');//setTimeout(hiddxedad,1000,\'edad\',\'find\');
	$c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('sexo','s','3',$d['sexo'],$w.' '.$o,'Sexo','sexo',null,'',true,false,'','col-2');
	$c[]=new cmp('fechanacimiento','d',10,$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',true,false,'','col-3');
  $c[]=new cmp('edad','t',30,$edad,$w.' '.$o,'edad en Años','edad',null,'',true,false,'','col-3');
	
	$o='prufin';
 	$c[]=new cmp($o,'e',null,'EVENTOS VSP',$w);
 	$c[]=new cmp('docum_base','t',22,$e,$w.' '.$o,'Documento Base','docum_base',null,null,true,true,'','col-25');
  $c[]=new cmp('evento','s',3, $e,$w,'Evento','evento',null,null,true,true,'','col-2');
  $c[]=new cmp('fecha_even','d',10,$e,$w.' '.$o,'Fecha Creación Evento','fecha_even',null,null,true,true,'','col-25',"validDate(this,$days,0);");
  for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}



function opc_tipodoc($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_sexo($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}

function opc_evento($id=''){
  $d=get_persona();
var_dump($d['anos']);
   if($d['anos'] > 11 && $d['anos'] < 18 && ($d['sexo']=='M' || $d['sexo']=='H')){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5,2,3,6) and estado='A' ORDER BY 2",$id);
  }

  if($d['sexo']=='M'){
    if($d['anos']<6){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5,1,2,3) and estado='A' ORDER BY 2",$id);
    }elseif($d['anos']>5 && $d['anos']<10){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5,2,3) and estado='A' ORDER BY 2",$id); 
    }elseif($d['anos']>9 && $d['anos']<18){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5,2,3,4,6) and estado='A' ORDER BY 2",$id); 
    }elseif($d['anos']>17 && $d['anos']<55){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5,2,4,6) and estado='A' ORDER BY 2",$id); 
    }elseif($d['anos']>54){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5) and estado='A' ORDER BY 2",$id); 
    }
  }else{
    if($d['anos']<6){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5,1,2,3) and estado='A' ORDER BY 2",$id);
    }elseif($d['anos']>5 && $d['anos']<18){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5,2,3) and estado='A' ORDER BY 2",$id); 
    }elseif($d['anos']>17 && $d['anos']<55){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5) and estado='A' ORDER BY 2",$id); 
    }elseif($d['anos']>54){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(0,5) and estado='A' ORDER BY 2",$id); 
    }
    /* elseif($d['anos']>9 && $d['anos']<18){
      return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,2,3) and estado='A' ORDER BY 2",$id); 
    } */
  }
  /* if($d['anos']<6){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,1,2,3) and estado='A' ORDER BY 2",$id);
  }elseif($d['anos']<18){

  }
  elseif($d['anos']<18 || ($d['anos']>17 && $d['sexo']=='M')){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,1,2,3) and estado='A' ORDER BY 2",$id);
  }elseif(($d['anos']>9 && $d['anos']<55 && $d['sexo']=='M')){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,4) and estado='A' ORDER BY 2",$id); */

  }



  // $d['sexo']&&
  


function gra_vspeve(){
  // Validación de campos obligatorios
  if (
    empty($_POST['docum_base']) ||
    empty($_POST['evento']) ||
    empty($_POST['fecha_even'])
  ) {
    return ['error' => 'Todos los campos son obligatorios.'];
  }
  // Validar formato de fecha (YYYY-MM-DD)
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['fecha_even'])) {
    return ['error' => 'El formato de la fecha es inválido.'];
  }
  $id = divide($_POST['id']);
  if (count($id) == 1) {
    $sql = "UPDATE vspeve SET docum_base= ?, evento= ?, fecha_even= ?, usu_update= ?, fecha_update= DATE_SUB(NOW(), INTERVAL 5 HOUR) 
      WHERE id_eve = ?";
    $params = [
      ['type' => 's', 'value' => trim($_POST['docum_base'])],
      ['type' => 'i', 'value' => intval($_POST['evento'])],
      ['type' => 's', 'value' => trim($_POST['fecha_even'])],
      ['type' => 's', 'value' => $_SESSION['us_sds']],
      ['type' => 'i', 'value' => intval($id[0])]
    ];
    $rta = mysql_prepd($sql, $params);
  } else if (count($id) == 2) {
    $sql = "INSERT INTO vspeve VALUES (?,?,?,?,?, ?,DATE_SUB(NOW(), INTERVAL 5 HOUR),?,?,?)";
    $params = [
      ['type' => 'i', 'value' => null], // id_eve auto-increment
      ['type' => 's', 'value' => $id[0]],
      ['type' => 's', 'value' => trim($_POST['docum_base'])],
      ['type' => 'i', 'value' => intval($_POST['evento'])],
      ['type' => 's', 'value' => trim($_POST['fecha_even'])],
      ['type' => 's', 'value' => $_SESSION['us_sds']],
      ['type' => 's', 'value' => null], // usu_update
      ['type' => 's', 'value' => null], // fecha_update
      ['type' => 's', 'value' => 'A'] // estado
    ];
    $rta = mysql_prepd($sql, $params);
  } else {
    return ['error' => 'ID inválido.'];
  }
  return $rta;
}

function get_persona(){
  if($_REQUEST['id']==''){
    return "";
  }else{
    $id=divide($_REQUEST['id']);
    $sql="SELECT P.idpeople,P.idpersona idpersona,P.tipo_doc tipodoc,CONCAT_WS(' ',nombre1,nombre2,apellido1,apellido2) nombre,P.fecha_nacimiento fechanacimiento,
		P.sexo sexo,
    TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS anos,
    TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE())-(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) * 12) AS meses,
    DATEDIFF(CURDATE(),DATE_ADD(fecha_nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) YEAR)) % 30 AS dias
		FROM person P
		left join vspeve E ON P.idpeople = E.idpeople
    WHERE P.idpeople='{$id[0]}'"; 
    // echo $sql;
    // print_r($_REQUEST);
    $info=datos_mysql($sql);
    return $info['responseResult'][0];
  }
}

function get_vspeve(){
  if($_REQUEST['id']==''){
    return "";
  }else{
      $id=divide($_REQUEST['id']);
      $sql="SELECT concat_ws('_',id_eve,idpeople),docum_base,evento,fecha_even
      FROM vspeve
      WHERE id_eve ='{$id[0]}'";
      // echo $sql;
      // print_r($id);
      $info=datos_mysql($sql);
      return json_encode($info['responseResult'][0]);
    } 
  }

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($a);
// var_dump($rta);
if ($a=='vspeve-lis' && $b=='acciones'){//a mnombre del modulo
	$rta="<nav class='menu right'>";	
	$rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'vspeve',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado'],'vspeve.php');\"></li>";
}
 return $rta;
}


function bgcolor($a,$c,$f='c'){
  $rta="";
  return $rta;
   }