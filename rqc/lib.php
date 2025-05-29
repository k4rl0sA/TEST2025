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

function lis_rqc(){
    $id=divide($_POST['id']);//id_rqc ACCIONES,
    $sql="SELECT tam_srq 'Cod Registro',fecha_toma 'Fecha Toma',
          CONCAT(totalsi,' SI / ',totalno,' NO') as Resultado,
          `nombre` Creó,`fecha_create` 'fecha Creó'
          FROM hog_tam_rqc A
          LEFT JOIN usuarios U ON A.usu_creo=U.id_usuario ";
    $sql.="WHERE idpeople='".$id[0];
    $sql.="' ORDER BY fecha_create";
    $datos=datos_mysql($sql);
    return panel_content($datos["responseResult"],"rqc-lis",5);
}

function cmp_tamrqc(){
    $rta="<div class='encabezado rqc'>TABLA RQC</div><div class='contenido' id='rqc-lis'>".lis_rqc()."</div></div>";
    $t=['tam_rqc'=>'','rqc_tipodoc'=>'','rqc_nombre'=>'','rqc_idpersona'=>'','rqc_fechanacimiento'=>'','rqc_edad'=>'']; 
    $w='tamrqc';
    $d=get_tamrqc(); 
    if ($d=="") {$d=$t;}
    $o='datos';
    $key='srch';
    $days=fechas_app('psicologia');
    
    $c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
    $c[]=new cmp('idrqc','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
    $c[]=new cmp('rqc_idpersona','n','20',$d['rqc_idpersona'],$w.' '.$o.' '.$key,'N° Identificación','rqc_idpersona',null,'',false,false,'','col-2');
    $c[]=new cmp('rqc_tipodoc','s','3',$d['rqc_tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','rqc_tipodoc',null,'',false,false,'','col-25');
    $c[]=new cmp('rqc_nombre','t','50',$d['rqc_nombre'],$w.' '.$o,'nombres','rqc_nombre',null,'',false,false,'','col-4');
    $c[]=new cmp('rqc_fechanacimiento','d','10',$d['rqc_fechanacimiento'],$w.' '.$o,'fecha nacimiento','rqc_fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('rqc_edad','n','3',$d['rqc_edad'],$w.' '.$o,'edad','rqc_edad',null,'',true,false,'','col-1');
    $c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");

    $o='sintomas';
    $c[]=new cmp($o,'e',null,'Síntomas',$w);
    $c[]=new cmp('sintoma1','s',3,'',$w.' '.$o,'1. ¿El Lenguaje Del Niño(A) Es Anormal En Alguna Forma?','sintoma',null,null,true,true,'','col-10');
    $c[]=new cmp('sintoma2','s',3,'',$w.' '.$o,'2. ¿El Niño(A) Duerme Mal?','sintoma',null,null,true,true,'','col-10');
    $c[]=new cmp('sintoma3','s',3,'',$w.' '.$o,'3. ¿Ha Tenido El Niño(A) En Algunas Ocasiones Convulsiones O Caídas Al Suelo Sin Razón?','sintoma',null,null,true,true,'','col-10');
    $c[]=new cmp('sintoma4','s',3,'',$w.' '.$o,'4. ¿Sufre El Niño(A) De Dolores Frecuentes De Cabeza?','sintoma',null,null,true,true,'','col-10');
    $c[]=new cmp('sintoma5','s',3,'',$w.' '.$o,'5. ¿El Niño(A) Ha Huido De La Casa Frecuentemente?','sintoma',null,null,true,true,'','col-10');
    $c[]=new cmp('sintoma6','s',3,'',$w.' '.$o,'6. ¿Ha Robado Cosas De La Casa?','sintoma',null,null,true,true,'','col-10');
    $c[]=new cmp('sintoma7','s',3,'',$w.' '.$o,'7. ¿Se Asusta O Se Pone Nervioso(A) Sin Razón?','sintoma',null,null,true,true,'','col-10');
    $c[]=new cmp('sintoma8','s',3,'',$w.' '.$o,'8. ¿Parece Como Retardado(A) O Lento(A) Para Aprender?','sintoma',null,null,true,true,'','col-10');
    $c[]=new cmp('sintoma9','s',3,'',$w.' '.$o,'9. ¿El (La) Niño(A) Casi Nunca Juega Con Otros Niños(As)?','sintoma',null,null,true,true,'','col-10');
    $c[]=new cmp('sintoma10','s',3,'',$w.' '.$o,'10. ¿El Niño(A) Se Orina O Defeca En La Ropa?','sintoma',null,null,true,true,'','col-10');

    $o='resultados';
    $c[]=new cmp($o,'e',null,'Resultados',$w);
    $c[]=new cmp('totalsi','n',2,'',$w.' '.$o,'Total Sí','totalsi',null,'',false,false,'','col-2');
    $c[]=new cmp('totalno','n',2,'',$w.' '.$o,'Total No','totalno',null,'',false,false,'','col-2');
    $c[]=new cmp('descripcion','t',100,'',$w.' '.$o,'Descripción','descripcion',null,'',false,false,'','col-2');

    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    return $rta;
}

function get_tamrqc(){
    if($_POST['id']==0){
        return "";
    }else{
        $id=divide($_POST['id']);
        $sql="SELECT P.idpeople,P.idpersona rqc_idpersona,P.tipo_doc rqc_tipodoc,
              concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) rqc_nombre,
              P.fecha_nacimiento rqc_fechanacimiento,
              TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS rqc_edad
              FROM person P
              WHERE P.idpeople ='{$id[0]}'";
        $info=datos_mysql($sql);
        return $info['responseResult'][0];
    }
} 

function focus_tamrqc(){
    return 'tamrqc';
}
   
function men_tamrqc(){
    $rta=cap_menus('tamrqc','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = "";
    $acc=rol($a);
    // var_dump($a);
    if ($a=='tamrqc' && isset($acc['crear']) && $acc['crear']=='SI') {  
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    $rta .= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this);\"></li>";  
    return $rta;
}

function gra_tamrqc(){
    $id=divide($_POST['idrqc']);
    $total_si = 0;
    $total_no = 0;
    for($i=1; $i<=10; $i++) {
        if($_POST['sintoma'.$i] == 1) {
            $total_si++;
        } else {
            $total_no++;
        }
    }
    $descripcion = $total_si > 0 ?'ALTERACIÓN': '';
    $sql="INSERT INTO hog_tam_rqc VALUES (
        null,
        {$id[0]},
        TRIM(UPPER('{$_POST['fecha_toma']}')),
        TRIM(UPPER('{$_POST['sintoma1']}')),
        TRIM(UPPER('{$_POST['sintoma2']}')),
        TRIM(UPPER('{$_POST['sintoma3']}')),
        TRIM(UPPER('{$_POST['sintoma4']}')),
        TRIM(UPPER('{$_POST['sintoma5']}')),
        TRIM(UPPER('{$_POST['sintoma6']}')),
        TRIM(UPPER('{$_POST['sintoma7']}')),
        TRIM(UPPER('{$_POST['sintoma8']}')),
        TRIM(UPPER('{$_POST['sintoma9']}')),
        TRIM(UPPER('{$_POST['sintoma10']}')),
        '{$total_si}',
        '{$total_no}',
        '{$descripcion}',
        TRIM(UPPER('{$_SESSION['us_sds']}')),
        DATE_SUB(NOW(), INTERVAL 5 HOUR),
        NULL,
        NULL,
        'A')";
    
    $rta=dato_mysql($sql);
    return $rta; 
}

function opc_rqc_tipodoc($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function opc_sintoma($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A'  ORDER BY 1 ",$id);
}

function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
    $rta=$c[$d];
    if ($a=='tamrqc' && $b=='acciones'){
        $rta="<nav class='menu right'>";        
        $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamrqc','pro',event,'','lib.php',7,'tamrqc');\"></li>";
    }
    return $rta;
}
   
function bgcolor($a,$c,$f='c'){
    // return $rta;
}