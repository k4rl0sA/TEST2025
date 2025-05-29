<?php
ini_set("display_errors", 1);
require_once "../libs/gestion.php";
$perfil=datos_mysql("SELECT perfil FROM usuarios WHERE id_usuario='".$_SESSION["us_sds"]."'");
if ($perfil['responseResult'][0]['perfil']=='GEO' || $perfil['responseResult'][0]['perfil']=='ADM'|| $perfil['responseResult'][0]['perfil']=='TECFAM' || $perfil['responseResult'][0]['perfil']=='SUPHOG'){
	$file = $_FILES['archivo']['tmp_name'];
	$name = $_FILES['archivo']['name'];
	$type = $_FILES['archivo']['type'];
	$size = $_FILES['archivo']['size'];
	$ext = explode(".", $name);
	$delimit = ",";
	if (strtolower(end($ext)) == "csv") {
		$handle = fopen($file, "r");
		if ($handle === FALSE) {
			die("No se pudo abrir el archivo " . $name);
		}
		$nFil = 1;
		$ok=0;
		$ncol=$_POST['ncol'];
		$tab=$_POST['tab'];
		$ope=(isset($_POST['ope'])) ? $_POST['ope']:'insert';
		
		if ($ope=='insert'){
			while (($campo = fgetcsv($handle,1024, $delimit)) !== false) {		
				if ($nFil !== 1) {
				// echo "En la Fila : $nFil\n";
					$sql = "INSERT INTO ".$tab." VALUES(";
					for ($i=0;$i<$ncol;$i++){
						if($i+1==$ncol){
							if ($campo[$i]!='NULL'){
								$sql.="'".trim($campo[$i])."'";
							}else{
								$sql.="NULL";
							}
						}else{
							if ($campo[$i]!='NULL'){
								$sql.="'".trim($campo[$i])."',";
							}else{
								$sql.="NULL,";
							}
						}
					}
					$sql.=");";
					//echo $sql."/n";
					$r=dato_mysql($sql);
					//~ $rta=strpos($r, 'Error');
					if(preg_match('/Error/i', $r)){
						$reg=$nFil-1;
						echo  "Registro #".$reg." - ".$r."<br>";
					}else{
						//~ echo $sql;
						$ok++;
					}
				}
					$nFil++;
			}
			$total=$nFil-2;
			echo "Se han insertado ".$ok." Registro(s) de ".$total." en total, Correctamente para la tabla ";
		}elseif($ope=='update'){
			while (($campo = fgetcsv($handle,1024, $delimit)) !== false) {		
				if ($nFil== 1) {
					for ($j=$_POST['nwhe'];$j<$ncol;$j++){
						$cmp[]=$campo[$j];
					}
					for ($k=0;$k<$_POST['nwhe'];$k++){
						$key[]=$campo[$k];
					}
					//~ ECHO json_encode($key);
				}
				if ($nFil!== 1) {
					$sql = "UPDATE ".$tab." SET ";
					for ($i=$_POST['nwhe'];$i<$ncol;$i++){
							$sql.= $cmp[$i-$_POST['nwhe']]."='".$campo[$i]."',";
					}
					 $sql=rtrim($sql,',');
					$sql.=" WHERE '1'='1' AND ";
					for ($l=0;$l<$_POST['nwhe'];$l++){
						$sql.= $key[$l]."='".$campo[$l]."',";
					}
					$sql=rtrim($sql,',');
					$sql.=";";
					//~ echo $sql;
					$r=dato_mysql($sql);
					if(preg_match('/Error/i', $r)){
						echo  "Registro #".$nFil." - ".$r."<br>";
					}else{
						$ok++;
					}
				}
					$nFil++;
			}
			$total=$nFil-2;
			echo "Se han actualizado ".$ok." Registro(s) de ".$total." en total, Correctamente para la tabla ";
		}
		echo "<script>actualizar();</script>";
		fclose($handle);
	} else {
		echo "El archivo contiene una extensión invalida! ".strtolower(end($ext));
	}
}else{
	echo "No tiene el perfil permitido para cargar el archivo Csv, por favor consulte al administrador del sistema";
}
?>
