<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if(!isset($_SESSION['sesionPortalWeb'])){
    header('Location: login');
    exit;
}

// Base de Datos 
include_once("clases/conexion.php");

$tipo_resumen = $_GET['tipo'];
if($tipo_resumen != "usuario" && $tipo_resumen != "documento" && $tipo_resumen != "noticia" && $tipo_resumen != "peticion"){
    header('Location: periódico');
    exit;
}
$tipo_resumen .= preg_match("/n$/", $tipo_resumen) ? "es" : "s";
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <?php
        // Detectar Estilos Necesarios 
        switch($tipo_resumen){
            case "usuarios":
                echo"<link rel='stylesheet' href='css/cuenta.css'>";
                break;
            case "documentos":
                echo"<link rel='stylesheet' href='css/biblioteca.css'>";
                break;
            case "noticias":
                echo"<link rel='stylesheet' href='css/noticias.css'>";
                break;
            case "peticiones":
                echo"<link rel='stylesheet' href='css/solicitudes.css'>";
                break;
        }
        ?>

        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/resumen.css">

        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Resumen de <?= $tipo_resumen ?></title>

        <!-- Estilo de DataTables -->
        <link href="DataTables/datatables.min.css" rel="stylesheet">
        <link href="DataTables/datatables.css" rel="stylesheet">
    </head>
    <body>
        <section id="principalContainer">
            <?php
            include_once("partials/header.php");
            include_once("partials/nav.php");
            ?>

            <div class="post">

            </div>

            <div class="Cuerpo">
                <section class="resumen">
                    <table id="datatable">
                        <?php
                        switch($tipo_resumen){
                            case "usuarios": 
                                Usuario::Resumen_Usuarios($cnx);
                                break;

                            case "documentos":
                                Documento::Resumen_Documentos($cnx);
                                break;

                            case "noticias":
                                Noticia::Resumen_Noticias($cnx);
                                break;

                            case "peticiones":
                                Peticion::Resumen_Peticiones($cnx);
                                break;
                        }
                        ?>
                    </table>
                </section>
            </div>

            <div id="outModalCuenta" style="display: none;"></div>
            <div id="modalCuenta" style="display: none;">
                <h2 id="tituloModalCuenta"></h2>

                <div class="boton_modal" id="cancelar_Modalcuenta">Cancelar</div>
                <div class="boton_modal" id="aceptar_Modalcuenta">Aceptar</div>
            </div>
        </section>
        
        <script src="JavaScript/cuenta.js"></script>
        <!-- "Librerías" de DataTable -->
        <script src="DataTables/datatables.min.js"></script>
        <script src="JavaScript/resumen.js"></script>
    </body>
</html>