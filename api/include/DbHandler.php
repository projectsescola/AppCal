<?php

class DbHandler {
 
    private $conn;
 
    function __construct() {
        require_once 'DbConnect.php';
        // abre conexión a la bdd
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function readFromTable($tabla){
        // consulta de todos los campos de la tabla calendarios
        $query = "SELECT * FROM `".$tabla."`";
     
        // prepara la consulta
        $stmt = $this->conn->prepare($query);
     
        // la ejecuta
        $stmt->execute();
     
        return $stmt;
    }

    public function insertData($tabla,$data){
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
     
        // prepara la consulta
        $stmt = $this->conn->prepare($query);
     
        // la ejecuta
        $stmt->execute();
     
        return $stmt;
    }

}
 
?>