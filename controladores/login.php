<?php
session_start();
include_once("../clases/conexion.php");
$inic_num = "/^\d/";
$poseeEspacios = "/\s/";

try{
    //  Recepción de datos
    $user   = trim($_POST['usuario']);
    $pw     = trim($_POST['clave']);
    $id     = null;

    if((strlen($user) < 8) || (strlen($user) > 16) || empty($user) || preg_match($inic_num, $user) || preg_match($poseeEspacios, $user)){
        header("Location: ../login=usererr4");
        exit;
    }elseif(strlen($pw) < 8 || strlen($pw) > 16 || empty($pw) || !isset($pw) || preg_match($inic_num, $pw) || preg_match($poseeEspacios, $pw)){
        header("Location: ../login=pwerr3");
        exit;
    }

    //  Consultas SQL
    $usuario = $cnx->prepare("SELECT * FROM usuario WHERE usuario = :user AND estado_user != -1");
    $usuario->bindParam(':user', $user, PDO::PARAM_STR);
    $usuario->execute();
    if(!$usuario = $usuario->fetch(PDO::FETCH_ASSOC)){
        header("Location: ../login=error");
        exit;
    }

    //  Comprobar Contraseña
    if(password_verify($pw, $usuario['contrasegna'])){
        if($usuario['estado_user'] == 1){
            $_SESSION['estado'] = 'blocked';
            header("Location: ../blocked");
            exit;
        }elseif($usuario['estado_user'] == -1){
            header("Location: ../login=error");
            exit;
        }

        $_SESSION['sesionPortalWeb']= $usuario['id_user'];
        $_SESSION['usuario']        = $usuario['usuario'];
        $_SESSION['tipo']           = $usuario['tipo'];
        $_SESSION['nombre']         = $usuario['nombre']. " " . $usuario['apellido'];
        date_default_timezone_set('America/Caracas');
        $_SESSION['last_action']    = date('H:i') . ' ~ ' . date('d-m-Y');

        header("Location: ../periódico");
        exit;
    }else{
        header("Location: ../login=error");
        exit;
    }
    
}catch(EXCEPTION $e){
    die("Error: ". $e->getMessage());
}
?>