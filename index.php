<?php

require 'DriveSql.class.php';

require 'api/libs/Slim/Slim.php'; 
\Slim\Slim::registerAutoloader(); 
$webapp = new \Slim\Slim();


$webapp->get('/listar-cursos', function(){
    $db = getConnection(array('server'=>"localhost",'user'=>"root",'password'=>"",'database'=>"calrec"));

    $resultados = getCursos($db);

    $db->cerrar();
});

function getCursos($db_instance){
	$query = "SELECT * FROM tabcursos";
	$respuesta_query = $db_instance->consulta_query($query);

	var_dump($respuesta_query);
}

function getConnection($data){
	$con = new DriveSql($data['server'],$data['user'],$data['password'],$data['database']);

	return $con->iniciar();
}

?>