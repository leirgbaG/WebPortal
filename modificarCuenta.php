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

$_SESSION['CambiarUsuario'] = trim($_GET['user']);

if($_SESSION['usuario'] != $_SESSION['CambiarUsuario'] && $_SESSION['tipo'] != 2){
    header("Location: cuenta");
    exit;
}

// Conexión Base de Datos 
include_once("clases/conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/cuenta.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        
        <title>Modificar Cuenta</title>
    </head>
    <body>
        <div id="principalContainer">
            <?php
                include_once("partials/header.php");
                include_once("partials/nav.php");
            ?>

            <div class="post">

            </div>

            <div class="Cuerpo">
                <?php
                    $user = $_GET['user'];
                    $sentencia  = $cnx->prepare("SELECT * FROM usuario JOIN cargo ON usuario.id_cargo = cargo.id_cargo WHERE usuario=:user");
                    $sentencia->bindParam(':user', $user, PDO::PARAM_STR);
                    $sentencia->execute();
                    $usuario    = $sentencia->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="caja">
                    <form action="controladores/modUser.php" class="modificarDatos personal" method="post" autocomplete="off">
                        <fieldset>
                            <legend>Datos Personales</legend>

                            <label for="nombre">
                                <h1>Nombre</h1>
                                <input type="text" value=<?php echo"'$usuario[nombre]'"; ?> id="nombre" name="nombre" class="input">
                            </label>

                            <label for="apellido">
                                <h1>Apellido</h1>
                                <input type="text" value=<?php echo"'$usuario[apellido]'"; ?> id="apellido" name="apellido" class="input">
                            </label>

                            <label for="cargo">
                                <h1>Cargo</h1>
                                <input type="text" value=<?php echo"'$usuario[name_cargo]'"; ?> id="cargo" name="cargo" class="input" list="cargos">
                                <?php
                                    $cargo = $cnx->query("SELECT * FROM cargo");
                                    echo "<datalist id=\"cargos\">";
                                        while($lista = $cargo->fetch(PDO::FETCH_ASSOC)){
                                            echo "<option value=\"" . $lista['name_cargo'] . "\"></option>";
                                        }
                                    echo "</datalist>";
                                ?>
                            </label>
                        </fieldset>
                        <input type="submit" value="Realizar Cambios" name="cambioPersonales" class="boton">
                    </form>
                        
                    <form action="controladores/modUser.php" class="modificarDatos cuenta" method="post" autocomplete="off">
                        <fieldset>
                        <legend>Datos de la cuenta</legend>
                        <?php
                            if($_SESSION['usuario'] != $_GET['user'] && $_SESSION['tipo'] == 2){ ?>
                                    <label for="tipo">
                                        <h1>Tipo de Usuario</h1>
                                        <select id="tipo" name="tipo" class="input">
                                            <option value="0" <?php if($usuario['tipo'] == 0){ ?>selected<?php } ?> >Visitante</option>
                                            <option value="1" <?php if($usuario['tipo'] == 1){ ?>selected<?php } ?> >Administrador(a)</option>
                                            <option value="2" <?php if($usuario['tipo'] == 2){ ?>selected<?php } ?> >Moderador(a)</option>
                                        </select>
                                    </label>
                    <?php   } ?>

                            
                            <?php if($_SESSION['usuario'] == $user){ ?>
                                <label for="password">
                                    <h1>Ingrese su contraseña actual:</h1>
                                    <input type="password" id="password" name="password" class="input">
                                </label>
                            <?php } ?>
                            <label for="newPassword">
                                <h1>Ingrese nueva contraseña:</h1>
                                <input type="password" id="newPassword" name="newPassword" class="input">
                            </label>
                            <label for="newPassword2">
                                <h1>Repita nueva contraseña:</h1>
                                <input type="password" name="newPassword2" id="newPassword2" class="input">
                            </label>
                        </fieldset>

                        <input type="submit" value="Realizar Cambios" class="boton">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>