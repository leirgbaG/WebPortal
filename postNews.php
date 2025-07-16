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
if($_SESSION['tipo'] < 1 || $_SESSION['tipo'] > 2){
    header("Location: periódico");
    exit;
}

if($_SESSION['tipo'] == 2){
    $mensaje_boton_publicar = "Publicar Noticia";
}else{
    $mensaje_boton_publicar = "Enviar a Revisión";
}

if(isset($_GET['error'])){
    if($_GET['error'] == 'p'){
        if($_SESSION['tipo'] == 2){
            $mensaje_error = "<b>Noticia publicada exitosamente.</b>";
        }else{
            $mensaje_error = "<b>Noticia enviada a los moderadores.</b>";
        }
        $class_mensaje_error = '';
    }else{
        $a = $_GET['error'];
        $errores = explode("-", $a);
        $mensaje_error = "<b>Error al publicar la noticia:</b> <br>";
        foreach($errores as $error){
            if($error == 't'){
                $mensaje_error .= "Título inválido. <br>";
            }
            if($error == 'd'){
                $mensaje_error .= "Descripción inválida. <br>";
            }
            if($error == 'f'){
                $mensaje_error .= "Formato de multimedia inválido. <br>";
            }
            if($error == 's'){
                $mensaje_error .= "Tamaño de la multimedia inválida. <br> Tam. máx. imágenes: 5MiB <br> Tam. máx. videos: 1GiB <br>";
            }
        }

        $class_mensaje_error = 'error';
        $mensaje_error = substr_replace($mensaje_error, '', strrpos($mensaje_error, '<br>'), strlen('<br>'));
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/noticias.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Publicar Noticias</title>
    </head>
    <body>
        <?php 
        if(isset($_GET['error'])){ ?>
            <span class="mensaje_error <?= $class_mensaje_error ?>" id="Mensaje_Error_Noticias">
                <?= $mensaje_error ?>
            </span>

            <script>
                window.addEventListener('load', () => {
                    setTimeout(() => {
                        document.querySelector('#Mensaje_Error_Noticias').style.right = '0';
                    }, 500);
                });
                document.querySelector('#Mensaje_Error_Noticias').addEventListener('click', ()=>{
                    document.querySelector('#Mensaje_Error_Noticias').style.right = '-600px';
                    setTimeout(() => {
                        document.querySelector('#Mensaje_Error_Noticias').style.display = 'none'
                    }, 20000);
                })

                setTimeout(()=>{
                    document.querySelector('#Mensaje_Error_Noticias').style.right = '-600px';
                    setTimeout(() => {
                        document.querySelector('#Mensaje_Error_Noticias').style.display = 'none'
                    }, 200);
                }, 5000);
            </script>
        <?php } ?>

        <div id="principalContainer">
            <?php
                include_once("partials/header.php");
                include_once("partials/nav.php");
            ?>
            
            <div class="post">
                
            </div>

            <section class="Cuerpo">
                
                <form action="controladores/publicarNoticias.php" method="post" class="formulario" enctype="multipart/form-data">
                    <h1>Publicar Noticias</h1>

                    <label for="title" class="leyenda">
                        <input type="text" id="title" name="titulo" class="input" spellcheck="true" placeholder="Título de la noticia..." maxlength="200" autofocus required>
                    </label>
                    
                    <label for="descripcion" class="leyenda">
                        <textarea id="descripcion" rows="5" class="input" spellcheck="true" placeholder="Cuerpo de la Noticia..." required></textarea>

                        <textarea name="descripcion" id="enviable" hidden></textarea>
                    </label>

                    <div id="imgsContainer">

                    </div>
                    
                    <label for="multimedia" class="leyenda">
                        <h4>Adjuntar multimedia:</h4>
                        <input type="file" id="multimedia" name="multimedia[]" class="input" accept="image/png, image/jpeg, image/jpg, video/mp4" multiple>
                    </label>

                    <input type="submit" value="<?= $mensaje_boton_publicar ?>" class="enviar leyenda" title="Una vez rellenados los campos, presionar este botón hará que la noticia sea enviada a los moderadores para ser aprobada.">
                </form>
            </section>
        </div>
        <script src="JavaScript/publicarNoticias.js"></script>
    </body>
</html>