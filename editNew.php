<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if(!isset($_SESSION['sesionPortalWeb']) || ($_SESSION['tipo'] > 2 || $_SESSION['tipo'] < 1)){
    header("Location: periódico");
    exit;
}

if($_SESSION['tipo'] == 2){
    $enviar_texto = 'Editar Noticia';
}else{
    $enviar_texto = 'Enviar a Revisión';
}


include_once("clases/conexion.php");
$id_new = $_GET['new'];

$noticias = $cnx->prepare("SELECT * FROM noticia WHERE id_new = :id");
$noticias->bindParam(':id', $id_new, PDO::PARAM_INT);
$noticias->execute();
$noticia = $noticias->fetch(PDO::FETCH_ASSOC);

$multimedia = $cnx->prepare("SELECT * FROM multimedia WHERE id_new = :id");
$multimedia->bindParam(':id', $id_new, PDO::PARAM_INT);
$multimedia->execute();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/noticias.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Editar Noticia</title>
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
                <form action="controladores/editarNoticia.php?id=<?= $id_new ?>" method="post" class="formulario">
                    <h1>Modificar Noticia</h1>

                    <label for="ttle" class="leyenda">
                        <h2>Título de Noticia</h2>
                        <input type="text" id="ttle" name="ttle" class="input" spellcheck="true" placeholder="Título de la noticia..." placeholder="Título de la noticia" value="<?= $noticia['ttle_new'] ?>" required>
                    </label>

                    <label for="descripcion" class="leyenda">
                        <h2>Descripción de Noticia</h2>
                        <textarea rows="5" id="descripcion" class="input" spellcheck="true" placeholder="Cuerpo de la Noticia..." required><?= str_replace("<br>", "\n", $noticia['corp_new']) ?></textarea>

                        <textarea name="corp" id="enviable" hidden><?= $noticia['corp_new'] ?></textarea> 
                    </label>

                    <div id="imgsContainer">
                        <?php
                        while($row = $multimedia->fetch(PDO::FETCH_ASSOC)){
                            if(preg_match("/\.mp4$/", $row['media'])){
                                ?>
                                <video src="<?= $row['media'] ?>" controls></video>
                                <?php
                            }else{
                                ?>
                                <img src="<?= $row['media'] ?>" alt="Imagen de la noticia">
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <input type="submit" value="<?= $enviar_texto ?>" class="enviar leyenda" title="Una vez rellenados los campos, presionar este botón hará que la noticia sea enviada a los moderadores para ser aprobada.">
                </form>
            </div>
        </section>
        <script src="JavaScript/publicarNoticias.js"></script>
    </body>
</html>