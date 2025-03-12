<?PHP
include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");

//session_start();
$tplmarco = new Template("");
$tplmarco->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);


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


$dbCMS   = new dbMysql($_SESSION["MYSQL"]["HOST"], $_SESSION["MYSQL"]["NAME"], $_SESSION["MYSQL"]["USER"], $_SESSION["MYSQL"]["PASSWORD"]);
$dbCMS->connect();


if (!isset($_SESSION["login_retry"])) 
	$_SESSION["login_retry"] = 0;

$objCMSUser = new Usuarios();
$objCMSUser->setDB($dbCMS);

if (isset($_SESSION["usuarios_idusuario"]) && $_SESSION["usuarios_idusuario"])
{
	$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);
	
}
elseif ($_POST['email'] && $_POST['password']) 
{
	$_SESSION["login_error"] = "";
	//Si hizo 3 intentos... lo hago esperar 1 minuto...
	if ($_SESSION["login_retry"] < 3)
	{
		if ($objCMSUser->validateUser($_POST['email'], $_POST['password'])) 
		{
			
			$_SESSION["usuarios_idusuario"]=$objCMSUser->ID();
			$_SESSION["usuarios_username"]=$objCMSUser->values['username'];
			
			$objLog  = new Eventlog();
			$objLog->db = $dbCMS;
			$objLog->field("timestamp", date("Y-m-d H:i:s"));
			$objLog->field("evento", "Informacion");
			$objLog->field("texto", "<b>".$objCMSUser->values['username']."</b> ha ingresado al sistema desde ".$_SERVER['REMOTE_ADDR']);
			$objLog->store();
			//pongo en CERO los intentos
			$_SESSION["login_error"] ="";
			$_SESSION["login_retry"] = 0;			
			$_SESSION["login_last"] = time();
			
		}
		else
		{
			//Aumento 1 a la cantidad de intentos que hizo de wrong username/passwd...
			$_SESSION["login_retry"]++;
			$_SESSION["login_last"] = time();
		}
	}
	
	if ($_SESSION["login_retry"] >= 3)
	{
		if ($_SESSION["login_retry"] == 3)
		{
			$_SESSION["login_retry"]++;
			$_SESSION["login_last"] = time();
			$_SESSION["login_error"] = "Demasiados intentos fallidos";
		}
		else
		{
			if (time() - $_SESSION["login_last"] > 300);
			{
				$_SESSION["login_error"] = "Cuenta deshabilitada por 5 minutos";
				$_SESSION["login_retry"] = 2;
			}
		}
	}
}

if ($objCMSUser->ID() && !$_GET["logout"]) {
	
	$tplmarco->Open("/marco.html");
	$_SESSION["login_error"] = "Acceso correcto";
	
	$tplcontent->Open("/inicio/index.html");


	$tplmarco->setVar("{page.footer}",$tplfooter->Template);
	$tplmarco->setVar("{page.title}","CRM - AP Velas & Aromas");
	$tplmarco->setVar("{page.header}",$tplheader->Template);
	$tplmarco->setVar("{page.sider}",$tplsider->Template);
	$result = $tplcontent->Template;
	
	 
			
} else {
	$_SESSION["login_error"] = "Salio correctamente del sistema";
	$_SESSION["usuarios_idusuario"]=0;
	$_SESSION["ACCOUNT"]["ID"]=0;
	$_SESSION["ACCOUNT"]["NAME"]=0;
	$tplcontent->Open("/inicio/login.html");
	$tplcontent->setVar("{error}", $_SESSION["login_error"]."-".$_SESSION["login_retry"]."-".$_SESSION["login_last"]);
	$tplmarco->Open("/marco_login.html");
	$tplmarco->setVar("{page.title}", "CRM - AP Velas & Aromas");
	$result = $tplcontent->Template;
}
//echo $_SESSION["login_error"]."-".$_SESSION["login_retry"];
//$tplmarco->setVar("{username}", $_SESSION["username"]);

$tplmarco->setVar("{page.contenido}", $result);



if (is_array($objCMSUser->values)) {
    $tplmarco->setVar("{usuario.username}", $objCMSUser->values['email'] ?? '');
} else {
    error_log("ERROR>> index.php: objCMSUser->values es null o no es un array.");
    $tplmarco->setVar("{usuario.username}", "Desconocido");
}
$tplmarco->setVar("{tiempo}",  date("now"));
// $tplmarco->setVar("{bienvenida}","Acceso al sistema");

header("Content-Type: text/html;charset=utf-8");
echo $tplmarco->Template;

$dbCMS->close();
exit();


?>
