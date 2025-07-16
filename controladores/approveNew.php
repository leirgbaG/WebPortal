<?php
// Sesión
session_start(); 
if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if(!isset($_SESSION['sesionPortalWeb'])){
    header('Location: ../login');
    exit;
}
if(($_SESSION['tipo'] != 2)){
    header("Location: ../periódico");
    exit;
}

include_once("../clases/conexion.php");
$id_new = $_GET['new'];

$noticia = $cnx->prepare("UPDATE noticia SET estado_new = 1 WHERE id_new = :id_new");
$noticia->bindParam(':id_new', $id_new, PDO::PARAM_INT);
$noticia->execute();

$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindValue(':accion_name', 'Aprobar Noticia', PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();

header("Location: ../periódico");
exit;
?>
