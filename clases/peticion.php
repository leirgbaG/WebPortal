<?php

class Peticion{
    private $cnx;
    private $id_pet;
    private $id_user;
    private $pet_ttle;
    private $pet_corp;
    private $pet_fecha;
    private $pet_estado;
    private $pet_msg;

    function __construct($cnx, $pet_ttle, $pet_corp, $id_user, $pet_estado = false, $pet_fecha=null, $id_pet=null, $pet_msg=null){
        $this->cnx      = $cnx;
        $this->pet_corp = $pet_corp;
        $this->pet_ttle = $pet_ttle;
        $this->id_user  = $id_user;
        $this->id_pet   = $id_pet;
        date_default_timezone_set('America/Caracas');
        $this->pet_fecha= $pet_fecha ?? date('Y-m-d');
        $this->pet_estado = $pet_estado;
        $this->pet_msg = $pet_msg;
    }

    function DB_Post(){
        $insert = $this->cnx->prepare("INSERT INTO peticion(pet_ttle, pet_corp, pet_fecha, pet_estado, id_user) VALUES (:ttle, :corp, :fecha, :estado, :id_user)");
        $insert->bindParam(":ttle"   , $this->pet_ttle  , PDO::PARAM_STR);
        $insert->bindParam(":corp"   , $this->pet_corp  , PDO::PARAM_STR);
        $insert->bindParam(":fecha"  , $this->pet_fecha , PDO::PARAM_STR);
        $insert->bindParam(":estado" , $this->pet_estado, PDO::PARAM_INT);
        $insert->bindParam(":id_user", $this->id_user   , PDO::PARAM_INT);
        $insert->execute();

        $this->id_pet = $this->cnx->lastInsertId();
        return $this->id_pet;
    }

    function Set_Message($approve, $msg){
        $update =$this->cnx->prepare("UPDATE peticion SET pet_estado = :estado, pet_msg = :msg WHERE id_pet = :id_pet");
        $update->bindParam(":estado", $approve     , PDO::PARAM_INT);
        $update->bindParam(":msg"   , $msg         , PDO::PARAM_STR);
        $update->bindParam(":id_pet", $this->id_pet, PDO::PARAM_INT);
        $update->execute();
    }

    function Mostrar_Peticion($completo = true){
        // Getting Solicitador
        $prepare = $this->cnx->prepare("SELECT usuario FROM usuario WHERE id_user = :id_user");
        $prepare->bindParam(":id_user", $this->id_user, PDO::PARAM_INT);
        $prepare->execute();
        $usuario = $prepare->fetchColumn();

        // Setting Estado y Clases 
        switch($this->pet_estado){
            case 0: // Sin Respuesta.
                $estado = "Sin Respuesta";
                $clases = "notAnswer";
                break;
            
            case 1: // Aprobada.
                $estado = "Aprobada";
                $clases  = "approved";
                break;

            case 2: // Rechazada.
                $estado = "Rechazada";
                $clases  = "rejected";
                break;
        }

        // Mostrando 
        if($completo){
            ?>
            <div class="solicitud <?= $clases ?>" id="<?= $this->id_pet ?>">
                <h2><?= $this->pet_ttle ?></h2> <!-- Título -->
                <h3><?= $estado ?></h3>         <!-- Estado -->
                <p><?= $this->pet_corp ?></p>   <!-- Cuerpo -->
                <h6>Solicitud realizada por @<?= $usuario ?> | <?= $this->pet_fecha ?></h6> <!-- Info -->
            </div>
            <?php
        }
        ?>

        <div class="outModal info_peticion" id="outModal<?= $this->id_pet ?>"></div>
        <div class="modal info_peticion <?= $clases ?>" id="modal<?= $this->id_pet ?>">
            <div class="modal-content">
                <h2><?= $this->pet_ttle ?></h2> <!-- Título -->
                <h3><?= $estado ?></h3>         <!-- Estado -->
                <p class="corp"><?= $this->pet_corp ?></p>   <!-- Cuerpo -->
                <?php if(!empty($this->pet_msg)){ ?>
                    <h4 class="msg" title="Los administradores respondieron tu petición.">Respuesta:</h4>
                    <p class="msg"><?= $this->pet_msg ?></p>     <!-- Mensaje -->
                <?php } ?>
                
                <?php
                // Opciones 
                if(($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2) && $this->pet_estado == 0){ ?>
                <div class="opciones">          <!-- Opciones -->
                    <div class="a" onclick="responder(true, <?= $this->id_pet ?>)">  <!-- Aprobar -->
                        <img src="images/comprobacion-de-comentarios.svg" alt="Aprobar" class="opcIcon">
                        <p>Aprobar</p>
                    </div>
                    
                    <div class="r" onclick="responder(false, <?= $this->id_pet ?>)"> <!-- Rechazar -->
                        <img src="images/comentario-xmark.svg" alt="Rechazar" class="opcIcon">
                        <p>Rechazar</p>
                    </div>
                </div>
                <?php } ?>

                <h6>Solicitud realizada por @<?= $usuario ?> | <?= $this->pet_fecha ?></h6> <!-- Info -->
            </div>

            <form action="controladores/responderSolicitud.php?id=<?= $this->id_pet ?>" method="post" id="formSolicitud" style="display: none;">
                <h1 id="ttleForm"></h1>
                <textarea name="mensaje" id="mensaje" class="texto" minlength="10" maxlength="2000" placeholder="Dale una respuesta la solicitante..." rows="5"></textarea>
                <input type="checkbox" name="aceptar" id="aprobar" hidden>
                <input type="submit" value="" id="enviar">
            </form>
        </div>
        <?php
    }

    # **
    # ** Hacer Resumen de Peticiones.
    # **
    # **        # Esta función está diseñada para ser utilizada dentro de
                # una DataTable, por ende: Muestra la estructura de una
                # tabla, sin abrir ni cerrar su etiqueta principal «<table>».

    static function Resumen_Peticiones($cnx){
        ?>
        <thead>
            <tr>
                <th>#N          </th>
                <th>Título      </th>
                <th>Descripción </th>
                <th>Estado      </th>
                <th>Fecha       </th>
                <th>Solicitante </th>
            </tr>
        </thead>

        <tbody>
            <?php
            $prepare = $cnx->prepare("SELECT * FROM peticion p JOIN usuario u ON p.id_user = u.id_user ORDER BY id_pet DESC");
            $prepare->execute();

            $a = 0;
            while($row = $prepare->fetch(PDO::FETCH_ASSOC)){
                $a++;
                // Obtener Estado
                switch($row['pet_estado']){
                    default:
                    case 0:
                        $estado = "Pendiente";
                        break;
                    case 1:
                        $estado = "Aprobada";
                        break;
                    case 2:
                        $estado = "Rechazada";
                        break;
                }
                ?>
                <tr>
                    <td> <?= $a ?> </td>                <!-- Número         -->
                    <td> <?= $row['pet_ttle'] ?> </td>  <!-- Título         -->
                    <td>                                <!-- Descripción    -->
                        <div class="seeMore" id="<?= $row['id_pet'] ?>">Ver más...</div>
                        <?php
                        $peticion = new Peticion($cnx, $row['pet_ttle'], $row['pet_corp'], $row['id_user'], $row['pet_estado'], $row['pet_fecha'], $row['id_pet'], $row['pet_msg']);
                        $peticion->Mostrar_Peticion(false);
                        ?>
                    </td>
                    <td> <?= $estado ?> </td><!-- Estado         -->
                    <td> <?= $row['pet_fecha'] ?>  </td><!-- Fecha          -->
                    <td> @<?= $row['usuario'] ?>   </td><!-- Solicitante    -->
                </tr>
                <?php
            }
            ?>
        </tbody>
        <?php
    }
}

?>