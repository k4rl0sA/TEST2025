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

function cmp_routing(){
    $rta="";
    $w='routing';
    $d = array();
    $o='datos';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN BÁSICA',$w);
    $c[]=new cmp('id_ruteo','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
    $c[]=new cmp('fuente','s','3',$d['fuente']??'',$w.' '.$o,'Fuente','fuente',null,'',true,true,'','col-25');
    $c[]=new cmp('priorizacion','s','3',$d['priorizacion']??'',$w.' '.$o,'Priorización','priorizacion',null,'',true,true,'','col-15',"selectDepend('priorizacion','tipo_prior','cargaRuteo.php');");
    $c[]=new cmp('tipo_prior','s','3',$d['tipo_prior']??'',$w.' '.$o,'Tipo Prioridad','tipo_prior',null,'',true,true,'','col-6');
    
    $o='beneficiario';
    $c[]=new cmp($o,'e',null,'DATOS DEL USUARIO',$w);
    $c[]=new cmp('tipo_doc','s','3',$d['tipo_doc']??'',$w.' '.$o,'Tipo Documento','tipo_doc',null,'',true,true,'','col-2');
    $c[]=new cmp('documento','t','18',$d['documento']??'',$w.' '.$o,'Documento','documento',null,'',true,true,'','col-2');
    $c[]=new cmp('nombres','t','50',$d['nombres']??'',$w.' '.$o,'Nombres Completos','nombres',null,'',true,true,'','col-4');
    $c[]=new cmp('sexo','s','1',$d['sexo']??'',$w.' '.$o,'Sexo','sexo',null,'',true,true,'','col-1');
    
    $o='contacto';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN DE CONTACTO',$w);
    $c[]=new cmp('direccion','t','50',$d['direccion']??'',$w.' '.$o,'Dirección','direccion',null,'',true,true,'','col-3');
    $c[]=new cmp('telefono1','t','10',$d['telefono1']??'',$w.' '.$o,'Teléfono 1','telefono1',null,'',true,true,'','col-2');
    $c[]=new cmp('telefono2','t','10',$d['telefono2']??'',$w.' '.$o,'Teléfono 2','telefono2',null,'',true,true,'','col-2');
    $c[]=new cmp('telefono3','t','10',$d['telefono3']??'',$w.' '.$o,'Teléfono 3','telefono3',null,'',true,true,'','col-2');
    
    $o='ubicacion';
    $c[]=new cmp($o,'e',null,'UBICACIÓN GEOGRÁFICA',$w);
    $c[]=new cmp('idgeo','n','11',$d['idgeo']??'',$w.' idG '.$o,'ID Geográfico','idgeo',null,'',true,true,'','col-2',"getDatForm('idG','idgeog','iDG',['subred','direccion'],'../ruteo1/cargaRuteo.php');");
    $c[]=new cmp('subred','s','11','',$w.' iDG '.$o,'Subred','subred',null,'',true,false,'','col-1');
    $c[]=new cmp('direccion','t',100,'',$w.' iDG '.$o,'Dirección','direccion',null,'',true,false,'','col-3');
    
    $o='asignacion';
    $c[]=new cmp($o,'e',null,'ASIGNACIÓN',$w);
    $c[]=new cmp('perfil1','s','3',$d['perfil1']??'',$w.' '.$o,'Perfil','perfil1',null,'',true,true,'','col-3',"selectDepend('perfil1','actividad1','cargaRuteo.php');");
    $c[]=new cmp('actividad1','s','11',$d['actividad1']??'',$w.' '.$o,'Usuario (Territorio Acorde al Predio)','actividad1',null,'',true,true,'','col-3');

/*  $c[]=new cmp('estado_ruteo','s','3',$d['estado_ruteo']??'',$w.' '.$o,'Estado Ruteo','estado_ruteo',null,'',true,true,'','col-3');
    $c[]=new cmp('estado_rut','s','10',$d['estado_rut']??'',$w.' '.$o,'Estado Ruta','estado_rut',null,'',true,true,'','col-3');
    $c[]=new cmp('famili','n','10',$d['famili']??'',$w.' '.$o,'Familia','famili',null,'',true,true,'','col-3');
    $c[]=new cmp('usuario','s','10',$d['usuario']??'',$w.' '.$o,'Usuario Asignado','usuario',null,'',true,true,'','col-3');
    $c[]=new cmp('perfil1','s','3',$d['perfil1']??'',$w.' '.$o,'Perfil','perfil1',null,'',true,true,'','col-3');
    $c[]=new cmp('actividad1','n','11',$d['actividad1']??'',$w.' '.$o,'Actividad','actividad1',null,'',true,true,'','col-3'); */
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    return $rta;
}
function get_idgeog() {
    $id = $_POST['id'];
    $sql = "SELECT idgeo,subred,direccion FROM hog_geo WHERE idgeo = $id";
    $result = datos_mysql($sql);
    if (!$result['responseResult']) {
        return json_encode(new stdClass());
    }
    return json_encode($result['responseResult'][0]);
}

function get_routing(){
    if($_POST['id']==0){
        return array();
    }else{
        $id = $_POST['id'];
        $sql = "SELECT * FROM eac_ruteo WHERE id_ruteo = '$id'";
        $result = datos_mysql($sql);
        return $result['responseResult'][0];
    }
}

function focus_routing(){
    return 'routing';
}

function men_routing(){
    $rta = "";
    $acc = rol('routing');
    if(isset($acc['crear']) && $acc['crear']=='SI') {
        $rta .= "<li class='icono ruteo grabar' title='Grabar' OnClick=\"grabar('routing',this);\"></li>";
    }
 /*    if(isset($acc['editar']) && $acc['editar']=='SI') {
        $rta .= "<li class='icono ruteo editar' title='Editar' Onclick=\"mostrar('ruteo','pro',event,'','lib.php',7,'ruteo');\"></li>";
    } */
    $rta .= "<li class='icono ruteo actualizar' title='Actualizar' Onclick=\"act_lista('routing',this);\"></li>";
    return $rta;
}

function gra_routing(){
    $data = array(
        'id_ruteo' => NULL,
        'fuente' => $_POST['fuente'],
        'fecha_asig' => date('Y-m-d'),
        'priorizacion' => $_POST['priorizacion'],
        'tipo_prior' => $_POST['tipo_prior'],
        'tipo_doc' => $_POST['tipo_doc'],
        'documento' => $_POST['documento'],
        'nombres' => $_POST['nombres'],
        'sexo' => $_POST['sexo'],
        'direccion' => $_POST['direccion'],
        'telefono1' => $_POST['telefono1'],
        'telefono2' => $_POST['telefono2'],
        'telefono3' => $_POST['telefono3'],
        'idgeo' => $_POST['idgeo'],
        'fecha' => NULL,
        'estado_ruteo' => NULL,
        'estado_rut' => NULL,
        'famili' => NULL,
        'usuario' => NULL,
        'perfil1' => $_POST['perfil1'],
        'actividad1' => $_POST['actividad1'],
        'usu_update' => NULL,
        'fecha_update' => NULL,
        'estado' => 'A'
    );
        $data['usu_create'] = $_SESSION['us_sds'];
        $data['fecha_create'] = date('Y-m-d H:i:s');
        $columns = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $sql = "INSERT INTO eac_ruteo ($columns) VALUES ($values)";
        $rta = dato_mysql($sql);

          $sql_id = "SELECT LAST_INSERT_ID() as id_ruteo";
    $result = datos_mysql($sql_id);
    $last_id = $result['responseResult'][0]['id_ruteo'] ?? null;
    return $rta . '<br>El id creado fue el : <span style="color:red;">' . $last_id . '</span>';
}
function opc_perfil1($id=''){
    return opc_sql("SELECT idcatadeta, descripcion FROM catadeta WHERE idcatalogo=218 AND estado='A' ORDER BY LENGTH(idcatadeta), idcatadeta", $id);
}
function opc_perfil1actividad1($id=''){
    // var_dump($_REQUEST);
    if($_REQUEST['id']!=''){
        $perfil = divide($_REQUEST['id']);
        $sql="SELECT id_usuario, CONCAT_WS('-', id_usuario, nombre) AS descripcion FROM usuarios WHERE subred IN (SELECT subred FROM usuarios WHERE id_usuario = {$_SESSION['us_sds']}) AND perfil IN (select descripcion from catadeta where idcatalogo =218 and idcatadeta={$perfil[0]})  AND estado = 'A'";
        $info=datos_mysql($sql);
        return json_encode($info['responseResult']);
    }
    
    return json_encode([]);
}
function opc_priorizaciontipo_prior($id=''){
    if($_REQUEST['id']!=''){
        $id=divide($_REQUEST['id']);
        $sql="SELECT idcatadeta ,descripcion FROM `catadeta` WHERE idcatalogo=235 and estado='A' and valor=".$id[0]." ORDER BY LENGTH(idcatadeta), idcatadeta;";
        // var_dump($sql);
        $info=datos_mysql($sql);
        return json_encode($info['responseResult']);
    }
}
function opc_fuente($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
}
function opc_priorizacion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=191 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_prior($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=235 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_doc($id=''){
    return opc_sql("SELECT idcatadeta, descripcion FROM catadeta WHERE idcatalogo=1 AND estado='A' ORDER BY descripcion", $id);
}

function opc_sexo($id=''){
    return opc_sql("SELECT idcatadeta, descripcion FROM catadeta WHERE idcatalogo=21 AND estado='A' ORDER BY descripcion", $id);
}
function opc_actividad1($id=''){
    /* return opc_sql("SELECT idcatadeta, descripcion FROM catadeta WHERE idcatalogo=218 AND estado='A' ORDER BY descripcion", $id); */
}
function opc_usuarios($id=''){
    return opc_sql("SELECT id_usuario ,CONCAT_WS('-', id_usuario, nombre) FROM usuarios WHERE subred IN (SELECT subred FROM usuarios WHERE id_usuario = {$_SESSION['us_sds']}) AND estado='A' ORDER BY nombre ", $id);
}
function opc_subred($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=72 and estado='A' and idcatadeta in(1,2,4,3) ORDER BY 1",$id);
}


function opc_actividades($id=''){
    return opc_sql("SELECT id_actividad as idcatadeta, nombre as descripcion FROM actividades WHERE estado='A' ORDER BY nombre", $id);
}

function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
    $rta=$c[$d];
    if ($a=='routing' && $b=='acciones'){
        $rta="<nav class='menu right'>";        
        // $rta.="<li class='icono editar' title='Editar' id='".$c['ID']."' Onclick=\"mostrar('ruteo','pro',event,'','lib.php',7,'ruteo');\"></li>";
        $rta.="</nav>";
    }
    return $rta;
}

function bgcolor($a,$c,$f='c'){
    return '';
}