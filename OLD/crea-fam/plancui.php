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


function focus_planDcui(){
	return 'planDcui';
   }
   
   
   function men_planDcui(){
	$rta=cap_menus('planDcui','pro');
	return $rta;
   }
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='planDcui'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	 
	   }
  return $rta;
}
FUNCTION lis_planDcui(){
	// var_dump($_POST['id']);
	$id=divide($_POST['id']);
	$sql="SELECT `idamb` ACCIONES,idamb 'Cod Registro',`fecha`,FN_CATALOGODESC(34,tipo_activi) Tipo,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM hog_amb A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idvivamb='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"planDcui-lis",5);
   }


function cmp_planDcui(){
	$rta="";
	$t=['id'=>'','fecha'=>'','accion1'=>'','desc_accion1'=>'','accion2'=>'','desc_accion2'=>'','accion3'=>'','desc_accion3'=>'','accion4'=>'','desc_accion4'=>'','observacion'=>''];
	$d=get_planDcui();
	if ($d==""){$d=$t;}
//var_dump($d);
	$u=($d['fecha']=='')?true:false;
	$hoy=date('Y-m-d');
    $w="planDcui";
	$o='accide';
	$e="";
	$key='pln';
    $days=fechas_app('vivienda');
    $c[]=new cmp($o,'e',null,'ACCIONES PROMOCIONALES Y DE IDENTIFICACIÓN DE RIESGOS REALIZADOS EN LA CARACTERIZACIÓN FAMILIAR',$w);
    $c[]=new cmp('fecha_caracteriza','d','10',$d['fecha'],$w.' '.$o,'fecha_caracteriza','fecha_caracteriza',null,null,true,$u,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('idp','h',15,$_POST['id'],$w.' '.$key.' '.$o,'id','id',null,'####',false,false);
	$c[]=new cmp('accion1','s',3,$d['accion1'],$w.' '.$o,'Accion 1','accion1',null,null,true,true,'','col-3',"selectDepend('accion1','desc_accion1','../crea-fam/plancui.php');");
	$c[]=new cmp('desc_accion1','s',3,$d['desc_accion1'],$w.' '.$o,'Descripcion Accion 1','desc_accion1',null,null,true,true,'','col-5');
    $c[]=new cmp('accion2','s','3',$d['accion2'],$w.' '.$o,'Accion 2','accion2',null,null,false,true,'','col-5',"selectDepend('accion2','desc_accion2','../crea-fam/plancui.php');");
    $c[]=new cmp('desc_accion2','s','3',$d['desc_accion2'],$w.' '.$o,'Descripcion Accion 2','desc_accion2',null,null,false,true,'','col-5');
    $c[]=new cmp('accion3','s','3',$d['accion3'],$w.' '.$o,'Accion 3','accion3',null,null,false,true,'','col-5',"selectDepend('accion3','desc_accion3','../crea-fam/plancui.php');");
    $c[]=new cmp('desc_accion3','s','3',$d['desc_accion3'],$w.' '.$o,'Descripcion Accion 3','desc_accion3',null,null,false,true,'','col-5');
    $c[]=new cmp('accion4','s','3',$d['accion4'],$w.' '.$o,'Accion 4','accion4',null,null,false,true,'','col-5',"selectDepend('accion4','desc_accion4','../crea-fam/plancui.php');");
    $c[]=new cmp('desc_accion4','s','3',$d['desc_accion4'],$w.' '.$o,'Descripcion Accion 4','desc_accion3',null,null,false,true,'','col-5');
	$c[]=new cmp('observacion','a',500,$d['observacion'],$w.' '.$o,'Observacion','observacion',null,null,true,true,'','col-10');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

	function gra_planDcui(){
		// print_r($_POST);
	$id=divide($_POST['idp']);
    $sql1="select idviv from hog_plancuid where idviv='{$id[0]}'";
    $info = datos_mysql($sql1);
    if (!$info['responseResult']) {
      $sql = "INSERT INTO hog_plancuid VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

      $params = [
        ['type' => 'i', 'value' => NULL],
        ['type' => 'i', 'value' => $id[0]],
        ['type' => 's', 'value' => $_POST['fecha_caracteriza']],
        ['type' => 's', 'value' => $_POST['accion1']],
        ['type' => 's', 'value' => $_POST['desc_accion1']],
        ['type' => 's', 'value' => $_POST['accion2']],
        ['type' => 's', 'value' => $_POST['desc_accion2']],
        ['type' => 's', 'value' => $_POST['accion3']],
        ['type' => 's', 'value' => $_POST['desc_accion3']],
        ['type' => 's', 'value' => $_POST['accion4']],
        ['type' => 's', 'value' => $_POST['desc_accion4']],
        ['type' => 's', 'value' => $_POST['observacion']],
        ['type' => 'i', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => date("Y-m-d H:i:s")],
        ['type' => 's', 'value' => ''],
        ['type' => 's', 'value' => ''],
        ['type' => 's', 'value' => 'A']
      ];
      $rta = mysql_prepd($sql, $params);
    }else{
$sql="UPDATE hog_plancuid SET  accion1=?,desc_accion1=?,accion2=?,desc_accion2=?,accion3=?,desc_accion3=?,accion4=?,desc_accion4=?,observacion=?,usu_update=?,fecha_update=? WHERE idviv=?";

$params = [
        ['type' => 's', 'value' => $_POST['accion1']],
        ['type' => 's', 'value' => $_POST['desc_accion1']],
        ['type' => 's', 'value' => $_POST['accion2']],
        ['type' => 's', 'value' => $_POST['desc_accion2']],
        ['type' => 's', 'value' => $_POST['accion3']],
        ['type' => 's', 'value' => $_POST['desc_accion3']],
        ['type' => 's', 'value' => $_POST['accion4']],
        ['type' => 's', 'value' => $_POST['desc_accion4']],
        ['type' => 's', 'value' => $_POST['observacion']],
        ['type' => 'i', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => date("Y-m-d H:i:s")],
['type' => 'i', 'value' => $id[0]]
      ];
      $rta = mysql_prepd($sql, $params);
    }
return $rta;
	}

	function get_planDcui(){
		if (!$_POST['id']) {
			return '';
		}
		$id = divide($_POST['id']);
		$sql = "SELECT concat(A.idviv,'_',A.id) 'id',fecha,accion1,desc_accion1,accion2,desc_accion2,accion3,desc_accion3,accion4,desc_accion4,observacion 
		FROM hog_plancuid A
		WHERE A.idviv='{$id[0]}'";
	//	echo $sql;		
		$info = datos_mysql($sql);
		// echo $sql; 
	//	print_r($info['responseResult'][0]);
		if (!$info['responseResult']) {
			return '';
		}else{
			return $info['responseResult'][0];
		}
	}

    function opc_accion1desc_accion1($id=''){
        if($_REQUEST['id']!=''){
                    $id=divide($_REQUEST['id']);
                    $sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
                    $info=datos_mysql($sql);
                    return json_encode($info['responseResult']);
            }
        }
        
        function opc_accion2desc_accion2($id=''){
          if($_REQUEST['id']!=''){
                $id=divide($_REQUEST['id']);
                $sql="SELECT idcatadeta,descripcion  FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
                $info=datos_mysql($sql);		
                return json_encode($info['responseResult']);
              }
          }
          function opc_accion3desc_accion3($id=''){
            if($_REQUEST['id']!=''){
                  $id=divide($_REQUEST['id']);
                  $sql="SELECT idcatadeta 'id',descripcion 'asc' FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
                  $info=datos_mysql($sql);		
                  return json_encode($info['responseResult']);
                }
            }
            function opc_accion4desc_accion4($id=''){
            if($_REQUEST['id']!=''){
                  $id=divide($_REQUEST['id']);
                  $sql="SELECT idcatadeta 'id',descripcion 'asc' FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
                  $info=datos_mysql($sql);		
                  return json_encode($info['responseResult']);
                }
            }
        
        function opc_desc_accion1($id=''){
          return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
          }
        function opc_desc_accion2($id=''){
            return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
        }
        function opc_desc_accion3($id=''){
            return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
        }
        function opc_accion1($id=''){
        return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
        }
        function opc_accion2($id=''){
        return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
        }
        function opc_accion3($id=''){
        return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
        }
        function opc_accion4($id=''){
        return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
        }
        
        function opc_equipo($id=''){
            return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=26 and estado='A' ORDER BY 1",$id);
        } 


	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
		if ($a=='planDcui-lis' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				// $rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'planDcui',event,this,['fecha','tipo_activi'],'../vivienda/amb.php');\"></li>";  //   act_lista(f,this);
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	   }
	   