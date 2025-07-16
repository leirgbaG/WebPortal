<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if($_SESSION['tipo'] != 2){
    header('Location: ../periódico');
    exit;
}

// Base de Datos
include_once '../clases/conexion.php';

$id_new = $_GET['new'];

$update = $cnx->prepare('UPDATE noticia SET estado_new = 1 WHERE id_new = :id_new');
$update->bindParam(':id_new', $id_new, PDO::PARAM_INT);
$update->execute();

$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindValue(':accion_name', 'Regresar Noticia', PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();

// Regresar
header('Location: ../approveNews');
exit;
?>