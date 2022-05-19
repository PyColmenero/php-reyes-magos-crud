<?php

class BBDD
{
    public $database_user = 'studium';
    public $database_pasw = 'studium__';
    public $database_name = 'studium_dws_p2';
    public $database_port = '3306';
    public $IP = 'localhost';
    public $conexion = null;

    private $result = "";

    public function __construct()
    {
        $this->con = mysqli_init();
        mysqli_real_connect($this->con, $this->IP, $this->database_user, $this->database_pasw, $this->database_name, $this->database_port);
        $this->con->set_charset("utf8");
    }
    public function select($sentencia)
    {
        return mysqli_query($this->con, $sentencia);
    }
    public function insertar($sentencia, $entidad)
    {
        if (mysqli_query($this->con, $sentencia)) {
            $this->result = "<p class='goodnews mb-2'><i class='bi bi-check me-2 '></i>$entidad insertado correctamente. </p>";
        } else {
            $this->result = "<p class='badnews'><i class='bi bi-x-lg me-3'></i>Error: " . mysqli_error($this->con) . "</p>";
        }
        return $this->result;
    }
    public function borrar($sentencia, $entidad)
    {
        if (mysqli_query($this->con, $sentencia)) {
            $this->result = "<p class='goodnews mb-2'><i class='bi bi-check me-2'></i>$entidad borrado correctamente. </p>";
        } else {
            $this->result = "<p class='badnews'><i class='bi bi-x-lg me-3'></i>Error: " . mysqli_error($this->con) . "</p>";
        }
        return $this->result;
    }
    public function editar($sentencia, $entidad)
    {
        if (mysqli_query($this->con, $sentencia)) {
            $this->result = "<p class='goodnews mb-2'><i class='bi bi-check me-2 '></i>$entidad editado correctamente. </p>";
        } else {
            $this->result = "<p class='badnews'><i class='bi bi-x-lg me-3'></i>Error: " . mysqli_error($this->con) . "</p>";
        }
        return $this->result;
    }
}
