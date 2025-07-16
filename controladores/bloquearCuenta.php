<?php
// Sesión 
session_start();
if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if($_SESSION['tipo'] != 2){
    header('Location: cuenta');
    exit;
}

// Base de Datos 
include_once '../clases/conexion.php';

$user = $_GET['user'];
$tipo = $_GET['tipo'];

if($tipo){
    $update = $cnx->prepare("UPDATE usuario SET estado_user = 1 WHERE id_user = :user");
    $accion_name = "Bloquear Usuario";
}else{
    $update = $cnx->prepare("UPDATE usuario SET estado_user = 0 WHERE id_user = :user");
    $accion_name = "Desbloquear Usuario";
}
$update->bindParam(':user', $user, PDO::PARAM_STR);
$update->execute();

$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindparam(':accion_name', $accion_name, PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();

header("Location: usuarios#$user");
exit;
?>