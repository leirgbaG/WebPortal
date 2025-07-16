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

// Base de datos 
include_once("clases/conexion.php");


// Datos
$pag = $_GET['pag'] ?? '1';


// Conocer la cantidad de Registros 
$sentencia = "SELECT * FROM documento WHERE estado_doc = 0 ORDER BY ttle_doc ASC";
$url = "biblioteca-";
$_GET['tipo'] = isset($_GET['tipo']) ? $_GET['tipo'] : 'name';
$_GET['busqueda'] = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$c_n = 'checked';
$c_c = '';
$c_k = '';
if(strlen($_GET['busqueda']) > 0 && $_GET['tipo'] == 'name'){
    // Búsqueda de Nombre 
    $url = "biblioteca.php?busqueda=$_GET[busqueda]&tipo=$_GET[tipo]&pag=";
    $sentencia = "SELECT * FROM documento WHERE ttle_doc LIKE '%".$_GET['busqueda']."%' AND estado_doc = 0 ORDER BY ttle_doc ASC";
    $c_n = 'checked';
    $c_c = '';
    $c_k = '';
}elseif(strlen($_GET['busqueda']) > 0 && $_GET['tipo'] == 'clasf'){
    // Búsqueda de Clasificaciones 
    $url = "biblioteca.php?busqueda=$_GET[busqueda]&tipo=$_GET[tipo]&pag=";
    $sentencia = "SELECT * FROM documento d JOIN clasificacion c ON d.id_clasf = c.id_clasf WHERE c.name_clasf LIKE '%".$_GET['busqueda']. "%' AND estado_doc = 0 ORDER BY c.name_clasf DESC";
    $c_n = '';
    $c_c = 'checked';
    $c_k = '';
}elseif(strlen($_GET['busqueda']) > 0 && $_GET['tipo'] == 'kw'){
    // Búsqueda de Palabras Clave 
    $url = "biblioteca.php?busqueda=$_GET[busqueda]&tipo=$_GET[tipo]&pag=";
    $sentencia = "SELECT * FROM palabraclave pc JOIN documento_palabraclave dpc ON dpc.id_kw = pc.id_kw JOIN documento d ON dpc.id_doc = d.id_doc WHERE pc.name_kw LIKE '%". $_GET['busqueda'] ."%' AND d.estado_doc = 0 ORDER BY pc.name_kw ASC";
    $c_n = '';
    $c_c = '';
    $c_k = 'checked';
}elseif($_GET['tipo'] == 'name'){
    // Ordenar por Nombre
    $url = "biblioteca.php?busqueda=&tipo=name&pag=";
    $sentencia = "SELECT * FROM documento WHERE estado_doc = 0 ORDER BY ttle_doc ASC";
    $c_n = 'checked';
    $c_c = '';
    $c_k = '';
}elseif($_GET['tipo'] == 'clasf'){
    // Ordenar por Clasificación
    $url = "biblioteca.php?busqueda=&tipo=clasf&pag=";
    $sentencia = "SELECT * FROM documento d JOIN clasificacion c ON d.id_clasf = c.id_clasf WHERE estado_doc = 0 ORDER BY c.name_clasf ASC";
    $c_n = '';
    $c_c = 'checked';
    $c_k = '';
}elseif($_GET['tipo'] == 'kw'){
    // Ordenar por Palabras Clave
    $url = "biblioteca.php?busqueda=&tipo=kw&pag=";
    $sentencia = "SELECT * FROM palabraclave pc JOIN documento_palabraclave dpc ON dpc.id_kw = pc.id_kw JOIN documento d ON dpc.id_doc = d.id_doc WHERE d.estado_doc = 0 GROUP BY d.ttle_doc ORDER BY pc.name_kw ASC";
    $c_n = '';
    $c_c = '';
    $c_k = 'checked';
}

$cant_libros = $cnx->prepare($sentencia);
$cant_libros->execute();
$cant_libros = $cant_libros->rowCount();

// Calcular los libros a Mostrar 
$cant_a_mostrar = 6;
$cant_pags = ceil($cant_libros / $cant_a_mostrar);
if($pag > $cant_pags && $pag > 1) $pag = $cant_pags;
$inicio = ($pag - 1) * $cant_a_mostrar;

?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/biblioteca.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Biblioteca</title>

        <script>
            function Eliminar(id){
                if(confirm("¿Quieres eliminar este libro? \nEs un acto irreversible.")){
                    window.location = `deleteBook${id}`;
                }
            }
        </script>
    </head>
    <body>
        <div id="principalContainer">
            <?php
            include_once("partials/header.php");
            include_once("partials/nav.php");
            ?>

            <div class="post">
                <?php
                if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2){ 
                    ?>
                    <a href="postDoc" class="Opcion">
                        <img src="images/libro-flecha-arriba.svg" class="opcIcon">
                        <p>Publicar Documento</p>
                    </a>
                    <?php
                }
                ?>

                <a href="solicitudes" class="Opcion">
                    <img src="images/comentarios-pregunta.svg" class="opcIcon">
                    <p>
                        <?php if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2){ ?>
                            Solicitudes de Documentos
                        <?php } else { ?>
                            Solicitar Documento
                        <?php } ?>
                    </p>
                </a>
                <?php
                    if($_SESSION['tipo'] == 2){
                        ?>
                        <a href="resumen-documento" class="Opcion">
                            <img src="images/arbol-de-mesa.svg" class="opcIcon">
                            <p>Resumen de Documentos</p>
                        </a>
                        <?php
                    }
                ?>
            </div>

            <section class="Cuerpo">
                <form class="buscador" tabindex="0" method="GET" action="biblioteca.php">
                    <label for="buscar" id="label_buscar">
                        <img src="images/busqueda.svg" class="lupa" alt="?">
                    </label>
                    <input type="submit" value="buscar" id="buscar" hidden>

                    <input type="search" name="busqueda" id="input_search" placeholder="Buscar..." class="search" tabindex="0">
                    <div class="extra_searchers">
                        <label for="nombre" class="extra_search" id="label_nombre">
                            Nombre
                        </label>
                        <label for="clasf" class="extra_search" id="label_clasf">
                            Clasificación
                        </label>
                        <label for="kw" class="extra_search" id="label_kw">
                            Palabras Clave
                        </label>

                        <input type="radio" value="name" name="tipo" id="nombre" <?= $c_n ?> hidden>
                        <input type="radio" value="clasf" name="tipo" id="clasf" <?= $c_c ?> hidden>
                        <input type="radio" value="kw" name="tipo" id="kw" <?= $c_k ?> hidden>
                    </div>
                </form>
                

                <!-- Paginación -->
                <?php if($cant_pags > 1){ ?>
                    <div class="paginacion">
                        <?php
                        if($pag > 1){
                            $pag_anterior = $pag - 1;
                            ?>
                            <a href="<?= $url ?>1" class="extremo_pag">«</a>
                            <a href="<?= $url . $pag_anterior ?>" class="opcion_pag"><?= $pag_anterior ?></a>
                            <?php
                        } 
                        ?>
                        <span class="seleccion_pag"><?= $pag ?></span>
                        <?php if($cant_pags > $pag){ ?>
                            <a href="<?= $url . $pag + 1 ?>" class="opcion_pag"><?= $pag + 1 ?></a>
                            <a href="<?= $url . $cant_pags ?>" class="extremo_pag">»</a>
                        <?php } ?>
                    </div>
                <?php } ?>

                <!-- Mostrar Documentos -->
                <div class="estante">
                    <?php
                    $query = $cnx->prepare($sentencia . " LIMIT $inicio, $cant_a_mostrar");
                    $query->execute();
                    while($row = $query->fetch()){
                        $documento = new Documento($row['ttle_doc'], $row['corp_doc'], $row['docx'], $row['id_clasf'], $cnx, null, $row['id_doc']);
                        $documento->mostrar_documento();
                    }
                    ?>
                </div>

                <!-- Paginación -->
                <?php if($cant_pags > 1){ ?>
                    <div class="paginacion">
                        <?php
                        if($pag > 1){
                            $pag_anterior = $pag - 1;
                            ?>
                            <a href="<?= $url ?>1" class="extremo_pag">«</a>
                            <a href="<?= $url . $pag_anterior ?>" class="opcion_pag"><?= $pag_anterior ?></a>
                            <?php
                        } 
                        ?>
                        <span class="seleccion_pag"><?= $pag ?></span>
                        <?php if($cant_pags > $pag){ ?>
                            <a href="<?= $url . $pag + 1 ?>" class="opcion_pag"><?= $pag + 1 ?></a>
                            <a href="<?= $url . $cant_pags ?>" class="extremo_pag">»</a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </section>
            

            <?php if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2){ ?>
                <div id="outModalBiblioteca"></div>
                <div id="modalEliminarLibro">
                    <h2>¿Deseas eliminar este libro?</h2>
                    <p>Se perderán sus datos y es un cambio irreversible.</p>

                    <div class="boton_modalEliminar" id="cancelar_modalEliminar">Cancelar</div>
                    <div class="boton_modalEliminar" id="eliminar_modalEliminar">Eliminar</div>
            </div>
            <?php } ?>
        </div>
        <script src="JavaScript/libros.js"></script>
    </body>
</html>