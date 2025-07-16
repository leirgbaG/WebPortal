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
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/login.css">
        <title>Seguridad Alimentaria y Cultura Nutricional</title>
    </head>

    <body>
        <div class="main">
            <div class="nada"></div>
            <form action="controladores/registrar.php" method="post" autocomplete="off" class="registro">
            
                    <h1>Datos Personales</h1>
                    
                    <label for="prenom" class="leyenda">
                        <input type="text" id="prenom" name="prenom" class="input" placeholder="Primer Nombre" minlength="2" maxlength="20" autofocus required>
                        <!-- Mensaje error: Nombre -->
                        <div class="error" id="nameerr1" style="display: none;">El nombre no puede contener espacios en blanco.</div>
                        <div class="error" id="nameerr2" style="display: none;">El nombre no puede contener números.</div>
                        <?php
                            if(isset($_GET['nameerr1'])){
                                if($_GET['nameerr1'] == 1){
                                    echo "<div class=\"error\">No se pudo asignar el nombre.</div>";
                                }
                            }
                        ?>
                    </label>
                    
                    <label for="nom" class="leyenda">
                        <input type="text" id="nom" name="nom" class="input" placeholder="Primer Apellido" minlength="2" maxlength="20" required>
                        <!-- Mensaje error: Nombre -->
                        <div class="error" id="nameerr4" style="display: none;">El apellido no puede contener espacios en blanco.</div>
                        <div class="error" id="nameerr5" style="display: none;">El nombre no puede contener números.</div>
                        <?php
                            if(isset($_GET['nameerr2'])){
                                if($_GET['nameerr2'] == 1){
                                    echo "<div class=\"error\">No se pudo asignar el apellido.</div>";
                                }
                            }
                        ?>
                    </label>

                    <label for="cargo" class="leyenda">
                        <input type="text" id="cargo" name="cargo" class="input" list="cargos" placeholder="Cargo" required>
                        <?php
                            include_once("clases/conexion.php");
                            $cargo = $cnx->query("SELECT * FROM cargo");
                            echo "<datalist id=\"cargos\">";
                                while($lista = $cargo->fetch(PDO::FETCH_ASSOC)){
                                    echo "<option value=\"" . $lista['name_cargo'] . "\"></option>";
                                }
                            echo "</datalist>";
                            if(isset($_GET['cargoerr1'])){
                                if($_GET['cargoerr1'] == 1){
                                    echo "<div class=\"error\">No se pudo agregar el cargo.</div>";
                                }
                            }
                        ?>
                        <div class="error" id="cargerr1" style="display: none;">El cargo no puede contener números.</div>
                    </label>
                    <h1>Datos de la Cuenta</h1>

                    <label for="user" class="leyenda">
                        <input type="text" name="usuario" id="user" class="input" minlength="8" maxlength="16" placeholder="Usuario Único" required>
                        <!-- Mensajes de Error: Usuario -->
                        <div class="error" id="usererr1" style="display: none;">El usuario debe poseer entre ocho (8) y dieciséis (16) caracteres.</div>
                        <div class="error" id="usererr2" style="display: none;">El usuario no puede iniciar con números.</div>
                        <div class="error" id="usererr3" style="display: none;">El usuario no puede contener espacios en blanco.</div>
                        <div class="error" id="usererr4" style="display: none;">Usuario incorrecto. </div>
                        <?php
                            if(isset($_GET['usererr1'])){
                                if($_GET['usererr1'] == 1){
                                    echo "<div class=\"error\">No se pudo agregar el usuario.</div>";
                                }elseif($_GET['usererr1'] == 2){
                                    echo "<div class=\"error\">El usuario ya existe.</div>";
                                }
                            }
                        ?>
                    </label>
                    
                    <label for="pw1" class="leyenda">
                        <input type="password" name="clave" id="pw1" class="input" minlength="8" maxlength="16" placeholder="Contraseña" required>
                        <!-- Mensajes de Error: Contraseña -->
                        <div class="error" id="pwerr1" style="display: none;">El contraseña debe poseer entre ocho (8) y dieciséis (16) caracteres.</div>
                        <div class="error" id="pwerr2" style="display: none;">La contraseña no puede iniciar con números.</div>
                        <div class="error" id="pwerr3" style="display: none;">La contraseña no puede contener espacios en blanco.</div>
                        <?php
                            if(isset($_GET['pwerr1'])){
                                if($_GET['pwerr1'] == 1){
                                    echo "<div class=\"error\">No se pudo asignar la contraseña.</div>";
                                }
                            }
                        ?>
                    </label>
                    
                    <label for="pw2" class="leyenda">
                        <input type="password" name="pw2" id="pw2" class="input" minlength="8" maxlength="16" placeholder="Confirmar Contraseña" required>
                        <!-- Mensajes de Error: Contraseña -->
                        <div class="error" id="pwerr4" style="display: none;">Las contraseñas no coinciden.</div>
                        <?php
                            if(isset($_GET['pwerr2'])){
                                if($_GET['pwerr2'] == 1){
                                    echo "<div class=\"error\">Las contraseñas no coinciden.</div>";
                                }
                            }
                        ?>
                    </label>

                    <input type="submit" value="Registrarse" class="submit" id="enviar">

                
            </form>
            <div class="nuevo">
                <header class="Cuenta">
                    <h2>¿Ya tienes una cuenta?</h2>
                    <h4>Ingresa tus datos para acceder. 🙂‍↕️</h4>
                    <a href="login" class="register">Iniciar Sesión</a>
                </header>
                <footer>
                    <a href="https://uptparia.edu.ve/" target="blank">Universidad Politécnica Territorial de Paria</a><br>
                    <a href="periódico">Departamento de Seguridad Alimentaria y Cultura Nutricional</a> <br>
                    Developed by
                    <a target="_blank" href="https://www.instagram.com/_lledgll/">Eduardo González</a>,
                    <a target="_blank" href="https://www.instagram.com/yuliangel20/">Yuliangel Marcano</a>,
                    <a target="_blank" href="https://www.instagram.com/yulss_dv/">Yulyannys Cedeño</a>.
                </footer>
            </div>
        </div>
        <script src="JavaScript/ValidarRegister.js"></script>
    </body>
</html>