<?php

class DriveSql {
    private $servidor;
    private $usuario;
    private $password;
    private $basededatos;
    private $connexio;

    function __construct($server,$user,$password,$database){
        $this->servidor=$server;
        $this->usuario=$user;
        $this->password=$password;
        $this->basededatos=$database;
    }

    //Abrir conexión con la base de datos
    public function iniciar(){
        $this->connexio = @new mysqli($this->servidor, $this->usuario, $this->password,$this->basededatos,3306);
        if ($this->connexio->connect_error){
            die('Error de conexión: ' . $this->connexio->connect_error);
        }
        mysqli_set_charset($this->connexio,"utf8");
    }

    //Cerrar conexión con la base de datos
    public function cerrar(){
        $this->connexio->close();
    }

    public function consulta_query($query){
        $array_consulta=$this->connexio->query($query);
        return $array_consulta;
    }
}

?>
