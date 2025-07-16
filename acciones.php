<?php
// Sesión
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if($_SESSION['tipo'] != 2){
    header('Location: cuenta');
    exit;
}

// Base de Datos
include_once 'clases/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/acciones.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Bitácora de Acciones</title>
    </head>
    <body>
        <div id="principalContainer">
            <?php
            include_once 'partials/header.php';
            include_once 'partials/nav.php';
            ?>

            <div class="post"></div>

            <section class="Cuerpo">
                <?php
                // Obtener la Cantidad de Registros en la BD
                $cant_acciones = $cnx->prepare('SELECT COUNT(*) FROM accion');
                $cant_acciones->execute();
                $cant_acciones = $cant_acciones->fetchColumn();

                // Conocer la Página
                if(empty($_GET['pag']) || $_GET['pag'] <= 0 || !is_int(intval($_GET['pag']))){
                    $pag = 1;
                }else{
                    $pag = $_GET['pag'];
                }

                // Calcular los Registros a Mostrar 
                $cant_a_mostrar = 20;
                $cant_pags = ceil($cant_acciones / $cant_a_mostrar);
                if($pag > $cant_pags) $pag = $cant_pags;
                $inicio = ($pag - 1) * $cant_a_mostrar;

                // Paginación
                if($cant_pags > 1){ ?>
                    <div class="paginacion">
                        <?php
                        if($pag > 1){
                            $pag_anterior = $pag - 1;
                            ?>
                            <a href="acciones-1" class="extremo_pag">«</a>
                            <a href="acciones-<?= $pag_anterior ?>" class="opcion_pag"><?= $pag_anterior ?></a>
                            <?php
                        } 
                        ?>
                        <span class="seleccion_pag"><?= $pag ?></span>
                        <?php if($cant_pags > $pag){ ?>
                            <a href="acciones-<?= $pag + 1 ?>" class="opcion_pag"><?= $pag + 1 ?></a>
                            <a href="acciones-<?= $cant_pags ?>" class="extremo_pag">»</a>
                        <?php } ?>
                    </div>
                <?php } 
                
                // Obtener Datos 
                $acciones = $cnx->prepare("SELECT * FROM accion a JOIN usuario u ON a.id_user = u.id_user ORDER BY id_accion DESC LIMIT $inicio, $cant_a_mostrar");
                $acciones->execute();
                ?>
                <div class="padre">
                    <table>
                        <thead>
                            <tr>
                                <th>#N      </th>
                                <th>Fecha   </th>
                                <th>Usuario </th>
                                <th>Acción  </th>
                                <th>Hora    </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            // Mostrar datos de la base de datos
                            $n = $inicio + 1;
                            if($cant_acciones == 0){
                                ?>
                                <tr>
                                    <td colspan="5" id="sinRegistro">No hay registros</td>
                                </tr>
                                <?php
                            }else while($row = $acciones->fetch(PDO::FETCH_ASSOC)){
                                $F = new DateTime($row['fecha_act']);
                                $H = new DateTime($row['hora_act']);
                                ?>
                                <tr>
                                    <td><?= $n                  ?></td> <!-- #N         -->
                                    <td><?= $F->format('d-m-Y') ?></td> <!-- Fecha      -->
                                    <td>@<?= $row['usuario']    ?></td> <!-- Usuario    -->
                                    <td><?= $row['accion_name'] ?></td> <!-- Acción     -->
                                    <td><?= $H->format('H:i')   ?></td> <!-- Hora       -->
                                </tr>
                                <?php
                                $n++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php
                // Paginación
                if($cant_pags > 1){ ?>
                    <div class="paginacion">
                        <?php
                        if($pag > 1){
                            $pag_anterior = $pag - 1;
                            ?>
                            <a href="acciones-1" class="extremo_pag">«</a>
                            <a href="acciones-<?= $pag_anterior ?>" class="opcion_pag"><?= $pag_anterior ?></a>
                            <?php
                        } 
                        ?>
                        <span class="seleccion_pag"><?= $pag ?></span>
                        <?php if($cant_pags > $pag){ ?>
                            <a href="acciones-<?= $pag + 1 ?>" class="opcion_pag"><?= $pag + 1 ?></a>
                            <a href="acciones-<?= $cant_pags ?>" class="extremo_pag">»</a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </section>
        </div>
    </body>
</html>