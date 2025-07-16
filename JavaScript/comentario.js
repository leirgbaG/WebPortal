// Reemplazar saltos de Línea 
const textInput = document.querySelector('#comentar_areafalsa');
const outputContainer = document.querySelector('#comentar_area');

textInput.addEventListener('input', function(){
    const input = textInput.value;
    const formattedText = input.replace(/\n/g, '<br>');
    outputContainer.innerHTML = formattedText;
});




// Responder comentarios 
const responder = document.querySelectorAll('.opcion-responder-comentario');
responder.forEach((e) => {
    e.addEventListener("click", () => {
        document.querySelector("#posicion").value = e.id.replace("responder-a-", "");
        document.querySelector("#comentar_areafalsa").setAttribute("placeholder", `Respondiendo a comentario de ${e.getAttribute("data-value")}...`);
        document.querySelector("#comentar_areafalsa").focus();
        document.querySelector("#posicion").removeAttribute("hidden");
        document.querySelector("#posicion").checked = true;
    });
});

document.querySelector("#posicion").addEventListener("change", (e) => {
    if(document.querySelector("#posicion").getAttribute("checked") == false){
        document.querySelector("#comentar_areafalsa").setAttribute("placeholder", "Escribre tu comentario...");
        document.querySelector("#posicion").setAttribute("hidden", true);
    }
});