<?php

include("./src/php/BBDD.php");

class Busqueda
{
    private $baseDeDatos = null;
    private $indice_tabla = 0;
    private $ninos_options_HTML = "";
    private $regalos_options_HTML = "";
    private $accion_realizada  = "";
    private $tabla_regalos_HTML = "";
    private $idNino = null;

    public function __construct()
    {
        $this->baseDeDatos = new BBDD();

        if (isset($_POST["idNino"])) {
            $this->idNino = $_POST["idNino"];
        }

        $this->comprobar_insertar();
        $this->comprobar_borrar();

        $this->rellenar_ninos_select();
        $this->rellenar_regalos_select();

        $this->construir_tabla();
    }
    private function comprobar_insertar()
    {
        if (isset($_POST["idNino"]) && isset($_POST["idRegalo"])) {
            $this->insertar_regalo();
        }
    }
    private function comprobar_borrar()
    {
        if (isset($_POST["borrarRegalacion"])) {
            $this->borrar_regalo();
        }
    }
    function rellenar_ninos_select()
    {
        $sentencia = "SELECT * FROM ninos ORDER BY nombreNino";
        $result = mysqli_query($this->baseDeDatos->con, $sentencia);

        foreach ($result as $key => $value) {

            $isSelected = "";
            if ($this->idNino == $value["idNino"]) {
                $isSelected = "selected";
            }

            $this->ninos_options_HTML .= "<option $isSelected value='" . $value["idNino"] . "'> " . $value["nombreNino"] . " </option>";
        }
    }
    function rellenar_regalos_select()
    {
        $sentencia = "SELECT * FROM regalos ORDER BY 2";
        $result = mysqli_query($this->baseDeDatos->con, $sentencia);

        foreach ($result as $key => $value) {
            $this->regalos_options_HTML .= "<option value='" . $value["idRegalo"] . "'> " . $value["nombreRegalo"] . " </option>";
        }
    }
    private function construir_tabla()
    {
        // si nos han mandado el ID de un niño
        if (isset($_POST["idNino"])) {

            $this->idNino =  (int) filter_input(INPUT_POST, 'idNino', FILTER_SANITIZE_STRING);

            $sentencia = "SELECT idRegalacion, regalos.nombreRegalo, ninos.idNino, ninos.nombreNino, ninos.apellidosNino ";
            $sentencia .= "FROM regalospedidos JOIN ninos ON regalospedidos.idNinoFK = ninos.idNino ";
            $sentencia .= "JOIN regalos ON regalospedidos.idRegaloFK = regalos.idRegalo ";
            $sentencia .= "WHERE idNinoFK = $this->idNino ";
            $sentencia .= "ORDER BY ninos.nombreNino";

            $result = $this->baseDeDatos->select($sentencia);
            if (mysqli_num_rows($result) != 0) {
                foreach ($result as $key => $value) {

                    $nombreCompletoNino = $value["nombreNino"] . " " . $value["apellidosNino"];

                    $this->tabla_regalos_HTML .= '<tr>';
                    $this->tabla_regalos_HTML .= '  <th>' . ++$this->indice_tabla . ' </th>';
                    $this->tabla_regalos_HTML .= '  <th>' . $value["nombreRegalo"] . ' </th>';
                    $this->tabla_regalos_HTML .= '  <td>' . $nombreCompletoNino . '</td>';
                    // $this->tabla_regalos_HTML .= '  <th><div class="_boton_borrar btn btn-danger  " data-idRegalacion=' . $value["idRegalacion"] . ' data-idNino=' . $value["idNino"] . '>BORRAR</div></th>';

                    $this->tabla_regalos_HTML .= '</tr>';
                }
            } else {
                $this->tabla_regalos_HTML .= "<tr><td colspan='3'><p class='badnews text-center mt-3'><i class='bi bi-x-lg me-3'></i>Este niño no ha pedido regalos.</p></td></tr>";
            }
        }
    }
    private function insertar_regalo()
    {
        $idNino =       (int) filter_input(INPUT_POST, 'idNino', FILTER_SANITIZE_STRING);
        $idRegalo =     (int) filter_input(INPUT_POST, 'idRegalo', FILTER_SANITIZE_STRING);

        $sentencia = "SELECT * FROM regalospedidos WHERE idRegaloFK = $idRegalo AND idNinoFK = $idNino";
        $result = $this->baseDeDatos->select($sentencia);
        $regaloRepetido = mysqli_num_rows($result);

        if ($regaloRepetido == 0) {
            $sentencia = "INSERT INTO regalospedidos VALUES(NULL,$idRegalo,$idNino);";

            $this->accion_realizada = $this->baseDeDatos->insertar($sentencia, "Regalo pedido");
        } else {
            $this->accion_realizada = "<label class='badnews'><i class='bi bi-x-lg me-3'></i>No puedes añadir otra vez este regalo.</label>";
        }
    }
    private function borrar_regalo()
    {

        $idRegalacion =  (int) filter_input(INPUT_POST, 'borrarRegalacion', FILTER_SANITIZE_STRING);
        $sentencia = "DELETE FROM regalospedidos WHERE idRegalacion = $idRegalacion";

        $this->accion_realizada = $this->baseDeDatos->borrar($sentencia, "Regalo pedido");
    }
    public function getTablaRegalos()
    {
        return $this->tabla_regalos_HTML;
    }
    public function getAccion()
    {
        return $this->accion_realizada;
    }
    public function getNinosOptions()
    {
        return $this->ninos_options_HTML;
    }
    public function getRegalosOptions()
    {
        return $this->regalos_options_HTML;
    }
}

$busqueda = new Busqueda();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./src/img/Logo_1.png">
    <title>BUSQUEDA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="./src/css/styles.css">

    <style>

    </style>

</head>

<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                BUSQUEDA
            </a>
            <div>
                <a class="btn btn-secondary" href="./ninos.php" role="button">Niños</a>
                <a class="btn btn-secondary mx-2" href="./regalos.php" role="button">Regalos</a>
                <a class="btn btn-secondary mx-2" href="./reyes.php" role="button">Reyes Magos</a>
            </div>
        </div>
    </nav>





    <div class=" pb-5 mb-5 container">
        <h1 class="_decoration pt-5 text-center position-relative">BUSQUEDA</h1>
        <h2 class="pb-4 pt-5 fs-6 text-center"><?php echo $busqueda->getAccion() ?></h2>

        <table class="table  mt-3 ">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Niño</th>
                    <th scope="col">Regalo</th>
                    <!-- <th scope="col"></th> -->
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="5">
                        <div class="container-fluid">
                            <div class="row">
                                <form action="./busqueda.php" method="post" class="d-flex container w-100 ">
                                    <div class="col-8 p-1">
                                        <select class="form-select" name="idNino" id="idNino">
                                            <?php echo $busqueda->getNinosOptions() ?>
                                        </select>
                                    </div>
                                    <div class="col-4 p-1">
                                        <button type="submit" class="btn btn-primary w-100">BUSCAR</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <div class="container-fluid">
                            <div class="row">
                                <form action="./busqueda.php" method="post" class="d-flex container w-100 ">
                                    <div class="col-4 p-1">
                                        <select class="form-select" name="idNino">
                                            <?php echo $busqueda->getNinosOptions() ?>
                                        </select>
                                    </div>
                                    <div class="col-4 p-1">
                                        <select class="form-select" name="idRegalo">
                                            <?php echo $busqueda->getRegalosOptions() ?>
                                        </select>
                                    </div>
                                    <div class="col-4 p-1">
                                        <button type="submit" class="btn btn-success w-100">Agregar</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </th>
                </tr>
                <?php
                echo $busqueda->getTablaRegalos();
                ?>

            </tbody>
        </table>


    </div>



    <footer class="bg-dark text-light p-3 fixed-bottom">
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.10/dist/sweetalert2.all.min.js"></script>
    <script>
        // ESTO ES PARA QUE AL RECARGAR LA PÁGINA NO ME SALGA UN PROMPT DE "Resubmit the form?"
        window.history.replaceState(null, null, window.location.href);

        let botones_borrar = $("._boton_borrar");

        botones_borrar.click(function() {

            let idRegalacion = $(this).attr("data-idRegalacion");
            let idNino = $(this).attr("data-idNino");
            let html = "";

            html += "<form action='./busqueda.php' method='POST'>";
            html += "   <input name='borrarRegalacion' value=" + idRegalacion + "  class='d-none' />"
            html += "   <input name='idNino' value=" + idNino + "  class='d-none' />"
            html += "   <button type='submit' class='btn btn-danger px-5 mt-4 border-0'>Sí.</button>"
            html += "</form>";

            Swal.fire({
                title: 'Seguro que quieres quitarle este regalo?',
                text: "No puedes deshacer esta acción...",
                icon: 'warning',
                html: html,
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: '#198754',
                cancelButtonText: 'Cancelar'
            })
        });
    </script>


</body>

</html>