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

function focus_segnoreg(){
	return 'segnoreg';
   }
   
   function men_segnoreg(){
	$rta=cap_menus('segnoreg','pro');
	return $rta;
   }
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='segnoreg'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	 
	   }
  return $rta;
}
function lis_segnoreg(){
    // print_r($_POST);
  $id = (isset($_POST['id'])) ? divide($_POST['id']) : divide($_POST['idp']) ;
$info=datos_mysql("SELECT COUNT(*) total FROM emb_segreg WHERE idpeople=".$id[0]."");
$total=$info['responseResult'][0]['total'];
$regxPag=5;
$pag=(isset($_POST['pag-segnoreg']))? ($_POST['pag-segnoreg']-1)* $regxPag:0;

    $sql="SELECT sr.idsegnoreg AS ACCIONES,sr.idsegnoreg 'cod Registro',sr.fecha_seg 'Fecha',segui 'N Seguimiento',FN_CATALOGODESC(73	,estado_seg) 'Estado', u.nombre 'creo' 
FROM emb_segreg sr
left join usuarios u ON sr.usu_creo=u.id_usuario
            WHERE idpeople='".$id[0];
        $sql.="' ORDER BY fecha_create";
        $sql.=' LIMIT '.$pag.','.$regxPag;
        //  echo $sql;
        $datos=datos_mysql($sql); 
        return create_table($total,$datos["responseResult"],"segnoreg",$regxPag,'embseg.php');
}

function cmp_segnoreg(){
   $rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div><div class='contenido' id='segnoreg-lis'>".lis_segnoreg()."</div></div>";
  //$rta='';
  $w='segnoreg';
  //$t=['id'=>'','idsegnoreg'=>'','idpeople'=>'','fecha_seg'=>'','segui'=>'','estado_seg'=>'','prioridad'=>'','gestaciones'=>'','partos'=>'','abortos'=>'','cesareas'=>'','vivos'=>'','muertos'=>'','fum'=>'','edad_gest'=>'','resul_gest'=>'','peso_nacer'=>'','asist_controles'=>'','exa_labo'=>'','cons_micronutri'=>'','esq_vacu'=>'','signos_alarma1'=>'','diag_sifigest'=>'','adhe_tto'=>'','diag_sificong'=>'','seg_partera'=>'','seg_med_ancestral1'=>'','diag_cronico'=>'','cual'=>'','tto_enf'=>'','ctrl_cronico'=>'','signos_alarma2'=>'','seg_med_ancestral2'=>'','doc_madre'=>'','ctrl_cyd'=>'','lactancia_mat'=>'','esq_vacunacion'=>'','sig_alarma_seg'=>'','seg_med_ancestral3'=>'','sistolica'=>'','diastolica'=>'','frec_cardiaca'=>'','frec_respiratoria'=>'','saturacion'=>'','gluco'=>'','peri_cefalico'=>'','peri_braqueal'=>'','peso'=>'','talla'=>'','imc'=>'','zcore'=>'','clasi_nutri'=>'','ser_remigesti'=>'','observaciones'=>'','users_bina'=>'','equipo_bina'=>'']; 
  $d='';
	$o='modini';
  $ob='Ob';
  $no='nO';
  $bl='bL';
  $x=false;
  $block=['gestan','cronicos'];
  $days=fechas_app('etnias');
  $ge='GEs';
  $cro='CrO';
  $me5='mE5';
  $p=get_persona();
	$c[]=new cmp($o,'e',null,'MODULO INICIAL',$w);
  $c[]=new cmp('fechanacimiento','h','10',$p['fecha_nacimiento'],$w.' zsc '.$o,'fecha nacimiento','fechanacimiento',null,'',true,false,'','col-2');
  $c[]=new cmp('sexo','h',1,$p['sexo'],$w.' zsc '.$o,'sexo','sexo',null,'',false,false,'','col-1');
  $c[]=new cmp('idsegnoreg','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
  $c[]=new cmp('fecha_seg','d',10,$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('segui','s',3,$d,$w.' '.$o,'Seguimiento N°','segui',null,null,true,true,'','col-2',"staEfe('segui','sta');EnabEfec(this,['ges','cronicos','menor5','signosV','antrop','aspfin'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('estado_seg','s',3,$d,$w.' sTa '.$o,'Estado','estado_seg',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);enabFielSele(this,false,['prioridad'],['3']);enabFielSele(this,true,['prioridad'],['1']);EnabEfec(this,['ges','cronicos','menor5','signosV','antrop','aspfin'],['Ob'],['nO'],['bL']);");//
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$x,'','col-2');
  $c[]=new cmp('prioridad','s',3,$d,$w.' '.$no,'Prioridad','prioridad',null,null,false,true,'','col-2',"enabPrioEtn();enCroGes();");
  
    $o='ges';
  $c[]=new cmp($o,'e',null,'GESTANTES',$w);
  $c[]=new cmp('gestaciones','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'Gestaciones','fxobs',null,null,true,true,'','col-2');
  $c[]=new cmp('partos','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'Partos','fxobs',null,null,true,true,'','col-2');
  $c[]=new cmp('abortos','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'Abortos','fxobs',null,null,true,true,'','col-2');
  $c[]=new cmp('cesareas','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'Cesareas','fxobs',null,null,true,true,'','col-2');
  $c[]=new cmp('vivos','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'Vivos','fxobs',null,null,true,true,'','col-2');
  $c[]=new cmp('muertos','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'Muertos','fxobs',null,null,true,true,'','col-2');
  $c[]=new cmp('fum','d',10,$d,$w.' '.$bl.' '.$ge.' '.$o,'Fecha Última Menstruación','fum',null,null,true,true,'','col-2');
  $c[]=new cmp('edad_gest','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'Edad Gestacional Al Momento De Identificacion En Semanas','edad_gest',null,null,true,true,'','col-4','enabEmbRGes();enabEmbPRg();');
  $c[]=new cmp('fecha_obs','d',10,$d,$w.' '.$bl.' Pue '.$o,'Fecha de Evento Obstetrico','fecha_obs',null,null,true,false,'','col-2');
  $c[]=new cmp('resul_gest','s',3,$d,$w.' '.$bl.' Pue '.$o,'Resultado De La Gestación','resul_gest',null,null,true,false,'','col-2',"enabNV();");
  $c[]=new cmp('peso_nacer','sd',5,$d,$w.' '.$bl.' Nav '.$o,'Peso Al Nacer (Gr)','peso_nacer',null,null,false,false,'','col-2');
  $c[]=new cmp('asist_controles','s',3,$d,$w.' PrG '.$bl.' '.$o,'¿Asiste A Controles Prenatales?','rta',null,null,true,true,'','col-2');
  $c[]=new cmp('exa_labo','s',3,$d,$w.' PrG '.$bl.' '.$o,'¿Cuenta Con Exámenes De Laboratorio Al Día? Con Relación Al Trimestre Gestacional?','rta',null,null,true,true,'','col-4');
  $c[]=new cmp('cons_micronutri','s',3,$d,$w.' PrG '.$bl.' '.$o,'¿Consume Micronutrientes?','rta',null,null,true,true,'','col-4');
  $c[]=new cmp('esq_vacu','s',3,$d,$w.' PrG '.$bl.' '.$o,'¿Tiene Esquema De Vacunacion Completo Para La Edad Gestacional?','rta',null,null,true,true,'','col-3');
  $c[]=new cmp('signos_alarma1','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'¿Presenta Signos De Alarma?','rta',null,null,true,true,'','col-2');
  $c[]=new cmp('diag_sifigest','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'¿Diagnosticada Con Sifilis Gestacional?','rta',null,null,true,true,'','col-3', 'enabEmbSif();');
  $c[]=new cmp('adhe_tto','s',3,$d,$w.' '.$bl.' SiF '.$o,'¿Adherencia A Tratamiento?','rta',null,null,true,true,'','col-2');
  $c[]=new cmp('diag_sificong','s',3,$d,$w.' '.$bl.' SiF '.$o,'¿Sifilis Congenita?','rta',null,null,true,true,'','col-3');
  $c[]=new cmp('seg_partera','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'¿Le Ha Realizado Seguimiento Partera?','rta',null,null,true,true,'','col-3');
  $c[]=new cmp('seg_med_ancestral1','s',3,$d,$w.' '.$bl.' '.$ge.' '.$o,'¿Le Ha Realizado Seguimiento El Médico Ancestral?','rta',null,null,true,true,'','col-4');

  $o='cronicos';
  $c[]=new cmp($o,'e',null,'CONDICIONES CRONICAS',$w);
  $c[]=new cmp('diag_cronico','s',3,$d,$w.' '.$bl.' '.$cro.' '.$o,'Diagnostico De Condicion Cronica','diag_cronico',null,null,true,true,'','col-3','diagCroEtn();');
  $c[]=new cmp('cual','t',50,$d,$w.' '.$bl.' dAG '.$cro.' '.$o,'¿Cual?','cual',null,null,true,false,'','col-4');
  $c[]=new cmp('tto_enf','s',3,$d,$w.' '.$bl.' '.$cro.' '.$o,'Cuenta Con Tratamiento Para Su Enfermedad','rta',null,null,true,true,'','col-3');
  $c[]=new cmp('ctrl_cronico','s',3,$d,$w.' '.$bl.' '.$cro.' '.$o,'Asiste A Control De Cronicos','rta',null,null,true,true,'','col-2');
  $c[]=new cmp('signos_alarma2','s',3,$d,$w.' '.$bl.' '.$cro.' '.$o,'Presenta Signos De Alarma','rta',null,null,true,true,'','col-2');
  $c[]=new cmp('seg_med_ancestral2','s',3,$d,$w.' '.$bl.' '.$cro.' '.$o,'¿Le Ha Realizado Seguimiento El Médico Ancestral?','rta',null,null,true,true,'','col-3');
  
  
  $o='menor5';
  $c[]=new cmp($o,'e',null,'MENOR DE 5 AÑOS',$w);
  $c[]=new cmp('doc_madre','n',18,$d,$w.' '.$bl.' '.$me5.' '.$o,'Número De Documento Madre','doc_madre',null,null,true,true,'','col-35');
  $c[]=new cmp('ctrl_cyd','s',3,$d,$w.' '.$bl.' '.$me5.' '.$o,'¿Asiste A Controles De Crecimiento Y Desarrollo?','rta',null,null,true,true,'','col-35');
  $c[]=new cmp('lactancia_mat','s',3,$d,$w.' '.$bl.' '.$me5.' '.$o,'¿Recibe Lactancia Materna?','rta',null,null,true,true,'','col-3');
  $c[]=new cmp('esq_vacunacion','s',3,$d,$w.' '.$bl.' '.$me5.' '.$o,'¿Tiene Esquema De Vacunación Completo Para La Edad?','rta',null,null,true,true,'','col-3');
  $c[]=new cmp('sig_alarma_seg','s',3,$d,$w.' '.$bl.' '.$me5.' '.$o,'Presenta Signos De Alarma En El Momento Del Seguimiento?','rta',null,null,true,true,'','col-35');
  $c[]=new cmp('seg_med_ancestral3','s',3,$d,$w.' '.$bl.' '.$me5.' '.$o,'¿Le Ha Realizado Seguimiento El Médico Ancestral?','rta',null,null,true,true,'','col-35');
  
  
  $o='signosV';
  $q='sigVit';
  $c[]=new cmp($o,'e',null,'SIGNOS VITALES',$w);
  $c[]=new cmp('aten_med','s',3,$d,$w.' '.$o,'Recibio Atención por Medico Ancestral','rta',null,null,false,true,'','col-25');
  $c[]=new cmp('aten_par','s',3,$d,$w.' '.$o,'Recibio Atención por Partera','rta',null,null,false,true,'','col-25');
  $men = ($p['mes']>143) ? true : false ;
  $c[]=new cmp('sistolica','sd',3,$d,$w.' '.$o,'Valor Sistolica (50 mmhg y 200 mmhg)','sistolica','rgxsisto',null,true,$men,'','col-25');
  $c[]=new cmp('diastolica','sd',3,$d,$w.' '.$o,'Valor Diastolica (30 mmhg y 150 mmhg)','diastolica','rgxdiast',null,true,$men,'','col-25');
  $c[]=new cmp('frec_cardiaca','sd',3,$d,$w.' '.$o,'Frecuencia Cardiaca (50 a 150 lt)','frec_cardiaca','rgxfcard',null,true,true,'','col-25');
  $c[]=new cmp('frec_respiratoria','sd',3,$d,$w.' '.$o,'Frecuencia Respiratoria (15 a 60 x min)','frec_respiratoria','rgxfresp',null,true,true,'','col-25');
  $c[]=new cmp('saturacion','sd',3,$d,$w.' '.$o,'Saturación (40 a 99 %)','saturacion','rgxsatu',null,true,true,'','col-25');
  $c[]=new cmp('gluco','sd',3,$d,$w.' GlU '.$o,'Glucometria (40 a 500 mg/dl)','gluco','rgxgluco',null,true,false,'','col-25');
  
  $o='antrop';
  $c[]=new cmp($o,'e',null,'VALORACIÓN ANTROPOMETRICA',$w);
  $men5 = ($p['mes']<60) ? true : false ;
  $c[]=new cmp('peri_cefalico','sd',4,$d,$w.' '.$me5.' '.$o,'Perimetro Cefalico (Cm)','peri_cefalico',null,null,true,$men5,'','col-25');

  $men = ($p['mes']>= 6 && $p['mes']< 60) ? true : false ;
  $c[]=new cmp('peri_braqueal','sd',4,$d,$w.' '.$me5.' '.$o,'Perimetro Braquial  (Cm)','peri_braqueal',null,null,true,$men,'','col-25');
  $c[]=new cmp('peso','sd',6,$d,$w.' '.$o,'Peso (Kg)','peso','rgxpeso',null,true,true,'','col-25',"calImc('peso','talla','imc');Zsco('zscore','../etnias/embsegnoreg.php');");
  $c[]=new cmp('talla','sd',5,$d,$w.' '.$o,'Talla (Cm)','talla','rgxtalla',null,true,true,'','col-25',"calImc('peso','talla','imc');Zsco('zscore','../etnias/embsegnoreg.php');");
  $c[]=new cmp('imc','sd',6,$d,$w.' '.$bl.' '.$o,'Imc','imc',null,null,false,false,'','col-3');
  $c[]=new cmp('zscore','t',50,$d,$w.' '.$bl.' '.$o,'Zcore','zscore',null,null,false,$men5,'','col-35');
  $c[]=new cmp('clasi_nut','s',3,$d,$w.' '.$o,'Clasificación Nutricional','clasi_nutri',null,null,false,true,'','col-35');
  
  $o='aspfin';
  $c[]=new cmp($o,'e',null,'ASPECTOS FINALES',$w);
  $c[]=new cmp('ser_remigesti','s',3,$d,$w.' '.$o,'Servicio De Remision Y/O Gestion','ser_remigesti',null,null,false,true,'','col-25');
  $c[]=new cmp('observaciones','a',7000,$d,$w.' '.$ob.' '.$o,'Observaciones','observaciones',null,null,true,true,'','col-75');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put(); 
	return $rta;
}

function get_persona(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		$sql="SELECT FN_CATALOGODESC(21,sexo) sexo,fecha_nacimiento,TIMESTAMPDIFF(YEAR,fecha_nacimiento, CURDATE() ) AS ano,
  		TIMESTAMPDIFF(MONTH,fecha_nacimiento ,CURDATE() ) AS mes,
		DATEDIFF(CURDATE(), DATE_ADD(fecha_nacimiento,INTERVAL TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) MONTH)) AS dia
		from person P WHERE P.idpeople='".$id[0]."'";
		// echo $sql;
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}else{
			return $info['responseResult'][0];
		}
		}
	}

  function get_zscore(){
		// var_dump($_POST);
		$id=divide($_POST['val']);
		 $fechaNacimiento = new DateTime($id[1]);
		 $fechaActual = new DateTime();
		 $diferencia = $fechaNacimiento->diff($fechaActual);
		 $edadEnDias = $diferencia->days;
		$ind = ($edadEnDias<=730) ? 'PL' : 'PT' ;
		$sex=$id[2];
	
	$sql="SELECT (POWER(($id[0] / (SELECT M FROM tabla_zscore WHERE indicador = '$ind' AND sexo = '$sex[0]' AND edad_dias = $id[3])),
		(SELECT L FROM tabla_zscore WHERE indicador = '$ind' AND sexo = '$sex[0]' AND edad_dias = $id[3])) - 1) / 
		((SELECT L FROM tabla_zscore WHERE indicador = '$ind' AND sexo = '$sex[0]' AND edad_dias = $id[3]) *
	 (SELECT S FROM tabla_zscore WHERE indicador = '$ind' AND sexo = '$sex[0]' AND edad_dias = $id[3])) as rta ";
	//   echo $sql;
	 $info=datos_mysql($sql);
		 if (!$info['responseResult']) {
			return '';
		}else{
			$z=number_format((float)$info['responseResult'][0]['rta'], 6, '.', '');
			switch ($z) {
				case ($z <=-3):
					$des='DESNUTRICIÓN AGUDA SEVERA';
					break;
				case ($z >-3 && $z <=-2):
					$des='DESNUTRICIÓN AGUDA MODERADA';
					break;
				case ($z >-2 && $z <=-1):
					$des='RIESGO DESNUTRICIÓN AGUDA';
					break;
				case ($z>-1 && $z <=1):
						$des='PESO ADECUADO PARA LA TALLA';
					break;
				case ($z >1 && $z <=2):
						$des='RIESGO DE SOBREPESO';
					break;
				case ($z >2 && $z <=3):
						$des='SOBREPESO';
					break;
					case ($z >3):
						$des='OBESIDAD';
					break;
				default:
					$des='Error en el rango, por favor valide';
					break;
			}
	
			return json_encode($z." = ".$des);
		}
	}

  
function gra_segnoreg(){
 	$id=divide($_POST['idsegnoreg']);
  if (($rtaFec = validFecha('etnias', $_POST['fecha_seg'] ?? '')) !== true) {return $rtaFec;}
  $pn=$_POST['peso_nacer']?? null;
  $docma=$_POST['doc_madre']?? null;
  $sis=$_POST['sistolica']?? null;
  $diast=$_POST['diastolica']?? null;
  $fcar=$_POST['frec_cardiaca']?? null;
  $fres= $_POST['frec_respiratoria']?? null;
  $satu=$_POST['saturacion']?? null;
  $gluco=$_POST['gluco']?? null;
  $pcef=$_POST['peri_cefalico']?? null;
  
  $zsc=$_POST['zcore']?? null;
  $cn=$_POST['clasi_nut']?? null;
  
  $pbra=$_POST['peri_braqueal']?? null;
  $pes= $_POST['peso']?? null;
  $tal=$_POST['talla']?? null;
  $imc= $_POST['imc']?? null;
  
      if(COUNT($id)==2){
        $equ=datos_mysql("select equipo from usuarios where id_usuario=".$_SESSION['us_sds']);
      $bina = isset($_POST['fequi'])?(is_array($_POST['fequi'])?implode("-", $_POST['fequi']):implode("-",array_map('trim',explode(",",str_replace("'","",$_POST['fequi']))))):'';
      $equi=$equ['responseResult'][0]['equipo'];
      $sql = "INSERT INTO emb_segreg VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,'A')";
    $params = [
['type' => 'i', 'value' => $id[0]],
['type' => 's', 'value' => $_POST['fecha_seg']],
['type' => 's', 'value' => $_POST['segui']],
['type' => 's', 'value' => $_POST['estado_seg']],
['type' => 's', 'value' => $_POST['motivo_estado']],
['type' => 's', 'value' => $_POST['prioridad']],
['type' => 's', 'value' => $_POST['fecha_obs']],
['type' => 's', 'value' => $_POST['gestaciones']],
['type' => 's', 'value' => $_POST['partos']],
['type' => 's', 'value' => $_POST['abortos']],
['type' => 's', 'value' => $_POST['cesareas']],
['type' => 's', 'value' => $_POST['vivos']],
['type' => 's', 'value' => $_POST['muertos']],
['type' => 's', 'value' => $_POST['fum']],
['type' => 's', 'value' => $_POST['edad_gest']],
['type' => 's', 'value' => $_POST['resul_gest']],
['type' => 's', 'value' => $pn],
['type' => 's', 'value' => $_POST['asist_controles']],
['type' => 's', 'value' => $_POST['exa_labo']],
['type' => 's', 'value' => $_POST['cons_micronutri']],
['type' => 's', 'value' => $_POST['esq_vacu']],
['type' => 's', 'value' => $_POST['signos_alarma1']],
['type' => 's', 'value' => $_POST['diag_sifigest']],
['type' => 's', 'value' => $_POST['adhe_tto']],
['type' => 's', 'value' => $_POST['diag_sificong']],
['type' => 's', 'value' => $_POST['seg_partera']],
['type' => 's', 'value' => $_POST['seg_med_ancestral1']],
['type' => 's', 'value' => $_POST['diag_cronico']],
['type' => 's', 'value' => $_POST['cual']],
['type' => 's', 'value' => $_POST['tto_enf']],
['type' => 's', 'value' => $_POST['ctrl_cronico']],
['type' => 's', 'value' => $_POST['signos_alarma2']],
['type' => 's', 'value' => $_POST['seg_med_ancestral2']],
['type' => 'i', 'value' => $docma],
['type' => 's', 'value' => $_POST['ctrl_cyd']],
['type' => 's', 'value' => $_POST['lactancia_mat']],
['type' => 's', 'value' => $_POST['esq_vacunacion']],
['type' => 's', 'value' => $_POST['sig_alarma_seg']],
['type' => 's', 'value' => $_POST['seg_med_ancestral3']],
['type' => 's', 'value' => $_POST['aten_med']],
['type' => 's', 'value' => $_POST['aten_par']],
['type' => 's', 'value' => $sis],
['type' => 's', 'value' => $diast],
['type' => 's', 'value' => $fcar],
['type' => 's', 'value' => $fres],
['type' => 's', 'value' => $satu],
['type' => 's', 'value' => $gluco],
['type' => 's', 'value' => $pcef],
['type' => 's', 'value' => $pbra],
['type' => 's', 'value' => $pes],
['type' => 's', 'value' => $tal],
['type' => 's', 'value' => $imc],
['type' => 's', 'value' => $zsc],
['type' => 's', 'value' => $cn],
['type' => 's', 'value' => $_POST['ser_remigesti']],
['type' => 's', 'value' => $_POST['observaciones']],
['type' => 's', 'value' => $bina],
['type' => 's', 'value' => $equi],
['type' => 's', 'value' => $_SESSION['us_sds']],
['type' => 's', 'value' => NULL],
['type' => 's', 'value' => NULL]
];
      // $rta = show_sql($sql, $params);
      //var_dump($cn);
    return $rta = mysql_prepd($sql, $params);
   }else{
   $sql="UPDATE emb_segreg SET observaciones=?,fecha_update=DATE_SUB(NOW(),INTERVAL 5 HOUR),usu_update=? WHERE idsegnoreg=?"; //  compromiso=?, equipo=?, 
    $params = [
         ['type' => 's', 'value' => $_POST['observaciones']],
        ['type' => 'i', 'value' => $_SESSION['us_sds']],
        ['type' => 'i', 'value' => $id[0]]
      ];
      $rta = mysql_prepd($sql, $params);
    }
    return $rta;
}

function get_segnoreg(){
  if($_REQUEST['id']==''){
    return "";
  }else{
    $id=divide($_REQUEST['id']);
    $sql="SELECT P.fecha_nacimiento,P.sexo,idsegnoreg,fecha_seg, segui, estado_seg, motivo, prioridad, fecha_obs, gestaciones, partos, abortos, cesareas, vivos, muertos, fum, edad_gest, resul_gest, peso_nacer, asist_controles, exa_labo, cons_micronutri, esq_vacu, signos_alarma1, diag_sifigest, adhe_tto, diag_sificong, seg_partera, seg_med_ancestral1, diag_cronico, cual, tto_enf, ctrl_cronico, signos_alarma2, seg_med_ancestral2, doc_madre, ctrl_cyd, lactancia_mat, esq_vacunacion, sig_alarma_seg, seg_med_ancestral3, at_med, at_partera, sistolica, diastolica, frec_cardiaca, frec_respiratoria, saturacion, gluco, peri_cefalico, peri_braqueal, peso, talla, imc, zcore, clasi_nutri, ser_remigesti, observaciones
          FROM `emb_segreg` S
          left join person P ON S.idpeople=P.idpeople
          WHERE idsegnoreg='{$id[0]}'";
          // return json_encode($sql);
          $info=datos_mysql($sql);
          if (!empty($info['responseResult'])) return json_encode($info['responseResult'][0]);
        } 
}

function opc_motivo_estado($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=265 and estado='A' ORDER BY 1",$id);
}
function opc_equi($id=''){
	return opc_sql("SELECT id_usuario, nombre FROM usuarios WHERE subred=(select subred from usuarios where id_usuario=".$_SESSION['us_sds'].") AND estado='A' AND equipo=(select equipo from usuarios where id_usuario=".$_SESSION['us_sds'].") ORDER BY LPAD(nombre, 2, '0')",$id);
}

function opc_segui($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=76 and estado='A' ORDER BY 1",$id);
  }

  function opc_estado_seg($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=73 and estado='A' ORDER BY 1",$id);
    }

  function opc_prioridad($id=''){
       return opc_sql('SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=258 and estado="A" ORDER BY 1',$id);
 }

 function opc_fxobs($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=267 and estado='A' ORDER BY cast(idcatadeta AS UNSIGNED)",$id);
}

function opc_edad_gest($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=268 and estado='A' ORDER BY LPAD(idcatadeta, 2, '0') ASC",$id);
}

function opc_resul_gest($id=''){
       return opc_sql('SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=259 and estado="A" ORDER BY 1',$id);
 }

 function opc_rta($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}

function opc_diag_cronico($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=252 and estado='A' ORDER BY 1",$id);
}

function opc_clasi_nutri($id=''){
       return opc_sql('SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=260 and estado="A" ORDER BY 1',$id);
 }

 function opc_ser_remigesti($id=''){
       return opc_sql('SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=261 and estado="A" ORDER BY 1',$id);
 }
	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
        // var_dump($a);
		if ($a=='segnoreg' && $b=='acciones'){
			$rta="<nav class='menu right'>";
      $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'segnoreg',event,this,['fecha_seg','segui','estado_seg','prioridad','gestaciones', 'partos','abortos','cesareas','vivos','muertos','fum','edad_gest','signos_alarma1','diag_sifigest','adhe_tto','diag_sificong','seg_partera','seg_med_ancestral1','aten_med','aten_par','diag_cronico','cual','tto_enf','ctrl_cronico','signos_alarma2','seg_med_ancestral2','doc_madre','ctrl_cyd','lactancia_mat','esq_vacunacion','sig_alarma_seg','seg_med_ancestral3','sistolica','diastolica','frec_cardiaca','frec_respiratoria','saturacion','gluco','peso','talla','clasi_nut','ser_remigesti','asist_controles','exa_labo','cons_micronutri','esq_vacu'],'../etnias/embsegnoreg.php');setTimeout(enabPrioEtn,700);setTimeout(enCroGes,700);\"></li>";//setTimeout(enabPrioEtn,700);enbValue('id','segnoreg','".$c['ACCIONES']."');enaFie(document.getElementById('observaciones'),false);
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	   }
	   