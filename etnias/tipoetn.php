<?php
ini_set('display_errors','1');
require_once "../libs/gestion.php";
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


function focus_ethnicity(){
	return 'ethnicity';
   }
   
   
   function men_ethnicity(){
	$rta=cap_menus('ethnicity','pro');
	return $rta;
   }
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='ethnicity'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	 
	   }
  return $rta;
}
function lis_ethnicity(){
    // print_r($_POST);
    $id = (isset($_POST['id'])) ? divide($_POST['id']) : divide($_POST['idp']) ;
$info=datos_mysql("SELECT COUNT(*) total FROM acc_indigenas WHERE idpeople=".$id[0]."");
$total=$info['responseResult'][0]['total'];
$regxPag=5;
$pag=(isset($_POST['pag-ethnicity']))? ($_POST['pag-ethnicity']-1)* $regxPag:0;

    $sql="SELECT id_acc 'Cod Registro', FN_CATALOGODESC(255,accion) Grupo,fecha_acc Fecha
        FROM `acc_indigenas` 
            WHERE idpeople='".$id[0];
        $sql.="' ORDER BY fecha_create";
        $sql.=' LIMIT '.$pag.','.$regxPag;
        //  echo $sql;
        $datos=datos_mysql($sql);
        return create_table($total,$datos["responseResult"],"ethnicity",$regxPag,'plncon.php');
}

function cmp_ethnicity(){
  $rta="";
  $w="placuifam";
  $t=['id_acc'=>'','idpeople'=>'','accion'=>'','fecha_acc'=>'']; 
	$key='pln';
	$o='ethnicity';
	$days=fechas_app('vivienda');
  $d='';
  $d=($d=="")?$d=$t:$d;
  $days=fechas_app('etnias');
  // $d=get_ethnicity();
  // var_dump($_POST);
	$c[]=new cmp($o,'e',null,'PLAN DE CUIDADO FAMILIAR CONCERTADO',$w);
  $c[]=new cmp('id_acc','h',11,$_POST['id'],$w.' '.$o,'Id de Acc','id_acc',null,null,true,true,'','col-2');
  $c[]=new cmp('accion','s',3,$d['accion'],$w.' '.$o,'Grupo','accion',null,null,true,true,'','col-2');
  $c[]=new cmp('fecha_acc','d',10,$d['fecha_acc'],$w.' '.$o,'Fecha','fecha_acc',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  // $c[]=new cmp('idp','h',15,$_POST['id'],$w.' '.$key.' '.$o,'id','id',null,'####',false,false);
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	$rta .="<div class='encabezado placuifam'>TABLA DE COMPROMISOS CONCERTADOS</div>
	<div class='contenido' id='ethnicity-lis' >".lis_ethnicity()."</div></div>";
	return $rta;
}

function gra_ethnicity(){
	$id=divide($_POST['id_acc']);
  if (($rtaFec = validFecha('etnias', $_POST['fecha_acc'] ?? '')) !== true) {return $rtaFec;}
    // var_dump(COUNT($id));
      $sql = "INSERT INTO acc_indigenas VALUES (null,?,?,?,?,DATE_SUB(NOW(), INTERVAL 5 HOUR),'','','A')";
      $params = [
        ['type' => 's', 'value' => $id[0]],
        ['type' => 's', 'value' => $_POST['accion']],
        ['type' => 's', 'value' => $_POST['fecha_acc']],
        ['type' => 's', 'value' => $_SESSION['us_sds']]];
      $rta = mysql_prepd($sql, $params);
return $rta;
}


function opc_accion($id=''){
  return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=255 and estado="A" ORDER BY 1',$id);
}

	function get_ethnicity(){
        if($_REQUEST['id']==''){
          return "";
        }else{
          // print_r($_POST);
          $id=divide($_REQUEST['id']);
          // print_r($id);
          $sql="SELECT id_acc,idpeople,accion,fecha_acc
                FROM acc_indigenas 
                WHERE id_acc='{$id[0]}'";
          $info=datos_mysql($sql);
           return json_encode($info['responseResult'][0]);
            } 
	}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
        // var_dump($a);
		if ($a=='ethnicity' && $b=='acciones'){
			$rta="<nav class='menu right'>";
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	   }
	   
