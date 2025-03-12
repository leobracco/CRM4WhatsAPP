<?PHP

include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");


function handleevent($event, &$objusuarios, &$objCMSUser) 
{
	switch ($event) {
		case "index":
			if (!$objCMSUser->checkPermission("Token::lectura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

		      
			$result=$objusuarios->doformat("/token/index.html",$_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);

			return $result;
			break;
		case "form":
			if (!$objCMSUser->checkPermission("Token::lectura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

			
			$objusuarios->fetch($_SESSION["usuarios_idusuario"]);
			
			$result = $objusuarios->doformat("/token/edicion.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			return $result;
			break;
		case "generateToken":
				if (!$objCMSUser->checkPermission("Token::escritura"))
					return handleevent("permission_denied", $objusuarios, $objCMSUser);
	
				
				$token = openssl_random_pseudo_bytes(8);

				$token = bin2hex($token);
				
				
				$dataArr[''] = Array();
				$dataArr['texto']="Accion realizada correctamente";
				$dataArr['estado']=1;
				$dataArr['resultado']=$token;
				header('Content-Type: application/json');
				echo json_encode($dataArr);
				exit(0);
				break;


		case "grabar":
			if (!$objCMSUser->checkPermission("Token::escritura")) 
				return handleevent("permission_denied", $objusuarios, $objCMSUser);
			$dataArr[''] = Array();
			//$objusuarios->debug=1;
			$objusuarios->fetch($_SESSION["usuarios_idusuario"]);
			$objusuarios->field("token", $_GET["token"]);
			$objusuarios->store();
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha grabado Token".$objusuarios->ID());
			$dataArr['texto']="El token se genero correctamente";
			$dataArr['estado']=1;
			
			header('Content-Type: application/json');
			 echo json_encode($dataArr);
			exit(0);
			break;

		
	      case "permission_denied":
			$dataArr[''] = Array();
			$dataArr['texto']="Usted no tiene permisos para realizar esta accion";
			$dataArr['estado']=0;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;

		default:
			return handleevent("form", $objusuarios, $objCMSUser);
	}
	
}


if (!$_SESSION["usuarios_idusuario"]) {
    header("Location: ../inicio/");
    exit(0);
}

$dbCMS   = new dbMysql($_SESSION["DB"]["HOST"], $_SESSION["DB"]["NAME"], $_SESSION["DB"]["USER"], $_SESSION["DB"]["PASSWD"]);
$dbCMS->connect();



$tplmarco = new Template("");
$tplmarco->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplmarco->Open("/marco.html");

$tplcontent = new Template("");
$tplcontent->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);

$tplheader = new Template("");
$tplheader->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplheader->Open("/header.html");



$tplfooter = new Template("");
$tplfooter->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplfooter->Open("/footer.html");

$tplsider = new Template("");
$tplsider->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplsider->Open("/sider.html");


$objusuarios = new usuarios();
$objusuarios->db = $dbCMS;


$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);





$tplmarco->setVar("{page.title}","IOT Tokens - Quodii SAS");
$tplmarco->setVar("{page.header}",$tplheader->Template);
$tplmarco->setVar("{page.sider}",$tplsider->Template);

$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"], $objusuarios, $objCMSUser));

$tplmarco->setVar("{page.footer}",$tplfooter->Template);

print($tplmarco->Template);

$dbCMS->close();


?>
