<?php



include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");
require '../vendor/autoload.php';

use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchException;
function handleevent($event,&$objcliente,  &$objCMSUser) 
{
	switch ($event) {
		case "index":
			if (!$objCMSUser->checkPermission("Mapas::lectura"))
				return handleevent("permission_denied",  $objCMSUser);
			$dataArr = Array();
			$result=$objCMSUser->doformat("/mapas/index.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			
			return $result;
			break;
			case "view":
				//$dataArr = Array();
				//$opts = array ( "include_docs" => TRUE, "limit" => 150, "descending" => true );
				//$opts = array ( "include_docs" => TRUE, "limit" => 150, "descending" => true );
				//echo "Nombre:".$_SESSION["ACCOUNT"]["NAME"];	
				$opts = array("include_docs" => true,"startkey" => array($_GET["action"],$_SESSION["ACCOUNT"]["NAME"]), "endkey" => array($_GET["action"],$_SESSION["ACCOUNT"]["NAME"]), "limit" => 100 , "descending" => true);
				//$result = $objcliente->setQueryParameters($opts)->getView($_GET["cuenta"],'by_phone');
				try {
					//$dataArr = $objcliente->setQueryParameters($opts)->getView('cuenta',$_GET["cuenta"] );
					$dataArr = $objcliente->setQueryParameters($opts)->getView($_SESSION["ACCOUNT"]["NAME"],'by_button');
				} catch (Exception $e) {
					echo "something weird happened: " . $e->getMessage() . "<BR>\n";
				}
				$geojson = array(
					'type'      => 'FeatureCollection',
					'features'  => array()
				 );
				header('Content-Type: application/json');
				echo json_encode($dataArr);
		   
				exit();
				break;

		
	      
	      case "permission_denied":
			header("Location: ../inicio/");  
			exit();
			break;

		default:
			return handleevent("index", $objcliente, $objCMSUser);
	}
	
}

if (!$_SESSION["usuarios_idusuario"]) {
	header("Location: ../inicio/");
	exit(0);
}

$dbCMS   = new dbMysql($_SESSION["MYSQL"]["HOST"], $_SESSION["MYSQL"]["NAME"], $_SESSION["MYSQL"]["USER"], $_SESSION["MYSQL"]["PASSWORD"]);
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


$objcliente = new CouchClient($_SESSION["COUCHDB"]["URL"],$_SESSION["COUCHDB"]["NAME"]);


$tplmarco->setVar("{page.title}","Pignus Mapas - Quodii SAS");
$tplmarco->setVar("{page.header}",$tplheader->Template);
$tplmarco->setVar("{page.sider}",$tplsider->Template);

$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"],$objcliente,  $objCMSUser));

$tplmarco->setVar("{page.footer}",$tplfooter->Template);

print($tplmarco->Template);

$dbCMS->close();

?>
