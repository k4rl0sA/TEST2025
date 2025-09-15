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

function lis_riesgomental(){
    $id=divide($_POST['id']);
    $sql="SELECT id_riesmental 'Cod Registro',fecha_toma,puntaje Puntaje,descripcion,`nombre` Creó,`fecha_create` 'fecha Creó'
    FROM tam_ries_mental A
    LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
    $sql.="WHERE idpeople='".$id[0];
    $sql.="' ORDER BY fecha_create";
    $datos=datos_mysql($sql);
    return panel_content($datos["responseResult"],"riesgomental-lis",5);
}

function cmp_riesgomental(){
    $rta="<div class='encabezado riesgomental'>TABLA RIESGO MENTAL</div><div class='contenido' id='riesgomental-lis'>".lis_riesgomental()."</div></div>";
    $t=['idpersona'=>'','tipodoc'=>'','nombre'=>'','fechanacimiento'=>'','edad'=>''];
    $w='riesgomental';
    $d=get_riesgomental(); 
    if ($d=="") {$d=$t;}
    $o='datos';
    $key='srch';
    $days=fechas_app('psicologia');
    $c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
    $c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
    $c[]=new cmp('idpersona','t','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','idpersona',null,'',false,false,'','col-2');
    $c[]=new cmp('tipodoc','s','3',$d['tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-25','getDatForm(\'srch\',\'person\',[\'datos\']);');
    $c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
    $c[]=new cmp('fechanacimiento','d','10',$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('edad','n','3',$d['edad'],$w.' '.$o,'edad','edad',null,'',true,false,'','col-1');
    $c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");

    $o='Tamizaje';
    $c[]=new cmp($o,'e',null,'Preguntas',$w);
    // Agregar preguntas según la tabla
    $pregs = [
        'exprsent'=>"¿Le cuesta expresar sus sentimientos, opiniones a los demás?",
        'optideci'=>"¿Frecuentemente se siente poco optimista / toma decisiones apresuradas?",
        'trisirri'=>"¿Se siente triste o irritado frecuentemente? ¿Llanto incontrolado? ¿Rabia permanente?",
        'nervans'=>"¿Se ha sentido nervioso, ansioso? ¿Ha perdido disfrutar la vida como antes?",
        'perdsep'=>"¿Afectado por pérdida o separación de persona significativa?",
        'famsuic'=>"¿En su familia hubo intento de suicidio o suicidio consumado?",
        'ideasui'=>"¿Ha tenido idea de acabar con su vida o intentos de suicidio?",
        'diagmen'=>"¿Diagnóstico de enfermedad mental? (Ansiedad, depresión, bipolar, etc)",
        'antefam'=>"¿Antecedentes familiares de trastornos/enfermedad mental?",
        'probdom'=>"¿No duerme bien o le cuesta conciliar el sueño?",
        'apoyfam'=>"¿Su familia/amigos son apoyo cuando tiene problemas?",
        'exptrau'=>"¿Afectado por experiencias traumáticas (violencia, accidentes, desastres)?",
        'estrtrab'=>"¿Su trabajo le causa estrés? (cansancio, desmotivación, desesperanza)",
        'desemple'=>"¿Está desempleado o le preocupa perder su trabajo?",
        'probsust'=>"¿El consumo de sustancias le ha generado problemas?",
        'concent'=>"¿Dificultad para concentrarse en actividades diarias?",
        'acoso'=>"¿Es o ha sido víctima de acoso escolar o laboral?",
        'somat'=>"¿Sufre de mala digestión, dolores de cabeza o tensiones musculares?",
        'enfcron'=>"¿Tiene alguna enfermedad crónica, dolorosa o incapacitante?",
        'discrim'=>"¿Se ha sentido discriminado por pertenecer a grupos específicos?",
        'actrecre'=>"¿Realiza actividad recreativa, deportiva o lúdica habitual?",
        'medprot'=>"¿Algún familiar ha tenido medida de protección?",
        'cambhorm'=>"¿Pasa por cambios hormonales o de rol/ciclo vital?",
        'ocupsalud'=>"¿Trabaja en salud, fuerzas militares, policía, docencia o transporte?",
        'gestante'=>"¿Es gestante con consumo de sustancias, eventos estresantes o violencia?"
    ];
    foreach ($pregs as $campo => $label) {
        $c[]=new cmp($campo,'s',3,'',$w.' '.$o,$label,'rta',null,null,true,true,'','col-10');
    }
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    return $rta;
}

function get_riesgomental(){
    if($_POST['id']==0){
        return "";
    }else{
         $id=divide($_POST['id']);
        $sql="SELECT P.idpeople,P.idpersona idpersona,P.tipo_doc tipodoc,
        concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,P.fecha_nacimiento fechanacimiento,
        TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS edad
        FROM person P
        WHERE P.idpeople ='{$id[0]}'";
        $info=datos_mysql($sql);
        return $info['responseResult'][0];
    }
}

function focus_riesgomental(){
    return 'riesgomental';
}

function men_riesgomental(){
    $rta=cap_menus('riesgomental','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = ""; 
    $acc=rol($a);
    if ($a=='riesgomental'  && isset($acc['crear']) && $acc['crear']=='SI'){  
     $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
    return $rta;
}

function gra_riesgomental() {
    $id = divide($_POST['id']);
    $idpeople = isset($id[0]) ? intval($id[0]) : 0;
    if (!isset($_POST['edad']) || !is_numeric($_POST['edad'])) {
        return "Edad no proporcionada o inválida.";
    }
    if ($idpeople <= 0) {
        return "ID de persona no válido.";
    }
    // Campos de preguntas (ajustar según tabla)
    $campos = ['exprsent','optideci','trisirri','nervans','perdsep','famsuic','ideasui','diagmen','antefam','probdom','apoyfam','exptrau','estrtrab','desemple','probsust','concent','acoso','somat','enfcron','discrim','medprot','cambhorm','ocupsalud','gestante'];
    $total = 0;
    foreach ($campos as $campo) {
        $valor = isset($_POST[$campo]) ? intval($_POST[$campo]) : 0;
        $val = ($valor == 1) ? 1 : 0;
        $total += $val;
    }
    // Pregunta especial: actrecre
    if (isset($_POST['actrecre'])) {
        if (intval($_POST['actrecre']) == 2) {
            $total += 4;
        }
    }
    // Clasificación (ajustar lógica según requerimiento)
    if ($total < 33) {
        $descripcion = 'Sin riesgo significativo';
    } elseif ($total >= 33 && $total =< 66) {
        $descripcion = 'Riesgo moderado';
    } else {
        $descripcion = 'Riesgo alto';
    }
    $sql = "INSERT INTO tam_ries_mental (
        idpeople, fecha_toma, exprsent, optideci, trisirri, nervans, perdsep, famsuic, ideasui, diagmen, antefam, probdom, apoyfam, exptrau, estrtrab, desemple, probsust, concent, acoso, somat, enfcron, discrim, actrecre, medprot, cambhorm, ocupsalud, gestante, puntaje, descripcion, usu_creo, fecha_create, estado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    $params = [
        ['type' => 'i', 'value' => $idpeople],
        ['type' => 's', 'value' => $_POST['fecha_toma']],
        ['type' => 's', 'value' => $_POST['exprsent']],
        ['type' => 's', 'value' => $_POST['optideci']],
        ['type' => 's', 'value' => $_POST['trisirri']],
        ['type' => 's', 'value' => $_POST['nervans']],
        ['type' => 's', 'value' => $_POST['perdsep']],
        ['type' => 's', 'value' => $_POST['famsuic']],
        ['type' => 's', 'value' => $_POST['ideasui']],
        ['type' => 's', 'value' => $_POST['diagmen']],
        ['type' => 's', 'value' => $_POST['antefam']],
        ['type' => 's', 'value' => $_POST['probdom']],
        ['type' => 's', 'value' => $_POST['apoyfam']],
        ['type' => 's', 'value' => $_POST['exptrau']],
        ['type' => 's', 'value' => $_POST['estrtrab']],
        ['type' => 's', 'value' => $_POST['desemple']],
        ['type' => 's', 'value' => $_POST['probsust']],
        ['type' => 's', 'value' => $_POST['concent']],
        ['type' => 's', 'value' => $_POST['acoso']],
        ['type' => 's', 'value' => $_POST['somat']],
        ['type' => 's', 'value' => $_POST['enfcron']],
        ['type' => 's', 'value' => $_POST['discrim']],
        ['type' => 's', 'value' => $_POST['actrecre']],
        ['type' => 's', 'value' => $_POST['medprot']],
        ['type' => 's', 'value' => $_POST['cambhorm']],
        ['type' => 's', 'value' => $_POST['ocupsalud']],
        ['type' => 's', 'value' => $_POST['gestante']],
        ['type' => 'i', 'value' => $total],
        ['type' => 's', 'value' => $descripcion],
        ['type' => 's', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => 'A']
    ];
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

function opc_rta($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}

function opc_tipodoc($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
    $rta=$c[$d];
    if ($a=='riesgomental' && $b=='acciones'){
        $rta="<nav class='menu right'>";   
        $rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('riesgomental','pro',event,'','lib.php',7,'riesgomental');\"></li>";
    }
    return $rta;
}

function bgcolor($a,$c,$f='c'){
    // return $rta;
}
