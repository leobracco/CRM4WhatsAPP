<?php

include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.nysoftv3.php");
function handleevent($event, &$objcelulares) 
{
	switch ($event) {
    		case "create_user":
				$dataArr = Array();
				//$objcelulares->debug=1;
				$objcelulares->fetchby("celular",$_POST['prefijo'].$_POST['celular']);
				$dataArr['id'] =$objcelulares->values['id'];
				$objbarrios = new barrios();
				$objbarrios->db = $objcelulares->db;
				addLog("info","Registra con ID#".$objcelulares->ID()." El numero".$_POST['prefijo'].$_POST['celular']);
				if ($objcelulares->ID()=='null' ||$objcelulares->values['id']=='')
				{
					$objcelulares->ID(0);
            				$objcelulares->field("nombre", $_POST['nombre']);
            				$objcelulares->field("apellido", $_POST['apellido']);
            				$objcelulares->field("dni", $_POST['dni']);
            				$objcelulares->field("celular", $_POST['prefijo'].$_POST['celular']);
            				$objcelulares->field("barrio", $_POST['codigo']);
            				$objcelulares->field("direccion", $_POST['direccion']);
					$objcelulares->field("imei", $_POST['imei']);
					$objcelulares->field("email", $_POST['email']);
					$objcelulares->field("modelo", $_POST['modelo']);
					$objcelulares->field("plataforma", $_POST['plataforma']);
					$objcelulares->field("version", $_POST['version']);
					$objcelulares->field("estado","ACTIVO");
					$objcelulares->field("vdg","FALSE");
					$objcelulares->field("codigo",rand(1000,9999));
					$dataArr['status'] ="index";
					$objcelulares->store();
					$objbarrios->field("idbarrio", $_POST['codigo']);
					$objbarrios->field("idcleular", $objcelulares->ID());
					//$objbarrios->store();
				}
				else
				{
					
					$objcelulares->field("codigo",rand(1000,9999));
					$dataArr['status'] ="verificar";
					$objcelulares->store();
				}
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
	                	echo json_encode($dataArr);
        		 	exit(0);
	                break;
			case "verify_user":
				$dataArr = Array();
				//$objcelulares->debug=1;
				$objcelulares->fetchby("celular",$_POST['prefijo'].$_POST['celular']);
				//$dataArr['codigo']=$objcelulares->values['codigo']."=".$_POST['codigo'];
				if ($_POST['codigo']===$objcelulares->values['codigo'])
				{
			    	$dataArr['status'] ="index";
			    	$objcelulares->field("imei",$_POST['imei']);
			    	$objcelulares->store();
				}
				else
				{
			    	$dataArr['status'] ="verify_error";
				}
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
				echo json_encode($dataArr);
				
            			//echo $dataArr['status'];
                exit(0);
			break;
			case "status":
				$dataArr = Array();
				//$objcelulares->debug=1;
				$objcelulares->fetchby("celular",$_POST['celular']);
				
				$dataArr['id']=$objcelulares->values['id'];
				if ($objcelulares->values['id']=='null' ||$objcelulares->values['id']=='')
				{
					$dataArr['status'] ="registro";
				}
				else
				{
					if ($objcelulares->values['imei']==$_POST['imei'])
					{
						$dataArr['status'] ="index";
						$dataArr['vdg'] =$objcelulares->values['vdg'];
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
				$objcelulares->fetchby("celular",$_POST['celular']);
				$objcelulares->field("codigo",rand(10000,99999));
				$dataArr['status'] ="verificar";
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
				echo json_encode($dataArr);
				exit(0);
			break;
			case "barrios":
				$objbarrios = new barrios();
				$objbarrios->db = $objcelulares->db;
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
				$objbarrios->db = $objcelulares->db;
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
				return handleevent("api", $objcelulares);
	}
	
}



$dbCMS   = new dbMysql($_SESSION["DB"]["HOST"], $_SESSION["DB"]["NAME"], $_SESSION["DB"]["USER"], $_SESSION["DB"]["PASSWD"]);
$dbCMS->connect();

$objcelulares = new celulares();
$objcelulares->db = $dbCMS;


handleevent($_GET["event"], $objcelulares);

$dbCMS->close();


?>
