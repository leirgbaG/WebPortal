<?php
// Sessión
session_start(); 

if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if(!isset($_SESSION['sesionPortalWeb'])){
    header("Location: ../login");
}

// Base de Datos y Barrel
include_once("../clases/conexion.php");

// Validación de datos de la Solicitud 
$ttle = trim($_POST['pet_ttle']);
$corp = trim($_POST['pet_corp']);

if(empty($ttle) || empty($corp)){
    header("Location: ../solicitudes-empty1");
    exit;
}elseif(strlen($ttle) < 3 || strlen($corp) < 6){
    header("Location: ../solicitudes-empty2");
    exit;
}elseif(strlen($ttle) > 200 || strlen($corp) > 4000){
    header("Location: ../solicitudes-overflow");
    exit;
}

// Publicar en la Base de Datos 
$pet = new Peticion($cnx, $ttle, $corp, $_SESSION['sesionPortalWeb']);
$pet->DB_Post();

$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindValue(':accion_name', 'Solicitar Libro', PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();

// Regresar 
header("Location: ../solicitudes-enviado");
exit;
?>