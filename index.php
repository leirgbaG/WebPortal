<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}

// Conexión BD
include_once("clases/conexion.php");

// Conocer la Cantidad de Registros por Aprobar 
if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 2){
    $cant_news_aprobar = $cnx->prepare("SELECT COUNT(*) FROM noticia WHERE estado_new = 0 OR estado_new = 3");
    $cant_news_aprobar->execute();
    $cant_news_aprobar = $cant_news_aprobar->fetchColumn();
}

// Conocer la Página
if(empty($_GET['pag']) || $_GET['pag'] <= 0 || !is_int(intval($_GET['pag']))){
    $pag = 1;
}else{
    $pag = $_GET['pag'];
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/noticias.css">
        <link rel="shortcut icon" href="images/logoSACN.png">
        <title>Periódico Virtual</title>
    </head>
    <body>

        <div id="principalContainer">
            <?php
                include_once("partials/header.php");

                if(isset($_SESSION['sesionPortalWeb'])){
                    include_once("partials/nav.php");
            ?>

            <div class="post">
                <?php
                // Opciones Extra 
                if(isset($_SESSION['tipo']) && ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2)){ ?>
                    <a href="noticia" class="Opcion">
                        <img src="images/editar.svg" class="opcIcon">
                        <p>Publicar Noticia</p>
                    </a>
                <?php }
                if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 2){ ?>
                    <a href="approveNews" class="Opcion">
                        <img src="images/verificacion-de-texto.svg" class="opcIcon">
                        <p>Aprobar Noticias: <?= $cant_news_aprobar?></p>
                    </a>
                    
                    <a href="resumen-noticia" class="Opcion">
                        <img src="images/arbol-de-mesa.svg" class="opcIcon">
                        <p>Resumen de Noticias</p>
                    </a>
                <?php } 
                ?>
            </div>
            <?php } ?>

            <main class="Cuerpo">
                    <?php
                    // Conocer la cantidad de Registros 
                    $sentencia = "SELECT * FROM noticia WHERE estado_new = 1";
                    if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 2){
                        $sentencia = "SELECT * FROM noticia WHERE estado_new = 1 OR estado_new = 2";
                    }
                    if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 1){
                        $sentencia = "SELECT * FROM noticia WHERE estado_new = 1 OR (estado_new = 2 AND id_user = " . $_SESSION['sesionPortalWeb'] . ")";
                    }
                    $cant_noticias = $cnx->prepare($sentencia);
                    $cant_noticias->execute();
                    $cant_noticias = $cant_noticias->rowCount();

                    // Calcular las noticias a Mostrar 
                    $cant_a_mostrar = 5;
                    $cant_pags = ceil($cant_noticias / $cant_a_mostrar);
                    if($pag > $cant_pags && $pag > 1) $pag = $cant_pags;
                    $inicio = ($pag - 1) * $cant_a_mostrar;
                    
                    // Paginación
                    if($cant_pags > 1){ ?>
                        <div class="paginacion">
                            <?php
                            if($pag > 1){
                                $pag_anterior = $pag - 1;
                                ?>
                                <a href="periódico-1" class="extremo_pag">«</a>
                                <a href="periódico-<?= $pag_anterior ?>" class="opcion_pag"><?= $pag_anterior ?></a>
                                <?php
                            } 
                            ?>
                            <span class="seleccion_pag"><?= $pag ?></span>
                            <?php if($cant_pags > $pag){ ?>
                                <a href="periódico-<?= $pag + 1 ?>" class="opcion_pag"><?= $pag + 1 ?></a>
                                <a href="periódico-<?= $cant_pags ?>" class="extremo_pag">»</a>
                            <?php } ?>
                        </div>
                        <?php
                    }

                    // Mostrar Noticias 
                    $query = $cnx->prepare($sentencia . " ORDER BY id_new DESC LIMIT $inicio, $cant_a_mostrar");
                    $query->execute();
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)){
                        $id             = $row['id_new'];
                        $titulo         = $row['ttle_new'];
                        $descripcion    = $row['corp_new'];
                        $fecha          = $row['fecha_new'];
                        $hora           = $row['hora_new'];
                        $estado         = $row['estado_new'];
                        $autor          = $row['id_user'];

                        $noticia = new Noticia($titulo, $descripcion, $autor, $cnx, $estado, $fecha, $hora, $id);
                        $noticia->MostrarNoticia();
                    }

                    // Paginación 
                    if($cant_pags > 1){?>
                        <div class="paginacion">
                            <?php
                            if($pag > 1){
                                $pag_anterior = $pag - 1;
                                ?>
                                <a href="periódico-1" class="extremo_pag">«</a>
                                <a href="periódico-<?= $pag_anterior ?>" class="opcion_pag"><?= $pag_anterior ?></a>
                                <?php
                            } 
                            ?>
                            <span class="seleccion_pag"><?= $pag ?></span>
                            <?php if($cant_pags > $pag){ ?>
                                <a href="periódico-<?= $pag + 1 ?>" class="opcion_pag"><?= $pag + 1 ?></a>
                                <a href="periódico-<?= $cant_pags ?>" class="extremo_pag">»</a>
                            <?php } ?>
                        </div>
                    <?php } ?>
            </main>
        </div>
        <div id="outModal_comments"></div>
        <script src="JavaScript/noticias.js"></script>
    </body>
</html>