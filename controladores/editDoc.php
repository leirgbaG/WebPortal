<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 2){
    header("Location: biblioteca");
    exit;
}

// Base de Datos 
include_once '../clases/conexion.php';

if(!is_dir('../'.$rutaDocs)){
    mkdir('../'.$rutaDocs, 0777, true);
}

$id_doc = $_GET['id_doc'];

// Obtener Datos Actuales de la Base de Datos 
#DatosLibro
$libro = $cnx->prepare("SELECT * FROM documento d JOIN clasificacion c ON d.id_clasf = c.id_clasf WHERE d.id_doc = :id_doc");
$libro->bindParam(':id_doc', $id_doc, PDO::PARAM_INT);
$libro->execute();
$libro = $libro->fetch(PDO::FETCH_ASSOC);

#PalabrasClaveLibro
$kws = $cnx->prepare("SELECT pc.id_kw, pc.name_kw FROM documento_palabraclave dpc JOIN palabraclave pc ON dpc.id_kw = pc.id_kw WHERE dpc.id_doc = :id_doc");
$kws->bindParam(':id_doc', $id_doc, PDO::PARAM_INT);
$kws->execute();
$kws = $kws->fetchAll(PDO::FETCH_ASSOC);

#PalabrasClaveGeneral
$palabrasClave = $cnx->prepare("SELECT * FROM palabraclave");
$palabrasClave->execute();
$palabrasClave = $palabrasClave->fetchAll(PDO::FETCH_ASSOC);

#AutoresLibro
$autorDocumento_DB = $cnx->prepare("SELECT * FROM autor_documento ad JOIN autor a ON ad.id_autor = a.id_autor WHERE ad.id_doc = :id_doc");
$autorDocumento_DB->bindParam(':id_doc', $id_doc, PDO::PARAM_INT);
$autorDocumento_DB->execute();
$autorDocumento_DB = $autorDocumento_DB->fetchAll(PDO::FETCH_ASSOC);

#AutoresGeneral
$autores_DB = $cnx->prepare("SELECT * FROM autor");
$autores_DB->execute();
$autores_DB = $autores_DB->fetchAll(PDO::FETCH_ASSOC);

// Obtener datos del Formulario
$titulo         = trim($_POST['titulo']);
$descripcion    = trim($_POST['descripcion']);
$clasf          = trim($_POST['clasf']);
$keywords       = explode(" ", trim($_POST['kws'])); 
$name_autor     = $_POST['nombre_autor'];
$surname_autor  = $_POST['apellido_autor'];
$docx           = $_FILES['docx']['tmp_name'];
$docx_name      = $_FILES['docx']['name'];
$docx_format    = strtolower(pathinfo($docx_name, PATHINFO_EXTENSION));
$docx_size      = $_FILES['docx']['size'];
$actualizar = [];

// Validaciones 
#Título
if(!empty($titulo) && $titulo != $libro['ttle_doc'] && strlen($titulo) < 200){
    array_push($actualizar, 'titulo');
}

#Cuerpo
if(strlen($descripcion) < 4000 && !empty($descripcion) && $descripcion != $libro['corp_doc']){
    array_push($actualizar, 'descripcion');
}

#Clasificación
if(!(strlen($clasf) > 50 || preg_match("/\d/", $clasf)) && !empty($clasf) && $clasf != $libro['name_clasf']){
    array_push($actualizar, 'clasf');
    echo $clasf;
}

#Documento
if(!empty($docx)){
    array_push($actualizar, 'docx');
    if($docx_size > 10*1024*1024){
        array_pop($actualizar);
    }elseif($docx_format != "pdf"){
        array_pop($actualizar);
    }
}

#PalabrasClave
$kw_validas = [];
foreach($keywords as $keyword){
    if(empty($keyword) || strlen($keyword) > 50){
        continue;
    }
    array_push($kw_validas, $keyword);
}
if(count($kw_validas) > 0){
    array_push($actualizar, 'kws');
}

#Autores
$autores = [];
if(count($name_autor) == count($surname_autor)){
    foreach($name_autor as $i => $nombre){
        if((!empty(trim($nombre)) && !empty(trim($surname_autor[$i]))) && preg_match('/[a-z]/i', trim($nombre)) && preg_match('/[a-z]/i', trim($surname_autor[$i])) && strlen(trim($nombre)) < 30 && strlen(trim($surname_autor[$i])) < 30){
            $aux = ['name' => trim($nombre), 'surname' => trim($surname_autor[$i])];
        }else{
            $aux = ['name' => false, 'surname' => false];
        }
        array_push($autores, $aux);
    }

    $autor_existente = [];
    $autor_igual = [];
    if(count($autores) > count($autorDocumento_DB)){
        $arreglo_aux = $autores;
        $arreglo_aux1= $autorDocumento_DB;
        $tipo = false;
    }else{
        $arreglo_aux = $autorDocumento_DB;
        $arreglo_aux1= $autores;
        $tipo = true;
    }

    #ComprobarSiEnLibro
    foreach($arreglo_aux as $i => $autor){
        $aux = 0;
        foreach($arreglo_aux1 as $autor1){
            if($autor1['name'] == $autor['name'] && $autor1['surname'] == $autor['surname']){
                $aux = ($tipo) ? $autor['id_autor'] : $autor1['id_autor'];
            }
        }
        $autor_igual[$i] = $aux;
    }

    #ComprobarSiExiste
    foreach($autores as $i => $autor){
        $aux = 0;
        foreach($autores_DB as $autor_DB){
            if($autor['name'] == $autor_DB['name'] && $autor['surname'] == $autor_DB['surname']){
                $aux = $autor_DB['id_autor'];
            }
        }
        if($autor['name'] == false) $aux = -1;
        $autor_existente[$i] = $aux;
        
        echo $autor['name'] . " " . $autor['surname'] . "= $autor_igual[$i] : $autor_existente[$i]<br>";
    }
}

if(count($autores) > 0){
    array_push($actualizar, 'autores');
}


// Preparar Edición de Datos 
$sql = "";

#Clasificación
if(in_array("clasf", $actualizar)){
    $select = $cnx->prepare("SELECT * FROM clasificacion WHERE name_clasf = :clasf");
    $select->bindParam(':clasf', $clasf, PDO::PARAM_STR);
    $select->execute();

    if(!$aux = $select->fetch(PDO::FETCH_ASSOC)){
        $insert = $cnx->prepare("INSERT INTO clasificacion (name_clasf) VALUES (:name_clasf)");
        $insert->bindParam(':name_clasf', $clasf, PDO::PARAM_STR);
        $insert->execute();

        $select->execute();
        $clasf = $select->fetch(PDO::FETCH_ASSOC);
    }else{
        $clasf = $aux;
    }

    $sql = "id_clasf = :id_clasf";
}

#PalabrasClave
$kws_iguales = [];
$kws_existentes = [];
if(in_array("kws", $actualizar)){
    $aux = [];
    foreach($kws as $kw){
        array_push($aux, $kw['name_kw']);
    }

    if(empty(array_diff($aux, $kw_validas)) && empty(array_diff($kw_validas, $aux))){
        $actualizar = array_diff($actualizar, array("kws"));
    }else{
        foreach($kw_validas as $i => $valida){
            #Repetidas
            $aux = false;
            foreach($kws as $kw){
                if($kw['name_kw'] == $valida){
                    $aux = $kw['id_kw'];
                }
            }
            if(!$aux) $aux = 0;

            #Existentes
            $aux1 = null;
            foreach($palabrasClave as $pc){
                if($pc['name_kw'] == $valida){
                    $aux1 = $pc['id_kw'];
                }
            }
            if(!$aux1) $aux1 = 0;

            #Consolidar
            $kws_iguales[$i] = $aux;
            $kws_existentes[$i] = $aux1;

            #Log
            echo "$valida = $kws_iguales[$i] : $kws_existentes[$i] <br>";
        }
    }
}

#Título
if(in_array("titulo", $actualizar)){
    if(strlen($sql) > 0){
        $sql .= ", ";
    }
    $sql .= "ttle_doc = :titulo";
}

#Cuerpo
if(in_array("descripcion", $actualizar)){
    if(strlen($sql) > 0){
        $sql .= ", ";
    }
    $sql .= "corp_doc = :cuerpo";
}

// Editar Datos
#Archivo
if(in_array('docx', $actualizar)){
    $rutaDocx = '../' . $rutaDocs . $id_doc . '.' . $docx_format;
    move_uploaded_file($docx, $rutaDocx);
    $actualizar = array_diff($actualizar, array('docx'));
    $edicion = true;
}


#Autores
if(in_array('autores', $actualizar)){
    // Eliminar Autores No Reasignados
    $delete = $cnx->prepare("DELETE FROM autor_documento WHERE id_doc = :id_doc AND id_autor = :id_autor");
    $delete->bindParam(':id_doc', $id_doc, PDO::PARAM_INT);
    $aux = 0;
    foreach($autorDocumento_DB as $i => $autor){
        if($autor && !$autor_igual[$i]){
            $delete->bindParam(':id_autor', $autor['id_autor'], PDO::PARAM_INT);
            $delete->execute();
            echo "Autor Eliminado: ". $autor['id_autor'] ."<br>";
        }
    }

    // Asignar Autores 
    $insert = $cnx->prepare("INSERT INTO autor_documento (id_doc, id_autor) VALUES (:id_doc, :id_autor)");
    $insert->bindParam(':id_doc', $id_doc, PDO::PARAM_INT);

    $insert_autor = $cnx->prepare("INSERT INTO autor (name, surname) VALUES (:name, :surname)");
    foreach($autores as $i => $autor){
        $aux = false;

        // Renegar Setteados y Colocar Nuevos
        if(($autor_existente[$i] != 0 && $autor_existente[$i] != -1) && $autor_igual[$i] != $autor_existente[$i]){
            $insert->bindParam(':id_autor', $autor_existente[$i], PDO::PARAM_INT);
            echo "Aquí el peo <br>";
            $aux = true;
        }

        // Crear Inexistentes
        if($autor_existente[$i] !== -1 && !$autor_existente[$i]){
            $insert_autor->bindParam(':name', $autor['name'], PDO::PARAM_STR);
            $insert_autor->bindParam(':surname', $autor['surname'], PDO::PARAM_STR);
            $insert_autor->execute();
            $id_autor = $cnx->lastInsertId();
            $insert->bindParam(':id_autor', $id_autor, PDO::PARAM_INT);
            echo"Embuste, está aquí <br>";
            $aux = true;
        }

        if($aux) $insert->execute();
    }
    $edicion = true;
    $actualizar = array_diff($actualizar, array('autores'));
}

#PalabrasClave
if(in_array('kws', $actualizar)){
    // Eliminar Palabras Clave que No Fueron Reasignadas.
    $delete = $cnx->prepare("DELETE FROM documento_palabraclave WHERE id_doc = :id_doc AND id_kw = :id_kw");
    $delete->bindParam(':id_doc', $id_doc, PDO::PARAM_INT);
    foreach($kws as $i => $kw){
        if(!$kws_iguales[$i] && $kw){
            $delete->bindParam(':id_kw', $kw['id_kw'], PDO::PARAM_INT);
            $delete->execute();
            echo "Eliminao Palabra Clave: $kw[id_kw]<br>";
        }
    }

    // Colocar Las Nuevas Palabras Clave
    $insert = $cnx->prepare("INSERT INTO documento_palabraclave (id_doc, id_kw) VALUES (:id_doc, :id_kw)");
    $insert->bindParam(':id_doc', $id_doc, PDO::PARAM_INT);

    $insert_kw = $cnx->prepare("INSERT INTO palabraclave (name_kw) VALUES (:name_kw)");
    foreach($kw_validas as $i => $keyword){
        echo $keyword . "<br>";
        $aux = false;
        // Renegar las ya setteadas y settear las nuevas
        if($kws_existentes[$i] != 0 && $kws_existentes[$i] != $kws_iguales[$i]){
            $insert->bindParam(':id_kw', $kws_existentes[$i], PDO::PARAM_INT);
            $aux = true;
        }

        // Crear las que no existen.
        if($kws_existentes[$i] == 0){
            $insert_kw->bindParam(':name_kw', $keyword, PDO::PARAM_STR);
            $insert_kw->execute();
            $kws_existentes[$i] = $cnx->lastInsertId();
            echo "Inserto Palabra Clave: ". $kws_existentes[$i]."<br>";
            $insert->bindParam(':id_kw', $kws_existentes[$i], PDO::PARAM_INT);
            $aux = true;
        }

        // Insertar 
        if($aux) $insert->execute();
    }

    $actualizar = array_diff($actualizar, array("kws"));
    $edicion = true;
}


#DocumentoPrincipal
if(count($actualizar) > 0){
    $prepare = $cnx->prepare("UPDATE documento SET $sql WHERE id_doc = :id_doc");
    $prepare->bindParam(':id_doc', $id_doc, PDO::PARAM_INT);

    if(in_array('clasf', $actualizar)){
        $prepare->bindParam(':id_clasf', $clasf['id_clasf'], PDO::PARAM_STR);
    }
    if(in_array('titulo', $actualizar)){
        $prepare->bindParam(':titulo', $titulo, PDO::PARAM_STR);
    }
    if(in_array('descripcion', $actualizar)){
        $prepare->bindParam(':cuerpo', $descripcion, PDO::PARAM_STR);
    }

    $prepare->execute();
    $edicion = true;
}

if($edicion){
    Documento::Gestion($_SESSION['sesionPortalWeb'], $id_doc, $cnx);

    $accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
    date_default_timezone_set('America/Caracas');
    $accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
    $accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
    $accion->bindValue(':accion_name', 'Editar Libro', PDO::PARAM_STR);
    $accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
    $accion->execute();
}

// Salir 
header('Location: ../biblioteca');
exit;