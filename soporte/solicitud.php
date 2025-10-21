<?php
// filepath: vscode-vfs://github/k4rl0sA/TEST2025/crea-fam/solicitudes.php
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
function cmp_solicitudes(){
    $rta = "";
    $w = 'solicitudes';
    $o = 'inftrasint';
    $t = ['documento'=>'','tipo'=>''];
    $c[] = new cmp($o,'e',null,'SOLICITUDES APLICATIVO',$w);
    //incluir campos idpeople
    $c[]=new cmp('solicitud','s',3,'',$w.' '.$o,'Tipo de Solicitud','solicitud',null,'####',true,true,'','col-4');
    $c[]=new  cmp('descripcion','a',2,'',$w.' '.$o,'Descripción','tipo',null,null,true,true,'','col-4');
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    return $rta;
}

// Enfocar el formulario
function focus_solicitudes(){
    return 'solicitudes';
}

// Menú de acciones
function men_solicitudes(){
    $rta = cap_menus('solicitudes','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = "";
    $acc = rol($a);
    if ($a=='solicitudes' && isset($acc['crear']) && $acc['crear']=='SI') {  
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    return $rta;
}
// Guardar traslado interlocalidad/subred
function gra_solicitudes() {
    $id = divide($_POST['idfam']);
    $doc = $_POST['docum'];
    $tipo = $_POST['tip_doc'];
    $usu_creo = $_SESSION['us_sds'];
    $creo = date('Y-m-d H:i:s', strtotime('-5 hours'));
    $estado = 3;

    //Obtener subred del usuario de la sesión
    $sql_usr = "SELECT subred FROM usuarios WHERE id_usuario = '{$usu_creo}' LIMIT 1";
    $info_usr = datos_mysql($sql_usr);
    $subred_usr = isset($info_usr['responseResult'][0]['subred']) ? $info_usr['responseResult'][0]['subred'] : null;

    if (!$subred_usr) {
        return "Error: msj['No se pudo determinar la subred del usuario.']";
    }
    //Obtener subred del cod_familia destino
    $sql_fam = "SELECT hg.subred,P.idpeople 
 	    FROM hog_fam hf 
        INNER JOIN hog_geo hg ON hf.idpre = hg.idgeo
        INNER JOIN person P ON hf.id_fam=P.vivipersona 
        WHERE P.idpersona= {$doc} AND P.tipo_doc='{$tipo}' LIMIT 1";
    //mostrar sql_fam
    // var_dump($sql_fam);
    $info_fam = datos_mysql($sql_fam);
    $subred_fam = isset($info_fam['responseResult'][0]['subred']) ? $info_fam['responseResult'][0]['subred'] : null;
    $familia = isset($info_fam['responseResult'][0]['idpeople']) ? $info_fam['responseResult'][0]['idpeople'] : null;

    if (!$subred_fam) {
        return "Error: msj['No se encontró la familia destino o no tiene subred asociada.']";
    }

    // Validar que la familia destino sea de otra subred
    if ($subred_usr === $subred_fam) {
        return "Error: msj['El traslado interlocalidad/inter-subred solo se permite entre diferentes subredes. Use el traslado local para la misma subred.']";
    }


    // Insertar en soporte con formulario=3
    $sql = "INSERT INTO soporte (idsoporte, idpeople, cod_familia, formulario,prioridad,ok,aprueba, usu_creo, fecha_create, estado) VALUES (NULL, ?,?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [
        ['type' => 'i', 'value' => $familia],  // cod_familia    
        ['type' => 'i', 'value' => $id[0]],    // idpeople
        ['type' => 'i', 'value' => 3],           // formulario (3 para interlocal)
        ['type' => 's', 'value' => 'A'],         // prioridad
        ['type' => 'i', 'value' => intval($subred_fam)],         // ok
        ['type' => 's', 'value' => 'PROAPO'],//aprueba
        ['type' => 's', 'value' => $usu_creo],   // usu_creo
        ['type' => 's', 'value' => $creo],       // fecha_create
        ['type' => 'i', 'value' => $estado]      // estado
    ];
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

// Si necesitas opciones para selects, puedes agregar funciones opc_solicitudes(), etc.
function opc_tipo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function formato_dato($a,$b,$c,$d){
    return $c[$d];
}
function bgcolor($a,$c,$f='c'){
    return "";
}