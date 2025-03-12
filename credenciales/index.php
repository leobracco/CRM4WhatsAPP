<?php



include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");
function handleevent($event, &$objusuarios, &$objCMSUser) 
{
	switch ($event) {
		case "config":
			if (!$objCMSUser->checkPermission("Usuarios::lectura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);
			
			$dataArr = Array();
			
			$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);
			$dataArr["username"]=$objCMSUser->values['username'];
			$dataArr["token"]=$objCMSUser->values['password'];
			$dataArr["topic"]="quodii/".$objCMSUser->values['username']."/pignus/cloud/#";
			$dataArr["port"]=443;
			$dataArr["host"]="mqtt.tools.quodii.com";
			$dataArr["path"]="/";
			$dataArr["secure"]=true;
			$dataArr["session"]=true;
			 header('Content-Type: application/json');
			 echo json_encode($dataArr);
				    
			exit();
			break;
			
	      case "permission_denied":
			header("Location: ../inicio/");  
			exit();
			break;

		default:
			return handleevent("index", $objusuarios, $objCMSUser);
	}
	
}

if (!$_SESSION["usuarios_idusuario"]) {
	header("Location: ../inicio/");
	exit(0);
}

$dbCMS   = new dbMysql($_SESSION["MYSQL"]["HOST"], $_SESSION["MYSQL"]["NAME"], $_SESSION["MYSQL"]["USER"], $_SESSION["MYSQL"]["PASSWORD"]);
$dbCMS->connect();


$objusuarios = new usuarios();
$objusuarios->db = $dbCMS;






$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);


handleevent($_GET["event"], $objusuarios, $objCMSUser);

$dbCMS->close();


?>
