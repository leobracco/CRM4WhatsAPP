<?php
include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");




function handleevent($event, &$objpermisos, &$objCMSUser) 
{
	switch ($event) {
		case "index":
			
			$result=$objpermisos->doformat("/permisos/index.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			return $result;
			break;
		case "form":
			if (!$objCMSUser->checkPermission("Permisos::lectura"))
				return handleevent("permission_denied", $objpermisos, $objCMSUser);			
			$objpermisos->fetch($_GET["idpermiso"]);
			$result = $objpermisos->doformat("/permisos/edicion.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			

			$dataArr[''] = Array();
			$dataArr['texto']="Usted  tiene permisos para realizar esta accion";
			$dataArr['permiso']=$objCMSUser->checkPermission("Permisos::lectura");
			$dataArr['resultado']=$result;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);

			
			break;
		case "borrar":
			if (!$objCMSUser->checkPermission("Permisos::escritura"))
				return handleevent("permission_denied", $objpermisos, $objCMSUser);
			$objpermisos->fetch($_GET["idpermiso"]);
			$objpermisos->delete();
			addLog("alerta","ha borrado Permisos#".$objpermisos->ID());

			header("Content-Type:text/html;charset=iso-8859-1");
			echo $objpermisos->ID();
			exit (0);
			break;
		
		case "grabar":
			if (!$objCMSUser->checkPermission("Permisos::escritura")) 
				return handleevent("permission_denied", $objpermisos, $objCMSUser);
			$objpermisos->fetch($_GET['idpermiso']);
			$objpermisos->field("nombre", $_GET['nombre']);
			$objpermisos->field("descripcion", $_GET['descripcion']);

			$objpermisos->store();
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha grabado Permisos".$objpermisos->ID());
			header("Content-Type: text/html;charset=iso-8859-1");
			echo $objpermisos->ID();
			exit(0);
			break;

		 case "lista_json":
			if (!$objCMSUser->checkPermission("Permisos::lectura"))
				return handleevent("permission_denied", $objpermisos, $objCMSUser);
			$dataArr= Array();
			
			
			
			$objpermisos->limit_from=$_GET['offset'];
			$objpermisos->limit_count=$_GET['limit'];
			if ($_GET['order']=='desc')
			$_GET['order']="DESC";
			else 
			$_GET['order']	="ASC";
			$objpermisos->where=1;
			if (isset($_GET["sort"]) && isset($_GET["order"]))
			$objpermisos->order_by=$_GET['sort'] ." ". $_GET['order'];
			if (isset($_GET["limit"]))
			$objpermisos->limit_count=$_GET['limit'];
			if (isset($_GET["search"]))
			$objpermisos->where.=" AND nombre LIKE '%".$_GET["search"]."%' AND descripcion LIKE '%".$_GET["search"]."%'";
			
			
			foreach ($objpermisos->fetchall() as $row)
			  { 
			    $r = Array();
				$r['id'] = $row['idpermiso'];
			    $r['nombre'] = $row['nombre'];
			    $r['descripcion'] = $row['descripcion'];
			    $r['herramientas'] = "
				<button class='btn btn-success btn-xs' id='editar' title='Editar' value='".$row['idpermiso']."'>
				<img src='../img/icons/edit.png'>
				</button>
				<button class='btn btn-danger btn-xs' id='borrar' title='Borrar' value='".$row['idpermiso']."'>
				<img src='../img/icons/delete.png'>
				</button>
					";
			    $dataArr["data"][] = $r;
			  }						
			    
			 header('Content-Type: application/json');
			 echo json_encode($dataArr);
				    
			exit();
			break;
	     
	      case "permission_denied":
			$dataArr[''] = Array();
			$dataArr['recordsTotal']=0;
			$dataArr['texto']="Usted no tiene permisos para realizar esta accion";
			$dataArr['permiso']=$objCMSUser->checkPermission("Permisos::lectura");
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;

		default:
			return handleevent("index", $objpermisos, $objCMSUser);
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


$objpermisos = new permisos();
$objpermisos->db = $dbCMS;


$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);





$tplmarco->setVar("{page.title}","IOT Permisos - Quodii SAS");
$tplmarco->setVar("{page.header}",$tplheader->Template);
$tplmarco->setVar("{page.sider}",$tplsider->Template);

$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"], $objpermisos, $objCMSUser));

$tplmarco->setVar("{page.footer}",$tplfooter->Template);

print($tplmarco->Template);

$dbCMS->close();


?>
