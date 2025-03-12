<?PHP
include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");

function handleevent($event, &$objgrupos, &$objCMSUser) 
{
	switch ($event) {
		case "index":
			if (!$objCMSUser->checkPermission("Grupos::lectura"))
				return handleevent("permission_denied", $objgrupos, $objCMSUser);
    
			$result=$objgrupos->doformat("/grupos/index.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);

			return $result;
			break;
		case "form":
			if (!$objCMSUser->checkPermission("Grupos::lectura"))
				return handleevent("permission_denied", $objgrupos, $objCMSUser);
			
			$objgrupos->enableTables(array("grupos_permisos",'permisos'));
			$objgrupos->fetch($_GET["idgrupo"]);
			$objpermisos = new permisos();
			
			$objpermisos->db = $objgrupos->db;
			$objpermisos->where="idpermiso not in (select idpermiso from grupos_permisos where idgrupo=".$_GET["idgrupo"].")";
			$objpermisos->order_by="nombre ASC";
			$result = $objgrupos->doformat("/grupos/edicion.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			
			$result = $objpermisos->doformatall($result);
			$dataArr[''] = Array();
			$dataArr['texto']="Accion realizada correctamente";
			$dataArr['estado']=1;
			$dataArr['resultado']=$result;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;

		case "eliminarpermiso":
			if (!$objCMSUser->checkPermission("Grupos::escritura")) 
				return handleevent("permission_denied", $objgrupos, $objCMSUser);
      
			foreach ($_GET['idpermisos'] as $id)
			{
			  $objgrupos_permisos = new grupos_permisos();
			  $objgrupos_permisos->db = $objgrupos->db;
			  $objgrupos_permisos->fetch($id);
			  $objgrupos_permisos->delete();
			}
			$objpermisos = new permisos();
			$objpermisos->db = $objgrupos->db;
			$objpermisos->where="idpermiso not in (select idpermiso from grupos_permisos where idgrupo=".$_GET["idgrupo"].")";
			$objpermisos->order_by="nombre ASC";
			$dataArr = Array();
			foreach ($objpermisos->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row['nombre'];
				$r['idpermiso'] = $row['idpermiso'];

			    $dataArr['permisosDisponibles'][] = $r;
			  }
			$objgrupos_Permisos = new grupos_permisos();
			$objgrupos_Permisos->db = $objgrupos->db;
			$objgrupos_Permisos->enableTables(array("permisos"));
			$objgrupos_Permisos->where="idgrupo=".$_GET["idgrupo"];
			foreach ($objgrupos_Permisos->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row['permiso']['nombre'];
				$r['idpermiso'] = $row['id'];

			    $dataArr['permisosAgregados'][] = $r;
			  }
			  header('Content-Type: application/json');
			  echo json_encode($dataArr);
			exit(0);
			break;
		case "permiso_grabar":
			if (!$objCMSUser->checkPermission("Grupos::escritura"))
				return handleevent("permission_denied", $objgrupos, $objCMSUser);
			foreach ($_GET['idpermisos'] as $id)
			{
			  $objgrupos_permisos = new grupos_permisos();
			  $objgrupos_permisos->db = $objgrupos->db;
			  $objgrupos_permisos->field("idgrupo", $_GET["idgrupo"]);
			  $objgrupos_permisos->field("idpermiso", $id);
			  $objgrupos_permisos->store();
			}
			$objpermisos = new permisos();
			$objpermisos->db = $objgrupos->db;
			$objpermisos->where="idpermiso not in (select idpermiso from grupos_permisos where idgrupo=".$_GET["idgrupo"].")";
			
			$dataArr = Array();
			foreach ($objpermisos->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row['nombre'];
				$r['idpermiso'] = $row['idpermiso'];

			    $dataArr['permisosDisponibles'][] = $r;
			  }
			  $objgrupos_Permisos = new grupos_permisos();
			  $objgrupos_Permisos->db = $objgrupos->db;
			$objgrupos_Permisos->enableTables(array("permisos"));
			$objgrupos_Permisos->where="idgrupo=".$_GET["idgrupo"];
			$objgrupos_Permisos->order_by="nombre ASC";
			foreach ($objgrupos_Permisos->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row['permiso']['nombre'];
				$r['idpermiso'] = $row['id'];

			    $dataArr['permisosAgregados'][] = $r;
			  }
			  header('Content-Type: application/json');
			  echo json_encode($dataArr);
			exit(0);
			break;	
		case "borrar":
			if (!$objCMSUser->checkPermission("Grupos::escritura"))
				return handleevent("permission_denied", $objgrupos, $objCMSUser);
			$objgrupos->fetch($_GET["idgrupo"]);
			$objgrupos->delete();
			addLog("alerta","ha borrado Grupos#".$objgrupos->ID());
			header("Content-Type:text/html;charset=iso-8859-1");
			echo $objgrupos->ID();
			exit (0);
			break;
		case "grabar":
			if (!$objCMSUser->checkPermission("Grupos::escritura")) 
				return handleevent("permission_denied", $objgrupos, $objCMSUser);

			$objgrupos->fetch($_GET['id']);
			$objgrupos->field("nombre", $_GET['nombre']);	

			$objgrupos->store();
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha grabado Grupos ".$objgrupos->ID());
			header("Content-Type: text/html;charset=iso-8859-1");
			echo $objgrupos->ID();
			exit(0);
			break;

	     case "lista_json":
			if (!$objCMSUser->checkPermission("Grupos::lectura"))
				return handleevent("permission_denied", $objgrupos, $objCMSUser);
			$dataArr[''] = Array();
			$dataArr['total']=0;
			$objgrupos->limit_from=$_GET['offset'];
			$objgrupos->limit_count=$_GET['limit'];
			if ($_GET['order']=='desc')
			$_GET['order']="DESC";
			else 
			$_GET['order']	="ASC";
			$objgrupos->where=1;
			if (isset($_GET["sort"]) && isset($_GET["order"]))
			$objgrupos->order_by=$_GET['sort'] ." ". $_GET['order'];
			if (isset($_GET["limit"]))
			$objgrupos->limit_count=$_GET['limit'];
			if (isset($_GET["search"]))
			$objgrupos->where.=" AND nombre LIKE '%".$_GET["search"]."%'";
			$dataArr['total']=$objgrupos->count();
 			
			foreach ($objgrupos->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row['nombre'];
			  
			    $r['herramientas'] = "
				<button class='btn btn-success btn-xs' id='editar' title='Editar' value='".$row['idgrupo']."'>
				<img src='../img/icons/edit.png'>
				</button>
				<button class='btn btn-danger btn-xs' id='borrar' title='Borrar' value='".$row['idgrupo']."'>
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
			$dataArr[''] = Array();
			$dataArr['texto']="Usted no tiene permisos para realizar esta accion";
			$dataArr['estado']=0;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;

		default:
			return handleevent("index", $objgrupos, $objCMSUser);
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


$objgrupos = new grupos();
$objgrupos->db = $dbCMS;


$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);





$tplmarco->setVar("{page.title}","IOT Grupos - Quodii SAS");
$tplmarco->setVar("{page.header}",$tplheader->Template);
$tplmarco->setVar("{page.sider}",$tplsider->Template);

$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"], $objgrupos, $objCMSUser));

$tplmarco->setVar("{page.footer}",$tplfooter->Template);

print($tplmarco->Template);

$dbCMS->close();


?>
