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
if(($_SESSION['tipo'] > 2 || $_SESSION['tipo'] < 1)){
    header("Location: ../periódico");
    exit;
}

include_once("../clases/conexion.php");
$id_new = $_GET['new'];

$noticia = $cnx->prepare("SELECT id_user FROM noticia WHERE id_new = :id");
$noticia->bindParam(":id", $id_new, PDO::PARAM_INT);
$noticia->execute();

if($_SESSION['tipo'] == 2 || $_SESSION['sesionPortalWeb'] == $noticia->fetchColumn()){
    $noticia = $cnx->prepare("UPDATE noticia SET estado_new = 2 WHERE id_new=:id");
    $noticia->bindParam(":id", $id_new, PDO::PARAM_INT);
    $noticia->execute();
}

$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindValue(':accion_name', 'Ocultar Noticia', PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();


if(isset($_GET['direccion'])){
    header("Location: ../resumen-noticia");
    exit;
}
header("Location: ../periódico-$_GET[pag]");
exit;
?>