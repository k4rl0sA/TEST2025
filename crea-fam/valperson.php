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
    if (is_array($rta)) echo json_encode($rta);
	else echo $rta;
  }
}

function cmp_validPerson(){
    $rta="";
	// $p=get_edad();
    $w='validperson';
	$o='infusu';
	$key='pEr';
	$t=['idpersona'=>'','tipo_doc'=>'','fecha_nacimiento'=>'','sexo'=>''];
	// print_r($_POST);
	// $d = get_person();
	// if ($d==""){$d=$t;}
	// var_dump($d);
	$c[]=new cmp($o,'e',null,'INFORMACIÓN GENERAL',$w);
	$c[]=new cmp('idp','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
	$c[]=new cmp('idpersona','nu','9999999999999999',$t['idpersona'],$w.' '.$key.' '.$o,'Identificación <a href="https://www.adres.gov.co/consulte-su-eps" target="_blank">     Abrir ADRES</a>','idpersona',null,null,true,true,'','col-4');
	$c[]=new cmp('tipo_doc','s','3',$t['tipo_doc'],$w.' '.$key.' '.$o,'Tipo documento','tipo_doc',null,null,true,true,'','col-4');
	$c[]=new cmp('fecha_nacimiento','d','',$t['fecha_nacimiento'],$w.' '.$o,'Fecha de nacimiento (dd/mm/aaaa)','fecha_nacimiento',null,null,true,true,'','col-2',"validDate(this,-43800,0);");
	$c[]=new cmp('sexo','s','3',$t['sexo'],$w.' '.$o,'Sexo','sexo',null,null,true,true,'','col-2');
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function focus_validPerson(){
	return 'validPerson';
}
   
   
function men_validPerson(){
	$rta=cap_menus('validPerson','pro');
	return $rta;
}

  function cap_menus($a,$b='cap',$con='con') {
	 $rta = "";
	 $acc=rol($a);
	 if ($a=='validPerson' && isset($acc['crear']) && $acc['crear']=='SI') {  
	 	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	 return $rta;
	 }
   }


/* function get_person(){
	//  print_r($_REQUEST);
	 $id=divide($_REQUEST['id']);
	if($_REQUEST['id']=='' || count($id)!=2){
		return "";
	}else{
		$sql="SELECT concat_ws('_',idpeople,vivipersona),idpersona,tipo_doc
		FROM `person`
		WHERE idpeople ='{$id[0]}'" ;
		// echo $sql;
		// print_r($id);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
	} 
} */

function gra_validPerson() {
    $id = divide($_POST['idp']);
    $edit = (count($id) == 2);


    // Obtener datos actuales de la tabla person
    $sql = "SELECT idpersona,tipo_doc,sexo, fecha_nacimiento FROM person WHERE idpeople = $id[0]";
    $info = datos_mysql($sql);
    $coincide = false;

    if ($info && isset($info['responseResult'][0])) {
        $row = $info['responseResult'][0];
        $coincide = (
			$row['idpersona'] == $_POST['idpersona'] &&
			$row['tipo_doc'] == $_POST['tipo_doc'] &&
            $row['sexo'] == $_POST['sexo'] &&
            $row['fecha_nacimiento'] == $_POST['fecha_nacimiento']
        );
    }
    $estado= $coincide ? 4 : 2;
	// Insertar en soporte
    $sql_soporte = "INSERT INTO soporte (idpeople, documento, tipo_doc, sexo, fecha_nacio, usu_creo, estado) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params_soporte = [
        ['type' => 'i', 'value' => $id[0]], // idpeople
        ['type' => 'i', 'value' => $_POST['idpersona'] ], // documento
        ['type' => 's', 'value' => $_POST['tipo_doc'] ], // tipo_doc
        ['type' => 's', 'value' => $_POST['sexo'] ], // sexo
        ['type' => 's', 'value' => $_POST['fecha_nacimiento'] ], // fecha_nacio
        ['type' => 's', 'value' => $_SESSION['us_sds']], // usu_creo
        ['type' => 'i', 'value' => $estado] // estado
    ];
	// show_sql($sql, $params);exit;
    if (!$coincide && empty($_REQUEST['confirmado'])) {
        return [
            'confirm' => true,
            'msg' => 'Los datos no coinciden con la información registrada. ¿Desea guardar de todas formas?',
			'estado' => $estado
        ];
    }

	//Mostrar como seria la sentencia cruda antes de guardar ejemplo :INSERT INTO validaUsuario VALUES(NULL, 4, 'M', '1984-10-10', '80811594', '2025-07-15 14:01:54', NULL, NULL, 'A');
	
    	// Insertar en validaUsuario
      $rta = mysql_prepd($sql, $params);
	  return [
        'success' => true,
        'msg' => 'Registro guardado correctamente',
        'estado' => $estado
    ];
    return $rta;

}

function opc_sexo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}
function opc_tipo_doc($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 2",$id);
}

function formato_dato($a,$b,$c,$d){
	$b=strtolower($b);
	$rta=$c[$d];
	// print_r($c);
	if ($a=='admision-lis' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
			$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'admision',event,this,['fecha','tipo_activi'],'../atencion/admision.php');\"></li>";  //   act_lista(f,this);
		}
	return $rta;
}

function bgcolor($a,$c,$f='c'){
	$rta="";
	return $rta;
}


