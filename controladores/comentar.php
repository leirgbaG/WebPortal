<?php
// Sesión 
session_start();
if(!isset($_SESSION['sesionPortalWeb'])){
    header('Location: periódico');
    exit;
}

// Base de Datos 
include_once("../clases/conexion.php");

$text = $_POST['comentario'];

if(strlen($text) < 1 || strlen($text) > 600){
    header("Location: ../comentarios-$_GET[new]-$_GET[pag]");
    exit;
}

// Publicar Comentario 
(new Comentario($_GET['new'], $_SESSION['sesionPortalWeb'], $text, $cnx, $_POST['posicion']))->DB_Post();

// Realizar Acción 
$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindValue(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindValue(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindValue(':accion_name', 'Publicar Comentario', PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();

// Regresar 
header("Location: ../comentarios-$_GET[new]-$_GET[pag]");
exit;