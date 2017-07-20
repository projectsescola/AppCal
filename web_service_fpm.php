<?php
require_once "nusoap/nusoap.php";

function insertData($tabla,$data){
	$connexio = @new mysqli("localhost", "escolapo_rodrigo", "R._})gT(h0VS","escolapo_cal_profesionales");
	if ($connexio->connect_error)
    	return false;

	$columnas = array_keys($data);

    $query = "INSERT INTO `".$tabla."` (";

    $numero_elementos = count($data);
    $i = 0;

    foreach ($columnas as $columna) {
        $query .= "`".$columna."`";
        if( ++$i < $numero_elementos ){
            $query .= ",";
        }
    }

    $query .= ") VALUES (";

    $i = 0;

    foreach ($data as $info) {
        $query .= "'".$info."'";
        if( ++$i < $numero_elementos ){
            $query .= ",";
        }
    }

    $query .= ");";

	$resultado=$connexio->query($query);

	$connexio->close();

	return $resultado;
}

$server = new soap_server();
$server->register("insertData");
$server->soap_defencoding = 'UTF-8';
$server->encode_utf8 = true;
$HTTP_RAW_POST_DATA = file_get_contents("php://input");
$server->service($HTTP_RAW_POST_DATA);