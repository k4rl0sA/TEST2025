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

function focus_emb_Id(){
	return 'emb_Id';
   }
   
   function men_emb_Id(){
	$rta=cap_menus('emb_Id','pro');
	return $rta;
   }
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='emb_Id'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	 
	   }
  return $rta;
}

function cmp_emb_Id(){
  $rta="";
  $w="placuifam";
	$t=['idriesgo'=>'','fechavisi'=>'','lider'=>'','educacion'=>'','espanol'=>'','saberes'=>'','enfoque'=>'','pueblo'=>''];
	$e="";
	$key='pln';
	$o='emb_Id';
  $d=get_emb_Id();
  if ($d==""){$d=$t;}
  $days=fechas_app('etnias');
  $id=$d['idriesgo']===''?$_POST['id']:$d['idriesgo'];
  $ke=divide($id);
  $up=count($ke)==2? true:false;
  /* if(count($id)==2){
    $up=true;
  }else{
    $up=false;
  } */
//  var_dump($up);
	$c[]=new cmp($o,'e',null,'IDENTIFICACIóN',$w);
    $c[]=new cmp('id','h',15,$id,$w.' '.$key.' '.$o,'id','id',null,'####',false,false);
    $c[]=new cmp('fechavisi','d',10,$d['fechavisi'],$w.' '.$o,'Fecha','fechavisi',null,null,true,$up,'','col-15',"validDate(this,$days,0);");
    $c[]=new cmp('lider','t',100,$d['lider'],$w.' '.$o,'Lider con el cual se Identifica la Familia','lider',null,null,true,true,'','col-3');
    $c[]=new cmp('educacion','o',2,$d['educacion'],$w.' '.$o,'Esta Vinculado(a) a servcios de Educacion','educacion',null,null,true,$up,'','col-2');
    $c[]=new cmp('espanol','o',2,$d['espanol'],$w.' '.$o,'Entiende Español','espanol',null,null,true,$up,'','col-15');
    $c[]=new cmp('saberes','s',3,$d['saberes'],$w.' '.$o,'Saberes Propios','saberes',null,null,true,true,'','col-2');
    $c[]=new cmp('enfoque','s',3,$d['enfoque'],$w.' '.$o,'Enfoque Diferencial','enfoque',null,null,true,$up,'','col-2');
    $c[]=new cmp('pueblo','s',3,$d['pueblo'],$w.' '.$o,'Pueblo','pueblo',null,null,true,$up,'','col-25');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

/* function gra_emb_Id(){
	$id=divide($_POST['id']);
  $usu=$_SESSION['us_sds'];
    if(COUNT($id)==2){
      $sql = "INSERT INTO etn_identi VALUES (NULL,?,?,?,?,?,?,?,?,$usu,DATE_SUB(NOW(),INTERVAL 5 HOUR),NULL,NULL,'A')";
      $params = [
        ['type' => 's', 'value' => $id[0]],
        ['type' => 's', 'value' => $_POST['fechavisi']],
        ['type' => 'i', 'value' => $_POST['lider']],
        ['type' => 's', 'value' => $_POST['educacion']],
        ['type' => 's', 'value' => $_POST['espanol']],
        ['type' => 's', 'value' => $_POST['saberes']],
        ['type' => 's', 'value' => $_POST['enfoque']],
        ['type' => 's', 'value' => $_POST['pueblo']]
      ];
      //  $rta = show_sql($sql, $params);
      $rta = mysql_prepd($sql, $params);
    }else{
      $sql = "UPDATE etn_identi SET fechavisi=?,lider=?,educacion=?,espanol=?,saberes=?,enfoque=?,pueblo=?,usu_update=$usu,fecha_update=DATE_SUB(NOW(),INTERVAL 5 HOUR) WHERE idriesgo=?";
      $params = [
        ['type' => 's', 'value' => $_POST['fechavisi']],
        ['type' => 'i', 'value' => $_POST['lider']],
        ['type' => 's', 'value' => $_POST['educacion']],
        ['type' => 's', 'value' => $_POST['espanol']],
        ['type' => 's', 'value' => $_POST['saberes']],
        ['type' => 's', 'value' => $_POST['enfoque']],
        ['type' => 's', 'value' => $_POST['pueblo']],
        ['type' => 'i', 'value' => $id]
      ];
      //  $rta = show_sql($sql, $params);
      $rta = mysql_prepd($sql, $params);
    }
return $rta;
} */

function gra_emb_Id() {
  $id = divide($_POST['id'] ?? '');
  $usu = $_SESSION['us_sds'];
  
  if (($rtaFec = validFecha('etnias', $_POST['fechavisi'] ?? '')) !== true) {
    return $rtaFec;
  }
  if (count($id) == 2) {
      $sql = "INSERT INTO etn_identi VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR), NULL, NULL, 'A')";
      $params =[
              ['type' => 's', 'value' => $id[0]],
              // ['type' => 's', 'value' => $id[1]],
              ['type' => 's', 'value' => $_POST['fechavisi'] ?? ''],
              ['type' => 's', 'value' => $_POST['lider'] ?? ''],
              ['type' => 's', 'value' => $_POST['educacion'] ?? ''],
              ['type' => 's', 'value' => $_POST['espanol'] ?? ''],
              ['type' => 'i', 'value' => $_POST['saberes'] ?? 0],
              ['type' => 'i', 'value' => $_POST['enfoque'] ?? 0],
              ['type' => 'i', 'value' => $_POST['pueblo'] ?? 0],
              ['type' => 's', 'value' => $usu]
      ];
  } else {
      $sql = "UPDATE etn_identi SET lider=?,saberes=?,pueblo=?, usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR) WHERE idriesgo=?";
      $params =[
            ['type' => 's', 'value' => $_POST['lider'] ?? ''],
            ['type' => 'i', 'value' => $_POST['saberes'] ?? 0],
            ['type' => 'i', 'value' => $_POST['pueblo'] ?? 0],
            ['type' => 's', 'value' => $usu],
            ['type' => 'i', 'value' => $id[0]]
      ];
  }
  //return show_sql($sql, $params);
   return mysql_prepd($sql, $params);
}
function get_emb_Id(){
	if($_POST['id']==''){
		return "";
	}else{
		$id=divide($_POST['id']);
    $sql="SELECT idriesgo,idpeople,fechavisi,lider,educacion,espanol,saberes,enfoque,pueblo
          FROM `etn_identi` 
          WHERE idpeople='{$id[0]}'";
          // var_dump($sql);
    $info=datos_mysql($sql);
    if (!$info['responseResult']) {
      return '';
    }else{
      return $info['responseResult'][0];
    }
  }
} 
function opc_saberes($id=''){
    return opc_sql('SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=256 and estado="A" ORDER BY 1',$id);
}
function opc_pueblo($id=''){
  return opc_sql('SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=266 and estado="A" ORDER BY 1',$id);
}  
function opc_enfoque($id=''){
       return opc_sql('SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=257 and estado="A" ORDER BY 1',$id);
 }
	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
        // var_dump($a);
		if ($a=='emb_Id' && $b=='acciones'){
			$rta="<nav class='menu right'>";
      // $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'seguim',event,this,['saberes'],'../etnias/embid.php');enbValue('idseg','seguim','".$c['ACCIONES']."');enaFie(document.getElementById('observaciones'),false);\"></li>";
				
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	   }
	   
