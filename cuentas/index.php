<?PHP

include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");

function handleevent($event, &$objusuarios_cuenta, &$objCMSUser) 
{

	switch ($event) {
		case "index":
			if (!$objCMSUser->checkPermission("Cuentas::lectura"))
				return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);

		      
			$result=$objusuarios_cuenta->doformat("/cuentas/index.html",$_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			
			return $result;
			break;
		case "form":
			if (!$objCMSUser->checkPermission("Cuentas::escritura"))
				return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);

			$objusuarios_cuenta->enableTables(array("grupos","usuarios_grupos"));
			$objusuarios_cuenta->fetch($_GET["idcuenta"]);
			
			
			
			
			$result = $objusuarios_cuenta->doformat("/cuentas/edicion.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			
			$dataArr = Array();
			$dataArr['texto']="Accion realizada correctamente";
			$dataArr['estado']=1;
			$dataArr['resultado']=$result;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;
		
		
		case "checkaccount":
			if (!$objCMSUser->checkPermission("Cuentas::lectura"))
			return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);
				$dataArr[''] = Array();
			
				$objusuarios_cuenta->fetchby("username",$_GET["username"]);
					
				if ($objusuarios_cuenta->ID()=='')
				{
					$dataArr["resultado"]="Usuario correcto";
					$dataArr["div"]="alert alert-success";
				}
				else
				{
					$dataArr["resultado"]="El usuario ya existe";
					$dataArr["div"]="alert alert-danger";

				}
					header('Content-Type: application/json');
				 echo json_encode($dataArr);
							
					exit();
					break;
		case "borrar":
			if (!$objCMSUser->checkPermission("Cuentas::escritura"))
				return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);


			$objusuarios_cuenta->fetch($_GET["idcuenta"]);
			$objusuarios_cuenta->delete();
			
			addLog("alerta","ha borrado la cuentao #".$objusuarios_cuenta->ID());
			$dataArr[''] = Array();
			$dataArr['texto']="Accion realizada correctamente";
			$dataArr['estado']=1;
			$dataArr['resultado']=$result;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;
		case "changeTO":
				if (!$objCMSUser->checkPermission("Cuentas::lectura"))
					return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);
	
	
				$_SESSION["USER"]["ACCOUNT"]["ID"]=$_GET["idcuenta"];
				
				$dataArr[''] = Array();
				$dataArr['texto']="Accion realizada correctamente";
				$dataArr['estado']=1;
				$dataArr['resultado']=$result;
				header('Content-Type: application/json');
				echo json_encode($dataArr);
				exit(0);
				break;
		case "grabar":
			if (!$objCMSUser->checkPermission("Cuentas::escritura")) 
				return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);
			$dataArr[''] = Array();
			$objusuarios_cuenta->fetch($_GET["idcuenta"]);
			$objusuarios_cuenta->field("username", $_GET["username"]);
			
			$objusuarios_cuenta->field("nombre", $_GET["nombre"]);
			$objusuarios_cuenta->field("owner", "true");
			$objusuarios_cuenta->field("defecto", "false");
			if ($objusuarios_cuenta->checkPermissionAccount($_SESSION["usuarios_idusuario"]) )
			$objusuarios_cuenta->store();
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha grabado Cuenta".$objusuarios_cuenta->ID());
			$dataArr['texto']="La cuenta se grabo correctamente";
			$dataArr['estado']=1;
			
			header('Content-Type: application/json');
			 echo json_encode($dataArr);
			exit(0);
			break;
		case "lista_inicio_json":
				if (!$objCMSUser->checkPermission("Cuentas::lectura"))
					return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);
					
			
				//$objusuarios_cuenta->debug=1;
				
				$objusuarios_cuenta->enableTables(array("usuarios"));
				$dataArr= Array();
				$objusuarios_cuenta->where="1 AND idusuario=".$_SESSION["ACCOUNT"]["ID"];


				foreach ($objusuarios_cuenta->fetchall() as $row)
				  { 
					$r = Array();
					$r['id'] = $row["usuario"]['idusuario'];
					$r['nombre'] = $row["usuario"]['nombre'];
					$r['username'] = $row["usuario"]['username'];					
					if ($row["usuario"]['idusuario']==$_SESSION["ACCOUNT"]["ID"]) 					
					$r['usethis'] =true;		
					else
					$r['usethis'] =false;		
					$dataArr['resultado'][] = $r;
				  }						
				  
				 header('Content-Type: application/json');
				 echo json_encode($dataArr);
						
				exit();
				break;
		case "lista_json":
			if (!$objCMSUser->checkPermission("Cuentas::lectura"))
				return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);
			



			$objusuarios_cuenta->enableTables(array("usuarios"));
			$dataArr[''] = Array();
			$objusuarios_cuenta->limit_from=$_GET['offset'];
			$objusuarios_cuenta->limit_count=$_GET['limit'];
			//$objusuarios_cuentas->debug=1;
			if ($_GET['order']=='desc')
			$_GET['order']="DESC";
			else 
			$_GET['order']	="ASC";
			$objusuarios_cuenta->where="1 AND idusuario=".$_SESSION["usuarios_idusuario"];
			if (isset($_GET["sort"]) && isset($_GET["order"]))
			$objusuarios_cuenta->order_by=$_GET['sort'] ." ". $_GET['order'];
			if (isset($_GET["limit"]))
			$objusuarios_cuenta->limit_count=$_GET['limit'];
			if (isset($_GET["search"]))
			$objusuarios_cuenta->where.="  cuentas.username LIKE '%".$_GET["search"]."%'";
			$dataArr['recordsFiltered']=$objusuarios_cuenta->count();
			$dataArr['rows'][]='';
			foreach ($objusuarios_cuenta->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row["usuario"]['fullname'];
			    $r['username'] = $row["usuario"]['username'];
				if ($row["usuario"]['idusuario']==$_SESSION["USER"]["ACCOUNT"]["ID"])
				$usethis="<button class='btn btn-success btn-xs'  title='Activa' value='".$row["usuario"]['idusuario']."'>
				<img src='../img/icons/on.png'>
				</button>";
				else
				$usethis="<button class='btn btn-secondary btn-xs' id='usethis' title='Activar' value='".$row["usuario"]['idusuario']."'>
				<img src='../img/icons/off.png'>
				</button>";
				if ($row['defecto']=="true")
				$default="<button class='btn btn-success btn-xs'  title='Default' value='".$row["usuario"]['idusuario']."'>
				<img src='../img/icons/on.png'>
				</button>";
				else
				$default="<button class='btn btn-secondary btn-xs' id='setdefault' title='Set Default' value='".$row["usuario"]['idusuario']."'>
				<img src='../img/icons/off.png'>
				</button>";
				$r['usethis'] =$usethis;
				$r['default'] =$default;
				
				

				if ($row["owner"]=="true")
				{
				$r['herramientas'] = "
				<button class='btn btn-success btn-xs' id='editar' title='Editar' value='".$row["cuenta"]['id']."'>
				<img src='../img/icons/edit.png'>
				</button>
				<button class='btn btn-danger btn-xs' id='borrar' title='Borrar' value='".$row["cuenta"]['id']."'>
				<img src='../img/icons/delete.png'>
				</button>
					";
				}
				else
				$r['herramientas'] = "";
			    
			    $dataArr['data'][] = $r;
			  }						
			  
			 header('Content-Type: application/json');
			 echo json_encode($dataArr);
				    
			exit();
			break;
		case "setdefault":
				if (!$objCMSUser->checkPermission("Cuentas::escritura"))
					return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);
				
	
				$objusuarios_cuenta = new usuarios_cuenta();
				$objusuarios_cuenta->db = $objcuenta->db;
				//$objusuarios_cuentas->debug=1;
						
				$objusuarios_cuenta->where="1 AND idusuario=".$_SESSION["usuarios_idusuario"];
				foreach ($objusuarios_cuenta->fetchall() as $row)
				  { 
					$objusuarios_cuenta->fetch($row['id']);
					if ($row['idcuenta']==$_GET['idcuenta'])
					{
						$objusuarios_cuenta->field("defecto", "true");
						$objusuarios_cuenta->store();
					}
					else
					{
					$objusuarios_cuenta->field("defecto", "false");
					$objusuarios_cuenta->store();
					}
				  }						
				  
				 header('Content-Type: application/json');
				 echo json_encode($_GET['idcuenta']);
						
				exit();
			break;
		case "usethis":
				if (!$objCMSUser->checkPermission("Cuentas::escritura"))
					return handleevent("permission_denied", $objusuarios_cuenta, $objCMSUser);
				
	
		
			
				$objusuarios_cuenta->enableTables(array("usuarios"));			
				$objusuarios_cuenta->where="1 AND idusuario=".$_SESSION["usuarios_idusuario"];
				foreach ($objusuarios_cuenta->fetchall() as $row)
				  { 
					
					if ($row['usuario']['idcuenta']==$_GET['idcuenta'])
					{
					//$_SESSION["USER"]["ACCOUNT"]["ID"]=$row['usuario']["idcuenta"];
					$_SESSION["ACCOUNT"]["ID"]=$row['usuario']["idcuenta"];
					$_SESSION["ACCOUNT"]["NAME"]=$row['usuario']["username"];
					}
				  }						
				  
				 header('Content-Type: application/json');
				 echo json_encode($_SESSION["USER"]["ACCOUNT"]["ID"]);
						
				exit();
			break;

	      case "permission_denied":
			$result = $objusuarios_cuenta->doformat("/permiso.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			$dataArr= Array();
			$dataArr['resultado']=$result;
			$dataArr['estado']=0;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;

		default:
			return handleevent("index", $objusuarios_cuenta, $objCMSUser);
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


$objusuarios_cuenta = new usuarios_cuenta();
$objusuarios_cuenta->db = $dbCMS;


$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);





$tplmarco->setVar("{page.title}","IOT Cuentas - Quodii SAS");
$tplmarco->setVar("{page.header}",$tplheader->Template);
$tplmarco->setVar("{page.sider}",$tplsider->Template);

$tplmarco->setVar("{page.contenido}", handleevent($_GET["event"], $objusuarios_cuenta, $objCMSUser));

$tplmarco->setVar("{page.footer}",$tplfooter->Template);

print($tplmarco->Template);

$dbCMS->close();


?>
