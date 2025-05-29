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

function lis_srq(){
    $id=divide($_POST['id']); //id_srq ACCIONES,
    $sql="SELECT tam_srq 'Cod Registro', fecha_toma 'Fecha Toma',
          ansiedad as 'Ansiedad', 
          suicida as 'Suicida', 
          psicosis as 'Psicosis', 
          epilepsia as 'Epilepsia', 
          alcoholismo as 'Alcoholismo',
          `nombre` Creó, `fecha_create` 'fecha Creó'
          FROM hog_tam_srq A
          LEFT JOIN usuarios U ON A.usu_creo=U.id_usuario ";
    $sql.="WHERE idpeople='".$id[0];
    $sql.="' ORDER BY fecha_create";
    $datos=datos_mysql($sql);
    return panel_content($datos["responseResult"],"srq-lis",5);
}

function cmp_tamsrq(){
    $rta="<div class='encabezado srq'>TABLA SRQ</div><div class='contenido' id='srq-lis'>".lis_srq()."</div></div>";
    $t=['tam_srq'=>'','srq_tipodoc'=>'','srq_nombre'=>'','srq_idpersona'=>'','srq_fechanacimiento'=>'','srq_edad'=>'']; 
    $w='tamsrq';
    $d=get_tamsrq(); 
    if ($d=="") {$d=$t;}
    $o='datos';
    $key='srch';
    $days=fechas_app('psicologia');
    
    $c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
    $c[]=new cmp('idsrq','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
    $c[]=new cmp('srq_idpersona','n','20',$d['srq_idpersona'],$w.' '.$o.' '.$key,'N° Identificación','srq_idpersona',null,'',false,false,'','col-2');
    $c[]=new cmp('srq_tipodoc','s','3',$d['srq_tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','srq_tipodoc',null,'',false,false,'','col-25');
    $c[]=new cmp('srq_nombre','t','50',$d['srq_nombre'],$w.' '.$o,'nombres','srq_nombre',null,'',false,false,'','col-4');
    $c[]=new cmp('srq_fechanacimiento','d','10',$d['srq_fechanacimiento'],$w.' '.$o,'fecha nacimiento','srq_fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('srq_edad','n','3',$d['srq_edad'],$w.' '.$o,'edad','srq_edad',null,'',true,false,'','col-1');
    $c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");

    $o='preguntas';
    $c[]=new cmp($o,'e',null,'Preguntas SRQ (Último mes)',$w);
    
    // Preguntas 1-30
    $preguntas = array(
        1 => '¿Tiene Frecuentes Dolores De Cabeza?',
        2 => '¿Tiene Mal Apetito?',
        3 => '¿Duerme Mal?',
        4 => '¿Se Asusta Con Facilidad?',
        5 => '¿Sufre De Temblor En Las Manos?',
        6 => '¿Se Siente Nervioso, Tenso O Aburrido?',
        7 => '¿Sufre De Mala Digestión?',
        8 => '¿No Puede Pensar Con Claridad?',
        9 => '¿Se Siente Triste?',
        10 => '¿Llora Usted Con Mucha Frecuencia?',
        11 => '¿Tiene Dificultad De Disfrutar Sus Actividades Diarias?',
        12 => '¿Tiene Dificultad Para Tomar Decisiones?',
        13 => '¿Tiene Dificultad En Hacer Su Trabajo? (¿Sufre Usted Con Su Trabajo?)',
        14 => '¿Es Incapaz De Desempeñar Un Papel Útil En Su Vida?',
        15 => '¿Ha Perdido Interés En Las Cosas?',
        16 => '¿Siente Que Usted Es Una Persona Inútil?',
        17 => '¿Ha Tenido La Idea De Acabar Con Su Vida?',
        18 => '¿Se Siente Cansado Todo El Tiempo?',
        19 => '¿Tiene Sensaciones Desagradables En Su Estómago?',
        20 => '¿Se Cansa Con Facilidad?',
        21 => '¿Siente Usted Que Alguien Ha Tratado De Herirlo En Alguna Forma?',
        22 => '¿Es Usted Una Persona Mucho Más Importante De Lo Que Piensan Los Demás?',
        23 => '¿Ha Notado Interferencias O Algo Raro En Su Pensamiento?',
        24 => '¿Oye Voces Sin Saber De Dónde Vienen O Que Otras Personas No Puede Oir?',
        25 => '¿Ha Tenido Convulsiones, Ataques O Caídas Al Suelo, Con Movimientos De Brazos Y Piernas; Con Mordedura De La Lengua O Pérdida Del Conocimiento?',
        26 => '¿Alguna Vez Le Ha Parecido A Su Familia, Sus Amigos, Su Médico O A Su Sacerdote Que Usted Estaba Bebiendo Demasiado Licor?',
        27 => '¿Alguna Vez Ha Querido Dejar De Beber, Pero No Ha Podido?',
        28 => '¿Ha Tenido Alguna Vez Dificultades En El Trabajo (O Estudio) A Causa De La Bebida, Como Beber En El Trabajo O En El Colegio, O Faltar A Ellos?',
        29 => '¿Ha Estado En Riñas O La Han Detenido Estando Borracho?',
        30 => '¿Le Ha Parecido Alguna Vez Que Usted Bebía Demasiado?'
    );
    
    foreach($preguntas as $num => $texto) {
        $c[]=new cmp('pregunta'.$num,'s',3,'',$w.' '.$o,$num.'. '.$texto,'pregunta',null,null,true,true,'','col-10');
    }

    $o='resultados';
    $c[]=new cmp($o,'e',null,'Resultados',$w);
    $c[]=new cmp('ansiedad','t',100,'',$w.' '.$o,'Ansiedad','ansiedad',null,'',false,false,'','col-2');
    $c[]=new cmp('suicida','t',100,'',$w.' '.$o,'suicida','suicida',null,'',false,false,'','col-2');
    $c[]=new cmp('psicosis','t',100,'',$w.' '.$o,'psicosis','psicosis',null,'',false,false,'','col-2');
    $c[]=new cmp('epilepsia','t',100,'',$w.' '.$o,'epilepsia','epilepsia',null,'',false,false,'','col-2');
    $c[]=new cmp('alcoholismo','t',100,'',$w.' '.$o,'alcoholismo','alcoholismo',null,'',false,false,'','col-2');


    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    
    return $rta;
}

function get_tamsrq(){
    if($_POST['id']==0){
        return "";
    }else{
        $id=divide($_POST['id']);
        $sql="SELECT P.idpeople,P.idpersona srq_idpersona,P.tipo_doc srq_tipodoc,
              concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) srq_nombre,
              P.fecha_nacimiento srq_fechanacimiento,
              TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS srq_edad
              FROM person P
              WHERE P.idpeople ='{$id[0]}'";
        $info=datos_mysql($sql);
        return $info['responseResult'][0];
    }
} 

function focus_tamsrq(){
    return 'tamsrq';
}
   
function men_tamsrq(){
    $rta=cap_menus('tamsrq','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = "";
    $acc=rol($a);
    if ($a=='tamsrq' && isset($acc['crear']) && $acc['crear']=='SI') {  
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    $rta .= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this);\"></li>";  
    return $rta;
}

function gra_tamsrq(){
    $id=divide($_POST['idsrq']);
    
    $ansiedad = "Bajo o sin riesgo";
    $suicida = "Bajo o sin riesgo";
    $psicosis = "Bajo o sin riesgo";
    $epilepsia = "Bajo o sin riesgo";
    $alcoholismo = "Bajo o sin riesgo";
  
     // Contar respuestas afirmativas
     $total_si_emocional = 0; // Preguntas 1 a 20
     $total_si_psicosis = 0;  // Preguntas 21 a 24
     $total_si_alcohol = 0;   // Preguntas 26 a 30

     for ($i = 1; $i <= 30; $i++) {
        if ($_POST['pregunta' . $i] ==1) {
            if ($i >= 1 && $i <= 20) $total_si_emocional++;
            if ($i >= 21 && $i <= 24) $total_si_psicosis++;
            if ($i >= 26 && $i <= 30) $total_si_alcohol++;
        }
    }
    // Suicida
    if ($_POST['pregunta17'] == 'SI') {
        $suicida = "Alto riesgo";
    }
    // Psicosis
    if ($total_si_psicosis >= 2) {
        $psicosis = "Alto riesgo";
    }
    // Ansiedad (trastorno emocional común)
    if ($total_si_emocional >= 8) {
        $ansiedad = "Moderado riesgo";
    } else {
        $ansiedad = "Bajo o sin riesgo";
    }
    // Epilepsia
    if ($_POST['pregunta25'] == 'SI') {
        $epilepsia = "Moderado riesgo";
    }
    // Alcoholismo
    if ($total_si_alcohol >= 2) {
        $alcoholismo = "Moderado riesgo";
    }
    
    $sql="INSERT INTO hog_tam_srq VALUES (
        null,
        {$id[0]},
        TRIM(UPPER('{$_POST['fecha_toma']}')),
        TRIM(UPPER('{$_POST['pregunta1']}')),
        TRIM(UPPER('{$_POST['pregunta2']}')),
        TRIM(UPPER('{$_POST['pregunta3']}')),
        TRIM(UPPER('{$_POST['pregunta4']}')),
        TRIM(UPPER('{$_POST['pregunta5']}')),
        TRIM(UPPER('{$_POST['pregunta6']}')),
        TRIM(UPPER('{$_POST['pregunta7']}')),
        TRIM(UPPER('{$_POST['pregunta8']}')),
        TRIM(UPPER('{$_POST['pregunta9']}')),
        TRIM(UPPER('{$_POST['pregunta10']}')),
        TRIM(UPPER('{$_POST['pregunta11']}')),
        TRIM(UPPER('{$_POST['pregunta12']}')),
        TRIM(UPPER('{$_POST['pregunta13']}')),
        TRIM(UPPER('{$_POST['pregunta14']}')),
        TRIM(UPPER('{$_POST['pregunta15']}')),
        TRIM(UPPER('{$_POST['pregunta16']}')),
        TRIM(UPPER('{$_POST['pregunta17']}')),
        TRIM(UPPER('{$_POST['pregunta18']}')),
        TRIM(UPPER('{$_POST['pregunta19']}')),
        TRIM(UPPER('{$_POST['pregunta20']}')),
        TRIM(UPPER('{$_POST['pregunta21']}')),
        TRIM(UPPER('{$_POST['pregunta22']}')),
        TRIM(UPPER('{$_POST['pregunta23']}')),
        TRIM(UPPER('{$_POST['pregunta24']}')),
        TRIM(UPPER('{$_POST['pregunta25']}')),
        TRIM(UPPER('{$_POST['pregunta26']}')),
        TRIM(UPPER('{$_POST['pregunta27']}')),
        TRIM(UPPER('{$_POST['pregunta28']}')),
        TRIM(UPPER('{$_POST['pregunta29']}')),
        TRIM(UPPER('{$_POST['pregunta30']}')),
        '{$ansiedad}',
        '{$suicida}',
        '{$psicosis}',
        '{$epilepsia}',
        '{$alcoholismo}',
        TRIM(UPPER('{$_SESSION['us_sds']}')),
        DATE_SUB(NOW(), INTERVAL 5 HOUR),
        NULL,
        NULL,
        'A')";
    
    $rta=dato_mysql($sql);
    return $rta; 
}

function opc_srq_tipodoc($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function opc_pregunta($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}

function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
    $rta=$c[$d];
    if ($a=='tamsrq' && $b=='acciones'){
        $rta="<nav class='menu right'>";        
        $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamsrq','pro',event,'','lib.php',7,'tamsrq');\"></li>";
    }
    return $rta;
}
   
function bgcolor($a,$c,$f='c'){
    // return $rta;
}