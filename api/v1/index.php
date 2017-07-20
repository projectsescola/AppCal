<?php

/* Los headers permiten acceso desde otro dominio (CORS) a nuestro REST API o desde un cliente remoto via HTTP
 * Removiendo las lineas header() limitamos el acceso a nuestro RESTfull API al mismo dominio
 * Nótese los métodos permitidos en Access-Control-Allow-Methods. Esto nos permite limitar los métodos de consulta a nuestro RESTfull API
 * Mas información: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
 **/
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"'); 

include_once '../include/Config.php';

require_once '../include/DbHandler.php'; 

require '../libs/Slim/Slim.php'; 
\Slim\Slim::registerAutoloader(); 
$app = new \Slim\Slim();

/* Usando GET para consultar las diferentes tablas */

// Cursos Programados

$app->get('/cursos-programados', function(){
    $db = new DbHandler();

    $resultados = getResult($db,"progcur");
    
    echoResponse($resultados[0], $resultados[1]);
});

// Prácticas Programadas

$app->get('/practicas-programadas', function(){
    $db = new DbHandler();

    $resultados = getResult($db,"progpra");
    
    echoResponse($resultados[0], $resultados[1]);
});

// Profesores

$app->get('/profesores', function(){
    $db = new DbHandler();

    $resultados = getResult($db,"tabpatrons");

    echoResponse($resultados[0], $resultados[1]);
});

// Barcos / Escuelas

$app->get('/barcos-escuelas', function(){
    $db = new DbHandler();

    $resultados = getResult($db,"tabvaixells");

    echoResponse($resultados[0], $resultados[1]);
});

// Cursos

$app->get('/cursos', function(){
    $db = new DbHandler();

    $resultados = getResult($db,"tabcursos");

    echoResponse($resultados[0], $resultados[1]);
});

// Prácticas

$app->get('/practicas', function(){
    $db = new DbHandler();

    $resultados = getResult($db,"tabpracticas");

    echoResponse($resultados[0], $resultados[1]);
});

/* Usando POST para insertar registros */

// Añadir profesor

$app->post('/addprofesor', 'authenticate', function() use ($app) {

    $resultados = insertInto("tabpatrons", array('patroid', 'nombre', 'dni', 'telefono', 'email', 'titulo'), $app );

    echoResponse($resultados[0], $resultados[1]);
});

/* corremos la aplicación */
$app->run();

/*********************** USEFULL FUNCTIONS **************************************/

/**
 * Generar un array con el resultado de la consulta a la base de datos
 */
function getResult($db,$tabla){

    $stmt = $db->readFromTable($tabla);

    $num = $stmt->rowCount();
     
    // comprueba si hay al menos un registro
    if($num>0){
     
        // calendarios array
        $elementos_array=array();
        $elementos_array[$tabla]=array();
     
        // recuepera los registros
        // fetch() es más rápido que fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            $elemento = array();
            
            foreach ($row as $key => $value) {
                $elemento[$key] = $value;
            }
     
            array_push($elementos_array[$tabla], $elemento);
        }
        return array(200, $elementos_array);
    }else{
        return array(404, "No hay registros");
    }
}

/**
 * Generar un array con el resultado de la consulta a la base de datos
 */

function insertInto($tabla,$columnas,$app){
    // check for required params
    verifyRequiredParams( $columnas );

    $response = array();
    $param = array();

    //capturamos los parametros recibidos y los almacenamos como un nuevo array
    foreach ($columnas as $nombre) {
        $param[$nombre] = $app->request->post($nombre);
    }
    
    /* Podemos inicializar la conexion a la base de datos si queremos hacer uso de esta para procesar los parametros con DB */
    $db = new DbHandler();

    /* Podemos crear un metodo que almacene el nuevo auto, por ejemplo: */
    $db->insertData($tabla,$param);

    if ( is_array($param) ) {
        $response["error"] = false;
        $response["message"] = "Auto creado satisfactoriamente!";
        $response["auto"] = $param;
    } else {
        $response["error"] = true;
        $response["message"] = "Error al crear auto. Por favor intenta nuevamente.";
    }

    return array(201, $response);
}

/**
 * Verificando los parametros requeridos en el metodo o endpoint
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
 
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Campo(s) necesarios' . substr($error_fields, 0, -2) . ' no existen o están vacíos';
        echoResponse(400, $response);
        
        $app->stop();
    }
}
 
/**
 * Validando parametro email si necesario
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoResponse(400, $response);
        
        $app->stop();
    }
}
 
/**
 * Mostrando la respuesta en formato json al cliente o navegador
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}

/**
 * Agregando un leyer intermedio e autenticación para uno o todos los metodos, usar segun necesidad
 * Revisa si la consulta contiene un Header "Authorization" para validar
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new DbHandler(); //utilizar para manejar autenticacion contra base de datos
 
        // get the api key
        $token = $headers['Authorization'];
        
        // validating api key
        if (!($token == API_KEY)) { //API_KEY declarada en Config.php
            
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Acceso denegado. Token inválido";
            echoResponse(401, $response);
            
            $app->stop(); //Detenemos la ejecución del programa al no validar
            
        } else {
            //procede utilizar el recurso o metodo del llamado
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Falta token de autorización";
        echoResponse(400, $response);
        
        $app->stop();
    }
}
?>