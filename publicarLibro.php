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
    header("Location: biblioteca");
    exit;
}

// Base de datos 
include_once("clases/conexion.php");


// Obetener Errores 
if(isset($_GET['error'])){
    $errores = $_GET['error'];
    if($errores == 'p'){
        $mensaje_error = "<b>Documento Publicado.</b>";
        $class_mensaje_error = '';
    }else{
        $errores = explode('-', $errores);
        $mensaje_error = "<b>Error al publicar el documento:</b> <br>";
        foreach($errores as $error){
            if($error == 't'){
                $mensaje_error .= 'Error en el título. <br>';
            }
            if($error == 'd'){
                $mensaje_error .= 'Error en la descripción. <br>';
            }
            if($error == 'c'){
                $mensaje_error .= 'Error en la categoría. <br>';
            }
            if($error == 's'){
                $mensaje_error .= 'El tamaño del archivo supera el límite permitido. Tam. máx: 10MiB. <br>';
            }
            if($error == 'f'){
                $mensaje_error .= 'Formato del archivo no permitido. Solo se permite PDF. <br>';
            }
            if($error == 'k'){
                $mensaje_error .= 'Error en las palabras clave. <br>';
            }
            if($error == 'a'){
                $mensaje_error .= 'Error en el nombre del autor. <br>';
            }
            if($error == 'n'){
                $mensaje_error .= 'Error en el apellido del autor. <br>';
            }
            if($error == 'z'){
                $mensaje_error .= 'Error en los datos. <br>';
            }
        }

        $class_mensaje_error = 'error';
        $mensaje_error = substr_replace($mensaje_error, '', strrpos($mensaje_error, '<br>'), strlen('<br>'));
    }
}



// Recibir Datos
$link = "controladores/newDocument.php";
$boton_archivo = "Seleccionar Archivo";
$accion = "Publicar Libro";

// Libro 
if(!empty($_GET['id_doc'])){
    $id_doc = $_GET['id_doc'];
    $link = "controladores/editDoc.php?id_doc=". $id_doc;
    $boton_archivo = "Reemplazar Archivo";
    $accion = "Editar Libro";

    $doc = $cnx->prepare("SELECT * FROM documento d JOIN clasificacion c ON d.id_clasf = c.id_clasf WHERE id_doc = :id");
    $doc->bindParam(':id', $id_doc, PDO::PARAM_INT);
    $doc->execute();
    $doc = $doc->fetch(PDO::FETCH_ASSOC);

    $kws = $cnx->prepare("SELECT * FROM documento_palabraclave dpc JOIN palabraclave pc ON dpc.id_kw = pc.id_kw WHERE dpc.id_doc = :id");
    $kws->bindParam(':id', $id_doc, PDO::PARAM_INT);
    $kws->execute();
    $kw= "";
    $a = 0;
    while($row = $kws->fetch(PDO::FETCH_ASSOC)){
        $kw .= $row['name_kw']." ";
        $a++;
    }

    $autor = $cnx->prepare("SELECT * FROM autor_documento ad JOIN autor a ON a.id_autor = ad.id_autor WHERE ad.id_doc = :id");
    $autor->bindParam(':id', $id_doc, PDO::PARAM_INT);
    $autor->execute();
    $nombres = [];
    $apellidos = [];
    $a = 0;
    while($row = $autor->fetch(PDO::FETCH_ASSOC)){
        $nombres[$a] = $row['name'];
        $apellidos[$a] = $row['surname'];
        $a++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/publicarLibro.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Publicar Libro</title>
    </head>
    <body>
        <?php if(isset($_GET['error'])){ ?>
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
                    document.querySelector('#Mensaje_Error_Noticias').style.right = '-660px';
                    setTimeout(() => {
                        document.querySelector('#Mensaje_Error_Noticias').style.display = 'none'
                    }, 20000);
                })

                setTimeout(()=>{
                    document.querySelector('#Mensaje_Error_Noticias').style.right = '-660px';
                    setTimeout(() => {
                        document.querySelector('#Mensaje_Error_Noticias').style.display = 'none'
                    }, 660);
                }, 5000);
            </script>
        <?php } ?>



        <div id="principalContainer">
            <?php
                include_once("partials/header.php");
                include_once("partials/nav.php");
            ?>

            <div class="post"></div>

            <section class="Cuerpo">
                <form id="crearDocForm" method="post" action="<?= $link ?>" autocomplete="off" enctype="multipart/form-data">

                    <h2 id="título"><?= $accion ?></h2>
                    <section id="info">
                        <label for="titulo">
                            <?php if(isset($_GET['id_doc'])){ ?> <h4>Título</h4> <?php } ?>
                            <input type="text" name="titulo" class="textInput" id="titulo" placeholder="Título del Libro" title="Título del Libro" maxlength="200" required  <?php if(isset($id_doc)){echo 'value="'.$doc['ttle_doc'].'"'; } ?> >
                        </label>

                        <label for="descripcion">
                            <?php if(isset($_GET['id_doc'])){ ?> <h4>Descripción</h4> <?php } ?>
                            <textarea class="textInput" id="descripcion" placeholder="Descripción del Libro" title="Descripción del Libro" rows="6" maxlength="4000" required><?php if(isset($id_doc)) echo $doc['corp_doc']; ?></textarea>

                            <textarea name="descripcion" id="enviable" hidden></textarea>
                        </label>

                        <label for="clasf">
                            <?php if(isset($_GET['id_doc'])){ ?> <h4>Clasificación</h4> <?php } ?>
                            <input type="text" list="clasif" class="textInput" id="clasf" name="clasf" placeholder="Clasificación del Libro" title="Clasificación del Libro" required <?php if(isset($id_doc)){echo 'value="'.$doc['name_clasf'].'"'; } ?> >
                            <datalist id="clasif">
                                <?php 
                                    $query = $cnx->prepare("SELECT * FROM clasificacion");
                                    $query->execute();
                                    while($row = $query->fetch(PDO::FETCH_ASSOC)){
                                        echo "<option value='" . $row['name_clasf'] . "'></option>";
                                    }
                                ?>
                            </datalist>
                        </label>
                        
                        <label for="kw">
                            <?php if(isset($_GET['id_doc'])){ ?> <h4>Palabras Clave</h4> <?php } ?>
                            <input type="text" id="kw" class="textInput" name="kws" placeholder="Palabras Clave" title="Las palabras clave serán separadas con cada espacio." required oninput="mostrarKws()" <?php if(isset($id_doc)){echo 'value="'.$kw.'"'; } ?> >
                            <div id="palabra"></div>
                        </label>
                    </section>
                    
                    <section id="pdf">
                        <label for="docx">
                            <h4>Archivo</h4>
                            <div id="docx_btn"><?= $boton_archivo ?></div>
                            <div id="texto_docx">No hay archivo seleccionado.</div>
                            <input type="file" name="docx" id="docx" accept="application/pdf" <?php if(!isset($id_doc)) echo "required"; ?> hidden>
                        </label>

                        <section id="autores">
                            <?php if(isset($_GET['id_doc'])){ ?> <h4>Autor(es)</h4> <?php } ?>
                            <section>
                                <label>
                                    <input type="text" class="autor autor_prenom" name="nombre_autor[]"   placeholder='Nombre del Autor'   required <?php if(isset($id_doc)){echo 'value="'.$nombres[0].'"'; } ?> >
                                    <input type="text" class="autor autor_nom" name="apellido_autor[]" placeholder='Apellido del Autor' required <?php if(isset($id_doc)){echo 'value="'.$apellidos[0].'"'; } ?> >
                                </label>
                            </section>

                            <?php
                            if(isset($id_doc))
                                for($a = 1; $a < count($nombres); $a++){
                                    ?>
                                    <section>
                                        <label>
                                            <input type="text" class="autor autor_prenom" name="nombre_autor[]"   placeholder='Nombre del Autor' <?php if(isset($id_doc)){echo 'value="'.$nombres[$a].'"'; } ?> >
                                            <input type="text" class="autor autor_nom" name="apellido_autor[]" placeholder='Apellido del Autor' <?php if(isset($id_doc)){echo 'value="'.$apellidos[$a].'"'; } ?> >
                                        </label>
                                    </section>
                                    <?php
                                }
                            ?>
                        </section>
                        <img src="images/agregar.svg" width="20px" title="Agregar nuevo Autor" onclick="CrearAutores()">
                    </section>

                    <label for="enviar" id="enviarForm">
                        <input type="submit" value="<?= $accion ?>" id="enviar">
                    </label>

                </form>
            </section>
        </div>
        <script src="JavaScript/formLibros.js"></script>
    </body>
</html>