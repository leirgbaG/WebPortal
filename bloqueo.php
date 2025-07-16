<?php
session_start();
if(!isset($_SESSION['estado'])){
    header('Location: login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/bloqueo.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">

        <title>Usuario Bloqueado</title>
    </head>
    <body>
        <main>
            <div class="mensaje">
                <h1>Usuario Bloqueado</h1>
                <p class="info">
                    Parece que un moderador decidió bloquear tu cuenta. <br>
                    No puedes acceder a la plataforma hasta que un moderador te desbloquee, lo sentimos.
                </p>
                <p class="extra">
                    Estos bloqueos son hechos de manera indefinida cuando un usuario realiza alguna acción indebida, si sientes que se trata de un error, te recomendamos dirigirte al departamento de Seguridad Alimentaria y Cultura Nutricional y contactarte con algún moderador y explica tu situación.
                </p>

                <div class="salir_container">
                    <a href="logout" class="salir">Regresar</a>
                </div>
            </div>
        </main>
    </body>
</html>