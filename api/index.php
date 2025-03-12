<?php

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
function handleevent($event, &$objusuarios) 
{
	switch ($event) {
    		case "create_user":
				$dataArr = Array();
				$objusuarios->fetchby("celular",$_POST['prefijo'].$_POST['celular']);
				$dataArr['id'] =$objusuarios->values['id'];
				//idusuario 	username 	password 	nombre 	apellido 	email 	celular 	token
				//$objusuarios->debug=1;
				if ($objusuarios->values['id']=='null' ||$objusuarios->values['id']=='')
				{
					$objusuarios->ID(0);
            		$objusuarios->field("nombre", $_POST['nombre']);
            		$objusuarios->field("apellido", $_POST['apellido']);
            		$objusuarios->field("celular", $_POST['prefijo'].$_POST['celular']);
					$objusuarios->field("email", $_POST['email']);
				}
				else
				{
					$objusuarios->field("codigo",rand(10000,99999));
					$dataArr['status'] ="verificar";
					$objusuarios->store();
				}
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
            	echo json_encode($dataArr);
             	exit(0);
            break;
			case "checkaccountUser":
				$dataArr= Array();
		
				$celular=tel_argentino($_POST["celular"]);
				
				$objusuarios->fetchby("celular",$celular['numero']);
				
				if ($objusuarios->ID()=='')
				{
					if($celular)
					{
						$dataArr["resultado"]="Celular correcto";
						$dataArr["div"]="alert alert-success";
						$dataArr["valid"]=true;
					}
					else
					{
						$dataArr["resultado"]="Verifique el numero de celular";
						$dataArr["div"]="alert alert-success";
						$dataArr["valid"]=false;
					}
				}
				else
				{
					$dataArr["resultado"]="El celular ya existe";
					$dataArr["div"]="alert alert-danger";
					$dataArr["valid"]=false;
				}
					header('Access-Control-Allow-Origin: *');
					header('Content-Type: application/json');
				 echo json_encode($dataArr);
							
		exit();
	break;
			case "existe_user":
				$dataArr = Array();
				//$objusuarios->debug=1;
				$objusuarios->fetchby("celular",$_POST['prefijo'].$_POST['celular']);
				//$dataArr['codigo']=$objusuarios->values['codigo']."=".$_POST['codigo'];
				if ($_POST['codigo']===$objusuarios->values['codigo'])
				{
			    	$dataArr['status'] ="index";
			    	$objusuarios->field("imei",$_POST['imei']);
			    	$objusuarios->store();
				}
				else
				{
			    	$dataArr['status'] ="verify_error";
				}
				header('Access-Control-Allow-Origin: *'); 
				
				
                echo $dataArr['status'];
                exit(0);
			break;
			case "verify_user":
				$dataArr = Array();
				//$objusuarios->debug=1;
				$objusuarios->fetchby("celular",$_POST['prefijo'].$_POST['celular']);
				//$dataArr['codigo']=$objusuarios->values['codigo']."=".$_POST['codigo'];
				if ($_POST['codigo']===$objusuarios->values['codigo'])
				{
			    	$dataArr['status'] ="index";
			    	$objusuarios->field("imei",$_POST['imei']);
			    	$objusuarios->store();
				}
				else
				{
			    	$dataArr['status'] ="verify_error";
				}
				header('Access-Control-Allow-Origin: *'); 
				
				
                echo $dataArr['status'];
                exit(0);
			break;
			case "status":
				$dataArr = Array();
				//$objusuarios->debug=1;
				$objusuarios->fetchby("celular",$_POST['celular']);
				
				$dataArr['id']=$objusuarios->values['id'];
				if ($objusuarios->values['id']=='null' ||$objusuarios->values['id']=='')
				{
					$dataArr['status'] ="registro";
				}
				else
				{
					if ($objusuarios->values['imei']==$_POST['imei'])
					{
						$dataArr['status'] ="index";
						$dataArr['vdg'] =$objusuarios->values['vdg'];
					}
					else
					$dataArr['status'] ="verificar";
				}
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
				echo json_encode($dataArr);
				exit(0);
			break;
			case "recovery_user":
				$dataArr = Array();
				$objusuarios->fetchby("celular",$_POST['celular']);
				$objusuarios->field("codigo",rand(10000,99999));
				$dataArr['status'] ="verificar";
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
				echo json_encode($dataArr);
				exit(0);
			break;
			case "barrios":
				$objbarrios = new barrios();
				$objbarrios->db = $objusuarios->db;
                $dataArr[''] = Array();
                $objbarrios->where=1;
				$objbarrios->order_by="nombre ASC";
	            foreach ($objbarrios->fetchall() as $row)
                { 
    				$r = Array();
					$r['nombre'] = $row['nombre'];
					$r['codigo'] = $row['id'];       
                    $dataArr['rows'][] = $r;
				}        
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
                echo json_encode($dataArr);                   
				exit();
            break;
			case "departamentos":
				$objbarrios = new barrios();
				$objbarrios->db = $objusuarios->db;
                $dataArr[''] = Array();
                $objbarrios->where=1;
				$objbarrios->order_by="nombre ASC";
                foreach ($objbarrios->fetchall() as $row)
				{ 
                	$r = Array();
                    $r['nombre'] = $row['nombre'];
			    	$r['codigo'] = $row['id'];      
                    $dataArr['rows'][] = $r;
                }         
                header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
                echo json_encode($dataArr);                    
                exit();
			break;
	      	case "api":
				echo "Request By";
				exit(0);
			break;
			default:
				return handleevent("api", $objusuarios);
	}
	
}


$dbCMS   = new dbMysql($_SESSION["DB"]["HOST"], $_SESSION["DB"]["NAME"], $_SESSION["DB"]["USER"], $_SESSION["DB"]["PASSWD"]);
$dbCMS->connect();

$objusuarios = new usuarios();
$objusuarios->db = $dbCMS;


handleevent($_GET["event"], $objusuarios);

$dbCMS->close();


?>
