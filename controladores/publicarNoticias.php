<?php
session_start();

if(isset($_SESSION['estado'])){
    header("Location: ../blocked");
    exit;
}
if($_SESSION['tipo'] < 1 || $_SESSION['tipo'] > 2){
    header('Location: ../periódico');
    exit;
}
include_once("../clases/conexion.php");

if(!is_dir('../'.$rutaMult)){
    mkdir('../'.$rutaMult, 0777, true);
}

try{
    $titulito       = trim($_POST['titulo']);
    $descripcion    = trim($_POST['descripcion']);
    $id_autor       = $_SESSION['sesionPortalWeb'];

    $idMult         = array();
    $multimedia     = array();
    
    if(empty($titulito) || strlen($titulito) <= 0 || strlen($titulito) > 200){
        header("Location: ../noticia-t");
        exit;
    }elseif(empty($descripcion) || strlen($descripcion) <=0 || strlen($descripcion) > 4000){
        header("Location: ../noticia-d");
        exit;
    }
    
    if(!empty($_FILES['multimedia']['name'][0])){
        $nombreMult     = $_FILES['multimedia']['name'];
        $tamannoMult    = $_FILES['multimedia']['size'];
        $formatoMult    = [];

        foreach($nombreMult as $i => $nombre){
            $formatoMult[$i] = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

            switch($formatoMult[$i]){
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'mp4':
                    break;
                default:
                    header("Location: ../noticia-f");
                    exit;
                    break;
            }
        }

        foreach($formatoMult as $i => $formato){
            if($formato == 'mp4'){
                if($tamannoMult[$i] > (1024*1024*1024)){
                    header("Location: ../noticia-s");
                    exit;
                }
            }else{
                if($tamannoMult[$i] > (1024*1024*5)){
                    header("Location: ../noticia-s");
                    exit;
                }
            }
        }
        
        foreach($formatoMult as $i => $formato){
            $secuencia = $cnx->prepare("INSERT INTO multimedia(media) VALUES ('')");
            $secuencia->execute();
            $idMult[$i] = $cnx->lastInsertId();
            $multimedia[$i] = $rutaMult . $idMult[$i] . "." . $formato;
        }
        
        $noticia = new Noticia($titulito, $descripcion, $id_autor, $cnx);
        $id_new  = $noticia->DB_Post();

        foreach($_FILES['multimedia']['tmp_name'] as $i => $mult){
            move_uploaded_file($mult, '../'.$multimedia[$i]);
            $sentencia = $cnx->prepare("UPDATE multimedia SET media=:media, id_new=:id_new WHERE id_media=:id_media");
            $sentencia->bindParam(':media', $multimedia[$i], PDO::PARAM_STR);
            $sentencia->bindParam(':id_new', $id_new, PDO::PARAM_INT);
            $sentencia->bindParam(':id_media', $idMult[$i], PDO::PARAM_INT);
            $sentencia->execute();
        }
    }else{
        $noticia = new Noticia($titulito, $descripcion, $id_autor, $cnx, $_SESSION['tipo'] == 2 ? 1 : 0);
        $id_new  = $noticia->DB_Post();
    }

    $accion = $cnx->prepare("INSERT INTO accion (fecha_act, hora_act, accion_name, id_user) VALUES (:fecha_act, :hora_act, :accion_name, :id_user)");
    date_default_timezone_set('America/Caracas');
    $accion->bindParam(':fecha_act', date('Y-m-d'), PDO::PARAM_STR);
    $accion->bindParam(':hora_act', date('H:i'), PDO::PARAM_STR);
    $accion->bindValue(':accion_name', 'Publicar Noticia', PDO::PARAM_STR);
    $accion->bindParam(':id_user', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
    $accion->execute();

    header("Location: ../noticia-p");
    exit;
}catch(EXCEPTION $e){
    die("Error: " . $e->getMessage());
}finally{
    $cnx = null;
}
?>