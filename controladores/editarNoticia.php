<?php
// Sesión 
session_start();
if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if($_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 1){
    header('Location: periódico');
    exit;
}

// Base de datos 
include_once("../clases/conexion.php");

$titulo = $_POST['ttle'];
$cuerpo = $_POST['corp'];
$id = $_GET['id'];
$error_aux = false;
$error = "Location: ../editNew-$id";


$noticia = $cnx->prepare("SELECT * FROM noticia WHERE id_new = :id");
$noticia->bindParam(':id', $id, PDO::PARAM_INT);
$noticia->execute();
$noticia = $noticia->fetch(PDO::FETCH_ASSOC);

/*
**
**  Validación de Datos
**
*/

// Título 
if(strlen($titulo) < 1 || strlen($titulo) > 200){
    $error .= "-t";
    $error_aux = true;
}

// Descripción 
if(strlen($cuerpo) < 1 || strlen($cuerpo) > 4000){
    $error .= "-d";
    $error_aux = true;
}


/*
**
**  Redireccionamiento si Error
**
*/
if($error_aux){
    header($error);
    exit;
}


/*
**
**  Comprobación y Actualización de Noticia
**
*/

if($_SESSION['tipo'] == 2){
    $editNew = $cnx->prepare("UPDATE noticia SET ttle_new = :ttle, corp_new = :corp WHERE id_new = :id");
    $editNew->bindParam(':ttle', $titulo, PDO::PARAM_STR);
    $editNew->bindParam(':corp', $cuerpo, PDO::PARAM_STR);
}else{
    $editNew = $cnx->prepare("UPDATE noticia SET estado_new = 0 WHERE id_new = :id");
}
$editNew->bindParam(':id', $id, PDO::PARAM_INT);
$editNew->execute();

/*
**
**  Realización de Acción
**
*/
$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindValue(':accion_name', 'Editar Noticia', PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();


/*
**
**  Redireccionamiento
**
*/
header('Location: ../periódico');
exit;
?>