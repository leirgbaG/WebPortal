<?php
session_start();
include_once("../clases/conexion.php");

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if(!isset($_SESSION['sesionPortalWeb'])){
    header('Location: login');
    exit;
}
if($_SESSION['sesionPortalWeb'] != $_GET['user'] && $_SESSION['tipo'] != 2){
    header('Location: cuenta');
    exit;
}

$user = $_GET['user'];


$sentencia = $cnx->prepare("SELECT COUNT(*) FROM usuario WHERE tipo=2");
$sentencia->execute();
if($sentencia->fetchColumn() <= 1 && 2 == $_SESSION['tipo']){
    header('Location: usuarios');
    exit;
}
$sentencia = $cnx->prepare("UPDATE usuario SET estado_user = -1 WHERE id_user = :usuario");
$sentencia->bindParam(':usuario', $user, PDO::PARAM_STR);
$sentencia->execute();

$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindValue(':accion_name', 'Eliminar Cuenta', PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();

if($_SESSION['sesionPortalWeb'] == $_GET['user']){
    session_destroy();
    header('Location: login');
    exit;
}else{
    header('Location: usuarios-1');
    exit;
}
?>