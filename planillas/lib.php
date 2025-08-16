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
    $info=datos_mysql("SELECT COUNT(*) total FROM `planillas` P WHERE estado_planilla!='G' ".whe_planillas());
    $total=$info['responseResult'][0]['total'];
    $regxPag=4;
    $pag=(isset($_POST['pag-planillas']))? ($_POST['pag-planillas']-1)* $regxPag:0;
    $sql="SELECT CONCAT_WS('_',P.id_planilla,P.idpeople) ACCIONES, P.id_planilla 'ID', P.idpeople 'ID Persona', P.cod_fam 'Código Familia', P.tipo 'Tipo', P.estado_planilla 'Estado', P.fecha_create 'Fecha Creación', P.usu_create 'Creó' 
    FROM `planillas` P 
    WHERE estado_planilla!='G' ";
    $sql.=whe_planillas();
    $sql.=" ORDER BY P.fecha_create DESC";
    $sql.=' LIMIT '.$pag.','.$regxPag;
    $datos=datos_mysql($sql);
    // Puedes ocultar columnas usando el array $no
    $no=['ID']; // Ejemplo: ocultar columna ID
    return create_table($total,$datos["responseResult"],"planillas",$regxPag,'lib.php',$no);
}

// Filtros para planillas
function whe_planillas() {
    $sql = "";
    if ($_POST['fidpeople'])
        $sql .= " AND P.idpeople = '".$_POST['fidpeople']."'";
    if ($_POST['fcod_fam'])
        $sql .= " AND P.cod_fam ='".$_POST['fcod_fam']."' ";
    if ($_POST['ftipo'])
        $sql .= " AND P.tipo ='".$_POST['ftipo']."' ";
    if ($_POST['festado_planilla'])
        $sql .= " AND P.estado_planilla ='".$_POST['festado_planilla']."' ";
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
        // Aquí podrías cargar los datos de la planilla si es edición
        $sql = "SELECT * FROM planillas WHERE idplanilla='{$id[0]}'";
        $info = datos_mysql($sql);
        if ($info['responseResult']) $data = $info['responseResult'][0];
    }
    // Componente oculto para idplanilla
    $c[] = new cmp('idplanilla','h',15,$data['idplanilla'] ?? '', 'planillas', 'ID Planilla');
    // ID Persona
    $c[] = new cmp('idpeople','t',10,$data['idpeople'] ?? '', 'planillas', 'ID Persona');
    // Código Familia
    $c[] = new cmp('cod_fam','t',10,$data['cod_fam'] ?? '', 'planillas', 'Código Familia');
    // Tipo Planilla (select)
    $c[] = new cmp('tipo','s',3,$data['tipo'] ?? '', 'planillas', 'Tipo Planilla', 'tipo_planilla');
    // Estado Planilla (select)
    $c[] = new cmp('estado_planilla','s',3,$data['estado_planilla'] ?? '', 'planillas', 'Estado Planilla', 'estado_planilla');
    // Fecha de creación (solo mostrar, no editar)
    $c[] = new cmp('fecha_crea','t',20,$data['fecha_crea'] ?? '', 'planillas', 'Fecha Creación', '', '', false, false);
    // Usuario que creó (solo mostrar)
    $c[] = new cmp('usu_crea','t',20,$data['usu_crea'] ?? '', 'planillas', 'Creó', '', '', false, false);
    // Renderizar todos los componentes
    foreach ($c as $cmp) $rta .= $cmp->put();
    return $rta;
}

// Opciones para selects si tienes catálogos
function opc_tipo_planilla($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=XXX and estado='A' ORDER BY 1",$id); // Reemplaza XXX por el idcatalogo correcto
}
function opc_estado_planilla($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=YYY and estado='A' ORDER BY 1",$id); // Reemplaza YYY por el idcatalogo correcto
}

// Grabar/actualizar planilla
function gra_planillas(){
    $id=divide($_POST['idplanilla']);
    $sql = "UPDATE `planillas` SET cod_fam=?, tipo=?, estado_planilla=?, usu_update=?, fecha_update=NOW() WHERE idplanilla=?";
    $params = [
        ['type' => 's', 'value' => $_POST['cod_fam']],
        ['type' => 's', 'value' => $_POST['tipo']],
        ['type' => 's', 'value' => $_POST['estado_planilla']],
        ['type' => 's', 'value' => $_SESSION['us_sds']],
        ['type' => 'i', 'value' => $id[0]],
    ];
    return mysql_prepd($sql, $params);
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
