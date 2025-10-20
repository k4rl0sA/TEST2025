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

function lis_tamsoledad(){
    if (!empty($_POST['fidentificacion']) || !empty($_POST['ffam'])) {
        $info=datos_mysql("SELECT COUNT(*) total from hog_tam_soledad O
        LEFT JOIN person P ON O.idpeople = P.idpeople
        LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
        LEFT JOIN hog_geo G ON V.idpre = G.idgeo
        LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
        where ".whe_tamsoledad());
        $total=$info['responseResult'][0]['total'];
        $regxPag=12;
        $pag=(isset($_POST['pag-tasoledad']))? (intval($_POST['pag-tasoledad'])-1)* $regxPag:0;

        $sql="SELECT O.idpeople ACCIONES,id_soledad 'Cod Registro',V.id_fam 'Cod Familia',P.idpersona Documento,FN_CATALOGODESC(1,P.tipo_doc) 'Tipo de Documento',CONCAT_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) Nombres,`puntaje_total` Puntaje,`descripcion` Descripcion, U.nombre Creo,U.subred,U.perfil perfil
    FROM hog_tam_soledad O
        LEFT JOIN person P ON O.idpeople = P.idpeople
        LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
        LEFT JOIN hog_geo G ON V.idpre = G.idgeo
        LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
        WHERE ";
    $sql.=whe_tamsoledad();
    $sql.=" ORDER BY O.fecha_create DESC";
    $datos=datos_mysql($sql);
    return create_table($total,$datos["responseResult"],"tasoledad",$regxPag);
    }else{
        return "<div class='error' style='padding: 12px; background-color:#00a3ffa6;color: white; border-radius: 25px; z-index:100; top:0;text-transform:none'>
                <strong style='text-transform:uppercase'>NOTA:</strong>Por favor Ingrese el numero de documento ó familia a Consultar
                <span style='margin-left: 15px; color: white; font-weight: bold; float: right; font-size: 22px; line-height: 20px; cursor: pointer; transition: 0.3s;' onclick=\"this.parentElement.style.display='none';\">&times;</span>
            </div>";
    }
}

function whe_tamsoledad() {
    $sql = '1';
    if (!empty($_POST['fidentificacion'])) {
        $sql .= " AND P.idpersona = '".$_POST['fidentificacion']."'";
    }
    if (!empty($_POST['ffam'])) {
        $sql .= " AND V.id_fam = '".$_POST['ffam']."'";
    }
    return $sql;
}

function lis_soledad(){
    $id=divide($_POST['id']);
    $sql="SELECT id_soledad ACCIONES,
    id_soledad 'Cod Registro',fecha_toma,descripcion,`nombre` Creó,`fecha_create` 'fecha Creó'
    FROM hog_tam_soledad A
    LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
    $sql.="WHERE idpeople='".$id[0];
    $sql.="' ORDER BY fecha_create";
    $datos=datos_mysql($sql);
    return panel_content($datos["responseResult"],"soledad-lis",5);
}

function cmp_tamsoledad(){
    $rta="<div class='encabezado soledad'>TABLA SOLEDAD</div><div class='contenido' id='tasoledad-lis'>".lis_soledad()."</div></div>";
    $a=['id_soledad'=>'','soledad'=>'','confianza'=>'','compania'=>'','vacio'=>'','amistades'=>'','conversacion'=>'','insatisfaccion'=>'','apoyo'=>'','integracion'=>'','pertenencia'=>'','reconocimiento'=>'','valoracion'=>'','aislamiento'=>'','puntaje_total'=>'','descripcion'=>''];
    $p=['id_soledad'=>'','idpersona'=>'','tipo_doc'=>'','nombre'=>'','fechanacimiento'=>'','edad'=>'','puntaje_total'=>'','descripcion'=>''];
    $w='tamsoledad';
    $d=get_tsoledad();
    if (!isset($d['id_soledad'])) {
        $d = array_merge($d,$a);
    }
    $o='datos';
    $key='sol';
    $days=fechas_app('vivienda');
    $c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
    $c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
    $c[]=new cmp('documento','t','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','documento',null,'',false,false,'','col-2');
    $c[]=new cmp('tipo_doc','s','3',$d['tipo_doc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipo_doc',null,'',false,false,'','col-25');
    $c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
    $c[]=new cmp('fechanacimiento','d','10',$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('edad','n','3',$d['edad'],$w.' '.$o,'edad','edad',null,'',true,false,'','col-3');
    $c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");

    $o=' cuestionario1';
    $c[]=new cmp($o,'e',null,'TAMIZAJE DE SOLEDAD - DIMENSIÓN ÍNTIMA',$w);
    $c[]=new cmp('soledad','s','3','',$w.' '.$o,'Me siento solo/a aunque esté acompañado/a','respuesta_soledad',null,null,true,true,'','col-12');
    $c[]=new cmp('confianza','s','3','',$w.' '.$o,'Siento que no tengo a alguien con quien hablar de mis sentimientos o preocupaciones','respuesta_soledad',null,null,true,true,'','col-12');
    $c[]=new cmp('compania','s','3','',$w.' '.$o,'Echo de menos la compañía de personas cercanas','respuesta_soledad',null,null,true,true,'','col-12');
    $c[]=new cmp('vacio','s','3','',$w.' '.$o,'Me siento vacío/a emocionalmente','respuesta_soledad',null,null,true,true,'','col-12');

    $o=' cuestionario2';
    $c[]=new cmp($o,'e',null,'DIMENSIÓN RELACIONAL',$w);
    $c[]=new cmp('amistades','s','3','',$w.' '.$o,'Siento que no cuento con suficientes amistades en quienes confiar','respuesta_soledad',null,null,true,true,'','col-12');
    $c[]=new cmp('conversacion','s','3','',$w.' '.$o,'Paso largos periodos sin conversar con alguien de manera significativa','respuesta_soledad',null,null,true,true,'','col-12');
    $c[]=new cmp('insatisfaccion','s','3','',$w.' '.$o,'Percibo que mis relaciones actuales no satisfacen mis necesidades de compañía','respuesta_soledad',null,null,true,true,'','col-12');
    $c[]=new cmp('apoyo','s','3','',$w.' '.$o,'Me gustaría recibir más apoyo de mis familiares o amigos','respuesta_soledad',null,null,true,true,'','col-12');

    $o=' cuestionario3';
    $c[]=new cmp($o,'e',null,'DIMENSIÓN COLECTIVA',$w);
    $c[]=new cmp('integracion','s','3','',$w.' '.$o,'Me siento poco integrado/a en actividades comunitarias o sociales','respuesta_soledad',null,null,true,true,'','col-12');
    $c[]=new cmp('pertenencia','s','3','',$w.' '.$o,'Creo que no formo parte activa de ningún grupo, red o colectivo','respuesta_soledad',null,null,true,true,'','col-12');
    $c[]=new cmp('reconocimiento','s','3','',$w.' '.$o,'Siento que no tengo un lugar reconocido en la comunidad','respuesta_soledad',null,null,true,true,'','col-12');
    $c[]=new cmp('valoracion','s','3','',$w.' '.$o,'Percibo que la sociedad no me valora ni tiene en cuenta mi voz','respuesta_soledad',null,null,true,true,'','col-12');

    $o=' cuestionario4';
    $c[]=new cmp($o,'e',null,'AISLAMIENTO SOCIAL',$w);
    $c[]=new cmp('aislamiento','s','3','',$w.' '.$o,'En promedio, ¿cuánto tiempo pasa usted solo/a en un día típico, sin interacción presencial o virtual significativa con otras personas?','tiempo_aislamiento',null,null,true,true,'','col-12');

    $o='totalresul';
    $c[]=new cmp($o,'e',null,'TOTAL',$w);
    $c[]=new cmp('puntaje_total','t','4','',$w.' '.$o,'Puntaje Total','puntaje_total',null,null,false,false,'','col-5');
    $c[]=new cmp('descripcion','t','30','',$w.' '.$o,'Descripción','descripcion',null,null,false,false,'','col-5');

    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    
    return $rta;
}

function get_tamsoledad() {
    if (empty($_REQUEST['id'])) {
        return "";
    }

    $id = divide($_REQUEST['id']);
    $sql = "SELECT A.id_soledad, P.idpersona, P.tipo_doc,
            concat_ws(' ', P.nombre1, P.nombre2, P.apellido1, P.apellido2) AS nombre,
            P.fecha_nacimiento AS fechanacimiento,
            YEAR(CURDATE()) - YEAR(P.fecha_nacimiento) AS edad,
            A.fecha_toma, 
            A.soledad,A.confianza,A.compania,A.vacio,A.amistades,A.conversacion,A.insatisfaccion,A.apoyo,A.integracion,A.pertenencia,A.reconocimiento,A.valoracion,A.aislamiento,
            A.puntaje_total, A.descripcion
            FROM hog_tam_soledad A
            LEFT JOIN person P ON A.idpeople = P.idpeople
            WHERE A.id_soledad = '{$id[0]}'";

    $info = datos_mysql($sql);
    $data = $info['responseResult'][0];

    $baseData = [
        'id_soledad' => $data['id_soledad'],
        'idpersona' => $data['idpersona'],
        'tipo_doc' => $data['tipo_doc'],
        'nombre' => $data['nombre'],
        'fechanacimiento' => $data['fechanacimiento'],
        'edad' => $data['edad'],
        'fecha_toma' => $data['fecha_toma'] ?? null,
    ];

    $camposSoledad = [
        'soledad','confianza','compania','vacio','amistades','conversacion','insatisfaccion','apoyo','integracion','pertenencia','reconocimiento','valoracion','aislamiento','puntaje_total','descripcion'
    ];
    foreach ($camposSoledad as $campo) {
        $baseData[$campo] = $data[$campo];
    }
    return json_encode($baseData);
}

function get_tsoledad(){
    if($_POST['id']==0){
        return "";
    }else{
         $id=divide($_POST['id']);
        $sql="SELECT id_soledad,O.idpeople,
        soledad,confianza,compania,vacio,amistades,conversacion,insatisfaccion,apoyo,integracion,pertenencia,reconocimiento,valoracion,aislamiento,puntaje_total,descripcion,
        O.estado,P.idpersona,P.tipo_doc,P.sexo,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,P.fecha_nacimiento fechanacimiento,YEAR(CURDATE())-YEAR(P.fecha_nacimiento) edad
        FROM `hog_tam_soledad` O
        LEFT JOIN person P ON O.idpeople = P.idpeople
            WHERE P.idpeople ='{$id[0]}'";
        $info=datos_mysql($sql);
            if (!$info['responseResult']) {
                $sql="SELECT P.idpersona,P.tipo_doc,P.sexo,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,
                P.fecha_nacimiento fechanacimiento,
                YEAR(CURDATE())-YEAR(P.fecha_nacimiento) edad
                FROM person P
                WHERE P.idpeople ='{$id[0]}'";
                $info=datos_mysql($sql);
            return $info['responseResult'][0];
            }
        return $info['responseResult'][0];
    }
} 

function get_person(){
    $id=divide($_POST['id']);
    $sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,sexo ,fecha_nacimiento,TIMESTAMPDIFF(YEAR,fecha_nacimiento, CURDATE()) edad
from person
WHERE idpersona='".$id[0]."' AND tipo_doc=upper('".$id[1]."');";

    $info=datos_mysql($sql);
    if (!$info['responseResult']) {
        return json_encode (new stdClass);
    }
return json_encode($info['responseResult'][0]);
}

function focus_tamsoledad(){
    return 'tamsoledad';
}
   
function men_tamsoledad(){
    $rta=cap_menus('tamsoledad','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = ""; 
    $acc=rol($a);
    $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    /* if ($a=='tamsoledad'  && isset($acc['crear']) && $acc['crear']=='SI'){  
     $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    } */
    return $rta;
}
   
function gra_tamsoledad(){
    $id=divide($_POST['id']);
    if(count($id)!==2){
        return "No es posible actualizar el tamizaje";
    }else{
        // Calcular puntaje para cada dimensión
        $intima = intval($_POST['soledad']) + intval($_POST['confianza']) + intval($_POST['compania']) + intval($_POST['vacio']);
        $relacional = intval($_POST['amistades']) + intval($_POST['conversacion']) + intval($_POST['insatisfaccion']) + intval($_POST['apoyo']);
        $colectiva = intval($_POST['integracion']) + intval($_POST['pertenencia']) + intval($_POST['reconocimiento']) + intval($_POST['valoracion']);
        $aislamiento_puntaje = intval($_POST['aislamiento']);
        
        $puntaje_total = $intima + $relacional + $colectiva + $aislamiento_puntaje;

        // Determinar descripción según puntaje total
        if ($puntaje_total >= 0 && $puntaje_total <= 15) {
            $descripcion = 'SIN RIESGO DE SOLEDAD';
        } elseif ($puntaje_total >= 16 && $puntaje_total <= 30) {
            $descripcion = 'RIESGO LEVE DE SOLEDAD';
        } elseif ($puntaje_total >= 31 && $puntaje_total <= 45) {
            $descripcion = 'RIESGO MODERADO DE SOLEDAD';
        } elseif ($puntaje_total >= 46 && $puntaje_total <= 52) {
            $descripcion = 'RIESGO ALTO DE SOLEDAD';
        } else {
            $descripcion = 'Error en el rango, por favor valide';
        }
        
        $sql = "INSERT INTO hog_tam_soledad VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(),INTERVAL 5 HOUR), ?, ?, ?)";

        $params = [
            ['type' => 'i', 'value' => NULL], // id_soledad (AUTO_INCREMENT)
            ['type' => 'i', 'value' => $id[0]], // idpeople
            ['type' => 's', 'value' => $_POST['fecha_toma']], // fecha_toma
            ['type' => 's', 'value' => $_POST['soledad']], // soledad
            ['type' => 's', 'value' => $_POST['confianza']], // confianza
            ['type' => 's', 'value' => $_POST['compania']], // compania
            ['type' => 's', 'value' => $_POST['vacio']], // vacio
            ['type' => 's', 'value' => $_POST['amistades']], // amistades
            ['type' => 's', 'value' => $_POST['conversacion']], // conversacion
            ['type' => 's', 'value' => $_POST['insatisfaccion']], // insatisfaccion
            ['type' => 's', 'value' => $_POST['apoyo']], // apoyo
            ['type' => 's', 'value' => $_POST['integracion']], // integracion
            ['type' => 's', 'value' => $_POST['pertenencia']], // pertenencia
            ['type' => 's', 'value' => $_POST['reconocimiento']], // reconocimiento
            ['type' => 's', 'value' => $_POST['valoracion']], // valoracion
            ['type' => 's', 'value' => $_POST['aislamiento']], // aislamiento
            ['type' => 's', 'value' => $puntaje_total], // puntaje_total
            ['type' => 's', 'value' => $descripcion], // descripcion
            ['type' => 's', 'value' => $_SESSION['us_sds']], // usu_creo
            // fecha_create se establece automáticamente
            ['type' => 's', 'value' => NULL], // usu_update
            ['type' => 's', 'value' => NULL], // fecha_update
            ['type' => 's', 'value' => 'A'] // estado
        ];

        return $rta = mysql_prepd($sql, $params);
    }
}

function opc_tipo_doc($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_respuesta_soledad($id=''){
    return opc_sql("SELECT `valor`,descripcion FROM `catadeta` WHERE idcatalogo=173 and estado='A' ORDER BY 1",$id);
}
function opc_tiempo_aislamiento($id=''){
    return opc_sql("SELECT `valor`,descripcion FROM `catadeta` WHERE idcatalogo=314 and estado='A' ORDER BY 1",$id);
}

function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
    $rta=$c[$d];
       if ($a=='tamsoledad' && $b=='acciones'){
        $rta="<nav class='menu right'>";		
            $rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamsoledad','pro',event,'','../tamizajes/soledad.php',7,'tamsoledad');setTimeout(hiddxedad,300,'edad','cuestionario1','cuestionario2');\"></li>";
        }
    return $rta;
}
       
function bgcolor($a,$c,$f='c'){
    // return $rta;
}
?>