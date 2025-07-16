const solicitar = document.querySelector("#solicitar");
const solicitud = document.querySelectorAll(".solicitud");
solicitud.forEach((soli) => {
    const id        = soli.getAttribute('id')
    const outModal  = document.querySelector(`#outModal${id}`);
    const modal     = document.querySelector(`#modal${id}`);

    // Mostrar Modal
    soli.addEventListener("click", ()=>{
        outModal.style.display = "block";
        modal.style.display = "block";
        modal.querySelector("#formSolicitud").style.opacity = 0;
        setTimeout(()=>{
            outModal.style.opacity = 1;
            modal.style.opacity = 1;
        }, 5);
    });

    outModal.addEventListener("click", ()=>{
        // Cerrar Modal
        outModal.style.opacity = 0;
        modal.style.opacity = 0;
        const texto = modal.querySelector("#formSolicitud").querySelector(".texto");

        setTimeout(()=>{
            outModal.style.display = "none";
            modal.style.display = "none";
            modal.querySelector("#formSolicitud").style.display = "none";
            texto.value = null;
        }, 200);
    });
});

if(solicitar != null){
    const outModal = document.querySelector("#outModalSolicitar");
    const modal = document.querySelector("#modalSolicitar");
    solicitar.addEventListener("click", ()=>{
        outModal.style.display = "block";
        modal.style.display = "block";

        setTimeout(()=>{
            outModal.style.opacity = 1;
            modal.style.opacity = 1;
        }, 5);
    });

    outModal.addEventListener("click", ()=>{
        outModal.style.opacity = 0;
        modal.style.opacity = 0;
        
        setTimeout(()=>{
            outModal.style.display = "none";
            modal.style.display = "none";

            let texto = modal.querySelectorAll(".texto");
            texto.forEach((input) =>{
                input.value = null;
            });
        }, 200);
    });
}


// Reemplazar saltos de Línea 
const textInput = document.querySelector('#descripcion');
const outputContainer = document.querySelector('#enviable');

textInput.addEventListener('input', function(){
    const input = textInput.value;
    const formattedText = input.replace(/\n/g, '<br>');
    outputContainer.innerHTML = formattedText;
});