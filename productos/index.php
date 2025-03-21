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
$objproductos = new productos();
$objproductos->setDB($dbCMS);

$objCMSUser = new usuarios();
$objCMSUser->setDB($dbCMS);
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);

/**
 * Maneja los eventos según la solicitud del usuario
 */
function handleevent($event, &$objproductos, &$objCMSUser, $dbCMS) {
    switch ($event) {
        case "index":
            if (!$objCMSUser->checkPermission("Productos::lectura")) {
                return handleevent("permission_denied", $objproductos, $objCMSUser, $dbCMS);
            }

            $result = $objproductos->doformat("/productos/index.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
            
            return $result;
			break;
        
        case "form":
            if (!$objCMSUser->checkPermission("Productos::lectura")) {
                return handleevent("permission_denied", $objproductos, $objCMSUser, $dbCMS);
            }
            $objproductos->enableTables(array("insumos","producto_insumo"));
            $objproductos->fetch($_GET["idproducto"]);

            

			
            $dataArr['idproducto'] = $objproductos->values['idproducto'];
            $dataArr['nombre'] = $objproductos->values['nombre'];
            $dataArr['stock'] = $objproductos->values['stock'];
            $dataArr['idtipo'] = $objproductos->values['idtipo'];
            $dataArr['insumos']=$objproductos->getInsumos();
            //$dataArr["precio_minorista"] = $objproductos->values["precio_minorista"];
            //$dataArr["precio_mayorista"] = $objproductos->values["precio_mayorista"];
    

            header('Content-Type: application/json');
            echo json_encode($dataArr);
            exit;

        case "borrar":
            if (!$objCMSUser->checkPermission("Productos::borrar")) {
                return handleevent("permission_denied", $objproductos, $objCMSUser, $dbCMS);
            }

            $objproductos->fetch($_GET["idcliente"]);
           
			$dataArr = [
                "action" => "borrar",
				"type" => "borrar",
				"nombre" => $objproductos->values['apellido'],
                "apellido" => $objproductos->values['apellido'],
                "direccion" => $objproductos->values['direccion'],
                "email" => $objproductos->values['email']
            ];
            addLog("alerta", "Cliente eliminado: " . $dataArr);
			
			$objproductos->delete();
            header('Content-Type: text/html;charset=iso-8859-1');
            echo $dataArr;
            exit;

        case "grabar":
            if (!$objCMSUser->checkPermission("Productos::grabar")) {
                return handleevent("permission_denied", $objproductos, $objCMSUser, $dbCMS);
            }

            $objproductos->fetch($_POST["idporducto"]);
            foreach (['nombre', 'apellido', 'email', 'telefono', 'direccion'] as $campo) {
                $objproductos->field($campo, $_POST[$campo]);
            }
            $objproductos->store();

            $dataArr = ["texto" => "El producto se grabó correctamente", "estado" => 1];

            header('Content-Type: application/json');
            echo json_encode($dataArr);
            exit;
        case "borrarInsumo":
                if (!$objCMSUser->checkPermission("Productos::grabar")) {
                    return handleevent("permission_denied", $objproductos, $objCMSUser, $dbCMS);
                }
                $obproducto_insumo = new producto_insumo();
                $obproducto_insumo->setDB($dbCMS);
                
   
                $objproductos->fetch($_POST["idproducto"]);
                $obproducto_insumo->delete($_POST["idinsumo"]);
                $dataArr = ["texto" => "El Insumo se borro correctamente", "estado" => 1,"insumos" =>$objproductos->getInsumos()];
    
                header('Content-Type: application/json');
                echo json_encode($dataArr);
            exit;
        case "grabarInsumo":
                if (!$objCMSUser->checkPermission("Productos::grabar")) {
                    return handleevent("permission_denied", $objproductos, $objCMSUser, $dbCMS);
                }
                $obproducto_insumo = new producto_insumo();
                $obproducto_insumo->setDB($dbCMS);
                
                foreach (['idinsumo', 'idproducto', 'cantidad', 'mayorista'] as $campo) {
                    if ($campo === 'mayorista') {
                        $obproducto_insumo->field($campo, ($_POST[$campo] == 'true' || $_POST[$campo] == 1) ? 1 : 0);
                    } else {
                        $obproducto_insumo->field($campo, $_POST[$campo]);
                    }
                    if ($campo === 'idproducto')
                    $id=$_POST[$campo];
                }
                $obproducto_insumo->store();
                $objproductos->fetch($id);
                $dataArr = ["texto" => "El Insumo se grabó correctamente", "estado" => 1,"insumos" =>$objproductos->getInsumos()];
    
                header('Content-Type: application/json');
                echo json_encode($dataArr);
                exit;
            case "lista_json":
                if (!$objCMSUser->checkPermission("Productos::lectura")) {
                    return handleevent("permission_denied", $objproductos, $objCMSUser, $dbCMS);
                }
            
                $objproductos->enableTables(["insumos", "producto_insumo"]);
                $dataArr = [];
                $dataArr['recordsTotal'] = $objproductos->count();
            
                $objproductos->limit_from = $_GET['offset'];
                $objproductos->limit_count = $_GET['limit'];
                
                if ($_GET['order'] == 'desc') {
                    $_GET['order'] = "DESC";
                } else {
                    $_GET['order'] = "ASC";
                }
            
                $objproductos->where = "1";
                if (isset($_GET["sort"]) && isset($_GET["order"])) {
                    $objproductos->order_by = $_GET['sort'] . " " . $_GET['order'];
                }
                if (isset($_GET["limit"])) {
                    $objproductos->limit_count = $_GET['limit'];
                }
                if (isset($_GET["search"])) {
                    $objproductos->where .= " AND keyToken LIKE '%" . $_GET["search"] . "%'";
                }
            
                $dataArr['recordsFiltered'] = $objproductos->count();
            
                foreach ($objproductos->fetchall() as $row) {
                    $r = [];
            
                    // Asegurar que $row sea una instancia de productos
                    $producto = new productos();
                    $producto->setDB($dbCMS);
                    $producto->fetch($row[$objproductos->ID_field()]);
                    $insumos_here=$producto->getInsumos();
                    $r['id'] = $row['id'];
                    $r['nombre'] = $row['nombre'];
                    $r["precio_minorista"] = $insumos_here["precio_minorista"];
                    $r["precio_mayorista"] = $insumos_here["precio_mayorista"];
					$r['stock'] = $row['stock'];
					$r['herramientas'] = "
					<button class='btn btn-success btn-xs' id='editar' title='Editar' value='".$row['idproducto']."'>
					<img src='../img/icons/edit.png'>
					</button>
					<button class='btn btn-danger btn-xs' id='borrar' title='Borrar' value='".$row['idproducto']."'>
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
            header('Content-Type: application/json');
            echo json_encode(["texto" => "No tiene permisos para esta acción", "estado" => 0]);
            exit;

        default:
            return handleevent("index", $objproductos, $objCMSUser, $dbCMS);
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
$tplmarco->setVar("{page.title}", "Productos - ".$_SESSION["EMPRESA"]["NAME"]);
$tplmarco->setVar("{page.header}", $tplheader->Template);
$tplmarco->setVar("{page.sider}", $tplsider->Template);
$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"] ?? "index", $objproductos, $objCMSUser, $dbCMS));
$tplmarco->setVar("{page.footer}", $tplfooter->Template);

// Mostrar la página
print($tplmarco->Template);

// Cerrar conexión a la base de datos
$dbCMS->close();
?>