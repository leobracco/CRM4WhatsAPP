<?PHP

include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");
function tel_argentino($tel) {
    $re = '/^(?:((?P<p1>(?:\( ?)?+)(?:\+|00)?(54)(?<p2>(?: ?\))?+)(?P<sep>(?:[-.]| (?:[-.] )?)?+)(?:(?&p1)(9)(?&p2)(?&sep))?|(?&p1)(0)(?&p2)(?&sep))?+(?&p1)(11|([23]\d{2}(\d)??|(?(-10)(?(-5)(?!)|[68]\d{2})|(?!))))(?&p2)(?&sep)(?(-5)|(?&p1)(15)(?&p2)(?&sep))?(?:([3-6])(?&sep)|([12789]))(\d(?(-5)|\d(?(-6)|\d)))(?&sep)(\d{4})|(1\d{2}|911))$/D';
    if (preg_match($re,$tel,$match)) {
        //texto capturado por cada grupo -> variables individuales
        list(
            ,$internacional_completo,,$internacional,,,$internacional_celu,$prefijo_acceso,$area,,,
            $prefijo_celu,$local_1a,$local_1b,$local_1c,$local_2,$numero_social
        ) = array_pad($match,20,'');

        //arreglar un poco los valores
        $local_1 = $local_1a . $local_1b . $local_1c;
        $local = $local_1 . $local_2;
        $es_fijo = !($internacional_celu || $prefijo_celu);
        $numero = $area.$local.$numero_social;
        $completo = $internacional.$internacional_celu.$area.$prefijo_celu.$local.$numero_social;

        //devolver sólo lo que importa en un array
        return compact(
                   'numero','completo','internacional','internacional_celu','area',
                   'prefijo_celu','local','local_1','local_2','numero_social','es_fijo'
               );
    }
    return false;
}
function validar_clave($clave,&$error_clave){
	if(strlen($clave) < 6){
	   $error_clave = "La clave debe tener al menos 6 caracteres";
	   return false;
	}
	if(strlen($clave) > 16){
	   $error_clave = "La clave no puede tener más de 16 caracteres";
	   return false;
	}
	if (!preg_match('`[a-z]`',$clave)){
	   $error_clave = "La clave debe tener al menos una letra minúscula";
	   return false;
	}
	if (!preg_match('`[A-Z]`',$clave)){
	   $error_clave = "La clave debe tener al menos una letra mayúscula";
	   return false;
	}
	if (!preg_match('`[0-9]`',$clave)){
	   $error_clave = "La clave debe tener al menos un caracter numérico";
	   return false;
	}
	$error_clave = "";
	return true;
 }
function handleevent($event, &$objusuarios) 
{
	switch ($event) {
		case "index":
			
		
			$result=$objusuarios->doformat("/usuarios/index.html",$_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			
			return $result;
			break;
		case "checkaccountEmpresa":
					$dataArr[''] = Array();
					$objcuentas = new cuentas();
			  		$objcuentas->db = $objusuarios->db;
					$objcuentas->fetchby("username",$_GET["cuenta"]);
						
					if ($objcuentas->ID()=='')
					{
						$dataArr["resultado"]="Cuenta correcta";
						$dataArr["div"]="alert alert-success";
						$dataArr["valid"]=true;
					}
					else
					{
						$dataArr["resultado"]="La cuenta ya existe";
						$dataArr["div"]="alert alert-danger";
						$dataArr["valid"]=false;
					}
					header('Content-Type: application/json');
					header('Access-Control-Allow-Origin: *'); 
					echo json_encode($dataArr);
								
			exit();
		break;
		case "checkaccountUser":
			$dataArr[''] = Array();
	
			$objusuarios->fetchby("username",$_GET["username"]);
				
			if ($objusuarios->ID()=='')
			{
				$dataArr["resultado"]="Usuario correcto";
				$dataArr["div"]="alert alert-success";
				$dataArr["valid"]=true;
			}
			else
			{
				$dataArr["resultado"]="El usuario ya existe";
				$dataArr["div"]="alert alert-danger";
				$dataArr["valid"]=false;
			}
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
			 	echo json_encode($dataArr);
						
	exit();
	break;
	case "LoginUser":
		$dataArr = Array();
		$userArr = Array();
	//idusuario', 'username', 'password', 'email','nombre', 'apellido','celular','token');
		$objusuarios->fetchby("username",$_POST["username"]);
		
		if ($objusuarios->ID()!='')
		{
			if ($objusuarios->values["password"]==$_POST["password"])
			{
				$dataArr['username']=$objusuarios->values["username"];
				$dataArr['password']=$objusuarios->values["password"];
				$dataArr['email']=$objusuarios->values["email"];
				$dataArr['nombre']=$objusuarios->values["nombre"];
				$dataArr['apellido']=$objusuarios->values["apellido"];
				$dataArr['celular']=$objusuarios->values["celular"];
			
				$login=true;
			}
			else
			{
				$result="La combinacion es invalida";
				$login=false;
			}
		}
		else
		{
			$objusuarios->fetchby("email",$_POST["username"]);
			if ($objusuarios->ID()!='')
			{
				if ($objusuarios->values["password"]==$_POST["password"])
				{
					$dataArr['username']=$objusuarios->values["username"];
					$dataArr['password']=$objusuarios->values["password"];
					$dataArr['email']=$objusuarios->values["email"];
					$dataArr['nombre']=$objusuarios->values["nombre"];
					$dataArr['apellido']=$objusuarios->values["apellido"];
					$dataArr['celular']=$objusuarios->values["celular"];
					$login=true;
				}
				else
				{
					$result="La combinacion es invalida";
					$login=false;
				}
			}
			else
			{
				$result="El usuario no existe";
				$login=false;
			}
		
		}
		$dataArr["resultado"]=$result;
		$dataArr["div"]="alert alert-danger";
		$dataArr["valid"]=$login;
		header('Content-Type: application/json');
		header('Access-Control-Allow-Origin: *'); 
		echo json_encode($dataArr);
				
		exit();
		break;
		case "checkemail":
			$dataArr[''] = Array();
			
			if (filter_var($_GET["email"], FILTER_VALIDATE_EMAIL))
			{
				$objusuarios->fetchby("email",$_GET["email"]);	
				if ($objusuarios->ID()=='')
				{
					$dataArr["resultado"]="Usuario correcto";
					$dataArr["div"]="alert alert-success";
					$dataArr["valid"]=true;
				}
				else	
				{
					$dataArr["resultado"]="El usuario ya existe";
					$dataArr["div"]="alert alert-danger";
					$dataArr["valid"]=false;
				}
			}
			else
			{
				$dataArr["resultado"]="El email es invalido";
				$dataArr["div"]="alert alert-danger";
				$dataArr["valid"]=false;
			}

			
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
			 	echo json_encode($dataArr);
						
				exit();
		break;
		case "checkcelular":
			$dataArr[''] = Array();
			$celular=tel_argentino($_GET["celular"]);
			if ($celular)
			{
				$objusuarios->fetchby("celular",$celular["completo"]);	
				if ($objusuarios->ID()=='')
				{
					$dataArr["resultado"]="Celular correcto";
					$dataArr["div"]="alert alert-success";
					$dataArr["valid"]=true;
				}
				else	
				{
					$dataArr["resultado"]="El celular ya existe";
					$dataArr["div"]="alert alert-danger";
					$dataArr["valid"]=false;
				}
			}
			else
			{
				$dataArr["resultado"]="El celular es invalido, debe ser <br> Ej: 5491140350221<br>541140350221 <br>1140350221";
				$dataArr["div"]="alert alert-danger";
				$dataArr["valid"]=false;
			}

			
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
			 	echo json_encode($dataArr);
						
				exit();
		break;
		case "checkpassword":
			$dataArr[''] = Array();
			$error="";
			if (validar_clave($_GET["password"],$error))
			{
				
					$dataArr["resultado"]="Clave correcta";
					$dataArr["div"]="alert alert-success";
					$dataArr["valid"]=true;
				
			}
			else	
				{
					$dataArr["resultado"]=$error;
					$dataArr["div"]="alert alert-danger";
					$dataArr["valid"]=false;
				}

			
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
			 	echo json_encode($dataArr);
						
				exit();
		break;
		case "register_web":
			$dataArr = Array();
			$objusuarios->debug=1;
			$objusuarios->fetch(0);
			$celular=tel_argentino($_POST["celular"]);
			$objusuarios->field("username", $_POST["username"]);
			$objusuarios->field("password", $_POST["password"]);
			$objusuarios->field("nombre", $_POST["nombre"]);
			$objusuarios->field("apellido", $_POST["apellido"]);
			$objusuarios->field("email", $_POST["email"]);
			$objusuarios->field("celular",  $celular["completo"]);
			$objusuarios->store();
			
			$objusuarios_grupos = new usuarios_grupos();
			$objusuarios_grupos->db = $objusuarios->db;
			$objusuarios_grupos->fetch(0);
			$objusuarios_grupos->field("idusuario",$objusuarios->ID());
			$objusuarios_grupos->field("idgrupo",15);
			$objusuarios_grupos->store();
			
			$objsubscribe = new subscribe();
			$objsubscribe->db = $objusuarios->db;
			$objsubscribe->fetch(0);
			$objsubscribe->field("idusuario", $objusuarios->ID());
			$objsubscribe->field("idcuenta", $objusuarios->ID());
			$objsubscribe->store();

			$objsubscribe = new subscribe();
			$objsubscribe->db = $objusuarios->db;
			$objsubscribe->fetch(0);
			$objsubscribe->field("idusuario", $objusuarios->ID());
			$objsubscribe->field("idcuenta", $objusuarios->ID());
			$objsubscribe->store();

			$objpublish = new publish();
			$objpublish->db = $objusuarios->db;
			$objpublish->fetch(0);
			$objpublish->field("idusuario", $objusuarios->ID());
			$objpublish->field("idcuenta", $objusuarios->ID());
			$objpublish->store();

			$objpublish = new publish();
			$objpublish->db = $objusuarios->db;
			$objpublish->fetch(0);
			$objpublish->field("idusuario", $objusuarios->ID());
			$objpublish->field("idcuenta", $objusuarios->ID());
			$objpublish->store();

			$objusuarios_cuenta = new usuarios_cuenta();
			$objusuarios_cuenta->db = $objusuarios->db;
			$objusuarios_cuenta->fetch(0);
			$objusuarios_cuenta->field("idusuario", $objusuarios->ID());
			$objusuarios_cuenta->field("idcuenta", $objusuarios->ID());
			$objusuarios_cuenta->field("defecto", "true");
			$objusuarios_cuenta->field("owner", "true");
			$objusuarios_cuenta->store();
			
			$dataArr['resultado']="la cuenta se grabo correctamente, presiones el boton cerrar e inicie sesion";
			$dataArr['estado']=1;
			header('Content-Type: application/json');
			header('Access-Control-Allow-Origin: *'); 
			echo json_encode($dataArr);
			exit(0);
			break;
		case "register":
			$dataArr = Array();
			//$objusuarios->debug=1;
			$objusuarios->fetch(0);
			$celular=tel_argentino($_POST["celular"]);
			$objusuarios->field("username", $_POST["username"]);
			$objusuarios->field("password", $_POST["password"]);
			$objusuarios->field("nombre", $_POST["nombre"]);
			$objusuarios->field("apellido", $_POST["apellido"]);
			$objusuarios->field("email", $_POST["email"]);
			$objusuarios->field("celular",  $celular["completo"]);
			$objusuarios->store();
			
			$objusuarios_grupos = new usuarios_grupos();
			$objusuarios_grupos->db = $objusuarios->db;
			$objusuarios_grupos->fetch(0);
			$objusuarios_grupos->field("idusuario",$objusuarios->ID());
			$objusuarios_grupos->field("idgrupo",16);
			$objusuarios_grupos->store();
			
			$objsubscribe = new subscribe();
			$objsubscribe->db = $objusuarios->db;
			$objsubscribe->fetch(0);
			$objsubscribe->field("idusuario", $objusuarios->ID());
			$objsubscribe->field("idcuenta", $objusuarios->ID());
			$objsubscribe->store();

			$objsubscribe = new subscribe();
			$objsubscribe->db = $objusuarios->db;
			$objsubscribe->fetch(0);
			$objsubscribe->field("idusuario", $objusuarios->ID());
			$objsubscribe->field("idcuenta", $_POST["idcuenta"]);
			$objsubscribe->store();

			$objpublish = new publish();
			$objpublish->db = $objusuarios->db;
			$objpublish->fetch(0);
			$objpublish->field("idusuario", $objusuarios->ID());
			$objpublish->field("idcuenta", $objusuarios->ID());
			$objpublish->store();

			$objpublish = new publish();
			$objpublish->db = $objusuarios->db;
			$objpublish->fetch(0);
			$objpublish->field("idusuario", $objusuarios->ID());
			$objpublish->field("idcuenta", $_POST["idcuenta"]);
			$objpublish->store();



			$dataArr['resultado']="la cuenta se grabo correctamente, presiones el boton cerrar e inicie sesion";
			$dataArr['estado']=1;
			header('Content-Type: application/json');
			header('Access-Control-Allow-Origin: *'); 
			echo json_encode($dataArr);
			exit(0);
			break;
	
		
	
	

		
		default:
			return handleevent("index", $objusuarios);
	}
	
}



$dbCMS   = new dbMysql($_SESSION["DB"]["HOST"], $_SESSION["DB"]["NAME"], $_SESSION["DB"]["USER"], $_SESSION["DB"]["PASSWD"]);
$dbCMS->connect();


$objusuarios = new usuarios();
$objusuarios->db = $dbCMS;



handleevent($_GET["event"], $objusuarios);

$dbCMS->close();


?>
