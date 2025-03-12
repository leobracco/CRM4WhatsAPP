<?PHP
include("../config.inc.php");
require '../vendor/autoload.php';
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchException;
/*
  "_id": "29058b8e-0eb0-4d39-8ecc-94c0d88afb34",
  "_rev": "1-a0126969422fe9e421ee950a9c0bc5cd",
  "username": "muniposadas",
  "celular": "3743520956",
  "cuenta": "muniposadas",
  "device": "MOBILE",
  "type": "PIGNUSAPP",
  "nombre": "Hernan",
  "apellido": "feifef",
  "latitud": -34.26303664549411,
  "longitud": -59.45901756468264,
  "boton": "PANICO",
  "timestamp": 1624571902048
*/
function handleevent($section,$event,&$objcliente) 
{
    //echo "Section:".$section." Event:".$event." First <br>";
	switch ($section) {
        case "eventos":
            echo "Section:".$section." Event:".$event." Eventos <br>";
            switch ($event) {
                case "by_celular":

                        $opts = array("include_docs" => true,"startkey" => array($_GET["action"],$_SESSION["ACCOUNT"]["NAME"]), "endkey" => array($_GET["action"],$_SESSION["ACCOUNT"]["NAME"]), "limit" => 100 , "descending" => true);
                        try {
                            $dataArr = $objcliente->setQueryParameters($opts)->getView($_SESSION["ACCOUNT"]["NAME"],'by_button');
                            } catch (Exception $e) {
                                echo "something weird happened: " . $e->getMessage() . "<BR>\n";
                            }
                                header('Content-Type: application/json');
                        echo json_encode($dataArr);
                        exit();
                        break;
                
                case "by_button_phone":
                    $opts = array("include_docs" => true,"startkey" => array($_GET["action"],$_SESSION["ACCOUNT"]["NAME"]), "endkey" => array($_GET["action"],$_SESSION["ACCOUNT"]["NAME"]), "limit" => 100 , "descending" => true);
                    try {
                        $dataArr = $objcliente->setQueryParameters($opts)->getView($_SESSION["ACCOUNT"]["NAME"],'by_button_phone');
                        } catch (Exception $e) {
                            echo "something weird happened: " . $e->getMessage() . "<BR>\n";
                        }
                            header('Content-Type: application/json');
                    echo json_encode($dataArr);
                    exit();
                    break;
            }
            exit();
            break;
        case "celulares":
            //echo "Section:".$section." Event:".$event." Celulares <br>";
                switch ($event) {
                    case "by_celular":
                           // echo "Section:".$section." Event:".$event." By Celulares Cuenta:".$_SESSION["ACCOUNT"]["NAME"]." Celular:".$_GET["celular"]." <br>";
                            $opts = array(  "include_docs" => true,
                                            "startkey" => array(
                                                                $_SESSION["ACCOUNT"]["NAME"],
                                                                $_GET["celular"],
                                                                "MOBILE"),
                                            "endkey"   => array(
                                                                $_SESSION["ACCOUNT"]["NAME"],
                                                                $_GET["celular"],
                                                                "MOBILE"), 
                                            "limit"    => 100,
                                            "descending" => true
                                        );
                            try {
                                $dataArr = $objcliente->setQueryParameters($opts)->getView($section,'by_celular');
                                } catch (Exception $e) {
                                    echo "something weird happened: " . $e->getMessage() . "<BR>\n";
                                }
                                    header('Content-Type: application/json');
                            echo json_encode($dataArr);
                            exit();
                            break;
                    
                    case "by_button":
                                        $opts = array(  "include_docs" => true,
                                                        "startkey" => array(
                                                                            $_SESSION["ACCOUNT"]["NAME"],
                                                                            $_GET["boton"],
                                                                            "MOBILE"),
                                                        "endkey"   => array(    
                                                                            $_SESSION["ACCOUNT"]["NAME"],
                                                                            $_GET["boton"],
                                                                            "MOBILE"), 
                                                        "limit"    => 100,
                                                        "descending" => true
                                                    );
                        try {
                            $dataArr = $objcliente->setQueryParameters($opts)->getView($_SESSION["ACCOUNT"]["NAME"],'by_button');
                            } catch (Exception $e) {
                                echo "something weird happened: " . $e->getMessage() . "<BR>\n";
                            }
                                header('Content-Type: application/json');
                        echo json_encode($dataArr);
                        exit();
                        break;
                case "by_button_phone":
                    ///emit([doc.cuenta,doc.celular,doc.device,doc.boton], doc)
                    $opts = array(  "include_docs" => true,
                    "startkey" => array(
                                        $_SESSION["ACCOUNT"]["NAME"],
                                        $_GET["celular"],
                                        "MOBILE",
                                        $_GET["boton"]),
                    "endkey"   => array(    
                                        $_SESSION["ACCOUNT"]["NAME"],
                                        $_GET["celular"],
                                        "MOBILE", 
                                        $_GET["boton"]),
                    "limit"    => 100,
                    "descending" => true
                );
                        try {
                            $dataArr = $objcliente->setQueryParameters($opts)->getView($_SESSION["ACCOUNT"]["NAME"],'by_button_phone');
                            } catch (Exception $e) {
                                echo "something weird happened: " . $e->getMessage() . "<BR>\n";
                            }
                        header('Content-Type: application/json');
                        echo json_encode($dataArr);
                        exit();
                        break;
                }
                exit();
                break;
        case "by_phone":           
            //$result = $objcliente->getView($_GET["cuenta"],'by_phone');

            //$opts = array("include_docs" => true, "key" => $_GET["celular"]);
            $opts = array("include_docs" => true,"startkey" => array($_GET["celular"],$_GET["cuenta"]), "endkey" => array($_GET["celular"],$_GET["cuenta"]), "limit" => 100 , "descending" => true);
            $result = $objcliente->setQueryParameters($opts)->getView($_GET["cuenta"],'by_phone');
            echo $_SESSION["USER"]["ACCOUNT"];
            header('Content-Type: application/json');
            print_r($result);
       
        exit();
        break;
        case "by_type":           
                $result = $objcliente->getView($_GET["cuenta"],'by_type');
                header('Content-Type: application/json');
                echo json_encode($result);
           
        exit();
        break;
        case "createByCuenta":
            $dataArr = Array();
            $view_fn="function (doc) {if (doc.cuenta=='".$_GET["cuenta"]."') {emit(doc._id, 1);}}";
            $design_doc = new stdClass();
            $design_doc->_id = '_design/'.$_GET["cuenta"];
            $design_doc->language = 'javascript';
            $design_doc->views = array ( 'by_cuenta'=> array ('map' => $view_fn ) );
            $objcliente->storeDoc($design_doc);

            header('Content-Type: application/json');
            echo json_encode($dataArr);
       
            exit();
            break;
        case "createFilters":
            try{
                
                /*username:username,
            celular:celular,
            cuenta:cuenta,
            device:"MOBILE",
            type:"PIGNUSAPP",
            nombre:nombre,
            apellido:apellido,
            latitud:latitud,
            longitud:longitud*/


                $view_by_alarma="function (doc) {if (doc.cuenta=='".$_GET["cuenta"]."' && doc.type=='ALARMA') {emit(doc._id, 1);}}";
                $view_by_app="function (doc) {if (doc.cuenta=='".$_GET["cuenta"]."' && doc.type=='PIGNUSAPP') {emit(doc._id, 1);}}";
                $view_by_alerta="function (doc) {if (doc.cuenta=='".$_GET["cuenta"]."' && doc.boton=='ALERTA') {emit(doc._id, 1);}}";
                $view_by_panico="function (doc) {if (doc.cuenta=='".$_GET["cuenta"]."' && doc.boton=='PANICO') {emit(doc._id, 1);}}";
                $view_by_salud="function (doc) {if (doc.cuenta=='".$_GET["cuenta"]."' && doc.boton=='SALUD') {emit(doc._id, 1);}}";
                $view_by_vdg="function (doc) {if (doc.cuenta=='".$_GET["cuenta"]."' && doc.boton=='VDG') {emit(doc._id, 1);}}";
                $view_by_bomberos="function (doc) {if (doc.cuenta=='".$_GET["cuenta"]."' && doc.boton=='BOMBEROS') {emit(doc._id, 1);}}";
                
                $design_doc = new stdClass();
                $design_doc->_id = '_design/'.$_GET["cuenta"];
                $design_doc->language = 'javascript';
                $design_doc->views = array ( 
                                                'by_alarma'=> array ('map' => $view_by_alarma ),
                                                'by_app'=> array ('map' => $view_by_app ),
                                                'by_alerta'=> array ('map' => $view_by_alerta ),
                                                'by_panico'=> array ('map' => $view_by_panico ),
                                                'by_salud'=> array ('map' => $view_by_salud ),
                                                'by_vdg'=> array ('map' => $view_by_vdg ),
                                                'by_bomberos'=> array ('map' => $view_by_bomberos ),

                                             );
                $objcliente->storeDoc($design_doc);
                
                }
                catch(Exceptions\CouchNotFoundException $ex){
                    echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
                if($ex->getCode() == 404)
                echo 'Document not found';
                }

              
              
               
           
        exit();
        break;
        
    }
}
$objcliente = new CouchClient($_SESSION["COUCHDB"]["URL"],$_SESSION["COUCHDB"]["NAME"]);
handleevent($_GET["section"],$_GET["event"],$objcliente);




