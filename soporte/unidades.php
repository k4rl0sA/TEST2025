<?php
// filepath: vscode-vfs://github/k4rl0sA/TEST2025/crea-fam/unidadeshs.php
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

// Componente del formulario
function cmp_unidadeshs(){
    $rta = "";
    $w = 'unidadeshs';
    $o = 'inftras';
    $t = ['idpeople'=>'','cod_familia'=>''];
    $c[] = new cmp($o,'e',null,'UNIDADES DE FAMILIA',$w);
    $c[]=new cmp('idp','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
    $c[] = new cmp('cod_familia','nu','999999999',$t['cod_familia'],$w.' '.$o,'Ingrese la cantidad de Unidades habitacionales al predio','cod_familia',null,null,true,true,'','col-0');
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    return $rta;
}

// Enfocar el formulario
function focus_unidadeshs(){
    return 'unidadeshs';
}

// Menú de acciones
function men_unidadeshs(){
    $rta = cap_menus('unidadeshs','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = "";
    $acc = rol($a);
    if ($a=='unidadeshs' && isset($acc['crear']) && $acc['crear']=='SI') {  
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    return $rta;
}

// Guardar unidadesH
function gra_unidadeshs() {
    $cod_predio = intval($_POST['cod_predio']);
    $id = divide($_POST['idp']); // idpeople
    //$familia = intval($_POST['cod_familia']); // id de la familia destino
    $usu_creo = $_SESSION['us_sds']; // usuario que crea
    $creo = date('Y-m-d H:i:s', strtotime('-5 hours')); // fecha creación ajustada
    $estado = 2;

    // Obtener subred del usuario de la sesión
    $sql_usr = "SELECT subred FROM usuarios WHERE id_usuario = '{$usu_creo}' LIMIT 1";
    $info_usr = datos_mysql($sql_usr);
    $subred_usr = isset($info_usr['responseResult'][0]['subred']) ? $info_usr['responseResult'][0]['subred'] : null;

    if (!$subred_usr) {
        return "Error: msj['No se pudo determinar la subred del usuario.']";
    }

    // Obtener subred del cod_familia destino
    $sql_fam = "SELECT hg.subred 
                FROM hog_fam hf 
                INNER JOIN hog_geo hg ON hf.idpre = hg.idgeo 
                WHERE hf.id_fam = {$familia} LIMIT 1";
    $info_fam = datos_mysql($sql_fam);
    $subred_fam = isset($info_fam['responseResult'][0]['subred']) ? $info_fam['responseResult'][0]['subred'] : null;

    // Insertar en soporte si la subred es la misma
    $sql = "INSERT INTO soporte (idsoporte, cod_predio,  cod_registro,formulario, prioridad, usu_creo, fecha_create, estado) VALUES (NULL, ?, ?, ?, ?, ?,?, ?)";
    $params = [
        ['type' => 'i', 'value' => $cod_predio],      // idpeople
        ['type' => 'i', 'value' => $registro],    // cod_familia
        ['type' => 'i', 'value' => 4],           // formulario (2 = Unidades Habitacionales)
        ['type' => 's', 'value' => 'A'],         // prioridad
        ['type' => 's', 'value' => $usu_creo],   // usu_creo
        ['type' => 's', 'value' => $creo],       // fecha_create
        ['type' => 'i', 'value' => $estado]      // estado
    ];
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

// Si necesitas opciones para selects, puedes agregar funciones opc_unidadeshs(), etc.
// Puedes agregar funciones como formato_dato y bgcolor si tu sistema las requiere.
function formato_dato($a,$b,$c,$d){
    return $c[$d];
}
function bgcolor($a,$c,$f='c'){
    return "";
}