<?php
// Sesión
session_start();

if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if($_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 1){
    header("Location: biblioteca");
    exit;
}

// Base de Datos 
include_once '../clases/conexion.php';

$id = $_GET['id_doc'];


Documento::Gestion($_SESSION['sesionPortalWeb'], $id, $cnx);

$prepare = $cnx->prepare("UPDATE documento SET estado_doc = -1 WHERE id_doc = :id");
$prepare->bindParam(':id', $id, PDO::PARAM_INT);
$prepare->execute();

$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindValue(':accion_name', 'Borrar Libro', PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();

header("Location: biblioteca");
exit;
?>