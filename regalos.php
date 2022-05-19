<?php

include("./src/php/BBDD.php");

class Regalo
{
    private $baseDeDatos = null;
    private $tabla_HTML = "";
    private $accion_realizada = "";
    private $indice_tabla = 0;

    public function __construct()
    {
        $this->baseDeDatos = new BBDD();
        $this->comprobar_insertar();
        $this->comprobar_borrar();
        $this->comprobar_editar();
        $this->construir_tabla();
    }
    private function comprobar_insertar()
    {
        if (isset($_POST["agregarRegalo"])) {
            if (isset($_POST["nombre"]) && isset($_POST["precio"]) && isset($_POST["idReyMago"])) {
                $this->insertar();
            }
        }
    }
    private function comprobar_borrar()
    {
        if (isset($_POST["borrarRegalo"])) {


            $idRegalo = $_POST["borrarRegalo"];
            $sentencia = "SELECT * FROM regalospedidos WHERE idRegaloFK = $idRegalo";
            $result = $this->baseDeDatos->select($sentencia);
            $regaloEnUso = mysqli_num_rows($result);

            if ($regaloEnUso == 0) {
                $this->borrar();
            } else {
                $this->accion_realizada = "<span class='badnews'><i class='bi bi-x-lg me-2'></i>Este regalo ha sido pedido $regaloEnUso veces, no puedes borrarlo.</span>";
            }
        }
    }
    private function comprobar_editar()
    {
        if (isset($_POST["editarRegalo"])) {
            if (isset($_POST["nombre"]) && isset($_POST["precio"]) && isset($_POST["idReyMago"])) {
                $this->editar();
            }
        }
    }
    private function construir_tabla()
    {
        $sentencia = "SELECT idRegalo, nombreRegalo, precioRegalo, reyesmagos.nombreReyMago FROM regalos JOIN reyesmagos ON regalos.idReyMagoFK = reyesmagos.idReyMago ORDER BY nombreRegalo";
        $result = $this->baseDeDatos->select($sentencia);

        foreach ($result as $key => $value) {
            $this->tabla_HTML .= '<tr>';
            $this->tabla_HTML .= '  <th>' . ++$this->indice_tabla . ' </th>';
            $this->tabla_HTML .= '  <td>' . $value["nombreRegalo"] . '</td>';
            $this->tabla_HTML .= '  <td>' . $value["precioRegalo"] . '€</td>';
            $this->tabla_HTML .= '  <td>' . $value["nombreReyMago"] . '</td>';

            $this->tabla_HTML .= '  <th><form action="./regalos.php" method="POST">';
            $this->tabla_HTML .= '      <input name="abrirEditarRegalo" value="' . $value["idRegalo"] . '" type="hidden"/>';
            $this->tabla_HTML .= '      <button class="btn btn-primary">EDITAR</button>';
            $this->tabla_HTML .= '  </form></th>';

            $this->tabla_HTML .= '  <th><div class="_boton_borrar btn btn-danger " data-id=' . $value["idRegalo"] . '>BORRAR</div></th>';
            $this->tabla_HTML .= '</tr>';
        }
        return $this->tabla_HTML;
    }
    private function insertar()
    {
        $nombre =    filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $precio =    filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_STRING);
        $idReyMago = filter_input(INPUT_POST, 'idReyMago', FILTER_SANITIZE_STRING);

        if (strlen($nombre) >= 1 && $precio >= 1) {
            if (strlen($nombre) <= 100 && $precio <= 9999) {
                $sentencia = "INSERT INTO regalos VALUES(NULL,'$nombre','$precio',$idReyMago);";
                $this->accion_realizada = $this->baseDeDatos->insertar($sentencia, "Regalo");
            } else {
                $this->accion_realizada .= "<p class='badnews'><i class='bi bi-x-lg me-2'></i>Datos/precio de creción muy cortos.</p>";
            }
        } else {
            $this->accion_realizada .= "<p class='badnews'><i class='bi bi-x-lg me-2'></i>Datos/precio de creción muy cortos.</p>";
        }
    }
    private function editar()
    {
        $idRegalo =  (int) filter_input(INPUT_POST, 'editarRegalo', FILTER_SANITIZE_STRING);
        $nombre =    filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $precio =    (float) filter_input(INPUT_POST, 'precio', FILTER_SANITIZE_STRING);
        $idReyMago = (int) filter_input(INPUT_POST, 'idReyMago', FILTER_SANITIZE_STRING);

        if (strlen($nombre) >= 2 && $precio >= 1) {
            if (strlen($nombre) <= 100 && $precio <= 9999) {
                $sentencia = "UPDATE regalos SET nombreRegalo   = '$nombre'";
                $sentencia .= ", precioRegalo                   = $precio";
                $sentencia .= ", idReyMagoFK                    = $idReyMago WHERE idRegalo = $idRegalo;";

                $this->accion_realizada .= $this->baseDeDatos->editar($sentencia, "Regalo");
            } else {
                $this->accion_realizada .= "<p class='badnews'><i class='bi bi-x-lg me-2'></i>Datos de edición muy largos.</p>";
            }
        } else {
            $this->accion_realizada .= "<p class='badnews'><i class='bi bi-x-lg me-2'></i>Datos de edición muy cortos.</p>";
        }
    }
    private function borrar()
    {
        $idRegalo = $_POST["borrarRegalo"];
        $sentencia = "DELETE FROM regalos WHERE idRegalo = $idRegalo";

        $this->accion_realizada = $this->baseDeDatos->borrar($sentencia, "Regalo");
    }
    public function select_unico($id)
    {
        $sentencia = "SELECT * FROM regalos WHERE idRegalo = $id";
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


$regalo = new Regalo();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./src/img/Logo_1.png">
    <title>REGALOS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="./src/css/styles.css">

</head>

<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                REGALOS
            </a>
            <div>
                <a class="btn btn-secondary" href="./ninos.php" role="button">Niños</a>
                <a class="btn btn-secondary mx-2" href="./reyes.php" role="button">Reyes Magos</a>
                <a class="btn btn-secondary mx-2" href="./busqueda.php" role="button">Busqueda</a>
            </div>
        </div>
    </nav>

    <h1 class="_decoration pt-5 text-center position-relative">Lista de Regalos</h1>
    <h2 class="py-4 fs-6 text-center"><?php echo $regalo->getAccion() ?></h2>



    <div class="vh-100 vw-100 bg-dark bg-opacity-50 fixed-top <?php if (!isset($_POST["abrirEditarRegalo"])) echo "d-none"; ?>">
        <?php
        if (isset($_POST["abrirEditarRegalo"])) {
            $idRegalo = $_POST["abrirEditarRegalo"];
            $filaRegalo = $regalo->select_unico($idRegalo);

            // DATOS DEL REGALO A EDITAR
            $nombreRegalo =   $filaRegalo["nombreRegalo"];
            $precioRegalo = $filaRegalo["precioRegalo"];
            $reyMago =      $filaRegalo["idReyMagoFK"];
            // echo $reyMago;
            if ($reyMago == 0) {
                $options = '<option value="0" selected>Melchor</option>
                    <option value="1">Gaspar</option>
                    <option value="2">Baltasar</option>';
            } else if ($reyMago == 1) {
                $options = '<option value="0">Melchor</option>
                    <option value="1" selected>Gaspar</option>
                    <option value="2">Baltasar</option>';
            } else {
                $options = '<option value="0">Melchor</option>
                    <option value="1">Gaspar</option>
                    <option value="2" selected>Baltasar</option>';
            }
        }
        ?>
        <div>
            <div class="position-absolute top-50 start-50 translate-middle w-50 bg-light p-4 shadow-lg">
                <div class="position-relative">
                    <div class="position-absolute top-0 end-0">
                        <a href="./regalos.php" class="text-decoration-none py-1 px-3 bg-dark text-light">x</a>
                    </div>
                </div>
                <h2 class="mb-5">Editar el regalo <span id="editartRegaloNombre"></span></h2>
                <form action="./regalos.php" method="post" class=" w-100">

                    <div class="mb-3">
                        <label>Nombre:</label>
                        <input class="form-control mt-2 " type="text" name="nombre" placeholder="Nombre" required value="<?php echo $nombreRegalo; ?>" />
                    </div>
                    <div class="mb-3">
                        <label>Precio:</label>
                        <input class="form-control mt-2 " type="number" name="precio" min="1" step="0.01" placeholder="Precio" required value="<?php echo $precioRegalo; ?>" />
                    </div>

                    <div class="mt-3">
                        <label>Rey Mago:</label>
                        <select name="idReyMago" class="form-select mt-2 " required>
                            <?php
                            echo $options;
                            ?>
                        </select>
                    </div>



                    <input type="hidden" name="editarRegalo" required value="<?php echo $idRegalo; ?>" />

                    <button type="submit" id="agregar" class="btn btn-success w-100 mt-2  mt-4 ">Editar</button>
                </form>
            </div>
        </div>
    </div>



    <div class="mb-5 pb-5">
        <table class="table container mt-2 ">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Rey Mago</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th></th>
                    <th colspan="5">
                        <form action="./regalos.php" method="post" class="d-flex container w-100">
                            <div class="d-flex w-100">
                                <div class="flex-fill p-2">
                                    <input class="form-control mt-2" type="text" name="nombre" />
                                </div>
                                <div class="flex-fill p-2">
                                    <input class="form-control mt-2" type="number" name="precio" />
                                </div>
                                <div class="flex-fill p-2">
                                    <select name="idReyMago" class="form-select mt-2">
                                        <option value="0">Melchor</option>
                                        <option value="1">Gaspar</option>
                                        <option value="2">Baltasar</option>
                                    </select>
                                </div>

                                <input type="hidden" name="agregarRegalo">

                                <div class="flex-fill p-2">
                                    <button type="submit" id="agregar" class="btn btn-success w-100 mt-2 ">Agregar</button>
                                </div>
                            </div>
                        </form>
                    </th>

                </tr>
                <?php echo $regalo->getTabla(); ?>

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
            html += "<form action='./regalos.php' method='POST'>";
            html += "   <input type='hidden' name='borrarRegalo' value=" + id + " />"
            html += "   <button type='submit' class='btn btn-danger px-5 mt-4 '>Sí.</button>"
            html += "</form>";

            Swal.fire({
                title: 'Seguro que quieres tirar este regalo?',
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