<?PHP

include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/WhatsAppSender.php");
//require 'WhatsAppSender.php';

// Instancia de la clase WhatsAppSender

function handleevent($event, &$objclientes, &$objCMSUser,$dbCMS) 
{
	switch ($event) {
		case "index":
			if (!$objCMSUser->checkPermission("Usuarios::lectura"))
				return handleevent("permission_denied", $objclientes, $objCMSUser);

		      
			$result=$objclientes->doformat("/clientes/index.html",$_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			$tpllist = new Template($result);
			$tpllist->setVar("{filtro_nombre}",$_SESSION['filter_nombre']);			
			$tpllist->setVar("{filtro_apellido}",$_SESSION['filter_apellido']);
			$tpllist->setVar("{filtro_username}",$_SESSION['filter_username']);
			$tpllist->setVar("{filtro_email}",$_SESSION['filter_email']);
			$tpllist->setVar("{filtro_telefono}",$_SESSION['filter_telefono']);
			
			$result = $tpllist->Template;
			return $result;
			break;
		case "sender":
				
			$objchats = new chats();
			$objchats->setDB($dbCMS);
			$idcliente = $_POST['idcliente'];
			$telefono = $_POST['telefono'];
			$mensaje = $_POST['mensaje'];
			$whatsapp = new WhatsAppSender();
			// Validar los datos (ejemplo básico)
			if (empty($idcliente) || empty($telefono) || empty($mensaje)) {
				http_response_code(400); // Bad Request
				echo json_encode(["error" => "Datos incompletos"]);
				exit;
			}
			//idchat 	idcliente 	mensaje 	original_idchat 	timestamp 	visto 	sender 	
			$objchats->field("idcliente", $_POST["idcliente"]);
			$objchats->field("mensaje", $_POST["mensaje"]);
			$objchats->field("original_idchat", "549232");
			$objchats->field("visto", 1);
			$objchats->field("sender", "assistant");
			
			$whatsapp = new WhatsAppSender();
			$result = $whatsapp->sendMessage($telefono, $mensaje);

			if ($result['success']) 
			$objchats->store();
				  
				
				
				
			header('Content-Type: application/json');
			echo json_encode($result);
				break;	
		case "form":
			if (!$objCMSUser->checkPermission("Usuarios::escritura"))
				return handleevent("permission_denied", $objclientes, $objCMSUser);

				$objclientes->enableTables(array("grupos","usuarios_grupos"));
			$objclientes->fetch($_GET["idusuario"]);
			
			
			
			$objgrupos = new grupos();
			$objgrupos->db = $objclientes->db;
			$objgrupos->where="idgrupo not in (select idgrupo from usuarios_grupos where idusuario=".$_GET["idusuario"].")";
			$result = $objclientes->doformat("/clientes/edicion.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
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
				return handleevent("permission_denied", $objclientes, $objCMSUser);

			$objclientes->debug=1;
			$objclientes->fetch($_GET["idusuario"]);
			$objclientes->delete();
			
			$objclientes_grupos = new usuarios_grupos();
			$objclientes_grupos->db = $objclientes->db;
			//$objclientes_grupos->debug=1;
			$objclientes_grupos->where ="usuarios_grupos.idusuario=".$_GET["idusuario"];
			
			foreach ($objclientes_grupos->fetchall() as $row)
			{
// 				$objclientes_grupos->fetch($row['id']);
				$objclientes_grupos->delete();
				
			}				
			addLog("alerta","ha borrado el Usuario #".$objclientes->ID());

			header("Content-Type: text/html;charset=iso-8859-1");
			echo "00000000000";
			
			exit(0);
			break;
			break;

		case "grabar":
			if (!$objCMSUser->checkPermission("Usuarios::escritura")) 
				return handleevent("permission_denied", $objclientes, $objCMSUser);
			$dataArr[''] = Array();
// 			echo "grabar";
// 			$objclientes->field("imagen", $carpeta);
			$objclientes->field("idusuario", $_GET["idusuario"]);
			$objclientes->field("username", $_GET["username"]);
			$objclientes->field("password", $_GET["password"]);
			$objclientes->field("nombre", $_GET["nombre"]);
			$objclientes->field("apellido", $_GET["apellido"]);
			$objclientes->field("email", $_GET["email"]);
			$objclientes->store();
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha grabado Usuarios".$objclientes->ID());
			$dataArr['texto']="El usuario se grabo correctamente";
			$dataArr['estado']=1;
			
			header('Content-Type: application/json');
			 echo json_encode($dataArr);
			exit(0);
			break;

		case "lista_json_chat":
					
					
					
					
					$dataArr= Array();
					
					$objclientes->where="1";
			
					foreach ($objclientes->fetchall() as $row)
					  { 
						$r = Array();
						$r['nombre'] = $row['nombre'];
						$r['apellido'] =$row['apellido'];
						$r['telefono'] = $row['telefono'];			    
						$r['direccion'] = $row['direccion'];
						$r['idcliente'] = $row['idcliente'];
						
						$dataArr[] = $r;
					  }						
					  
					 header('Content-Type: application/json');
					 echo json_encode($dataArr);
							
			exit();
			case "chats":
					
					
					
				$objchats = new chats();
				$objchats->setDB($dbCMS);
				$dataArr= Array();
				
				$objchats->where="idcliente=".$_GET["idcliente"];
		
				foreach ($objchats->fetchall() as $row)
				  { 
					// idchat 	idcliente 	mensaje 	original_idchat 	timestamp 	visto 	sender 	
					$r = Array();
					$r['idchat'] = $row['idchat'];
					$r['idcliente'] = $row['idcliente'];
					$r['mensaje'] = $row['mensaje'];
					$r['original_idchat'] =$row['original_idchat'];
					$r['timestamp'] = $row['timestamp'];			    
					$r['visto'] = $row['visto'];
					$r['sender'] = $row['sender'];
					
					
					$dataArr[] = $r;
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
			return handleevent("index", $objclientes, $objCMSUser,$dbCMS);
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

$objclientes = new clientes();

$objclientes->setDB($dbCMS);

$objCMSUser = new usuarios();
$objCMSUser->setDB($dbCMS);
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);





$tplmarco->setVar("{page.title}","IOT Usuarios - Quodii SAS");
$tplmarco->setVar("{page.header}",$tplheader->Template);
$tplmarco->setVar("{page.sider}",$tplsider->Template);

$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"], $objclientes, $objCMSUser,$dbCMS));

$tplmarco->setVar("{page.footer}",$tplfooter->Template);

print($tplmarco->Template);

$dbCMS->close();


?>
