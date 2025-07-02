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


function focus_seguim(){
	return 'seguim';
   }
   
   
   function men_seguim(){
	$rta=cap_menus('seguim','pro');
	return $rta;
   }
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='seguim'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	 
	   }
  return $rta;
}
function lis_seguim(){
  // print_r($_POST);
   $id = (isset($_POST['id'])) ? divide($_POST['id']) : divide($_POST['idp']) ;
$info=datos_mysql("SELECT COUNT(*) total FROM emb_segui WHERE idpeople=".$id[0]."");
$total=$info['responseResult'][0]['total'];
$regxPag=5;
$pag=(isset($_POST['pag-seguimi']))? ($_POST['pag-seguimi']-1)* $regxPag:0;

    $sql="SELECT es.idseg AS ACCIONES, es.idseg 'Cod_Registro', es.fecha_seg 'Fecha', es.segui 'N Seguimiento', FN_CATALOGODESC(73,es.estado_seg)'Estado', FN_CATALOGODESC(262,es.interven) 'Intervención',u.nombre 
FROM emb_segui es
left join usuarios u ON es.usu_creo = u.id_usuario
            WHERE idpeople='".$id[0];
        $sql.="' ORDER BY fecha_create";
        $sql.=' LIMIT '.$pag.','.$regxPag;
        //  echo $sql;
        $datos=datos_mysql($sql);
        return create_table($total,$datos["responseResult"],"seguimi",$regxPag,'embsegui.php');
}

function cmp_seguim(){
   $rta="<div class='encabezado seguiEmbera'>TABLA SEGUIMIENTOS</div><div class='contenido' id='seguimi-lis'>".lis_seguim()."</div>";
// $rta='';
  $w="seguim";
	$o='modini';
  $bl='bL';
  $no='nO';
  $d='';
  //$d=get_seguim();
  $days=fechas_app('etnias');
  $p=get_persona();
  // var_dump($_POST);
	  $c[]=new cmp($o,'e',null,'MODULO INICIAL',$w);
    $c[]=new cmp('idseg','h',11,$_POST['id'],$w.' '.$o,'idseg','idseg',null,'####',false,false);
    $c[]=new cmp('sexo','h',1,$p['sexo'],$w.' GeST '.$o,'sexo','sexo',null,'',false,false,'','col-1');
    $c[]=new cmp('fecha_seg','d',10,$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
    $c[]=new cmp('segui','s',3,$d,$w.' '.$o,'Seguimiento N°','segui',null,null,true,true,'','col-2',"staEfe('segui','sta');EnabEfec(this,['datiden','infoserv','detsegh','detsegp'],['Ob'],['nO'],['bL']);");
    $c[]=new cmp('estado_seg','s',3,$d,$w.' sTa '.$o,'Estado','estado_seg',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);enabEmbInt();disFall();");
    $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,'','','col-2');
    $c[]=new cmp('interven','s',3,$d,$w.' iNt '.$o,'Intervención','interven',null,null,true,true,'','col-2',"enabSegEmb();enabEmbGes();enabEspe2();");

    $o='datiden';
    $c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN - HOSPITALARIO',$w);
    $c[]=new cmp('gestante','s',3,$d,$w.' GeS '.$bl.' '.$o,'Gestante','rta',null,null,false,true,'','col-2','enabEmbEdGes();');
    $c[]=new cmp('edad_gest','s',3,$d,$w.' EGe '.$bl,'Edad Gestacional (Semanas)','edad_gesta',null,null,false,false,'','col-2');
    $c[]=new cmp('paren','s',3,$d,$w.' '.$bl.' '.$o,'Parentesco','paren',null,null,false,true,'','col-25',"enabEmbPare('paren');");
    $c[]=new cmp('Nom_fami','t',50,$d,$w.' prT '.$bl.' '.$o,'Nombre Completo del familiar','Nom_fami',null,null,false,true,'','col-4');
    $c[]=new cmp('tipo_doc','s',3,$d,$w.' prT '.$bl.' '.$o,'Tipo De Documento','tipo_doc',null,null,false,true,'','col-2');
    $c[]=new cmp('num_doc','n',18,$d,$w.' prT '.$bl.' '.$o,'Número De Documento','num_doc',null,null,false,true,'','col-25');
    $c[]=new cmp('tel_conta','n',21,$d,$w.' prT '.$bl.' '.$o,'Teléfono De Contacto','tel_conta',null,null,false,true,'','col-25');
    $c[]=new cmp('ubi','s',3,$d,$w.' '.$bl.' prT '.$o,'Ubicacion','ubi',null,null,false,true,'','col-25');

    $o='infoserv';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN DE SERVICIO - HOSPITALARIO',$w);
    $c[]=new cmp('ser_req','t',50,$d,$w.' '.$bl.' '.$o,'Servicio Requerido','ser_req',null,null,false,true,'','col-2');
    $c[]=new cmp('fecha_ing','d',10,$d,$w.' '.$bl.' '.$o,'Fecha De Ingreso','fecha_ing',null,null,false,true,'','col-2');
    $c[]=new cmp('uss_ing','t',50,$d,$w.' '.$bl.' '.$o,'Unidad De Servicio De Salud A La Que Ingresa','uss_ing',null,null,false,true,'','col-3');
    $c[]=new cmp('motivo_cons','t',50,$d,$w.' '.$bl.' '.$o,'Motivo De Consulta/Ingresó','motivo_cons',null,null,false,true,'','col-3');
    $c[]=new cmp('uss_tras','t',50,$d,$w.' '.$bl.' '.$o,'Unidad De Servicio De Salud De Traslado','uss_tras',null,null,false,true,'','col-25');
    $c[]=new cmp('ing_unidad','t',50,$d,$w.' '.$bl.' '.$o,'Tipo De Ingreso A La Unidad','ing_unidad',null,null,false,true,'','col-25');
    $c[]=new cmp('ante_salud','t',50,$d,$w.' '.$bl.' '.$o,'Antecedentes En Salud','ante_salud',null,null,false,true,'','col-25');
    $c[]=new cmp('imp_diag','t',50,$d,$w.' '.$bl.' '.$o,'Impresión Diagnostica','imp_diag',null,null,false,true,'','col-25');

    $o='detsegh';
    $c[]=new cmp($o,'e',null,'DETALLE DEL SEGUIMIENTO INTRA-HOSPITALARIO',$w);
    $c[]=new cmp('uss_encu','t',50,$d,$w.' '.$bl.' '.$o,'Unidad De  Salud A La Que Se Encuentra','uss_encu',null,null,false,true,'','col-25');
    $c[]=new cmp('servicio_encu','t',50,$d,$w.' '.$bl.' '.$o,'Servicio De En El Que Se Encuentra','servicio_encu',null,null,false,true,'','col-25');
    $c[]=new cmp('imp_diag2','t',50,$d,$w.' '.$bl.' '.$o,'Impresión Diagnostica','imp_diag2',null,null,false,true,'','col-3');
    $c[]=new cmp('nece_apoy','s',3,$d,$w.' '.$bl.' '.$o,'Necesidad De Apoyo Intersectorial','rta',null,null,false,true,'','col-2');

    $o='detsegp';
    $c[]=new cmp($o,'e',null,'DETALLE DEL SEGUIMIENTO POS EGRESO',$w);
    $c[]=new cmp('fecha_egreso','d',10,$d,$w.' '.$bl.' '.$o,'Fecha De Egreso','fecha_egr',null,null,false,true,'','col-2');
    $c[]=new cmp('espe1','t',50,$d,$w.' '.$bl.' '.$o,'Especialidad 1','espe1',null,null,false,true,'','col-35');
    $c[]=new cmp('espe2','t',50,$d,$w.' '.$bl.' '.$no,'Especialidad 2','espe2',null,null,false,false,'','col-35');
    $c[]=new cmp('adh_tto','s',3,$d,$w.' '.$bl.' '.$o,'Adherente Al Tratamiento','rta',null,null,false,true,'','col-3');

    $o='aspfin';
    $c[]=new cmp($o,'e',null,'ASPECTOS FINALES',$w);
    $c[]=new cmp('observaciones','a',7000,$d,$w.' '.$o,'Observaciones','observaciones',null,null,true,true,'','col-10');
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_persona(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		$sql="SELECT sexo	from person P WHERE P.idpeople='".$id[0]."'";
		// echo $sql;
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}else{
			return $info['responseResult'][0];
		}
		}
	}

function gra_seguim() {
  $id = divide($_POST['idseg']);
  if (($rtaFec = validFecha('etnias', $_POST['fecha_seg'] ?? '')) !== true) {return $rtaFec;}
  if(COUNT($id)==2){
  $numdoc=($_POST['num_doc']=== '')? 0: $_POST['num_doc'];
      $equ = datos_mysql("select equipo from usuarios where id_usuario=".$_SESSION['us_sds']);
      $bina = isset($_POST['fequi']) ? (is_array($_POST['fequi']) ? implode("-", $_POST['fequi']) : str_replace("'", "", $_POST['fequi'])) : '';
      $equi = $equ['responseResult'][0]['equipo'];

      $sql = "INSERT INTO emb_segui VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,'A')";
      $params = [
        ['type' => 'i', 'value' => $id[0]],
        ['type' => 's', 'value' => $_POST['fecha_seg']],
        ['type' => 's', 'value' => $_POST['segui']],
        ['type' => 's', 'value' => $_POST['estado_seg']],
        ['type' => 's', 'value' => $_POST['motivo_estado']],
        ['type' => 's', 'value' => $_POST['interven']],
        ['type' => 's', 'value' => $_POST['gestante']],
        ['type' => 's', 'value' => $_POST['edad_gest']],
        ['type' => 's', 'value' => $_POST['Nom_fami']],
        ['type' => 's', 'value' => $_POST['tipo_doc']],
        ['type' => 'i', 'value' => $numdoc],
        ['type' => 's', 'value' => $_POST['paren']],
        ['type' => 's', 'value' => $_POST['tel_conta']],
        ['type' => 's', 'value' => $_POST['ubi']],
        ['type' => 's', 'value' => $_POST['ser_req']],
        ['type' => empty($_POST['fecha_ing']) ? 'z' : 's','value' => empty($_POST['fecha_ing']) ? null : $_POST['fecha_ing']],
        ['type' => 's', 'value' => $_POST['uss_ing']],
        ['type' => 's', 'value' => $_POST['motivo_cons']],
        ['type' => 's', 'value' => $_POST['uss_tras']],
        ['type' => 's', 'value' => $_POST['ing_unidad']],
        ['type' => 's', 'value' => $_POST['ante_salud']],
        ['type' => 's', 'value' => $_POST['imp_diag']],
        ['type' => 's', 'value' => $_POST['uss_encu']],
        ['type' => 's', 'value' => $_POST['servicio_encu']],
        ['type' => 's', 'value' => $_POST['imp_diag2']],
        ['type' => 's', 'value' => $_POST['nece_apoy']],
        ['type' => empty($_POST['fecha_egreso']) ? 'z' : 's','value' => empty($_POST['fecha_egreso']) ? null : $_POST['fecha_egreso']],
        ['type' => 's', 'value' => $_POST['espe1']],
        ['type' => 's', 'value' => $_POST['espe2']],
        ['type' => 's', 'value' => $_POST['adh_tto']],
        ['type' => 's', 'value' => $_POST['observaciones']],
        ['type' => 's', 'value' => $bina],
        ['type' => 's', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => NULL],
        ['type' => 's', 'value' => NULL],
      ];
//$rta=show_sql($sql, $params);
$rta = mysql_prepd($sql, $params);
}else{
  $sql="UPDATE emb_segui SET observaciones=?,fecha_update=DATE_SUB(NOW(),INTERVAL 5 HOUR),usu_update=? WHERE idseg=?"; //  compromiso=?, equipo=?, 
   $params = [
       ['type' => 's', 'value' => $_POST['observaciones']],
       ['type' => 'i', 'value' => $_SESSION['us_sds']],
       ['type' => 'i', 'value' => $id[0]]
     ];
     $rta = mysql_prepd($sql, $params);
   }
return $rta;
}

function get_seguim(){
  if($_REQUEST['id']==''){
    return "";
  }else{
    // var_dump($_POST,$_GET);
    $id=divide($_REQUEST['id']);
    // print_r($id);
    $sql="SELECT idseg, P.sexo,fecha_seg, segui, estado_seg, motivo, interven, gestante, edad_gest,paren,Nom_fami, S.tipo_doc, num_doc,  tel_conta, ubi, ser_req, fecha_ing, uss_ing, motivo_cons, uss_tras, ing_unidad, ante_salud, imp_diag, uss_encu, servicio_encu, imp_diag2, nece_apoy, fecha_egreso, espe1, espe2, adh_tto, observaciones
          FROM `emb_segui` S
          left join person P ON S.idpeople=P.idpeople
          WHERE idseg='{$id[0]}'";
          // echo $sql;
  $info=datos_mysql($sql);
  if (!empty($info['responseResult'])) return json_encode($info['responseResult'][0]);
} 
}

function opc_segui($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=76 and estado='A' ORDER BY LENGTH(idcatadeta), idcatadeta",$id);
}

function opc_estado_seg($id=''){
   return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=73 and estado='A' ORDER BY 1",$id);
}

function opc_motivo_estado($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=265 and estado='A' ORDER BY 1",$id);
}

function opc_interven($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=262 and estado='A' ORDER BY 1",$id);
}

function opc_rta($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}

function opc_edad_gesta($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=268 and estado='A' ORDER BY LPAD(idcatadeta, 2, '0') ASC",$id);
}

function opc_tipo_doc($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function opc_paren($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=263 and estado='A' ORDER BY 1",$id);
}
  
function opc_ubi($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=264 and estado='A' ORDER BY 1",$id);
}
  
function opc_equi($id=''){
  return opc_sql("SELECT id_usuario, nombre FROM usuarios WHERE subred=(select subred from usuarios where id_usuario=".$_SESSION['us_sds'].") AND estado='A' AND equipo=(select equipo from usuarios where id_usuario=".$_SESSION['us_sds'].") ORDER BY LPAD(nombre, 2, '0')",$id);
}

function formato_dato($a,$b,$c,$d){
  $b=strtolower($b);
	$rta=$c[$d];
  // var_dump($a);
  if ($a=='seguimi' && $b=='acciones'){
    $rta="<nav class='menu right'>";
    $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getDataFetch,500,'seguim',event,this,'../etnias/embsegui.php',[]);enbValue('idseg','seguim','".$c['ACCIONES']."');enaFie(document.getElementById('observaciones'),false);\"></li>";
    }
		return $rta;
}

function bgcolor($a,$c,$f='c'){
	$rta="";
	return $rta;
}
	   
