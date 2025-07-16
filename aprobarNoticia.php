<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if($_SESSION['tipo'] != 2){
    header('Location: periódico');
    exit;
}

// Base de Datos 
include_once 'clases/conexion.php';

// Obtener Noticias por aprobar 
$select = $cnx->prepare('SELECT * FROM noticia WHERE estado_new = :e ORDER BY id_new DESC');
$e = isset($_POST['del']) ? 3 : 0;
$select->bindParam(':e', $e, PDO::PARAM_INT); 
$select->execute();

$clase1 = isset($_POST['del']) ? '' : 'select';
$clase2 = isset($_POST['del']) ? 'select' : '';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/noticias.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Aprobar Noticias</title>
    </head>
    <body>
        <div id="principalContainer">
            <?php
            include_once("partials/header.php");
            include_once("partials/nav.php");
            ?>
            <div class="post"></div>

            <div class="Cuerpo">
                <form action="" method="post" id="seleccion_tipo_approve">
                    <input type="submit" value="Noticias por Publicar" name="pub" class="<?= $clase1 ?>">
                    <input type="submit" value="Noticias por Eliminar" name="del" class="<?= $clase2 ?>">
                </form>

                <?php
                $time = 0;
                while($row = $select->fetch(PDO::FETCH_ASSOC)){
                    $id             = $row['id_new'];
                    $titulo         = $row['ttle_new'];
                    $descripcion    = $row['corp_new'];
                    $fecha          = $row['fecha_new'];
                    $hora           = $row['hora_new'];
                    $estado         = $row['estado_new'];
                    $autor          = $row['id_user'];

                    $noticia = new Noticia($titulo, $descripcion, $autor, $cnx, $estado, $fecha, $hora, $id);
                    $noticia->MostrarNoticia();
                    $time++;
                }
                if($time == 0){
                    echo '<p id="noNews">No hay noticias por aprobar. 😊</p>';
                }
                ?>
            </div>
        </div>
    </body>
</html>