<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if(!isset($_SESSION['sesionPortalWeb'])){
    header("Location: login");
    exit;
}

// Base de Datos
include_once("clases/conexion.php");

$id         = $_SESSION['sesionPortalWeb'];
$sentencia  = $cnx->query("SELECT * FROM usuario WHERE id_user = '$id'");
$datos      = $sentencia->fetch(PDO::FETCH_ASSOC);
$usuario    = new usuario($datos['nombre'], $datos['apellido'], $datos['usuario'], $datos['contrasegna'], $datos['id_cargo'], $cnx, $datos['tipo'], $datos['id_user']);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/cuenta.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">

        <title>Cuenta</title>
    </head>
    <body>
        <div id="principalContainer">
            <?php
                include_once("partials/header.php");
                include_once("partials/nav.php");
            ?>

            <div class="post">
                <?php
                    if($_SESSION['tipo'] == 2){ ?>
                        <a href='usuarios' class='Opcion'>
                            <img src="images/encuesta-de-personas.svg" class="opcIcon">
                            <p>Gestionar Usuarios</p>
                        </a>

                        <a href="reporte" class="Opcion">
                            <img src="images/archivo-pdf.svg" class="opcIcon">
                            <p>Generar Reportes</p>
                        </a>
                    <?php } ?>
                    
                <div class="Opcion" onclick="mostrar()">
                    <img src="images/fuerza.svg" class="opcIcon">
                    <p>Cerrar Sesión</p>
                </div>
            </div>

            <section class="Cuerpo">
                <?php
                $usuario->MostrarUsuario(false);
                ?>
            </section>

            <div id="outModalCuenta"></div>
            <div id="modalCuenta">
                <h2 id="tituloModalCuenta"></h2>

                <div class="boton_modal" id="cancelar_Modalcuenta">Cancelar</div>
                <div class="boton_modal" id="aceptar_Modalcuenta" >Aceptar</div>
            </div>
        </div>
        <script src="JavaScript/cuenta.js"></script>
    </body>
</html>