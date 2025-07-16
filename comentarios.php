<?php
// Sesión 
session_start();
if(!isset($_SESSION['sesionPortalWeb'])){
    header('Location: login');
    exit;
}

// Base de Datos 
include_once("clases/conexion.php");

$id = $_GET['new'];
$pag = $_GET['pag'];

$cant = $cnx->prepare("SELECT COUNT(*) FROM comentario WHERE id_new = :id");
$cant->bindParam(':id', $id, PDO::PARAM_INT);
$cant->execute();
$cant = $cant->fetchColumn();

$comments = $cnx->prepare("SELECT * FROM comentario WHERE id_new = :id AND pos_comment = 0 ORDER BY id_comment ASC");
$comments->bindParam(':id', $id, PDO::PARAM_INT);
$comments->execute();
$comments = $comments->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/noticias.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Comentarios</title>
    </head>
    <body>
        <div id="principalContainer">
            <?php
            include_once("partials/header.php");
            include_once("partials/nav.php");
            ?>

            <div class="post"></div>

            <div class="Cuerpo">
                <?php
                $n = $cnx->prepare("SELECT * FROM noticia WHERE id_new = :id");
                $n->bindParam(':id', $id, PDO::PARAM_INT);
                $n->execute();
                $n = $n->fetch(PDO::FETCH_ASSOC);
                
                $noticia = new Noticia($n['ttle_new'], $n['corp_new'], $n['id_user'], $cnx, $n['estado_new'], $n['fecha_new'], $n['hora_new'], $n['id_new']);
                $noticia->MostrarNoticia();
                ?>

                <div class="comentarios">
                    <a href="periódico-<?=$pag?>#<?=$id?>" id="regresar-a-periódico">Regresar</a><br><br>

                    <h2>Comentarios (<?= $cant ?>)</h2>
                    <section>
                        <?php $a = 0;
                        foreach($comments as $c){
                            (new Comentario($c['id_new'], $c['id_user'], $c['text_comment'], $cnx, $c['pos_comment'], $c['fecha_comment'], $c['id_comment']))->MostrarComentario();
                            $a++;
                        }
                        if(!$a){ ?>
                            <br><br>
                            <p>No hay comentarios</p>
                        <?php } ?>
                        <br><br><br>
                    </section>
                    <form action="controladores/comentar.php?new=<?=$id?>&pag=<?=$pag?>" method="post" id="escribir">
                        <textarea spellcheck="true" id="comentar_areafalsa" placeholder="Escribe tu comentario..."></textarea>
                        <textarea name="comentario" id="comentar_area" hidden></textarea>
                        <input type="checkbox" name="posicion" value='0' id="posicion" checked hidden>

                        <label for="enviar-comentario" id="label-enviar-comentario">
                            <img src="images/paper-plane.svg" alt="Enviar" id="foto-enviar-comentario">
                            <input type="submit" value="" id="enviar-comentario" hidden>
                        </label>
                    </form>
                </div>
            </div>
        </div>
        
        <script src="JavaScript/comentario.js"></script>
    </body>
</html>