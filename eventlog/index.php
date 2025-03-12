<?php



include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.nysoftv3.php");
function handleevent($event, &$objeventlog, &$objCMSUser) 
{
	switch ($event) {
		case "index":
			if (!$objCMSUser->checkPermission("Eventlog::lectura"))
				return handleevent("permission_denied", $objeventlog, $objCMSUser);
    
			$result=$objeventlog->doformat("/eventlog/index.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			$objeventlog->debug=1;
			
			$tpllist = new Template($result);
			$tpllist->setVar("{filtro_nombre}",$_SESSION['filter_nombre']);			
			$tpllist->setVar("{filtro_apellido}",$_SESSION['filter_apellido']);
			$tpllist->setVar("{filtro_username}",$_SESSION['filter_username']);
			$tpllist->setVar("{filtro_email}",$_SESSION['filter_email']);
			$tpllist->setVar("{filtro_telefono}",$_SESSION['filter_telefono']);
			
			$result = $tpllist->Template;
			return $result;
			break;
		case "form":
			if (!$objCMSUser->checkPermission("Eventlog::lectura"))
				return handleevent("permission_denied", $objeventlog, $objCMSUser);
			
			
			
			$objeventlog->fetch($_GET["id"]);
			$result = $objeventlog->doformat("/eventlog/form_abm.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			
			
			header("Content-Type: text/html;charset=iso-8859-1");
			echo $result;
			exit (0);
			break;
		case "borrar":
			if (!$objCMSUser->checkPermission("Eventlog::escritura"))
				return handleevent("permission_denied", $objeventlog, $objCMSUser);
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha borrado el cliente".$objeventlog->ID());
			$objeventlog->delete();
			
			header("Content-Type:text/html;charset=iso-8859-1");
			echo $objeventlog->ID();
			exit (0);
			break;

		case "grabar":
			if (!$objCMSUser->checkPermission("Eventlog::escritura")) 
				return handleevent("permission_denied", $objeventlog, $objCMSUser);
			$objeventlog->fetch($_GET['id']);
			$objeventlog->field("nombre", htmlentities($_GET['nombre'], ENT_QUOTES, "UTF-8"));
			$objeventlog->field("apellido", $_GET['apellido']);
			$objeventlog->field("celular", $_GET['celular']);
			
			$objeventlog->store();
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha grabado el/la camarera/o".$objeventlog->ID());
			header("Content-Type: text/html;charset=iso-8859-1");
			echo $objeventlog->ID();
			exit(0);
			break;
	      case "lista_json":
			if (!$objCMSUser->checkPermission("Eventlog::lectura"))
				return handleevent("permission_denied", $objeventlog, $objCMSUser);
			
			// $this->fields   = array('id', 'timestamp', 'evento', 'texto');
			
			$dataArr[''] = Array();

			$dataArr['total']=0;
			$objeventlog->limit_from=$_GET['offset'];
			$objeventlog->limit_count=$_GET['limit'];
			if ($_GET['order']=='desc')
			$_GET['order']="DESC";
			else 
			$_GET['order']	="ASC";
			$objeventlog->where=1;
			if (isset($_GET["sort"]) && isset($_GET["order"]))
			$objeventlog->order_by=$_GET['sort'] ." ". $_GET['order'];
			if (isset($_GET["limit"]))
			$objeventlog->limit_count=$_GET['limit'];
			if (isset($_GET["search"]))
			$objeventlog->where.=" AND eventlog.nombre LIKE '%".$_GET["search"]."%'";
			
			foreach ($objeventlog->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['timestamp'] = $row['timestamp.nice'];
			    $r['evento'] = $row['evento'];
			    $r['texto'] = $row['texto'];
			   
			   $dataArr['rows'][] = $r;
			    $dataArr['total']+=1;
			    
			  }						
			    
			 header('Content-Type: application/json');
			 echo json_encode($dataArr);
				    
			exit();
			break;
	      case "autocomplete_json":
			if (!$objCMSUser->checkPermission("Eventlog::lectura"))
				return handleevent("permission_denied", $objeventlog, $objCMSUser);
			
			
			$return_arr = array();
			$objeventlog->enableTables(array("condicion_iva"));
			$objeventlog->where="razonsocial LIKE '%".$_GET['term']."%'";
// 			$objeventlog->limit_count = $_GET['maximo'];

			    foreach ($objeventlog->fetchall() as $row)
			    {
				
				$row_array['id'] = $row['id'];
				$row_array['razonsocial'] = $row['razonsocial'];
				$row_array['DocNro'] = $row['DocNro'];
         
			      array_push($return_arr,$row_array);

			    }					
			    
			    
			 header('Content-Type: application/json');
			 echo json_encode($return_arr);
			flush();    
			exit();
			break;
	      case "clientes_sincontacto":
			if (!$objCMSUser->checkPermission("Eventlog::lectura"))
				return handleevent("permission_denied", $objeventlog, $objCMSUser);
			
			$objeventlog->where=1;
			$cantidad=0;
			foreach ($objeventlog->fetchall() as $row)
			    {
				if ($row['cantidad']==0)
				$cantidad++;

			    }		
			    
			header("Content-Type:text/html;charset=iso-8859-1");
			echo $cantidad;
			    
			exit();
			break;
	      case "clientes_total":
			if (!$objCMSUser->checkPermission("Eventlog::lectura"))
				return handleevent("permission_denied", $objeventlog, $objCMSUser);
			
// 			$objeventlog->debug=1;
// 			$objeventlog->where=1;
		
			header("Content-Type:text/html;charset=iso-8859-1");
			echo $objeventlog->count();
			    
			exit();
			break;
	      case "permission_denied":
			$result = $objCMSUser->doformat("/home/permission_denied.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			header("Content-Type: text/html;charset=iso-8859-1");
			echo $result;   
			exit();
			break;

		default:
			return handleevent("index", $objeventlog, $objCMSUser);
	}
	
}

if (!$_SESSION["usuarios_idusuario"]) {
	header("Location: ../home/");
	exit(0);
}

$dbCMS   = new dbMysql($_SESSION["DB"]["HOST"], $_SESSION["DB"]["NAME"], $_SESSION["DB"]["USER"], $_SESSION["DB"]["PASSWD"]);
$dbCMS->connect();
if(!$_GET['where']) 
  $_GET['where'] = 0;
if(!$_GET['limit_from']) 
  $_GET['limit_from'] = 0;
if(!$_GET['page_size']) 
  $_GET['page_size'] =8;
if(!$_GET['orderby']) 
  $_GET['orderby'] = "razonsocial ASC";
if(!$_GET['page']) 
  $_GET['page'] = 1;

$tplmarco = new Template("");
$tplmarco->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplmarco->Open("/marco.html");


$tplbotonera = new Template("");
$tplbotonera->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplbotonera->Open("/botonera.html");

$tplheader = new Template("");
$tplheader->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplheader->Open("/header.html");

$objeventlog = new eventlog();
$objeventlog->db = $dbCMS;


$objeventlog->where     = urldecode(stripslashes($_SESSION["eventlog_where"]));
$objeventlog->order_by  = urldecode(stripslashes($_SESSION["eventlog_order_by"]));
$objeventlog->fetch($_GET['id']);
$objeventlog->field("nombre", htmlentities($_GET['nombre'], ENT_QUOTES, "UTF-8"));
$objeventlog->field("apellido", $_GET['apellido']);
$objeventlog->field("celular", $_GET['celular']);





$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);

$tplmarco->setVar("{titulo.pagina}", "Administraci&oacute;n de Sistema :: ABM Camareras");

$tplmarco->setVar("{contenido}", handleevent($_GET["event"], $objeventlog, $objCMSUser));
$tplmarco->setVar("{user}",$objCMSUser->field('username'));
$tplmarco->setVar("{header}",$tplheader->Template);
$tplmarco->setVar("{botonera}",$tplbotonera->Template);
$tplmarco->setVar("{time}",date("Y-m-d-H:i:s"));
$tplmarco->setVar("{usuario.nombre}", $objCMSUser->values['nombre']);
$tplmarco->setVar("{usuario.apellido}", $objCMSUser->values['apellido']);
$tplmarco->setVar("{usuario.username}", $objCMSUser->values['username']);
$tplmarco->setVar("{usuario.idusuario}", $objCMSUser->values['idusuario']);

setcookie("eventlog_where", $objeventlog->where, 0, "/");
setcookie("eventlog_order_by", $objeventlog->order_by, 0, "/");

print($tplmarco->Template);

$dbCMS->close();


?>
