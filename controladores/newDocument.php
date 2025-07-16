<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if(!isset($_SESSION['sesionPortalWeb']) || ($_SESSION['tipo'] > 2 || $_SESSION['tipo'] < 1)){
    header("Location: ../biblioteca");
    exit;
}

// Conexión a la base de datos
include_once("../clases/conexion.php");

if(!is_dir('../'.$rutaDocs)){
    mkdir('../'.$rutaDocs, 0777, true);
}


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
$error = "Location: ../postDoc";

// Validar Datos 
if (!empty($titulo) && !empty($descripcion) && !empty($clasf) && !empty($keywords) && !empty($name_autor) && !empty($surname_autor) && !empty($docx_name)){
    $aux = 0;
    if(strlen($titulo) > 200){
        $error .= "-t";
        $aux = 1;
    }else{
        $titulo = ucwords(strtolower($titulo));
    }
    if(strlen($descripcion) > 4000 || strlen($descripcion) < 1){
        $error .= "-d";
        $aux = 1;
    }
    if(strlen($clasf) > 50 || preg_match("/\d/", $clasf)){
        $error .= "-c";
        $aux = 1;
    }

    if($docx_size > 10*1024*1024){
        $error .= "-s";
        $aux = 1;
    }
    
    if($docx_format != "pdf"){
        $error .= "-f";
        $aux = 1;
    }

    foreach($keywords as $kw){
        if(strlen($kw) > 50 || !preg_match("/\w/", $kw)){
            $error .= "-k";
            $aux = 1;
            break;
        }
    }
    foreach($name_autor as $na){
        if(strlen($na) > 30 || !preg_match("/[a-z]/i", $na)){
            $error .= "-a";
            $aux = 1;
            break;
        }
    }
    foreach($surname_autor as $sa){
        if(strlen($sa) > 30 || !preg_match("/[a-z]/i", $sa)){
            $error .= "-n";
            $aux = 1;
            break;
        }
    }

    if($aux == 1){
        header($error);
        exit;
    }
}else{
    header($error . "-z");
    exit;
}


// Clasificación
$query = $cnx->prepare("SELECT * FROM clasificacion ORDER BY id_clasf DESC");
$query->execute();
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    if($row['name_clasf'] == $clasf){
        $clasf = $row['id_clasf'];
    }
}
if(!is_numeric($clasf)){
    $insert = $cnx->prepare("INSERT INTO clasificacion(name_clasf) VALUES (:clasf)");
    $insert->bindParam(':clasf', $clasf, PDO::PARAM_STR);
    $insert->execute();
    $clasf = $cnx->lastInsertId();
}

// Publicación Documento 
$documento = new Documento($titulo, $descripcion, "vacío", $clasf, $cnx, $_SESSION['sesionPortalWeb']);
$id_doc = $documento->DB_Post();

// Publicación Archivo 
$ruta_docx = $rutaDocs . $id_doc . '.' . $docx_format;
move_uploaded_file($docx, "../".$ruta_docx);
$insert = $cnx->prepare("UPDATE documento SET docx = :ruta WHERE id_doc = :id");
$insert->bindParam(':ruta', $ruta_docx, PDO::PARAM_STR);
$insert->bindParam(':id', $id_doc, PDO::PARAM_INT);
$insert->execute();

// Palabras Clave 
foreach($keywords as $kw){
    $query = $cnx->prepare("SELECT id_kw FROM palabraclave WHERE name_kw = :name_kw"); 
    $query->bindParam(':name_kw', $kw, PDO::PARAM_STR); 
    $query->execute(); 
    $id_pc = $query->fetchColumn();

    if(!$id_pc){
        $insert = $cnx->prepare("INSERT INTO palabraclave(name_kw) VALUE (:pc)");
        $insert->bindParam(':pc', $kw, PDO::PARAM_STR);
        $insert->execute();
        $id_pc = $cnx->lastInsertId();
    }
    $insert = $cnx->prepare("INSERT INTO documento_palabraclave(id_doc, id_kw) VALUES (:doc, :kw)");
    $insert->bindParam(':doc', $id_doc, PDO::PARAM_INT);
    $insert->bindParam(':kw',  $id_pc,  PDO::PARAM_INT);
    $insert->execute();
}

// Autores 
for($i = 0; $i < count($name_autor); $i++){
    $query = $cnx->prepare("SELECT a.id_autor FROM autor a WHERE a.name = :nombre AND a.surname = :apellido");
    $query->bindParam(':nombre', $name_autor[$i], PDO::PARAM_STR);
    $query->bindParam(':apellido', $surname_autor[$i], PDO::PARAM_STR);
    $query->execute();
    $id_autor = $query->fetchColumn();
    
    if(!$id_autor){
        $insert = $cnx->prepare("INSERT INTO autor(name, surname) VALUES (:nombre, :apellido)");
        $insert->bindParam(':nombre', $name_autor[$i], PDO::PARAM_STR);
        $insert->bindParam(':apellido', $surname_autor[$i], PDO::PARAM_STR);
        $insert->execute();
        $id_autor = $cnx->lastInsertId();
    }

    $insert = $cnx->prepare("INSERT INTO autor_documento (id_doc, id_autor) VALUES (:doc, :aut)");
    $insert->bindParam(':doc', $id_doc, PDO::PARAM_INT);
    $insert->bindParam(':aut', $id_autor, PDO::PARAM_INT);
    $insert->execute();
}

Documento::Gestion($_SESSION['sesionPortalWeb'], $id_doc, $cnx);

$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindValue(':accion_name', 'Publicar Libro', PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();

// Regresar 
header("Location: ../postDoc-p");
exit;

?>