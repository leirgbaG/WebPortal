<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if(isset($_SESSION['sesionPortalWeb'])){
    header("Location: periódico");
    exit;
}
include_once("clases/conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/login.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Seguridad Alimentaria y Cultura Nutricional</title>
    </head>

    <body>
        <div class="main">
            <div class="nada"></div>
            
                <!-- Formulario -->
                <form action="controladores/login.php" method="post" class="registro">
                    <h1>Iniciar Sesión</h1>

                    <!-- Usuario -->
                    <label for="user" class="leyenda">
                        <input type="text" name="usuario" id="user" class="input" minlength="8" maxlength="16" autofocus required placeholder="Usuario">
                        <!-- Mensajes de Error: Usuario -->
                        <div class="error" id="usererr1" style="display: none;">El usuario debe poseer entre ocho (8) y quince (15) caracteres.</div>
                        <div class="error" id="usererr2" style="display: none;">El usuario no puede iniciar con números.</div>
                        <div class="error" id="usererr3" style="display: none;">El usuario no puede contener espacios en blanco.</div>
                        <?php
                            if(isset($_GET['error'])){
                                if($_GET['error'] == 'usererr4'){ 
                                    echo "<div class=\"error\">Usuario incorrecto.</div>"; 
                                }
                            }
                        ?>
                    </label>

                    <!-- Contraseña -->
                    <label for="pw" class="leyenda">
                        <input type="password" name="clave" id="pw" class="input" minlength="8" maxlength="16" required placeholder="Contraseña">
                        <!-- Mensajes de Error: Contraseña -->
                        <div class="error" id="pwerr1" style="display: none;">El contraseña debe poseer entre ocho (8) y quince (15) caracteres.</div>
                        <div class="error" id="pwerr2" style="display: none;">Contraseña incorrecta.</div>
                        <?php
                            if(isset($_GET['error'])){
                                if($_GET['error'] == 'pwerr3'){ 
                                    echo "<div class=\"error\">Contraseña incorrecta.</div>"; 
                                }
                            }
                        ?>
                    </label>

                    <!-- Enviar -->
                    <input type="submit" value="Ingresar" id="enviar" class="submit">
                    <label for="enviar">
                        <?php
                            if(isset($_GET['error'])){
                                if($_GET['error'] == 'error'){
                                    echo "<div class=\"error\">Usuario o Contraseña incorrectos.</div>";
                                }
                                elseif($_GET['error'] == 'registro'){
                                    echo "<div class=\"error\">Registro completado.</div>";
                                }elseif($_GET['error'] == 'errorRegistro'){
                                    echo "<div class=\"error\">Error al registrar.</div>";
                                }
                            }
                        ?>
                    </label>
                </form><script src="JavaScript/ValidarLogin.js"></script>

            
            <div class="nuevo">
                <header class="Cuenta">
                    <h2>¿No tienes una cuenta?</h2>
                    <h4>Regístrate para continuar. 😊</h4>
                    <a href="sign_in" class="register">Registrarse</a>
                </header><br>

                <footer>
                        <a href="https://uptparia.edu.ve/" target="blank">Universidad Politécnica Territorial de Paria</a><br>
                        <a href="periódico">Departamento de Seguridad Alimentaria y Cultura Nutricional</a><br>
                        Developed by
                        <a target="_blank" href="https://www.instagram.com/_lledgll/">Eduardo González</a>,
                        <a target="_blank" href="https://www.instagram.com/yuliangel20/">Yuliangel Marcano</a>,
                        <a target="_blank" href="https://www.instagram.com/yulss_dv/">Yulyannys Cedeño</a>.
                </footer>
            </div>
        </div>
    </body>
</html>