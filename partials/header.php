<header class="Cabecera" id="Presentacion">

    <a id="uptp" href="https://uptparia.edu.ve/" target="_blank" title='Ir a la página de la UPTP "Luis Mariano Rivera"'>
        <img alt="Logo UPTParia" class="logo" src="images/logoUPTP.png">
    </a>

    <h1 id="Titulo">
        Seguridad Alimentaria y Cultura Nutricional
        <a href="aboutUs" id="MasInfo" title="Sobre Nosotros">
            <img src="images/informacion.svg" alt="Más info">
        </a>
    </h1>

    <a id="sacn" href="periódico" title="Página Principal: Periódico Virtual">
        <img alt="Logo SACN" class="logo" src="images/logoSACN.png">
    </a>


    <div id="InfoUser">
        <!-- Link ADACES -->
        <a class="Opcion" id="LinkAdaces" href="http://adaces.uptparia.edu.ve/" target="_blank" title="Ir a ADACES">
            <img class="adaces" alt="logoADACES" src="images/miniLogoADACES.png">
        </a>
        <!-- Información de la Sesión -->
        <?php
        if(isset($_SESSION['tipo'])){
            switch($_SESSION['tipo']){
                case 1:
                    $tipo = 'Administrador';
                    break;
                case 2:
                    $tipo = 'Moderador';
                    break;
                default:
                    $tipo = 'Visitante';
            }
            echo "<b>$_SESSION[nombre]</b>: $tipo | $_SESSION[last_action]"; 
            ?>
            <div id="cerrarSesion" title="Cerrar Sesión">
                <img src="images/fuerza.svg" class="opcIcon">
            </div>
        <?php }else{ ?>
            <a href="login" id="LoginButton">Iniciar Sesión Aquí</a>
            <?php
        }
        ?>

    </div>
</header>
<div id="outModalSalir"></div>
<div id="modalSalir">
    <h2>¿Deseas cerrar sesión realmente?</h2>
    <div class="boton" id="quedarse_salir">Preferí quedarme.</div>
    <div class="boton" id="salir_salir" onclick="window.location.href = 'logout'">Sí, deseo cerrar sesión.</div>
</div>

<script>
    let outModal = document.querySelector("#outModalSalir");
    let modal    = document.querySelector("#modalSalir");

    function mostrar(){
        outModal.style.display = 'block';
        modal.style.display = 'flex';
    
        setTimeout(()=>{
            outModal.style.opacity = 1;
            modal.style.opacity = 1;
        }, 5);
    }

    function cerrar(){
        outModal.style.opacity = 0;
        modal.style.opacity = 0;
        

        setTimeout(()=>{
            outModal.style.display = 'none';
            modal.style.display = 'none';
        }, 500);
    }

    document.querySelector('#cerrarSesion').addEventListener('click', mostrar);
    
    document.querySelector("#outModalSalir").addEventListener('click', cerrar);

    document.querySelector("#modalSalir > #quedarse_salir").addEventListener('click', cerrar);
</script>