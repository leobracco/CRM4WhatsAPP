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
$objinsumos = new insumos();
$objinsumos->setDB($dbCMS);

$objCMSUser = new usuarios();
$objCMSUser->setDB($dbCMS);
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);

/**
 * Maneja los eventos según la solicitud del usuario
 */
function handleevent($event, &$objinsumos, &$objCMSUser, $dbCMS) {
    switch ($event) {
        case "index":
            if (!$objCMSUser->checkPermission("Insumos::lectura")) {
                return handleevent("permission_denied", $objinsumos, $objCMSUser, $dbCMS);
            }

            $result = $objinsumos->doformat("/insumos/index.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
            
            return $result;
			break;
        
        case "form":
            if (!$objCMSUser->checkPermission("Insumos::lectura")) {
                return handleevent("permission_denied", $objinsumos, $objCMSUser, $dbCMS);
            }

            $objinsumos->fetch($_GET["idcliente"]);
            $dataArr = [
                "nombre" => $objinsumos->values['nombre'],
                "apellido" => $objinsumos->values['apellido'],
                "direccion" => $objinsumos->values['direccion'],
                "email" => $objinsumos->values['email']
            ];

            header('Content-Type: application/json');
            echo json_encode($dataArr);
            exit;

        case "borrar":
            if (!$objCMSUser->checkPermission("Insumos::borrar")) {
                return handleevent("permission_denied", $objinsumos, $objCMSUser, $dbCMS);
            }

            $objinsumos->fetch($_GET["idcliente"]);
           
			$dataArr = [
                "action" => "borrar",
				"type" => "borrar",
				"nombre" => $objinsumos->values['apellido'],
                "apellido" => $objinsumos->values['apellido'],
                "direccion" => $objinsumos->values['direccion'],
                "email" => $objinsumos->values['email']
            ];
            addLog("alerta", "Cliente eliminado: " . $dataArr);
			
			$objinsumos->delete();
            header('Content-Type: text/html;charset=iso-8859-1');
            echo $dataArr;
            exit;

        case "grabar":
            if (!$objCMSUser->checkPermission("Insumos::grabar")) {
                return handleevent("permission_denied", $objinsumos, $objCMSUser, $dbCMS);
            }

            $objinsumos->fetch($_POST["idcliente"]);
            foreach (['nombre', 'apellido', 'email', 'telefono', 'direccion'] as $campo) {
                $objinsumos->field($campo, $_POST[$campo]);
            }
            $objinsumos->store();

            $dataArr = ["texto" => "El usuario se grabó correctamente", "estado" => 1];

            header('Content-Type: application/json');
            echo json_encode($dataArr);
            exit;

     
		case "lista_json":
					if (!$objCMSUser->checkPermission("Insumos::lectura")) {
						return handleevent("permission_denied", $objinsumos, $objCMSUser, $dbCMS);
					}
		
			
	
				$dataArr[''] = Array();
				$dataArr['recordsTotal']=$objinsumos->count();
				
				$objinsumos->limit_from=$_GET['offset'];
				$objinsumos->limit_count=$_GET['limit'];
				if ($_GET['order']=='desc')
				$_GET['order']="DESC";
				else 
				$_GET['order']	="ASC";
				$objinsumos->where=1;
				if (isset($_GET["sort"]) && isset($_GET["order"]))
				$objinsumos->order_by=$_GET['sort'] ." ". $_GET['order'];
				if (isset($_GET["limit"]))
				$objinsumos->limit_count=$_GET['limit'];
				if (isset($_GET["search"]))
				$objinsumos->where.=" AND keyToken LIKE '%".$_GET["search"]."%'";
				$dataArr['recordsFiltered']=$objinsumos->count();
				
				foreach ($objinsumos->fetchall() as $row)
				  { 
					$r = Array();
					$r['nombre'] = $row['nombre'];
					$r['costo'] = $row['costo'];
					$r['stock'] = $row['stock'];
					$r['mayorista'] = $row['mayorista'] ? "Sí" : "No";
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
            case "listado":
					if (!$objCMSUser->checkPermission("Insumos::lectura")) {
						return handleevent("permission_denied", $objinsumos, $objCMSUser, $dbCMS);
					}
		
			
	
				$dataArr[''] = Array();
				$dataArr['recordsTotal']=$objinsumos->count();
				
				$objinsumos->limit_from=$_GET['offset'];
				$objinsumos->limit_count=$_GET['limit'];
				if ($_GET['order']=='desc')
				$_GET['order']="DESC";
				else 
				$_GET['order']	="ASC";
				$objinsumos->where=1;
				if (isset($_GET["sort"]) && isset($_GET["order"]))
				$objinsumos->order_by=$_GET['sort'] ." ". $_GET['order'];
				if (isset($_GET["limit"]))
				$objinsumos->limit_count=$_GET['limit'];
				if (isset($_GET["search"]))
				$search = isset($_GET["search"]) ? addslashes($_GET["search"]) : '';
$idproducto = isset($_GET["idproducto"]) ? intval($_GET["idproducto"]) : 0;

$objinsumos->where .= " AND nombre LIKE '%{$search}%' AND idinsumo NOT IN (SELECT idinsumo FROM producto_insumo WHERE idproducto = {$idproducto})";

                
				$dataArr['recordsFiltered']=$objinsumos->count();

				foreach ($objinsumos->fetchall() as $row)
				  { 
					$r = Array();
                    $r['id'] = $row['idinsumo'];
					$r['nombre'] = $row['nombre'];
					$r['costo'] = $row['costo'];
					$r['stock'] = $row['stock'];
					$r['idinsumo'] = $row['idinsumo'];
					
					
					$dataArr['data'][] = $r;
				  }						
				  
				 header('Content-Type: application/json');
				 echo json_encode($dataArr);
						
				exit();
				break;

        case "permission_denied":
            header('Content-Type: application/json');
            echo json_encode(["texto" => "No tiene permisos para esta acción", "estado" => 0]);
            exit;

        default:
            return handleevent("index", $objinsumos, $objCMSUser, $dbCMS);
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
$tplmarco->setVar("{page.title}", "Insumos - ".$_SESSION["EMPRESA"]["NAME"]);
$tplmarco->setVar("{page.header}", $tplheader->Template);
$tplmarco->setVar("{page.sider}", $tplsider->Template);
$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"] ?? "index", $objinsumos, $objCMSUser, $dbCMS));
$tplmarco->setVar("{page.footer}", $tplfooter->Template);

// Mostrar la página
print($tplmarco->Template);

// Cerrar conexión a la base de datos
$dbCMS->close();
?>
