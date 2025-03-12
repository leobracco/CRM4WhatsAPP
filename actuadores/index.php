<?PHP

include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");

function handleevent($event, &$objcuentas, &$objCMSUser) 
{
	switch ($event) {
		case "index":
			if (!$objCMSUser->checkPermission("Sensores::lectura"))
				return handleevent("permission_denied", $objcuentas, $objCMSUser);

		      
			$result=$objcuentas->doformat("/sensores/index.html",$_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);

			return $result;
			break;
		case "form":
			if (!$objCMSUser->checkPermission("Sensores::escritura"))
				return handleevent("permission_denied", $objcuentas, $objCMSUser);

			
			$objcuentas->fetch($_GET["idtoken"]);
			
			$result = $objcuentas->doformat("/sensores/edicion.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			
			$dataArr = Array();
			$dataArr['texto']="Accion realizada correctamente";
			$dataArr['estado']=1;
			$dataArr['resultado']=$result;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;
		
	
		case "lista_json":
			if (!$objCMSUser->checkPermission("Sensores::lectura"))
				return handleevent("permission_denied", $objcuentas, $objCMSUser);
			
		

			$dataArr[''] = Array();
			$dataArr['recordsTotal']=$objcuentas->count();
			
			$objcuentas->limit_from=$_GET['offset'];
			$objcuentas->limit_count=$_GET['limit'];
			if ($_GET['order']=='desc')
			$_GET['order']="DESC";
			else 
			$_GET['order']	="ASC";
			$objcuentas->where=1;
			if (isset($_GET["sort"]) && isset($_GET["order"]))
			$objcuentas->order_by=$_GET['sort'] ." ". $_GET['order'];
			if (isset($_GET["limit"]))
			$objcuentas->limit_count=$_GET['limit'];
			if (isset($_GET["search"]))
			$objcuentas->where.=" AND keyToken LIKE '%".$_GET["search"]."%'";
			$dataArr['recordsFiltered']=$objcuentas->count();
			
			foreach ($objcuentas->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['keyToken'] = $row['keyToken'];
				$r['herramientas'] = "
				<button class='btn btn-success btn-xs' id='editar' title='Editar' value='".$row['id']."'>
				<img src='../img/icons/edit.png'>
				</button>
				<button class='btn btn-danger btn-xs' id='borrar' title='Borrar' value='".$row['id']."'>
				<img src='../img/icons/delete.png'>
				</button>
					";
			    
			    $dataArr['data'][] = $r;
			  }						
			  
			 header('Content-Type: application/json');
			 echo json_encode($dataArr);
				    
			exit();
			break;
	      case "permission_denied":
			$result=$objcuentas->doformat("/permiso.html",$_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			$tpllist = new Template($result);
			$tpllist->setVar("{permiso}","sensores");			
			
			$result = $tpllist->Template;
			return $result;
			break;

		default:
			return handleevent("index", $objcuentas, $objCMSUser);
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


$objcuentas = new cuentas();
$objcuentas->db = $dbCMS;


$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);





$tplmarco->setVar("{page.title}","IOT Sensores - Quodii SAS");
$tplmarco->setVar("{page.header}",$tplheader->Template);
$tplmarco->setVar("{page.sider}",$tplsider->Template);

$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"], $objcuentas, $objCMSUser));

$tplmarco->setVar("{page.footer}",$tplfooter->Template);

print($tplmarco->Template);

$dbCMS->close();


?>
