<?php
// Sesión 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}
if(!isset($_SESSION['sesionPortalWeb'])){
    header("Location: login");
    exit;
}

// Base de datos 
include_once("clases/conexion.php");

// Acción 
if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2){
    $nombre = "Solicitudes de Documentos";
}else{
    $nombre = "Solicitar Documento";
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/solicitudes.css">
        <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        
        <title> <?= $nombre ?> </title>
        <?php 
        if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2){ 
            ?>
        <script>
            const responder = (respuesta, id) => {
                const modal = document.querySelector("#modal"+id);
                const form  = modal.querySelector("#formSolicitud");

                // Adaptar Formulario 
                form.getElementsByTagName("h1")[0].textContent = respuesta ? "Aprobar" : "Rechazar";
                const boton = form.querySelector("#enviar");
                boton.value = respuesta ? "Aprobar" : "Rechazar";
                if(respuesta){
                    if(boton.classList.contains('rechazo'))
                        boton.classList.remove('rechazo');
                }else{
                    if(!boton.classList.contains('rechazo'))
                        boton.classList.add('rechazo');
                }

                form.querySelector(".texto").title = respuesta ? "¡Dile al solicitante que su solicitud ha sido aprobada! \nTe recomentamos que le digas con qué nombre puede conseguir su libro." : "Explícale al solicitante por qué has rechazado su propuesta.";

                form.querySelector('#aprobar').checked = respuesta;
                
                // Mostrar Formulario
                form.style.display = "flex";
                setTimeout(()=>{
                    form.style.opacity = 1;
                }, 5);
            };
        </script>
            <?php 
        } ?>
    </head>

    <body>
        <div id="principalContainer">
            <?php
            include_once("partials/header.php");
            include_once("partials/nav.php");
            ?>

            <div class="post">
                <?php
                if($_SESSION['tipo'] == 2){
                    ?>
                    <a href="resumen-peticion" class="Opcion">
                        <img src="images/arbol-de-mesa.svg" class="opcIcon">
                        <p>Resumen de Solicitudes</p>
                    </a>
                    <?php
                }
                ?>
            </div>

            <section class="Cuerpo">
                <?php
                // Obtener datos de la base de datos
                if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2){
                    $prepare = $cnx->prepare("SELECT * FROM peticion ORDER BY pet_estado ASC");
                }else{
                    $prepare = $cnx->prepare("SELECT * FROM peticion WHERE id_user = :usuario");
                    $prepare->bindParam(':usuario', $_SESSION['sesionPortalWeb'], PDO::PARAM_INT);
                }
                ?>
                <div id="solicitar">
                    <p>Solicitar Documento</p>
                </div>
                <?php
                $prepare->execute();
                if($prepare->rowCount() > 0){
                    while($row = $prepare->fetch(PDO::FETCH_ASSOC)){
                        $peticion = new Peticion($cnx, $row['pet_ttle'], $row['pet_corp'], $row['id_user'], $row['pet_estado'], $row['pet_fecha'], $row['id_pet'], $row['pet_msg']);
                        $peticion->Mostrar_Peticion();
                    }
                }else{
                    if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2){
                    ?>
                    <div class="noSolicitudes">
                        <p>No hay solicitudes pendientes. 😊</p>
                    </div>
                    <?php
                    }else{
                        ?>
                        <div class="noSolicitudes">
                            <p>
                                No has hecho ninguna solicitud. 😊 <br><br>
                                ¿Deseas Solicitar un Documento? <br>
                                Haz click en el botón de arriba.
                            </p>
                        </div>
                        <?php
                    }
                }
                ?>
            </section>
            <!-- Modal -->
            <div class="outModal modal-hidden" id="outModalSolicitar"></div>
            <div class="modal modal-hidden" id="modalSolicitar">
                <form action="controladores/solicitarDocumento.php" method="post" id="formSolicitud">
                    <h1>Solicitud de Documento</h1>
                    <input type="text" placeholder="Título del Libro" minlength="30" title="Ofrece el título original (o un título descriptivo) para que los administradores puedan encontrarlo con facilidad" maxlength="200" class="texto" name="pet_ttle" required>

                    <textarea placeholder="Descripción del Libro" title="Puedes explicar su contenido, quiénes son sus autores, etc." rows="6" maxlength="4000" class="texto" id="descripcion" required></textarea>

                    <textarea name="pet_corp" id="enviable" hidden></textarea>
                    <input type="submit" id="enviar" value="Solicitar">
                </form>
            </div>
        </div>
        <script src="JavaScript/solicitudes.js"></script>
    </body>
</html>