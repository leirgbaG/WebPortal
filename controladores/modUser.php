<?php
session_start();

if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if(!isset($_SESSION['sesionPortalWeb'])){
    header("Location: ../login");
    exit;
}

include_once("../clases/conexion.php");

try{
    $nums = "/\d/";
    $espc = "/\s/";

    $campos = [];
    $error  = "";

    $aux = $_SESSION['CambiarUsuario'];
    $sentencia = $cnx->prepare("SELECT * FROM usuario WHERE usuario=:user");
    $sentencia->bindParam(':user', $aux, PDO::PARAM_STR);
    $sentencia->execute();
    // Datos Originales del Usuario
    $usuario = $sentencia->fetch(PDO::FETCH_ASSOC); 

    // Cambio de Datos 
    $cambio = array(
        'nombre'=> $_POST['nombre'], 
        'apellido' => $_POST['apellido'], 
        'tipo' => $_POST['tipo'], 
        'cargo' => $_POST['cargo'], 
        'contrasegna' => $_POST['password'], 
        'pw2' => $_POST['newPassword'], 
        'pw3' => $_POST['newPassword2']
    );

    
    // Validación de datos:

    // Nombre
    if($cambio['nombre'] != $usuario['nombre'] && !empty($cambio['nombre'])){
        if(!preg_match($nums, $cambio['nombre']) && !preg_match($espc, $cambio['nombre'])){
            $campos[] = "nombre = '". $cambio['nombre'] ."'";
        }else $error .= "&nombre=error";
    }

    // Apellido
    if($cambio['apellido'] != $usuario['apellido'] && !empty($cambio['apellido'])){
        if(!preg_match($nums, $cambio['apellido']) && !preg_match($espc, $cambio['apellido'])){
            $campos[] = "apellido = '". $cambio['apellido'] ."'";
        }else $error .= "&apellido=error";
    }

    // Tipo 
    if($cambio['tipo'] != $usuario['tipo'] && is_numeric($cambio['tipo'])){
        if($cambio['tipo'] >= 0 && $cambio['tipo'] <= 2){
            $campos[] = "tipo = ". $cambio['tipo'];
        }else $error .= "&tipo=error";
    }

    // Validar Cargo 
    $aux = $cambio['cargo'];
    $sentencia = $cnx->prepare("SELECT * FROM cargo WHERE name_cargo=:cargo");
    $sentencia->bindParam(':cargo', $aux, PDO::PARAM_STR);
    $sentencia->execute();
    
    if($sentencia->rowCount() == 0){
        if(!empty($cambio['cargo'])){
            if((!preg_match($nums, $cambio['cargo']) && !preg_match($espc, $cambio['cargo']))){
                $sentencia = $cnx->prepare("INSERT INTO cargo (name_cargo) VALUES (:cargo)");
                $sentencia->bindParam(':cargo', $aux, PDO::PARAM_STR);
                $sentencia->execute();
                $sentencia = $cnx->prepare("SELECT * FROM cargo WHERE name_cargo=:cargo");
                $sentencia->bindParam(':cargo', $aux, PDO::PARAM_STR);
                $sentencia->execute();
            }else $error .= "&cargo=error";
        }
            
    }
    if($sentencia->rowCount() > 0){
        $cargo = $sentencia->fetch(PDO::FETCH_ASSOC);
        $campos[] = "id_cargo = ". $cargo['id_cargo'];
    }
    

    // Validar Contraseña 
    if(password_verify($cambio['contrasegna'], $usuario['contrasegna'])){
        if($cambio['contrasegna'] != $cambio['pw2'] && $cambio['pw2'] == $cambio['pw3'] && !empty($cambio['pw1'])){
            $aux = password_hash($cambio['pw2'], PASSWORD_DEFAULT);
            $campos[] = "contrasegna = '". $aux ."'";
        }else $error .= "&contrasegna=error"; 
    }

    // Actualizar Datos
    if(count($campos)){
        $campos = implode(", ", $campos);
        echo $campos;

        $sentencia = $cnx->prepare("UPDATE usuario SET $campos WHERE usuario=:user");
        $sentencia->bindParam(":user", $usuario['usuario'], PDO::PARAM_STR);
        $sentencia->execute();

        $accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
        date_default_timezone_set('America/Caracas');
        $accion->bindValue(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
        $accion->bindValue(':hora_act', date('H:i'), PDO::PARAM_STR);
        $accion->bindValue(':accion_name', 'Editar Usuario', PDO::PARAM_STR);
        $accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
        $accion->execute();
    }

    $url = "Location: ../modificarCuenta.php?user=$_SESSION[CambiarUsuario]";
    if(!empty($error)){
        $url .= "&". $error;
        header($url);
    }else{
        header("Location: ../usuarios");
    }
    exit;

}catch(EXCEPTION $e){
    die("ERROR: ". $e->getMessage());
}