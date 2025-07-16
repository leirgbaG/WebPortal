<?php
session_start();
if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}

include_once("../clases/conexion.php");

$nums = "/\d/";
$espacios = "/\s/";

try{
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $cargo = trim($_POST['cargo']);
    $user = trim($_POST['usuario']);
    $pw1 = trim($_POST['clave']);
    $pw2 = trim($_POST['pw2']);
    $error = "Location: ../registro.php?";


    //  Validaciones.
    if(empty($prenom) || !isset($prenom) || strlen($prenom) < 2 || strlen($prenom) > 20 || preg_match($nums, $prenom) || preg_match($espacios, $prenom)){
        $error .= (preg_match("/\?$/", $error)) ?"nameerr1=1"
                                                :"&nameerr1=1";
    }
    if(empty($nom) || !isset($nom) || strlen($nom) < 2 || strlen($nom) > 20 || preg_match($nums, $nom) || preg_match($espacios, $nom)){
        $error .= (preg_match("/\?$/", $error)) ?"nameerr2=1"
                                                :"&nameerr2=1";
    }
    if(empty($cargo) || !isset($cargo) || strlen($cargo) < 2 || strlen($cargo) > 50 || preg_match($nums, $cargo)){
        $error .= (preg_match("/\?$/", $error)) ?"cargoerr1=1"
                                                :"&cargoerr1=1";
    }
    if(empty($user) || !isset($user) || strlen($user) < 8 || strlen($user) > 16 || preg_match("/^\d/", $user) || preg_match($espacios, $user)){
        $error .= (preg_match("/\?$/", $error)) ?"usererr1=1"
                                                :"&usererr1=1";
    }else{
        $usuario = $cnx->query("SELECT * FROM usuario");
        while($registro = $usuario->fetch(PDO::FETCH_ASSOC)){
            if($registro['usuario'] == $user){
                $error .= (preg_match("/\?$/", $error)) ?"usererr1=2"
                                                        :"&usererr1=2";
            }
        }
    }
    if(empty($pw1) || !isset($pw1) || strlen($pw1) < 8 || strlen($pw1) > 16 || preg_match("/^\d/", $pw1) || preg_match($espacios, $pw1)){
        $error .= (preg_match("/\?$/", $error)) ?"pwerr1=1"
                                                :"&pwerr1=1";
    }
    if($pw1 != $pw2){
        $error .= (preg_match("/\?$/", $error)) ?"pwerr2=1"
                                                :"&pwerr2=1";
    }
    if(!preg_match("/\?$/", $error)){
        header($error);
        exit;
    }

    //  Registrar
    $id_cargo = null;
    $cargoList = $cnx->query("SELECT * FROM cargo");
    while($registroCargo = $cargoList->fetch(PDO::FETCH_ASSOC)){
        if($registroCargo['name_cargo'] == $cargo){
            $id_cargo = $registroCargo['id_cargo'];
        }
    }
    
    if($id_cargo == null){
        $newCargo = new Cargo($cargo, $cnx);
        $newCargo->DB_Post();
        $id_cargo = $newCargo->GetID();
    }

    $newUsuario = new Usuario($prenom, $nom, $user, $pw2, $id_cargo, $cnx);
    $id = $newUsuario->DB_Post();

    $accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
    date_default_timezone_set('America/Caracas');
    $accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
    $accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
    $accion->bindValue(':accion_name', 'Crear Usuario', PDO::PARAM_STR);
    $accion->bindParam(':id_user', $id, PDO::PARAM_INT);
    $accion->execute();

    include_once("login.php");

}catch(EXCEPTION $e){
    echo "Error: " . $e->getMessage();
}
?>