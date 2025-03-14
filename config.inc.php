<?PHP
//error_reporting(0);
setlocale (LC_ALL, 'es-ar');
date_default_timezone_set('America/Argentina/Buenos_Aires');
if (PHP_MAJOR_VERSION >= 7) {
    set_error_handler(function ($errno, $errstr) {
       return strpos($errstr, 'Declaration of') === 0;
    }, E_WARNING);
}
function parentpath($path, $level = -1) { 
	$dirs = explode("/",$path); 
	$path = $dirs[0];
	for($i = 1;$i < count($dirs)+$level;$i++) { 
		$path .= "/".$dirs[$i];
	}
	return $path;
} 
session_start();
// MySQL Config



$_SESSION["MYSQL"]["HOST"]="localhost";
$_SESSION["MYSQL"]["NAME"]=getenv("MYSQL_DATABASE");
$_SESSION["MYSQL"]["USER"]=getenv("MYSQL_USER");
$_SESSION["MYSQL"]["PASSWORD"]=getenv("MYSQL_PASSWORD");
$_SESSION["MYSQL"]["PORT"]=getenv("MYSQL_PORT");


$_SESSION["WHATSAPP"]["PHONE_NUMBER_ID"]=getenv('PHONE_NUMBER_ID');
$_SESSION["WHATSAPP"]["ACCESS_TOKEN"] = getenv('ACCESS_TOKEN');
error_log("📞 PHONE_NUMBER_ID sin modificar: " . $_SESSION["WHATSAPP"]["PHONE_NUMBER_ID"]);
error_log("📞 PHONE_NUMBER_ID sin modificar get directo: " . getenv('PHONE_NUMBER_ID'));
error_log("User mysql: " . getenv('MYSQL_USER'));

$_SESSION["APP"]["NAME"]="CRM";
$_SESSION["EMPRESA"]["NAME"]="AP Velas & Aromas";
$_SESSION["USER"]["ID"]=0;
$_SESSION["ACCOUNT"]["ID"]=0;
$_SESSION["ACCOUNT"]["NAME"]=0;
/* Constante formateo de fecha y hora */
$FORMAT_DATE			= "%d de %B de %Y %H:%M:%S";
$FORMAT_HOURS			= "H:i";

$_SESSION["TEMPLATE"]["APP_ROOTPATH"]=getenv("APP_ROOTPATH");
$_SESSION["TEMPLATE"]["APP_UPLOADPATH"]= parentpath($_SESSION["TEMPLATE"]["APP_ROOTPATH"], -1)."/upload";
$_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]=$_SESSION["TEMPLATE"]["APP_ROOTPATH"]."/includes";
$_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]=$_SESSION["TEMPLATE"]["APP_ROOTPATH"]."/templates";



?>

