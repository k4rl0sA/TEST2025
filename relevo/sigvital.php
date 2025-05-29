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



function focus_vitals_signs(){
  return 'vitals_signs';
 }
 
 
 function men_vitals_signs(){
  $rta=cap_menus('vitals_signs','pro');
  return $rta;
 }
 
 
 function cap_menus($a,$b='cap',$con='con') {
   $rta = ""; 
   $acc=rol($a);
   $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
   $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
   return $rta;
 }


 FUNCTION lists_vitals_signs(){
	// var_dump($_POST['id']);
	$id=divide($_POST['id']);
	$sql="SELECT idsignos ACCIONES,
  idpersona 'N° Documento',tipo_doc 'Tipo',momento,tas sistolica,tad diastolica,frecard frecuencia,satoxi saturación 
  FROM rel_signvitales A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE tipo_doc='".$id[0]."' AND idpersona='".$id[1];
	$sql.="' ORDER BY A.fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"vitals_signs-lis",5);
   }


function cmp_vitals_signs(){
	$rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div>
	    <div class='contenido' id='vitals_signs-lis'>".lists_vitals_signs()."</div></div>";
    $hoy=date('Y-m-d');
    $t=['tipo_doc'=>'','idpersona'=>'','nombre1'=>'','fecha_nacimiento'=>'','sexo'=>''];
    $w='vitals_signs';
    $i=get_personas();
    $d="";
    if ($i=="") {$i=$t;}
    // $u=($j['tipo_doc']=='')?true:false;
    $o='infper';
    
    
    // $c[]=new cmp('idrel','h','20',$j['tipo_doc'] . "_" . $j['idpersona'] ,$w.' '.$o,'','',null,null,false,$u,'','col-1');
    $c[]=new cmp($o,'e',null,'INFORMACIÓN PERSONAL',$w);	
	$c[]=new cmp('id_svital','h','50',$_POST['id'],$w.' '.$o,'Id de vitals_signs','id_vitals_signsacio',null,null,false,false,'','col-2');
  
  $c[]=new cmp('tipo_doc','t','3',$i['tipo_doc'],$w.' '.$o,'Tipo documento','tipo_doc',null,null,true,false,'','col-1');
	$c[]=new cmp('documento','t','20',$i['idpersona'],$w.' '.$o,'N° de identificacion','documento',null,null,true,false,'','col-15');
	$c[]=new cmp('nombre1','t','50',$i['nombre1'],$w.' '.$o,'Nombre Completo','nombre1',null,'',false,false,'','col-4');
	$c[]=new cmp('fecha_nacimiento','d','20',$i['fecha_nacimiento'],$w.' '.$o,'Fecha nacimiento','fecha_nacimiento',null,'',true,false,'','col-2');
	$c[]=new cmp('sexo','s','20',$i['sexo'],$w.' '.$o,'Sexo','sexo',null,'',true,false,'','col-15');

  $o='sigvit';
  $c[]=new cmp($o,'e',null,'SIGNOS VITALES',$w);
  $c[]=new cmp('fecha_toma','d',3,$d,$w.' aux '.$o,'Fecha','fecha_toma',null,null,true,true,'','col-1','validDate(this,-22,0)');
	$c[]=new cmp('momento','s',3,$d,$w.' aux '.$o,'Momento','momento',null,null,true,true,'','col-15');
	$c[]=new cmp('tas','n',3, $d,$w.' aux '.$o,'Tensión Sistolica Mín=40 - Máx=250','tas','rgxsisto','###',true,true,'','col-15');
	$c[]=new cmp('tad','n',3, $d,$w.' aux '.$o,'Tensión Diastolica Mín=40 - Máx=150','tad','rgxdiast','###',true,true,'','col-2');
	$c[]=new cmp('frecard','n',3, $d,$w.' aux '.$o,'Frecuencia Cardiaca Mín=60 - Máx=120','frecard',null,'##',true,true,'','col-2');
	$c[]=new cmp('satoxi','n',3, $d,$w.' aux '.$o,'saturación de Oxigeno Mín=60 - Máx=100','satoxi',null,'##',true,true,'','col-2');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}


function get_personas(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT tipo_doc,idpersona,concat_ws(' ',nombre1,nombre2,apellido1,apellido2)  nombre1 ,fecha_nacimiento,sexo,genero,etnia,nacionalidad,regimen,eapb,
		rel_validacion1,rel_validacion2,rel_validacion3,rel_validacion13,rel_validacion14,rel_validacion15,rel_validacion16,rel_validacion17,rel_validacion18
		FROM personas P 
		LEFT JOIN rel_relevo R ON idpersona=rel_documento AND tipo_doc=rel_tipo_doc
		WHERE tipo_doc='{$id[0]}' AND idpersona='{$id[1]}'";

		$info=datos_mysql($sql);
		if ($info['responseResult']){
			return $info['responseResult'][0];
		} else {
			return "";
		}
	} 
}


function opc_momento($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=116 and estado='A' ORDER BY 1",$id);
  }
  function opc_sexo($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
  }

function gra_vitals_signs(){
// print_r($_POST);
$id=divide($_POST['id_svital']);
  if(count($id)==1){
    $sql="UPDATE rel_signvitales SET
tas=trim(upper('{$_POST['tas']}')),
tad=trim(upper('{$_POST['tad']}')),
frecard=trim(upper('{$_POST['frecard']}')),
satoxi =trim(upper('{$_POST['satoxi']}')),
    `usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
    WHERE idsignos =TRIM(UPPER('{$id[0]}'))";
    // echo $sql;
  }else if(count($id)==2){
    $sql="INSERT INTO rel_signvitales VALUES (NULL,trim(upper('{$id[1]}')),trim(upper('{$id[0]}')),
    trim(upper('{$_POST['fecha_toma']}')),
    trim(upper('{$_POST['momento']}')),trim(upper('{$_POST['tas']}')),trim(upper('{$_POST['tad']}')),
    trim(upper('{$_POST['frecard']}')),trim(upper('{$_POST['satoxi']}')),
    TRIM(UPPER('{$_SESSION['us_sds']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
    // echo $sql;
  }
    $rta=dato_mysql($sql);
    return $rta;
  } 


  function get_vitals_signs(){
    if($_REQUEST['id']==''){
      return "";
    }else{
      $id=divide($_REQUEST['id']);
      $sql="SELECT idsignos,
        S.tipo_doc,S.idpersona,CONCAT_WS(' ',nombre1,nombre2,apellido1,apellido2) nombre1,fecha_nacimiento,sexo, fecha_toma,momento,tas,tad,frecard,satoxi  
from rel_signvitales S
left join personas P ON S.idpersona=P.idpersona AND S.tipo_doc=P.tipo_doc 
      WHERE idsignos ='{$id[0]}'";
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
	if ($a=='vitals_signs-lis' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class='menu right'>";	
		$rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'vitals_signs',event,this,['tipo_doc','documento','nombre1','fecha_nacimiento','sexo','momento'],'sigvital.php');\"></li>";
	}
	
 return $rta;
}


function bgcolor($a,$c,$f='c'){
  $rta="";
  return $rta;
   }