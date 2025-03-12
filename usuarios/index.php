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
			if (!$objCMSUser->checkPermission("Usuarios::lectura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

		      
			$result=$objusuarios->doformat("/usuarios/index.html",$_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
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
			if (!$objCMSUser->checkPermission("Usuarios::escritura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

				$objusuarios->enableTables(array("grupos","usuarios_grupos"));
			$objusuarios->fetch($_GET["idusuario"]);
			
			
			
			$objgrupos = new grupos();
			$objgrupos->db = $objusuarios->db;
			$objgrupos->where="idgrupo not in (select idgrupo from usuarios_grupos where idusuario=".$_GET["idusuario"].")";
			$result = $objusuarios->doformat("/usuarios/edicion.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			$result=$objgrupos->doformatall($result);
			$dataArr[''] = Array();
			$dataArr['texto']="Accion realizada correctamente";
			$dataArr['estado']=1;
			$dataArr['resultado']=$result;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;
	
		case "borrar":
			if (!$objCMSUser->checkPermission("Usuarios::escritura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

			$objusuarios->debug=1;
			$objusuarios->fetch($_GET["idusuario"]);
			$objusuarios->delete();
			
			$objusuarios_grupos = new usuarios_grupos();
			$objusuarios_grupos->db = $objusuarios->db;
			//$objusuarios_grupos->debug=1;
			$objusuarios_grupos->where ="usuarios_grupos.idusuario=".$_GET["idusuario"];
			
			foreach ($objusuarios_grupos->fetchall() as $row)
			{
// 				$objusuarios_grupos->fetch($row['id']);
				$objusuarios_grupos->delete();
				
			}				
			addLog("alerta","ha borrado el Usuario #".$objusuarios->ID());

			header("Content-Type: text/html;charset=iso-8859-1");
			echo "00000000000";
			
			exit(0);
			break;
			break;

		case "grabar":
			if (!$objCMSUser->checkPermission("Usuarios::escritura")) 
				return handleevent("permission_denied", $objusuarios, $objCMSUser);
			$dataArr[''] = Array();
// 			echo "grabar";
// 			$objusuarios->field("imagen", $carpeta);
			$objusuarios->field("idusuario", $_GET["idusuario"]);
			$objusuarios->field("username", $_GET["username"]);
			$objusuarios->field("password", $_GET["password"]);
			$objusuarios->field("nombre", $_GET["nombre"]);
			$objusuarios->field("apellido", $_GET["apellido"]);
			$objusuarios->field("email", $_GET["email"]);
			$objusuarios->store();
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha grabado Usuarios".$objusuarios->ID());
			$dataArr['texto']="El usuario se grabo correctamente";
			$dataArr['estado']=1;
			
			header('Content-Type: application/json');
			 echo json_encode($dataArr);
			exit(0);
			break;
		case "check":
				
			$dataArr[''] = Array();
				
				$dataArr["permiso"]=$objCMSUser->checkPermission($_GET["nombre"]);
				header('Content-Type: application/json');
			 echo json_encode($dataArr);
						
				exit();
				break;
		case "lista_json_chat":
					if (!$objCMSUser->checkPermission("Usuarios::lectura"))
						return handleevent("permission_denied", $objusuarios, $objCMSUser);
					
					$objusuarios_cuenta = new usuarios_cuenta();
					$objusuarios_cuenta->db = $objusuarios->db;
		
					$objusuarios_cuenta->enableTables(array("usuarios"));
					
					$dataArr= Array();
					
					$objusuarios_cuenta->where="1 AND idcuenta=".$_SESSION["USER"]["ACCOUNT"]["ID"];
			
					foreach ($objusuarios_cuenta->fetchall() as $row)
					  { 
						$r = Array();
						$r['nombre'] = $row['usuario']['nombre'];
						$r['apellido'] = $row['usuario']['apellido'];
						$r['username'] = $row['usuario']['username'];			    
						$r['email'] = $row['usuario']['email'];
						$r['celular'] = $row['usuario']['celular'];
						
						$dataArr[] = $r;
					  }						
					  
					 header('Content-Type: application/json');
					 echo json_encode($dataArr);
							
			exit();
		break;
		case "lista_json":
			if (!$objCMSUser->checkPermission("Usuarios::lectura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);
			

			$objusuarios_cuenta = new usuarios_cuenta();
			$objusuarios_cuenta->db = $objusuarios->db;
			//$objusuarios_cuenta->debug=1;
			$objusuarios_cuenta->enableTables(array("usuarios"));
			
			$dataArr[''] = Array();
			$dataArr['recordsTotal']=$objusuarios_cuenta->count();
			
			$objusuarios_cuenta->limit_from=$_GET['offset'];
			$objusuarios_cuenta->limit_count=$_GET['limit'];
			if ($_GET['order']=='desc')
			$_GET['order']="DESC";
			else 
			$_GET['order']	="ASC";
			
			$objusuarios_cuenta->where="1 AND idcuenta=".$_SESSION["USER"]["ACCOUNT"]["ID"];
			if (isset($_GET["sort"]) && isset($_GET["order"]))
			$objusuarios_cuenta->order_by=$_GET['sort'] ." ". $_GET['order'];
			if (isset($_GET["limit"]))
			$objusuarios_cuenta->limit_count=$_GET['limit'];
			if (isset($_GET["search"]))
			$objusuarios_cuenta->where.=" AND username LIKE '%".$_GET["search"]."%'";

			$dataArr['recordsFiltered']=$objusuarios_cuenta->count();
			$dataArr['rows'][]='';
			foreach ($objusuarios_cuenta->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row['usuario']['nombre'];
			    $r['apellido'] = $row['usuario']['apellido'];
			    $r['username'] = $row['usuario']['username'];			    
			    $r['email'] = $row['usuario']['email'];
				$r['herramientas'] = "
				<button class='btn btn-success btn-xs' id='editar' title='Editar' value='".$row['usuario']['idusuario']."'>
				<img src='../img/icons/edit.png'>
				</button>
				<button class='btn btn-danger btn-xs' id='borrar' title='Borrar' value='".$row['usuario']['idusuario']."'>
				<img src='../img/icons/delete.png'>
				</button>
					";
			    
			    $dataArr['data'][] = $r;
			  }						
			  
			 header('Content-Type: application/json');
			 echo json_encode($dataArr);
				    
			exit();
			break;
		
		case "grupo_grabar":
			if (!$objCMSUser->checkPermission("Usuarios::escritura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

			//if (!$objusuarios->ID()) handleevent("grabar", $objusuarios, $objCMSUser);

			foreach ($_GET['idgrupos_agregar'] as $id)
			{
			  $objusuarios_grupos = new usuarios_grupos();
			  $objusuarios_grupos->db = $objusuarios->db;
			  $objusuarios_grupos->field("idgrupo",  $id);
			  $objusuarios_grupos->field("idusuario", $_GET["idusuario"]);
			  $objusuarios_grupos->store();

			}
			/******************POST SAVE******************/
			$objgrupos = new grupos();
			$objgrupos->db = $objusuarios->db;
			$objgrupos->where="idgrupo not in (select idgrupo from usuarios_grupos where idusuario=".$_GET["idusuario"].")";
			$dataArr = Array();
			foreach ($objgrupos->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row['nombre'];
				$r['idgrupo'] = $row['idgrupo'];

			    $dataArr['gruposDisponibles'][] = $r;
			  }

			$objusuarios_grupos = new usuarios_grupos();
			$objusuarios_grupos->db = $objusuarios->db;
			$objusuarios_grupos->enableTables(array("grupos"));
			$objusuarios_grupos->where="idusuario=".$_GET["idusuario"];
			
			foreach ($objusuarios_grupos->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row['grupo']['nombre'];
				$r['idgrupo'] = $row['id'];

			    $dataArr['gruposAgregados'][] = $r;
			  }
			  header('Content-Type: application/json');
			  echo json_encode($dataArr);
		
			exit(0);
			
			break;

		case "grupo_borrar":
			if (!$objCMSUser->checkPermission("Usuarios::escritura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

				
				foreach ($_GET['grupos_permisos'] as $id)
				{
					$objusuarios_Grupos = new usuarios_grupos();
					$objusuarios_Grupos->db = $objusuarios->db;
					$objusuarios_Grupos->ID($id);
					$objusuarios_Grupos->delete();
				}

				$objgrupos = new grupos();
				$objgrupos->db = $objusuarios->db;
				$objgrupos->where="idgrupo not in (select idgrupo from usuarios_grupos where idusuario=".$_GET["idusuario"].")";
				$dataArr = Array();
				foreach ($objgrupos->fetchall() as $row)
				  { 
					$r = Array();
					$r['nombre'] = $row['nombre'];
					$r['idgrupo'] = $row['idgrupo'];
	
					$dataArr['gruposDisponibles'][] = $r;
				  }
	
				$objusuarios_grupos = new usuarios_grupos();
				$objusuarios_grupos->db = $objusuarios->db;
				$objusuarios_grupos->enableTables(array("grupos"));
				$objusuarios_grupos->where="idusuario=".$_GET["idusuario"];
				
				foreach ($objusuarios_grupos->fetchall() as $row)
				  { 
					$r = Array();
					$r['nombre'] = $row['grupo']['nombre'];
					$r['idgrupo'] = $row['id'];
	
					$dataArr['gruposAgregados'][] = $r;
				  }
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
			return handleevent("index", $objusuarios, $objCMSUser);
	}
	
}
session_start();

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





$tplmarco->setVar("{page.title}","IOT Usuarios - Quodii SAS");
$tplmarco->setVar("{page.header}",$tplheader->Template);
$tplmarco->setVar("{page.sider}",$tplsider->Template);

$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"], $objusuarios, $objCMSUser));

$tplmarco->setVar("{page.footer}",$tplfooter->Template);

print($tplmarco->Template);

$dbCMS->close();


?>
