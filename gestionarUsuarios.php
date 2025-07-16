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
if($_SESSION['tipo'] != 2){
    header("Location: cuenta.php");
    exit;
}

// Base de Datos
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
        <title>Gestión de Usuarios</title>
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
                        <a href="resumen-usuario" class="Opcion">
                            <img src="images/arbol-de-mesa.svg" class="opcIcon">
                            <p>Resumen de Usuarios</p>
                        </a>
                        <a href="acciones" class="Opcion">
                            <img src="images/config.svg" class="opcIcon">
                            <p>Acciones de Usuarios</p>
                        </a>
                    <?php }
                ?>
            </div>

            <section class="Cuerpo">
                <?php
                // Conocer la Cantidad de Usuarios 
                $cant_users = $cnx->prepare("SELECT COUNT(*) FROM usuario WHERE estado_user != -1");
                $cant_users->execute();
                $cant_users = $cant_users->fetchColumn();

                // Conocer la Página
                if(empty($_GET['pag']) || $_GET['pag'] <= 0 || !is_int(intval($_GET['pag']))){
                    $pag = 1;
                }else{
                    $pag = $_GET['pag'];
                }

                // Calcular Usuarios a Mostrar 
                $cant_a_mostrar = 5;
                $cant_pags = ceil($cant_users / $cant_a_mostrar);
                if($pag > $cant_pags) $pag = $cant_pags;
                $inicio = ($pag - 1) * $cant_a_mostrar;

                // Paginación Superior
                if($cant_pags > 1){ ?>
                    <div class="paginacion">
                        <?php
                        if($pag > 1){
                            $pag_anterior = $pag - 1;
                            ?>
                            <a href="usuarios-1" class="extremo_pag">«</a>
                            <a href="usuarios-<?= $pag_anterior ?>" class="opcion_pag"><?= $pag_anterior ?></a>
                            <?php
                        } 
                        ?>
                        <span class="seleccion_pag"><?= $pag ?></span>
                        <?php if($cant_pags > $pag){ ?>
                            <a href="usuarios-<?= $pag + 1 ?>" class="opcion_pag"><?= $pag + 1 ?></a>
                            <a href="usuarios-<?= $cant_pags ?>" class="extremo_pag">»</a>
                        <?php } ?>
                    </div>
                <?php }

                $usuario = $cnx->query("SELECT * FROM usuario WHERE estado_user != -1 ORDER BY id_user DESC LIMIT $inicio, $cant_a_mostrar");
                while($row = $usuario->fetch(PDO::FETCH_ASSOC)){
                    $nombre     = $row['nombre'];
                    $apellido   = $row['apellido'];
                    $tipo       = $row['tipo'];
                    $user       = $row['usuario'];
                    $pw         = $row['contrasegna'];
                    $cargo      = $row['id_cargo'];
                    $id         = $row['id_user'];
                    $estado     = $row['estado_user'];

                    $user = new Usuario($nombre, $apellido, $user, $pw, $cargo, $cnx, $tipo, $id, $estado);
                    $user->MostrarUsuario();
                }

                // Paginación Inferior
                if($cant_pags > 1){ ?>
                    <div class="paginacion">
                        <?php
                        if($pag > 1){
                            $pag_anterior = $pag - 1;
                            ?>
                            <a href="biblioteca-1" class="extremo_pag">«</a>
                            <a href="biblioteca-<?= $pag_anterior ?>" class="opcion_pag"><?= $pag_anterior ?></a>
                            <?php
                        } 
                        ?>
                        <span class="seleccion_pag"><?= $pag ?></span>
                        <?php if($cant_pags > $pag){ ?>
                            <a href="biblioteca-<?= $pag + 1 ?>" class="opcion_pag"><?= $pag + 1 ?></a>
                            <a href="biblioteca-<?= $cant_pags ?>" class="extremo_pag">»</a>
                        <?php } ?>
                    </div>
                <?php }
                ?>
            </section>

            <div id="outModalCuenta"></div>
            <div id="modalCuenta">
                <h2 id="tituloModalCuenta"></h2>
    
                <div class="boton_modal" id="cancelar_Modalcuenta">Cancelar</div>
                <div class="boton_modal" id="aceptar_Modalcuenta">Aceptar</div>
            </div>
        </div>
        <script src="JavaScript/cuenta.js"></script>
    </body>
</html>
