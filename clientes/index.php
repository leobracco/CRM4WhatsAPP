<?php
// Cargar configuración y dependencias
include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/WhatsAppSender.php");

// Iniciar sesión y validar usuario
if (!isset($_SESSION["usuarios_idusuario"])) {
    header("Location: ../inicio/");
    exit;
}

// Conectar a la base de datos
$dbCMS = new dbMysql(
    $_SESSION["MYSQL"]["HOST"],
    $_SESSION["MYSQL"]["NAME"],
    $_SESSION["MYSQL"]["USER"],
    $_SESSION["MYSQL"]["PASSWORD"]
);
$dbCMS->connect();

// Crear instancias de objetos principales
$objclientes = new clientes();
$objclientes->setDB($dbCMS);

$objCMSUser = new usuarios();
$objCMSUser->setDB($dbCMS);
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);

/**
 * Maneja los eventos según la solicitud del usuario
 */
function handleevent($event, &$objclientes, &$objCMSUser, $dbCMS) {
    switch ($event) {
        case "index":
            if (!$objCMSUser->checkPermission("Clientes::lectura")) {
                return handleevent("permission_denied", $objclientes, $objCMSUser, $dbCMS);
            }

            $result = $objclientes->doformat("/clientes/index.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
            
            return $result;
			break;
        case "sender":
            if (!$objCMSUser->checkPermission("Clientes::enviar")) {
                return handleevent("permission_denied", $objclientes, $objCMSUser, $dbCMS);
            }

            date_default_timezone_set('America/Argentina/Buenos_Aires'); 
            $objchats = new chats();
            $objchats->setDB($dbCMS);

            $idcliente = $_POST['idcliente'] ?? null;
            $telefono  = $_POST['telefono'] ?? null;
            $mensaje   = $_POST['mensaje'] ?? null;

            if (!$idcliente || !$telefono || !$mensaje) {
                http_response_code(400);
                echo json_encode(["error" => "Datos incompletos"]);
                exit;
            }

            $objchats->field("idcliente", $idcliente);
            $objchats->field("mensaje", $mensaje);
            $objchats->field("original_idchat", null);
            $objchats->field("timestamp", date('Y-m-d H:i:s'));
            $objchats->field("visto", 1);
            $objchats->field("sender", "assistant");

            $whatsapp = new WhatsAppSender();
            $result = $whatsapp->sendMessage($telefono, $mensaje);

            if ($result['success']) {
                $objchats->store();
            }

            header('Content-Type: application/json');
            echo json_encode($result);
            exit;

        case "form":
            if (!$objCMSUser->checkPermission("Clientes::lectura")) {
                return handleevent("permission_denied", $objclientes, $objCMSUser, $dbCMS);
            }

            $objclientes->fetch($_GET["idcliente"]);
            $dataArr = [
                "nombre" => $objclientes->values['nombre'],
                "apellido" => $objclientes->values['apellido'],
                "direccion" => $objclientes->values['direccion'],
                "email" => $objclientes->values['email']
            ];

            header('Content-Type: application/json');
            echo json_encode($dataArr);
            exit;

        case "borrar":
            if (!$objCMSUser->checkPermission("Clientes::borrar")) {
                return handleevent("permission_denied", $objclientes, $objCMSUser, $dbCMS);
            }

            $objclientes->fetch($_GET["idcliente"]);
           
			$dataArr = [
                "action" => "borrar",
				"type" => "borrar",
				"nombre" => $objclientes->values['apellido'],
                "apellido" => $objclientes->values['apellido'],
                "direccion" => $objclientes->values['direccion'],
                "email" => $objclientes->values['email']
            ];
            addLog("alerta", "Cliente eliminado: " . $dataArr);
			
			$objclientes->delete();
            header('Content-Type: text/html;charset=iso-8859-1');
            echo $dataArr;
            exit;

        case "grabar":
            if (!$objCMSUser->checkPermission("Clientes::grabar")) {
                return handleevent("permission_denied", $objclientes, $objCMSUser, $dbCMS);
            }

            $objclientes->fetch($_POST["idcliente"]);
            foreach (['nombre', 'apellido', 'email', 'telefono', 'direccion'] as $campo) {
                $objclientes->field($campo, $_POST[$campo]);
            }
            $objclientes->store();

            $dataArr = ["texto" => "El usuario se grabó correctamente", "estado" => 1];

            header('Content-Type: application/json');
            echo json_encode($dataArr);
            exit;

        case "lista_json_chat":
            if (!$objCMSUser->checkPermission("Clientes::lectura")) {
                return handleevent("permission_denied", $objclientes, $objCMSUser, $dbCMS);
            }

            $chatClientes = new chat_clientes();
            $chatClientes->setDB($dbCMS);
            $clientes = $chatClientes->listarClientes();

            header('Content-Type: application/json');
            echo json_encode($clientes);
            exit;
		case "lista_json":
					if (!$objCMSUser->checkPermission("Clientes::lectura")) {
						return handleevent("permission_denied", $objclientes, $objCMSUser, $dbCMS);
					}
		
			
	
				$dataArr[''] = Array();
				$dataArr['recordsTotal']=$objclientes->count();
				
				$objclientes->limit_from=$_GET['offset'];
				$objclientes->limit_count=$_GET['limit'];
				if ($_GET['order']=='desc')
				$_GET['order']="DESC";
				else 
				$_GET['order']	="ASC";
				$objclientes->where=1;
				if (isset($_GET["sort"]) && isset($_GET["order"]))
				$objclientes->order_by=$_GET['sort'] ." ". $_GET['order'];
				if (isset($_GET["limit"]))
				$objclientes->limit_count=$_GET['limit'];
				if (isset($_GET["search"]))
				$objclientes->where.=" AND keyToken LIKE '%".$_GET["search"]."%'";
				$dataArr['recordsFiltered']=$objclientes->count();
				
				foreach ($objclientes->fetchall() as $row)
				  { 
					$r = Array();
					$r['nombre'] = $row['nombre'];
					$r['apellido'] = $row['apellido'];
					$r['username'] = $row['telefono'];
					$r['email'] = $row['email'];
					$r['herramientas'] = "
					<button class='btn btn-success btn-xs' id='editar' title='Editar' value='".$row['idcliente']."'>
					<img src='../img/icons/edit.png'>
					</button>
					<button class='btn btn-danger btn-xs' id='borrar' title='Borrar' value='".$row['idcliente']."'>
					<img src='../img/icons/delete.png'>
					</button>
						";
					
					$dataArr['data'][] = $r;
				  }						
				  
				 header('Content-Type: application/json');
				 echo json_encode($dataArr);
						
				exit();
				break;
        case "chats":
            if (!$objCMSUser->checkPermission("Clientes::lectura")) {
                return handleevent("permission_denied", $objclientes, $objCMSUser, $dbCMS);
            }

            $objchats = new chats();
            $objchats->setDB($dbCMS);
            $objchats->where = "idcliente=" . $_GET["idcliente"];
            $dataArr = $objchats->fetchall();

            header('Content-Type: application/json');
            echo json_encode($dataArr);
            exit;

        case "permission_denied":
            header('Content-Type: application/json');
            echo json_encode(["texto" => "No tiene permisos para esta acción", "estado" => 0]);
            exit;

        default:
            return handleevent("index", $objclientes, $objCMSUser, $dbCMS);
    }
}

// Cargar plantillas
$tplmarco = new Template("");
$tplmarco->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplmarco->Open("/marco.html");

$tplheader = new Template("");
$tplheader->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplheader->Open("/header.html");

$tplfooter = new Template("");
$tplfooter->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplfooter->Open("/footer.html");

$tplsider = new Template("");
$tplsider->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplsider->Open("/sider.html");

// Insertar contenido en la plantilla principal
$tplmarco->setVar("{page.title}", "Clientes - ".$_SESSION["EMPRESA"]["NAME"]);
$tplmarco->setVar("{page.header}", $tplheader->Template);
$tplmarco->setVar("{page.sider}", $tplsider->Template);
$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"] ?? "index", $objclientes, $objCMSUser, $dbCMS));
$tplmarco->setVar("{page.footer}", $tplfooter->Template);

// Mostrar la página
print($tplmarco->Template);

// Cerrar conexión a la base de datos
$dbCMS->close();
?>
