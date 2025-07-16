<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 2){
    header("Location: ../solicitudes");
    exit;
}

// Base de Datos
include_once("../clases/conexion.php"); 

// Comprobar Datos 
$id = $_GET['id'];
$msg = $_POST['mensaje'];
$aceptar = isset($_POST['aceptar']) ? 1 : 2;


if(empty($id) || empty($msg)){
    header("Location: ../solicitudes");
    exit;
}
if(strlen($msg) > 2000 || strlen($msg) < 10){
    header("Location: ../solicitudes");
    exit;
}

// Responder
$solicitud = new Peticion($cnx, null, null, null, null, null, $id, $msg);
$solicitud->Set_Message($aceptar, $msg);

$accion_name = $aceptar == 1 ? "Aprobar Solicitud de Documento" : "Rechazar Solicitud de Documento";
$accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
date_default_timezone_set('America/Caracas');
$accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
$accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
$accion->bindParam(':accion_name', $accion_name, PDO::PARAM_STR);
$accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
$accion->execute();

// Regresar
header("Location: ../solicitudes");
exit;

?>