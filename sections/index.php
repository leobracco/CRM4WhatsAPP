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
		case "nodos":
			if (!$objCMSUser->checkPermission("Usuarios::escritura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);


			$result = $objusuarios->doformat("/partials/".$_GET["section"].".html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			$dataArr[''] = Array();
			$dataArr['texto']="Accion realizada correctamente";
			$dataArr['estado']=1;
			$dataArr['resultado']=$result;
			header('Content-Type: application/json');
			echo json_encode($dataArr);
			exit(0);
			break;
		case "usuarios_grupos":
			if (!$objCMSUser->checkPermission("Usuarios::lectura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);
			$objusuarios->fetch($_GET["idusuario"]);
			//echo $_POST['idusuario']."----<br>";
			//$objusuarios->debug=1;
			$objusuarios->enableTables(array("grupos"));
			
			
			$result=$objusuarios->doformat("/usuarios/grupos.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			

			header("Content-Type: text/html;charset=iso-8859-1");
			echo $result;
				    
			exit();
			break;
		
		case "borrar":
			if (!$objCMSUser->checkPermission("Usuarios::escritura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

			$objusuarios->debug=1;
			$objusuarios->fetch($_GET["idusuario"]);
			$objusuarios->delete();
			
			$objusuarios_grupos = new usuarios_grupos();
			$objusuarios_grupos->db = $objusuarios->db;
			$objusuarios_grupos->debug=1;
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

		case "list":
			if (!$objCMSUser->checkPermission("Usuarios::lectura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);
	      
			$objusuarios->order_by = "apellido, nombre";
			
			$objusuarios->where=1;			
			$result=$objusuarios->doformat("/usuarios/lista.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			header("Content-Type: text/html;charset=iso-8859-1");
			echo $result;
				    
			exit();
			break;
		case "lista_json":
			if (!$objCMSUser->checkPermission("Usuarios::lectura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);
			
		

			$dataArr[''] = Array();
			$dataArr['recordsTotal']=$objusuarios->count();
			
			$objusuarios->limit_from=$_GET['offset'];
			$objusuarios->limit_count=$_GET['limit'];
			if ($_GET['order']=='desc')
			$_GET['order']="DESC";
			else 
			$_GET['order']	="ASC";
			$objusuarios->where=1;
			if (isset($_GET["sort"]) && isset($_GET["order"]))
			$objusuarios->order_by=$_GET['sort'] ." ". $_GET['order'];
			if (isset($_GET["limit"]))
			$objusuarios->limit_count=$_GET['limit'];
			if (isset($_GET["search"]))
			$objusuarios->where.=" AND username LIKE '%".$_GET["search"]."%'";
			$dataArr['recordsFiltered']=$objusuarios->count();
			$dataArr['rows'][]='';
			foreach ($objusuarios->fetchall() as $row)
			  { 
			    $r = Array();
			    $r['nombre'] = $row['nombre'];
			    $r['apellido'] = $row['apellido'];
			    $r['username'] = $row['username'];			    
			    $r['email'] = $row['email'];
			    $r["acciones"] = "
				      <a  onclick='Editar(".$row['idusuario'].")' >
					
					<ion-icon name='create'></ion-icon>	Editar			  
				      </a>
				      <a  onclick='Borrar(".$row['idusuario'].")' ><ion-icon name='trash'></ion-icon>Borrar</a>
				    ";
			    
			    $dataArr['rows'][] = $r;
			  }						
			  
			 header('Content-Type: application/json');
			 echo json_encode($dataArr);
				    
			exit();
			break;
		
		case "grupo_grabar":
			if (!$objCMSUser->checkPermission("Usuarios::escritura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

			//if (!$objusuarios->ID()) handleevent("grabar", $objusuarios, $objCMSUser);

			foreach ($_GET['idgrupos'] as $id)
			{
			  $objusuarios_Grupos = new usuarios_grupos();
			  $objusuarios_Grupos->db = $objusuarios->db;
			  $objusuarios_Grupos->field("idgrupo",  $id);
			  $objusuarios_Grupos->field("idusuario", $_GET["idusuario"]);
			  $objusuarios_Grupos->store();

			}
			header("Content-Type: text/html;charset=iso-8859-1");
			echo $_GET["idusuario"];
			exit(0);
			
			break;

		case "grupo_borrar":
			if (!$objCMSUser->checkPermission("Usuarios::escritura"))
				return handleevent("permission_denied", $objusuarios, $objCMSUser);

			
			  $objusuarios_Grupos = new usuarios_grupos();
			  $objusuarios_Grupos->db = $objusuarios->db;
			  $objusuarios_Grupos->ID($id);
			  $objusuarios_Grupos->delete();
			
			header("Content-Type: text/html;charset=iso-8859-1");
			echo $_GET["idusuario"];
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


if (!$_SESSION["usuarios_idusuario"]) {
    header("Location: ../inicio/");
    exit(0);
}

$dbCMS   = new dbMysql($_SESSION["DB"]["HOST"], $_SESSION["DB"]["NAME"], $_SESSION["DB"]["USER"], $_SESSION["DB"]["PASSWD"]);
$dbCMS->connect();


$objusuarios = new usuarios();
$objusuarios->db = $dbCMS;

$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);


handleevent($_GET["event"], $objusuarios, $objCMSUser);






$dbCMS->close();


?>
