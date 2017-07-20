<?php

require_once "nusoap/nusoap.php";
require_once "web_service_config.php";

function insertData($tabla,$data){
	$connexio = @new mysqli(DB_HOST_EPB, DB_USERNAME_EPB, DB_PASSWORD_EPB, DB_NAME_EPB);
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

function truncateTable($tabla){
    $connexio = @new mysqli(DB_HOST_EPB, DB_USERNAME_EPB, DB_PASSWORD_EPB, DB_NAME_EPB);
    if ($connexio->connect_error)
        return false;

    $query = "TRUNCATE TABLE ".$tabla;

    if($connexio->query($query)){
        $connexio->close();
        return true;
    }else{
        return false;
    }
}

$server = new soap_server();
$server->register("insertData");
$server->register("truncateTable");
$server->soap_defencoding = 'UTF-8';
$server->encode_utf8 = true;
$HTTP_RAW_POST_DATA = file_get_contents("php://input");
$server->service($HTTP_RAW_POST_DATA);