<?php

include("./src/php/BBDD.php");

class ReyMago
{
    private $baseDeDatos = null;
    private $id = 0;
    private $precio_total = 0;
    private $tabla_HTML = "";
    private $indice_tabla = 0;

    public function __construct($id)
    {
        $this->baseDeDatos = new BBDD();
        $this->id = $id;
        $this->construir_tabla();
    }
    public function construir_tabla()
    {
        $sentencia = "SELECT idRegalacion, regalos.nombreRegalo, regalos.precioRegalo, ninos.nombreNino, ninos.apellidosNino, ninos.esBuenoNino, reyesmagos.nombreReyMago ";
        $sentencia .= "FROM regalospedidos JOIN ninos ON regalospedidos.idNinoFK = ninos.idNino ";
        $sentencia .= "JOIN regalos ON regalospedidos.idRegaloFK = regalos.idRegalo ";
        $sentencia .= "JOIN reyesmagos ON regalos.idReyMagoFK = reyesmagos.idReyMago ";
        $sentencia .= "WHERE reyesmagos.idReyMago = $this->id AND ninos.esBuenoNino = 1 ";
        $sentencia .= "ORDER BY ninos.nombreNino";
        $result = $this->baseDeDatos->select($sentencia);


        foreach ($result as $key => $value) {

            $nombreCompletoNino = $value["nombreNino"] . " " . $value["apellidosNino"];

            $precioRegalo = $value["precioRegalo"];
            $this->precio_total += $precioRegalo;

            $this->tabla_HTML .= '<tr>';
            $this->tabla_HTML .= '  <th>' . ++$this->indice_tabla . ' </th>';
            $this->tabla_HTML .= '  <td>' . $nombreCompletoNino . '</td>';
            $this->tabla_HTML .= '  <td>' . $value["nombreRegalo"] . '</td>';
            $this->tabla_HTML .= '  <td>' . $precioRegalo . '€</td>';
            $this->tabla_HTML .= '</tr>';
        }

        // PRECIO FINAL
        $this->tabla_HTML .= '<tr>';
        $this->tabla_HTML .= '  <th colspan="3"> </th>';
        $this->tabla_HTML .= '  <th>' . $this->precio_total . '€ </th>';
        $this->tabla_HTML .= '</tr>';
    }
    public function getTabla()
    {
        return $this->tabla_HTML;
    }
}


$melchor = new ReyMago(0);
$gaspar = new ReyMago(1);
$baltasar = new ReyMago(2);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./src/img/Logo_1.png">
    <title>REYES MAGOS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">

    <link rel="stylesheet" href="./src/css/styles.css">

    <style>

    </style>

</head>

<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                REYES MAGOS
            </a>
            <div>
                <a class="btn btn-secondary" href="./ninos.php" role="button">Niños</a>
                <a class="btn btn-secondary mx-2" href="./regalos.php" role="button">Regalos</a>
                <a class="btn btn-secondary mx-2" href="./busqueda.php" role="button">Busqueda</a>
            </div>
        </div>
    </nav>

    <h1 class="_decoration pt-5 text-center position-relative">REYES MAGOS</h1>

    <div class="mb-5 pb-5">
        <table class="table container mt-3">
            <thead>
                <tr>
                    <th colspan="4">MELCHOR</th>
                </tr>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Niño</th>
                    <th scope="col">Regalo</th>
                    <th scope="col">Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                echo $melchor->getTabla();
                ?>
            </tbody>
        </table>
        <table class="table container mt-3">
            <thead>
                <tr>
                    <th colspan="4">GASPAR</th>
                </tr>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Niño</th>
                    <th scope="col">Regalo</th>
                    <th scope="col">Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                echo $gaspar->getTabla();
                ?>
            </tbody>
        </table>
        <table class="table container mt-3">
            <thead>
                <tr>
                    <th colspan="4">BALTASAR</th>
                </tr>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Niño</th>
                    <th scope="col">Regalo</th>
                    <th scope="col">Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                echo $baltasar->getTabla();
                ?>
            </tbody>
        </table>

    </div>



    <footer class="bg-dark text-light p-3 fixed-bottom mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <label>Alejandro Colmenero Moreno</label>
                </div>
                <div class="col-12 col-lg-3">
                    <label>pycolmenero@gmail.com</label>
                </div>
                <div class="col-12 col-lg-3 text-end">
                    <pre class="m-0">2º DAW GRUPO STUDIUM</pre>
                </div>
                <div class="col-12 col-lg-3 text-end">
                    <pre class="m-0">Entorno Servidor
                        Práctica Tema 1</pre>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>