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

// Listado principal de planillas
function lis_planillas(){
    $info = datos_mysql("SELECT COUNT(*) total FROM `planillas` P WHERE estado='A' " . whe_planillas());
    $total = $info['responseResult'][0]['total'] ?? 0;
    $regxPag = 10;
    $pag = (isset($_POST['pag-planillas'])) ? ($_POST['pag-planillas']-1)* $regxPag : 0;
    $sql = "SELECT CONCAT_WS('_',P.id_planilla,P.idpeople) ACCIONES, P.id_planilla 'ID', P.idpeople 'ID Persona', P.cod_fam 'Código Familia', P.tipo 'Tipo', P.evento 'Evento', P.seguimiento 'Seguimiento', P.formato_direccion 'Formato Dirección', P.estado_planilla 'Estado', P.carpeta 'Carpeta', P.caja 'Caja', P.fecha_max 'Fecha Máxima', P.fecha_formato 'Fecha Formato', P.fecha_create 'Fecha Creación', P.usu_create 'Creó' FROM `planillas` P WHERE estado='A' ";
    $sql .= whe_planillas();
    $sql .= " ORDER BY P.fecha_create DESC";
    $sql .= ' LIMIT '.$pag.','.$regxPag;
    $datos = datos_mysql($sql);
    $no = ['ID'];
    return create_table($total, $datos["responseResult"], "planillas", $regxPag, 'lib.php', $no);
}

// Filtros para planillas
function whe_planillas() {
    $sql = "";
    if (!empty($_POST['fidpeople']))
        $sql .= " AND P.idpeople = '".cleanTx($_POST['fidpeople'])."'";
    if (!empty($_POST['fcod_fam']))
        $sql .= " AND P.cod_fam ='".cleanTx($_POST['fcod_fam'])."' ";
    if (!empty($_POST['ftipo']))
        $sql .= " AND P.tipo ='".cleanTx($_POST['ftipo'])."' ";
    if (!empty($_POST['festado_planilla']))
        $sql .= " AND P.estado_planilla ='".cleanTx($_POST['festado_planilla'])."' ";
    if (!empty($_POST['fevento']))
        $sql .= " AND P.evento ='".cleanTx($_POST['fevento'])."' ";
    if (!empty($_POST['fcarpeta']))
        $sql .= " AND P.carpeta ='".cleanTx($_POST['fcarpeta'])."' ";
    if (!empty($_POST['fcaja']))
        $sql .= " AND P.caja ='".cleanTx($_POST['fcaja'])."' ";
    return $sql;
}

function focus_planillas(){
 return 'planillas';
}

function men_planillas(){
 $rta=cap_menus('planillas','pro');
 return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  $acc=rol($a);
  if ($a=='planillas'  && isset($acc['crear']) && $acc['crear']=='SI'){  
    $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
  }
  return $rta;
}

// Componente principal para mostrar datos de una planilla
function cmp_planillas(){
    $rta = "<div class='encabezado planillas'>FORMULARIO PLANILLA</div>";
    $c = [];
    $id = isset($_POST['id']) ? divide($_POST['id']) : ['',''];
    $data = [];
    if ($_POST['id'] ?? '') {
        $sql = "SELECT * FROM planillas WHERE id_planilla='".intval($id[0])."'";
        $info = datos_mysql($sql);
        if ($info['responseResult']) $data = $info['responseResult'][0];
    }
    $c[] = new cmp('id_planilla','h',15,$data['id_planilla'] ?? '', 'planillas', 'ID Planilla');
    $c[] = new cmp('idpeople','t',10,$data['idpeople'] ?? '', 'planillas', 'ID Persona');
    $c[] = new cmp('cod_fam','t',10,$data['cod_fam'] ?? '', 'planillas', 'Código Familia');
    $c[] = new cmp('tipo','s',3,$data['tipo'] ?? '', 'planillas', 'Tipo Planilla', 'tipo_planilla');
    $c[] = new cmp('evento','t',3,$data['evento'] ?? '', 'planillas', 'Evento');
    $c[] = new cmp('seguimiento','t',3,$data['seguimiento'] ?? '', 'planillas', 'Seguimiento');
    $c[] = new cmp('formato_direccion','t',50,$data['formato_direccion'] ?? '', 'planillas', 'Formato Dirección');
    $c[] = new cmp('estado_planilla','s',3,$data['estado_planilla'] ?? '', 'planillas', 'Estado Planilla', 'estado_planilla');
    $c[] = new cmp('carpeta','t',50,$data['carpeta'] ?? '', 'planillas', 'Carpeta');
    $c[] = new cmp('caja','t',50,$data['caja'] ?? '', 'planillas', 'Caja');
    $c[] = new cmp('fecha_max','t',10,$data['fecha_max'] ?? '', 'planillas', 'Fecha Máxima');
    $c[] = new cmp('fecha_formato','t',10,$data['fecha_formato'] ?? '', 'planillas', 'Fecha Formato');
    $c[] = new cmp('fecha_create','t',20,$data['fecha_create'] ?? '', 'planillas', 'Fecha Creación', '', '', false, false);
    $c[] = new cmp('usu_create','t',20,$data['usu_create'] ?? '', 'planillas', 'Creó', '', '', false, false);
    foreach ($c as $cmp) $rta .= $cmp->put();
    return $rta;
}

// Opciones para selects si tienes catálogos
function opc_tipo_planilla($id=''){
    // Reemplaza 1 por el idcatalogo real de tipo planilla
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_estado_planilla($id=''){
    // Reemplaza 2 por el idcatalogo real de estado planilla
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=2 and estado='A' ORDER BY 1",$id);
}

// Grabar/actualizar planilla
function gra_planillas(){
    $id = divide($_POST['id_planilla']);
    $isNew = empty($id[0]);
    if ($isNew) {
        $sql = "INSERT INTO planillas (idpeople, cod_fam, tipo, evento, seguimiento, formato_direccion, estado_planilla, carpeta, caja, fecha_max, fecha_formato, usu_create, fecha_create, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'A')";
        $params = [
            ['type' => 'i', 'value' => $_POST['idpeople']],
            ['type' => 'i', 'value' => $_POST['cod_fam']],
            ['type' => 's', 'value' => $_POST['tipo']],
            ['type' => 's', 'value' => $_POST['evento']],
            ['type' => 's', 'value' => $_POST['seguimiento']],
            ['type' => 's', 'value' => $_POST['formato_direccion']],
            ['type' => 's', 'value' => $_POST['estado_planilla']],
            ['type' => 's', 'value' => $_POST['carpeta']],
            ['type' => 's', 'value' => $_POST['caja']],
            ['type' => 's', 'value' => $_POST['fecha_max']],
            ['type' => 's', 'value' => $_POST['fecha_formato']],
            ['type' => 's', 'value' => $_SESSION['us_sds']],
        ];
    } else {
        $sql = "UPDATE planillas SET idpeople=?, cod_fam=?, tipo=?, evento=?, seguimiento=?, formato_direccion=?, estado_planilla=?, carpeta=?, caja=?, fecha_max=?, fecha_formato=?, usu_update=?, fecha_update=NOW() WHERE id_planilla=?";
        $params = [
            ['type' => 'i', 'value' => $_POST['idpeople']],
            ['type' => 'i', 'value' => $_POST['cod_fam']],
            ['type' => 's', 'value' => $_POST['tipo']],
            ['type' => 's', 'value' => $_POST['evento']],
            ['type' => 's', 'value' => $_POST['seguimiento']],
            ['type' => 's', 'value' => $_POST['formato_direccion']],
            ['type' => 's', 'value' => $_POST['estado_planilla']],
            ['type' => 's', 'value' => $_POST['carpeta']],
            ['type' => 's', 'value' => $_POST['caja']],
            ['type' => 's', 'value' => $_POST['fecha_max']],
            ['type' => 's', 'value' => $_POST['fecha_formato']],
            ['type' => 's', 'value' => $_SESSION['us_sds']],
            ['type' => 'i', 'value' => $id[0]],
        ];
    }
    return mysql_prepd($sql, $params);
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
