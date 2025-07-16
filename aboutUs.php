<?php
// Sesión Iniciada 
session_start();

if(isset($_SESSION['estado'])){
    header("Location: blocked");
    exit;
}

switch(isset($_SESSION['tipo']) && $_SESSION['tipo']){
    case 1:
        $manual = "images/manualUsuarioAdministrador.pdf";
        break;
    case 2:
        $manual = "images/manualUsuarioModerador.pdf";
        break;
    default:
        $manual = "images/manualUsuarioVisitante.pdf";
        break;
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/aboutUs.css">
         <link rel="shortcut icon" href="images/logoSACN.png" type="image/x-icon">
        <title>Información del Departamento</title>
    </head>
    <body>
        <div id="principalContainer">
            <?php
            include_once 'partials/header.php';

            if(isset($_SESSION['tipo'])){
                include_once 'partials/nav.php';
                ?>
                <div class="post">
                    <a href="<?= $manual ?>" target="_blank" title="¿Necesitas ayuda en el uso de este sistema? Descarga el manual de usuario aquí." class="Opcion">
                        <img src="images/arbol-de-mesa.svg" class="opcIcon">
                        <p>Manual de Usuario</p>
                    </a>
                </div>
            <?php } ?>

            <div class="Cuerpo">
                <section id="m-vision">
                    <div class="cosa">
                        <h2>Misión del Departamento de SACN</h2>
                        <p>
                            Formar profesionales altamente capacitados, humanistas, éticos, integrales e innovadores con conocimientos, habilidades y destrezas para desenvolverse en la dinámica del sector alimentario nutricional con una mirada holística que permita el desarrollo y gestión participativa, a través de la elaboración de diagnósticos, planes, programas, proyectos y políticas que mejoren el estado nutricional del pueblo venezolano garantizando el derecho al alimento y la seguridad alimentaria con pertinencia y soberanía.
                        </p>
                    </div>

                    <div class="cosa">
                        <h2>Visión del Departamento de SACN</h2>
                        <p>
                            Consolidarse como referente nacional e internacional en la formación de profesionales comprometidos con la seguridad alimentaria y la cultura nutricional. Busca contribuir activamente a la transformación del sistema alimentario venezolano, promoviendo la soberanía alimentaria, el respeto por el ambiente, la equidad social y la valoración de la diversidad cultural. Además, busca generar conciencia en la población sobre la importancia de una alimentación saludable y sostenible, y promover la participación activa de la sociedad en la toma de decisiones relacionadas con la seguridad alimentaria.
                        </p>
                    </div>

                    <div class="cosa" id="contact">
                        <h2>Nuestras Redes Sociales</h2><br>
                        <a href="https://www.instagram.com/pnfsacn.cpno?igsh=Nnh1MWh1YWo1bHNi">
                            <img src="images/instagram.svg" class="opcIcon contactImg">
                            <span>pnfsacn.cpno</span>
                        </a> <br>

                        <a href="https://www.facebook.com/share/15y177XdBx/">
                            <img src="images/facebook.svg" class="opcIcon contactImg">
                            <span>PNF SACN</span>
                        </a> <br>

                        <a href="mailto:PNFSACN17@gmail.com">
                            <img src="images/envelope.svg" class="opcIcon contactImg">
                            <span>PNFSACN17@gmail.com</span>
                        </a> 
                    </div>
                    
                    <section id="desarrolladores" class="cosa">
                        <h2>Desarrolladores</h2>
                        <p>
                            Este portal web fue desarrollado por 
                            <a href="mailto:odraudegonzalezm@gmail.com" target="_blank">Eduardo González</a>, 
                            Yuliangel Marcano y Yulyannys Cedeño. <br> <br>

                            Los íconos de este sistema provienen de <a href="https://www.flaticon.com/" target="_blank">FlatIcon</a>.
                        </p>
                    </section>
                    
                </section>
                

                <section id="ubicacion" class="cosa">
                    <h2>Ubicación</h2>
                    <p>
                        El Departamento de Seguridad Alimentaria y Cultura Nutricional se encuentra dentro del edificio principal de la Universidad Politécnica Territorial de Paria “Luis Mariano Rivera”, Núcleo Carúpano. <br>
                        La UPTP “Luis Mariano Rivera” se localiza en la Carretera Nacional Vía Carúpano-El Pilar, en el Valle de Canchunchú Florido (Charallave).
                    </p>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d626.0974711582961!2d-63.25289716359642!3d10.626057766181509!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8c33c31f98a2db81%3A0xb63add99382b9f54!2s6150%20Troncal%209%2C%20Car%C3%BApano%206150%2C%20Sucre!5e1!3m2!1ses-419!2sve!4v1737766092344!5m2!1ses-419!2sve" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </section>

                
            </div>
        </div>
        <script src="JavaScript/noticias.js"></script>
    </body>
</html>