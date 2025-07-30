<?php
 require_once '../libs/gestion.php';
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



function lis_gestuser(){
	$info=datos_mysql("SELECT COUNT(*) total FROM adm_usunew WHERE 1 ".whe_gestuser());
	$total=$info['responseResult'][0]['total'];
	$regxPag=10;
	$pag=(isset($_POST['pag-gestuser']))? ($_POST['pag-gestuser']-1)* $regxPag:0;

	$sql="SELECT * FROM adm_usunew WHERE 1 ";
	$sql.= whe_gestuser();
	$sql.=" ORDER BY fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"gestuser",$regxPag);
}

function whe_gestuser() {
	$sql = "";
    if ($_POST['fcaso']) {
        $sql .= " AND id_usu = '" . $_POST['fcaso'] . "'";
    } elseif ($_POST['fdoc']) {
        $sql .= " AND documento LIKE '%".$_POST['fdoc']."%'";
    } else {
        $sql .= " AND DATE(fecha_create) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND CURDATE() AND SUBRED=(select subred from usuarios where id_usuario='" . $_SESSION['us_sds'] . "')";
    }
    return $sql;
}


function focus_gestuser(){
 return 'gestuser';
}

function men_gestuser(){
 $rta=cap_menus('gestuser','pro');
 return $rta;
} 


function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  if ($a=='gestuser'){  
	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
  	$rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
  }
  return $rta;
}


function cmp_gestuser(){
	$rta="";
	$hoy=date('Y-m-d');
	$t=['gestion'=>'','perfil'=>'','usuario'=>''];
	$d='';
	if ($d==""){$d=$t;}
	$w='adm_usuarios';
	$o='creusu';
	$c[]=new cmp($o,'e',null,'GESTIÓN DE USUARIOS',$w);
	$c[]=new cmp('gestion','s','3',$d['gestion'],$w.' '.$o,'Acción','gestion',null,'',true,true,'','col-3');
	$c[]=new cmp('perfil','s',3,$d['perfil'],$w.' '.$o,'Perfil','perfil',null,'',true,true,'','col-2',"changeSelect('perfil','usuario');");//  ,"enabDepeValu('perfil','uSR');
	$c[]=new cmp('usuario','s',20,$d['usuario'],$w.' uSR '.$o,'Usuario','usuario',null,'',false,true,'','col-5');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_gestuser(){
	
}

function gra_gestuser(){
switch ($_POST['gestion']) {
	case '1':
		$sql = "update usuarios SET clave=? WHERE id_usuario=?";
		$params = [['type' => 'z', 'value' => '$2y$10$U1.jyIhJweaZQlJK6jFauOAeLxEOTJX8hlWzJ6wF5YVbYiNk1xfma'],
			['type' => 'i', 'value' => $_POST['usuario']]];
			$rta = mysql_prepd($sql, $params);
		break;
	case '2':
		$sql = "update usuarios SET estado=? WHERE id_usuario=?";
		$params = [['type' => 's', 'value' => 'I'],
			['type' => 'i', 'value' => $_POST['usuario']]];
			$rta = mysql_prepd($sql, $params);
		break;
	default:
		# code...
		break;
}

  /* $sql = "INSERT INTO adm_usunew VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
   $rta=datos_mysql("select FN_USUARIO('".$_SESSION['us_sds']."') as usu;");
   $usu=divide($rta["responseResult"][0]['usu']);

   $rta=datos_mysql("select FN_CATALOGODESC(218,'".$_POST['perfil']."') AS perfil ,FN_CATALOGODESC(202,'".$_POST['territorio']."') AS terr,FN_CATALOGODESC(217,'".$_POST['bina']."') AS bina;");
   $data=$rta["responseResult"][0];

   $params = [
	['type' => 'i', 'value' => NULL],
	['type' => 'i', 'value' => $_POST['documento']],
	['type' => 's', 'value' => $_POST['nombre']],
	['type' => 's', 'value' => $_POST['correo']],
	['type' => 's', 'value' => $data['perfil']],
	['type' => 's', 'value' => $data['terr']],
	['type' => 's', 'value' => $data['bina']],
	['type' => 'i', 'value' => $usu[2]],
	['type' => 's', 'value' => $usu[4]],
	['type' => 'i', 'value' => $_SESSION['us_sds']],
	['type' => 's', 'value' => date("Y-m-d H:i:s")],
	['type' => 's', 'value' => NULL],
	['type' => 's', 'value' => NULL],
	['type' => 's', 'value' => NULL]];
	$rta1 = mysql_prepd($sql, $params);

	$sql1 = "INSERT INTO usuarios VALUES (?,?,?,?,?,?,?,?,?)";
	$equ = ($data['bina']=='') ? $data['terr'] : $data['bina'] ;
	$params1 = [
		['type' => 'i', 'value' => $_POST['documento']],
		['type' => 's', 'value' => $_POST['nombre']],
		['type' => 's', 'value' => $_POST['correo']],
		['type' => 's', 'value' => '$2y$10$U1.jyIhJweaZQlJK6jFauOAeLxEOTJX8hlWzJ6wF5YVbYiNk1xfma'],
		['type' => 's', 'value' => $data['perfil']],
		['type' => 'i', 'value' => $usu[2]],
		['type' => 's', 'value' => $equ],
		['type' => 's', 'value' => $usu[4]],
		['type' => 's', 'value' => 'P']];
		$rta2 = mysql_prepd($sql1, $params1); */
	return $rta;
}

function adm(){
    $info = datos_mysql("SELECT perfil FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}'");
    $adm = $info['responseResult'][0]['perfil'];
    return $adm;
}

function opc_perfilusuario($id=''){
    if($_REQUEST['id']!=''){	
        if(adm()=='ADM'){	
            $sql = "SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE 
            perfil=(select descripcion from catadeta c where idcatalogo=218 and idcatadeta='{$_REQUEST['id']}' and estado='A') 
            ORDER BY nombre";
            $info = datos_mysql($sql);		
            return json_encode($info['responseResult']);	
        } else {
            $sql = "SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE 
            perfil=(select descripcion from catadeta c where idcatalogo=218 and idcatadeta='{$_REQUEST['id']}' and estado='A') 
            and componente=(SELECT componente FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}') 
            and subred=(SELECT subred FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}') ORDER BY nombre";
            echo $sql;
            $info = datos_mysql($sql);		
            return json_encode($info['responseResult']);	
        }
    } 
}

function opc_perfil($id=''){
    if (adm()=='ADM') {
        return opc_sql("SELECT idcatadeta, descripcion FROM `catadeta` WHERE idcatalogo = 218 AND estado = 'A'",$id);
    } else {
        // $com = datos_mysql("SELECT CASE WHEN componente = 'MIN' THEN 2 WHEN componente = 'HOG' THEN 1 END as componente FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}'");
		$comp = '1,2';
        // $comp = $com['responseResult'][0]['componente'] ;
        return opc_sql("SELECT idcatadeta, descripcion FROM `catadeta` WHERE idcatalogo = 218 AND estado = 'A' AND valor in($comp)",$id);
    }
}


function opc_gestion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=216 and estado='A' ORDER BY 1",$id);
}
function opc_usuario($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=216 and estado='A' ORDER BY 1",$id);
}

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($rta);
	if ($a=='gestuser' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono asigna1' title='Asignar Usuario' id='".$c['ACCIONES']."' Onclick=\"mostrar('gestuser','pro',event,'','lib.php',7);\"></li>";
	}
	
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
