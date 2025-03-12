<?php


include("../config.inc.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.dbMysql.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.template.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/class.objdb.php");
require_once($_SESSION["TEMPLATE"]["APP_INCLUDEPATH"]."/lib.quodii.php");
function handleevent($event, &$objalarmas, &$objCMSUser) 
{
	switch ($event) {
		case "index":
			if (!$objCMSUser->checkPermission("Alarmas::lectura"))
				return handleevent("permission_denied", $objalarmas, $objCMSUser);
    
				$result=$objalarmas->doformat("/puntosseguros/index.html", $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
				$tpllist = new Template($result);
				$result = $tpllist->Template;
			return $result;
			break;
		case "form":
			if (!$objCMSUser->checkPermission("Alarmas::lectura"))
				return handleevent("permission_denied", $objalarmas, $objCMSUser);
				$dataArr=Array();
			
			$objalarmas->fetch($_GET["id"]);
			$result = $objalarmas->doformat("/puntosseguros/form_abm.html",  $_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
			$dataArr['respuesta']=$result;
			header('Content-Type: application/json');
			 echo json_encode($dataArr);
			exit (0);
			break;
		
		case "borrar":
			if (!$objCMSUser->checkPermission("Alarmas::escritura"))
				return handleevent("permission_denied", $objalarmas, $objCMSUser);
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha borrado el cliente".$objalarmas->ID());
			$objalarmas->delete();
			
			header("Content-Type:text/html;charset=iso-8859-1");
			echo $objalarmas->ID();
			exit (0);
			break;
		
		case "grabar":
			if (!$objCMSUser->checkPermission("Alarmas::escritura")) 
				return handleevent("permission_denied", $objalarmas, $objCMSUser);

			$objalarmas->fetch($_GET['id']);
			$objalarmas->field("latitud", $_GET['latitud']);
			$objalarmas->field("longitud", $_GET['longitud']);
			$objalarmas->field("direccion", $_GET['direccion']);
			$objalarmas->field("puerto", $_GET['puerto']);
			
			$objalarmas->store();
			addLog("alerta","El usuario ".$_SESSION["usuarios_username"]." ha grabado el producto".$objalarmas->ID());
			header("Content-Type: text/html;charset=iso-8859-1");
			echo $objalarmas->ID();
			exit(0);
			break;

		
		
	      case "lista_json":
			if (!$objCMSUser->checkPermission("Alarmas::lectura"))
				return handleevent("permission_denied", $objalarmas, $objCMSUser);
			
				$dataArr = Array();
				//$objalarmas->debug=1;
				$objalarmas->limit_from=$_GET['offset'];
				$objalarmas->limit_count=$_GET['limit'];
				if ($_GET['order']=='desc')
					$_GET['order']="DESC";
				else 
					$_GET['order']	="ASC";
				
				if (isset($_GET["sort"]) && isset($_GET["order"]))
				{
			    	if ($_GET["sort"]=='estado')
			    		$_GET["sort"]="estado";
			    	$objalarmas->order_by=$_GET['sort'] ." ". $_GET['order'];
				}
				if (isset($_GET["limit"]))
					$objalarmas->limit_count=$_GET['limit'];
				if (isset($_GET["search"]))
					$objalarmas->where.=" nserie LIKE '%".$_GET["search"]."%' OR ippublica LIKE '%".$_GET["search"]."%' OR direccion LIKE '%".$_GET["search"]."%'";
			
			 	$dataArr['total']=$objalarmas->count();
				foreach ($objalarmas->fetchall() as $row)
			  	{ 
			    	$r = Array();
			    	$r['id'] = $row['id'];
			    	$r['nserie'] = $row['nserie'];
			    	$r['direccion'] = htmlentities($row['direccion']);
			    	$r['ippublica'] = "<a href='http://".$row['ippublica']."' target='_blank'>".$row["nserie"]."</a>";
			    	$r['offline'] = $row['tiempo.offline'];
			    	$r['estado'] = $row['date_uptime.nice'];
			    	$r['bateria'] = "<div id='".$row["nserie"]."'></div>";		    
			    	$r['imei'] = "<div id='".$row["nserie"]."-imei'></div>";		    
			    	$r['signal'] = "<div id='".$row["nserie"]."-signal'></div>";		    
					$r['acciones'] = "  
                                      <a class='btn btn-info' onclick='Editar(".$row['id'].")' >
                                          Editar                                            
                                      </a>
                                      <a class='btn btn-info' onclick=Check('".$row['nserie']."')>
                                          Test                                            
                                      </a>
				      												<a class='btn btn-info' onclick=Off('".$row['nserie']."')>
                                          Alloff                                            
                                      </a>
																			<a class='btn btn-info' onclick=LuzOn('".$row['nserie']."')>
                                          Luz On                                            
                                      </a>
																			<a class='btn btn-info' onclick=LuzOff('".$row['nserie']."')>
                                          Luz Off                                            
                                      </a>
                                        <a class='btn btn-info' onclick=Imei('".$row['nserie']."')>
                                         Imei                                            
                                      </a>
                                        <a class='btn btn-info' onclick=Signal('".$row['nserie']."')>
                                          Signal                                            
                                      </a>
                                        <a class='btn btn-info' onclick=Bateria('".$row['nserie']."') >
                                          Bateria                                            
                                      </a>
                                    ";

					
			   		$dataArr['rows'][] = $r;
			  	}						
			    
			 	header('Content-Type: application/json');
			 	echo json_encode($dataArr);
				    
				exit();
			break;
			case "puntos_seguros":
			if (!$objCMSUser->checkPermission("Alarmas::lectura"))
				return handleevent("permission_denied", $objalarmas, $objCMSUser);
			
				$geojson = array(
					'type'      => 'FeatureCollection',
					'features'  => array()
				 );
				 //$objalarmas->debug=1;
			foreach ($objalarmas->fetchall() as $row)
			  { 
				$feature = array(
					'id' => "AlarmasID-".$row['id'],
					'type' => 'Feature', 
					'geometry' => array(
						'type' => 'Point',
						# Pass Longitude and Latitude Columns here
						'coordinates' => array($row['longitud'], $row['latitud'])
					),
					# Pass other attribute columns here
					'properties' => array(
						//'title'=> "<b>".$row['nserie']."</b><br>Direccion:"+$row['direccion'].
						//			"<br>Bateria:<div id=".$row['nserie']."-bateria>
						//			<br>Signal:<div id=".$row['nserie']."-signal>
						//			<br>IMEI:<div id=".$row['nserie']."-imei>"
						//			,
						'marker-color'=> '#3bb2d0',
						'marker-size'=> 'large',
						'marker-symbol'=> 'marker'
						
						)
					);

				array_push($geojson['features'], $feature);

			  }						
			    
			 header('Content-Type: application/json');
			 echo json_encode($geojson, JSON_NUMERIC_CHECK);
				    
			exit();
			break;
			
	      case "permission_denied":
			header("Location: ../inicio/");  
			exit();
			break;

		default:
			return handleevent("index", $objalarmas, $objCMSUser);
	}
	
}

if (!$_SESSION["usuarios_idusuario"]) {
	header("Location: ../inicio/");
	exit(0);
}

$dbCMS   = new dbMysql($_SESSION["DB"]["HOST"], $_SESSION["DB"]["NAME"], $_SESSION["DB"]["USER"], $_SESSION["DB"]["PASSWD"]);
$dbCMS->connect();

$tplmarco = new Template("");
$tplmarco->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplmarco->Open("/marco.html");



$tplfooter = new Template("");
$tplfooter->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplfooter->Open("/footer.html");

$tplbotonera = new Template("");
$tplbotonera->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplbotonera->Open("/botonera.html");

$tplbotoneraheader = new Template("");
$tplbotoneraheader->setFileRoot($_SESSION["TEMPLATE"]["TEMPLATE_ROOT"]);
$tplbotoneraheader->Open("/botonera-header.html");
$tplbotoneraheader->setVar("{pagina.url}", "Usuarios");
$tplbotoneraheader->setVar("{usuario.nombre}", $objCMSUser->values['nombre']);
$tplbotoneraheader->setVar("{usuario.apellido}", $objCMSUser->values['apellido']);

$objalarmas = new alarmas();
$objalarmas->db = $dbCMS;

$objCMSUser = new usuarios();
$objCMSUser->db = $dbCMS;
$objCMSUser->fetch($_SESSION["usuarios_idusuario"]);

$tplmarco->setVar("{titulo.pagina}", "Administraci&oacute;n de Sistema :: ABM Alarmas");

$tplmarco->setVar("{contenido}", handleevent($_GET["event"], $objalarmas, $objCMSUser));
$tplmarco->setVar("{header}",$tplheader->Template);
$tplmarco->setVar("{botonera.header}",$tplbotoneraheader->Template);
$tplmarco->setVar("{botonera.lateral}",$tplbotonera->Template);
$tplmarco->setVar("{footer}",$tplfooter->Template);
$tplmarco->setVar("{pagina.nombre}","Listado de alarmas");
$tplmarco->setVar("{random}",rand(1000,10000));



print($tplmarco->Template);

$dbCMS->close();


?>
