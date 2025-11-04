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
    $t = ['idpeople'=>'','unidades'=>''];
    $c[] = new cmp($o,'e',null,'UNIDADES HABITACIONALES',$w);
    $c[]=new cmp('idp','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
    $c[] = new cmp('unidades','nu','9999',$t['unidades'],$w.' '.$o,'Ingrese la cantidad de Unidades habitacionales al predio','unidades',null,null,true,true,'','col-0');
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
    $id = divide($_POST['idp']); // idpeople
    $usu_creo = $_SESSION['us_sds']; // usuario que crea
    $creo = date('Y-m-d H:i:s'); // fecha creación ajustada
    //validar numeros positivos $_POST['unidades']
    if (!isset($_POST['unidades']) || !is_numeric($_POST['unidades']) || intval($_POST['unidades']) < 0 || intval($_POST['unidades']> 2500)) {
        return "Error: msj['El número de unidades debe ser un valor numérico positivo o menor o igual a 2500.']";
    }   
    $unidades = intval($_POST['unidades']);
       if ($unidades > 2500) {
        return "Error: msj['El número de unidades no puede ser mayor a 2500.']";
    }
    $estado = 2;

   
    // Insertar en soporte si la subred es la misma
    $sql = "INSERT INTO soporte (cod_predio,cod_registro,formulario, prioridad, usu_creo, fecha_create, estado) VALUES (?, ?, ?, ?, ?, ?,?)";
    $params = [
        ['type' => 'i', 'value' => $id[0]],      // idpeople
         ['type' => 'i', 'value' => $unidades],    // cod_familia
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