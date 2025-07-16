let p = document.querySelectorAll(".noticia > p");

p.forEach((e) => {
    if(e.textContent.length > 600){
        let aux = e.textContent;
        e.innerHTML = e.innerHTML.substring(0, 500);
        let span = document.createElement("span");
        span.id = "more";
        span.style.color = "#09F";
        span.style.cursor = "pointer";
        span.innerHTML = "... Ver más";
        e.appendChild(span);

        span.addEventListener('click', () => {
            e.innerHTML = aux;
            e.querySelector('#more').remove();
        });
    }
});

if(document.querySelector("#LoginButton")){
    // Definir el tamaño máximo de pantalla
    var mediaQuery = window.matchMedia('(max-width: 768px)');
    function handleScreenResize(e){
        if (e.matches) {
            document.querySelector("#Presentacion").style.borderRadius = "14px";
            document.querySelector("#Presentacion").style.boxShadow = "none";
            document.querySelector(".Cuerpo").style.width = "100%";
            document.querySelector(".Cuerpo").style.margin = "5px auto";
            document.querySelector(".Cuerpo").style.padding = "30px 10px 10px";
        }else{
            document.querySelector("#principalContainer").style.gridTemplateAreas = "'header' 'cuerpo'";
            document.querySelector("#principalContainer").style.gridTemplateRows = "1fr 6fr";
            document.querySelector("#principalContainer").style.gridTemplateColumns = "1fr";
            document.querySelector("#principalContainer").style.backgroundColor = "#0000";
            document.querySelector("html").style.backgroundColor = "#EEFEEE";
            
            document.querySelector("#Presentacion").style.zIndex = "10";
            document.querySelector("#Presentacion").style.boxShadow = "0 10px 20px #0209";
            
            document.querySelector(".Cuerpo").style.width = "90%";
            document.querySelector(".Cuerpo").style.margin = "-30px auto 0";
            document.querySelector(".Cuerpo").style.padding = "30px 10px 10px";
        }
    }

    handleScreenResize(mediaQuery);
    // Escuchar cambios en el tamaño de la pantalla
    mediaQuery.addListener(handleScreenResize);
}