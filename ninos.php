<?php

include("./src/php/BBDD.php");

class Nino
{
    private $baseDeDatos = null;
    private $tabla_HTML = "";
    private $indice_tabla = 0;
    private $accion_realizada = "";

    public function __construct()
    {
        $this->baseDeDatos = new BBDD();
        // CRUD
        // CREATE
        $this->comprobar_insertar();
        // DELETE
        $this->comprobar_borrar();
        // UPDATE
        $this->comprobar_editarNino();
        // READ
        $this->construir_tabla();
    }
    private function comprobar_insertar()
    {
        // si NO estamos editando
        if (isset($_POST["agregarNino"])) {
            if (isset($_POST["nombre"]) && isset($_POST["apellidos"]) && isset($_POST["fechaNacimiento"]) && isset($_POST["bondad"])) {
                $this->insertar();
            }
        }
    }
    private function comprobar_borrar()
    {
        if (isset($_POST["borrarNino"])) {
            $this->borrar();
        }
    }
    private function comprobar_editarNino()
    {
        if (isset($_POST["editarNino"])) {
            if (isset($_POST["nombre"]) && isset($_POST["apellidos"]) && isset($_POST["fechaNacimiento"]) && isset($_POST["bondad"])) {
                $this->editar();
            }
        }
    }
    private function construir_tabla()
    {

        $sentencia = "SELECT * FROM ninos ORDER BY nombreNino";
        $result = $this->baseDeDatos->select($sentencia);


        foreach ($result as $key => $value) {

            $esBueno = ($value["esBuenoNino"] == 0) ? "No" : "Sí";

            $this->tabla_HTML .= '<tr>';
            $this->tabla_HTML .= '  <th>' . ++$this->indice_tabla . ' </th>';
            $this->tabla_HTML .= '  <td>' . $value["nombreNino"] . '</td>';
            $this->tabla_HTML .= '  <td>' . $value["apellidosNino"] . '</td>';
            $this->tabla_HTML .= '  <td>' . $value["fechaNacimientoNino"] . '</td>';
            $this->tabla_HTML .= '  <th>' . $esBueno . '</th>';

            $this->tabla_HTML .= '  <th><form action="./ninos.php" method="POST">';
            $this->tabla_HTML .= '      <input name="abrirEditarNino" value="' . $value["idNino"] . '" type="hidden"/>';
            $this->tabla_HTML .= '      <button class="btn btn-primary">EDITAR</button>';
            $this->tabla_HTML .= '  </form></th>';

            $this->tabla_HTML .= '  <th><div class="_boton_borrar btn btn-danger  " data-id=' . $value["idNino"] . '>BORRAR</div></th>';
            $this->tabla_HTML .= '</tr>';
        }
        return $this->tabla_HTML;
    }
    private function insertar()
    {

        $nombre =           filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $apellidos =        filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_STRING);
        $fechaNacimiento =  filter_input(INPUT_POST, 'fechaNacimiento', FILTER_SANITIZE_STRING);
        $bondad =           filter_input(INPUT_POST, 'bondad', FILTER_SANITIZE_STRING);

        $sentencia = "INSERT INTO ninos VALUES(NULL,'$nombre','$apellidos','$fechaNacimiento',$bondad);";

        $this->accion_realizada .= $this->baseDeDatos->insertar($sentencia, "Niño");
    }
    private function borrar()
    {

        $idNino = $_POST["borrarNino"];

        // BORRAR EN LA TABLA NIÑOS
        $sentencia = "DELETE FROM ninos WHERE idNino = $idNino";
        $this->accion_realizada .= $this->baseDeDatos->borrar($sentencia, "Niño");

        // BORRAR TODOS LOS PEDIDOS DE REGALOS QUE HA PEDIDO 
        $sentencia = "DELETE FROM regalospedidos WHERE idNinoFK = $idNino";
        $this->accion_realizada .= $this->baseDeDatos->borrar($sentencia, "Regalos pedidos del niño");
    }
    private function editar()
    {

        $idNino = (int) filter_input(INPUT_POST, 'editarNino', FILTER_SANITIZE_STRING);
        $nombre =       filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $apellidos =    filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_STRING);
        $fechaNacimiento =         filter_input(INPUT_POST, 'fechaNacimiento', FILTER_SANITIZE_STRING);
        $bondad =       filter_input(INPUT_POST, 'bondad', FILTER_SANITIZE_STRING);

        if (strlen($nombre) >= 2 && strlen($apellidos) >= 2 && strlen($fechaNacimiento) == 10) {
            if (strlen($nombre) <= 50 && strlen($apellidos) <= 100){
                // SENTENCIA UPDATE
                $sentencia = "UPDATE ninos SET nombreNino = '$nombre'";
                $sentencia .= ", apellidosNino       = '$apellidos'";
                $sentencia .= ", fechaNacimientoNino = '$fechaNacimiento'";
                $sentencia .= ", esBuenoNino         = $bondad WHERE idNino = $idNino;";
                // EDITAR 
                $this->accion_realizada .= $this->baseDeDatos->editar($sentencia, "Niño");
            } else {
                $this->accion_realizada .= "<p class='badnews'><i class='bi bi-x-lg me-2'></i>Datos de edición muy largos.</p>";
            }
        } else {
            $this->accion_realizada .= "<p class='badnews'><i class='bi bi-x-lg me-2'></i>Datos de edición muy cortos.</p>";
        }
    }
    public function select_unico($id)
    {
        $sentencia = "SELECT * FROM ninos WHERE idNino = $id";
        $result = $this->baseDeDatos->select($sentencia);
        foreach ($result as $key => $fila) {
            return $fila;
        }
    }
    public function getTabla()
    {
        return $this->tabla_HTML;
    }
    public function getAccion()
    {
        return $this->accion_realizada;
    }
}

$nino = new Nino();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./src/img/Logo_1.png">
    <title>NIÑOS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./src/css/styles.css">

</head>

<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                NIÑOS
            </a>
            <div>
                <a class="btn btn-secondary mx-2" href="./regalos.php" role="button">Regalos</a>
                <a class="btn btn-secondary mx-2" href="./reyes.php" role="button">Reyes Magos</a>
                <a class="btn btn-secondary" href="./busqueda.php" role="button">Busqueda</a>
            </div>
        </div>
    </nav>



    <div class="vh-100 vw-100 position-absolute top-0 start-0 bg-dark bg-opacity-50 fixed-top <?php if (!isset($_POST["abrirEditarNino"])) echo "d-none"; ?>">
        <?php
        if (isset($_POST["abrirEditarNino"])) {
            $idNino = $_POST["abrirEditarNino"];
            // OBTENER DATOS DEL NIÑO
            $filaNino = $nino->select_unico($idNino);

            // VARIABLES DEL NIÑÖ
            $nombreNino = $filaNino["nombreNino"];
            $apellidosNino = $filaNino["apellidosNino"];
            $fechaNacimientoNino = $filaNino["fechaNacimientoNino"];
            $esBuenoNino = $filaNino["esBuenoNino"];
            if ($esBuenoNino) {
                $options = '<option value="1" selected>Sí</option>
                    <option value="0">No</option>';
            } else {
                $options = '<option value="1">Sí</option>
                    <option value="0" selected>No</option>';
            }
        }
        ?>
        <div>
            <div class="position-absolute top-50 start-50 translate-middle w-50 bg-light p-4 shadow-lg">
                <div class="position-relative">
                    <div class="position-absolute top-0 end-0">
                        <a href="./ninos.php" class="text-decoration-none py-1 px-3 bg-dark text-light">x</a>
                    </div>
                </div>
                <h2 class="mb-5">Editar a <?php echo $nombreNino ?></h2>
                <form action="./ninos.php" method="post" class=" w-100">

                    <div class="mb-3">
                        <label>Nombre y apellidos:</label>
                        <input class="form-control mt-2 " type="text" name="nombre" placeholder="Nombre" required value="<?php echo $nombreNino; ?>" />
                        <input class="form-control mt-2 " type="text" name="apellidos" placeholder="Apellidos" required value="<?php echo $apellidosNino; ?>" />
                    </div>

                    <div class="mt-3">
                        <label>Fecha de nacimiento:</label>
                        <input class=" form-control mt-2 " type="date" name="fechaNacimiento" required value="<?php echo $fechaNacimientoNino ?>" />

                    </div>

                    <div class="mt-3">
                        <label for="bondad">¿Ha sido bueno?</label>
                        <select name="bondad" id="bondad" class="form-select mt-2" required>
                            <?php
                            echo $options;
                            ?>
                        </select>
                    </div>

                    <input type="hidden" value="<?php echo $idNino ?>" name="editarNino" required />

                    <button type="submit" id="agregar" class="btn btn-success w-100 mt-2  mt-4">Editar</button>
                </form>
            </div>
        </div>
    </div>


    <div class="pb-5 mb-5 container">
        <h1 class="_decoration pt-5 text-center position-relative">Lista de Niños</h1>
        <h2 class="my-4 fs-6 text-center"><?php echo $nino->getAccion(); ?></h2>

        <table class="table  mt-3 pb-5 mb-5">

            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Fecha Nacimiento</th>
                    <th scope="col">Ha sido bueno?</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th></th>
                    <th colspan="6">
                        <form action="./ninos.php" method="post" class="d-flex container w-100">

                            <div class="flex-fill p-2">
                                <input class="form-control mt-2 " type="text" name="nombre" required />
                            </div>
                            <div class="flex-fill p-2">
                                <input class="form-control mt-2 " type="text" name="apellidos" required />
                            </div>

                            <div class="flex-fill p-2 d-flex">
                                <input class=" form-control mt-2" type="date" name="fechaNacimiento" required />
                            </div>

                            <div class="flex-fill p-2">
                                <select name="bondad" id="bondad" class="form-select mt-2" required>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <input type="hidden" name="agregarNino">

                            <div class="flex-fill p-2">
                                <button type="submit" id="agregar" class="btn btn-success w-100 mt-2 ">Agregar</button>
                            </div>
                        </form>
                    </th>
                </tr>
                <?php echo $nino->getTabla() ?>

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
            let id = $(this).attr("data-id");
            let html = "";
            html += "<form action='./ninos.php' method='POST'>";
            html += "   <input name='borrarNino' value=" + id + "  class='d-none' />"
            html += "   <button type='submit' class='btn btn-danger px-5 mt-4 border-0'>Sí.</button>"
            html += "</form>";

            Swal.fire({
                title: 'Seguro que te quieres deshacer este niño?',
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