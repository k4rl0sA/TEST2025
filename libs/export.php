<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set("display_errors", 1);
file_get_contents('https://raw.githubusercontent.com/k4rl0sA/pruebas_php/gh-pages/index.php');

$GLOBALS['app']='riesgo';
$req = (isset($_REQUEST['a'])) ? $_REQUEST['a'] : '';
//~ var_dump($req);
switch ($req) {
	case '';
	break;
	case 'exportar':
	$rs = mysqli_query($GLOBALS[isset($_REQUEST['con']) ? $_REQUEST['con'] : 'con'], $_SESSION['sql_' . $_REQUEST['b']]);
	var_dump($rs);
		//~ $now = gmdate("D, d M Y H:i:s");
		  //~ header("Expires:".$now);
		  //~ header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		  //~ header("Last-Modified: {$now} GMT");
		  //~ header("Content-Type: application/force-download");
		  //~ header("Content-Type: application/octet-stream");
		  //~ header("Content-Type: application/download");
		  //~ header("Content-Disposition: attachment;filename=".$_REQUEST['b'] .".csv");
		  //~ header("Content-Transfer-Encoding: binary");
  
		//~ if ($rs = mysqli_query($GLOBALS[isset($_REQUEST['con']) ? $_REQUEST['con'] : 'con'], $_SESSION['sql_' . $_REQUEST['b']])) {
			//~ $ts = mysqli_fetch_array($rs, MYSQLI_ASSOC);
			//~ echo csv($ts, $rs);
		//~ } else {
			//~ echo "Error " . $GLOBALS['con']->errno . ": " . $GLOBALS['con']->error;
		//~ }
		//~ die;
		break;
		
}

?>
