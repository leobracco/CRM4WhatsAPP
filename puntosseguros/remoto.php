<?php



include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.posadas.php");

function handleevent($event, &$objalarmas, &$objCMSUser) 
{
	switch ($event) {
	      case "lista_json":
			
			$dataArr = Array();


			$objalarmas->where=1;
			foreach ($objalarmas->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['id'] = $row['id'];
			    $r['direccion'] = $row['direccion'];
			    $r['latitud'] = $row['latitud'];
			    $r['longitud'] = $row['longitud'];
			    $r['celular'] = $row["celular"];
			    $r['nserie'] = $row["nserie"];
			   $dataArr[] = $r;
			  }						
			    
			 header('Content-Type: application/json');
			 echo json_encode($dataArr);
				    
			exit();
			break;
	   
	      case "permission_denied":
			header("Location: ../inicio/");  
			exit();
			break;

		default:
			return handleevent("index", $objalarmas, $objCMSUser);
	}
	
}


$dbCMS   = new dbMysql($_SESSION["DB"]["HOST"], $_SESSION["DB"]["NAME"], $_SESSION["DB"]["USER"], $_SESSION["DB"]["PASSWD"]);
$dbCMS->connect();


$tplmarco = new Template("");
$tplmarco->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplmarco->Open("/marco.html");


$tplbotonera = new Template("");
$tplbotonera->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplbotonera->Open("/botonera.html");

$tplheader = new Template("");
$tplheader->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplheader->Open("/header.html");

$objalarmas = new alarmas();
$objalarmas->db = $dbCMS;



$objalarmas->fetch($_GET['id']);
$objalarmas->field("nombre", htmlentities($_GET['nombre'], ENT_QUOTES, "UTF-8"));
$objalarmas->field("precio", $_GET['precio']);
$objalarmas->field("disponible", $_GET['disponible']);
$objalarmas->field("cocina", $_GET['cocina']);





$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);

$tplmarco->setVar("{titulo.pagina}", "Administraci&oacute;n de Sistema :: ABM Alarmas");

$tplmarco->setVar("{contenido}", handleevent($_GET["event"], $objalarmas, $objCMSUser));
$tplmarco->setVar("{user}",$objCMSUser->field('username'));
$tplmarco->setVar("{header}",$tplheader->Template);
$tplmarco->setVar("{botonera}",$tplbotonera->Template);
$tplmarco->setVar("{time}",date("Y-m-d-H:i:s"));
$tplmarco->setVar("{usuario.nombre}", $objCMSUser->values['nombre']);
$tplmarco->setVar("{usuario.apellido}", $objCMSUser->values['apellido']);
$tplmarco->setVar("{usuario.username}", $objCMSUser->values['username']);
$tplmarco->setVar("{usuario.idusuario}", $objCMSUser->values['idusuario']);


print($tplmarco->Template);

$dbCMS->close();


?>
