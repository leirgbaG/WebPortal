function CerrarReporte(){
    let reportes = document.querySelectorAll(".fieldset-generar-reporte");
    reportes.forEach((reporte) => {
        if(reporte.id != "seleccionar-tipo-reporte"){
            if(!reporte.classList.contains("extra")){
                reporte.classList.add("extra");
            }
            reporte.style.height = '0';
            reporte.style.padding = '0';
            reporte.style.opacity = '0';
        }
    });
}

function MostrarReporte(id, height){
    let reporte = document.querySelector(id);
    reporte.style.padding = "10px";
    reporte.style.opacity = '1';
    reporte.style.height = height;

    setTimeout(()=>{
        reporte.classList.remove('extra');
    }, 400);
}

//  Seleccionar el Tipo de Reporte
let tipo_reporte;
let elementos;
let idUserBlocked;
let botonEnviar = document.querySelector("#generar-el-reporte");

function PreventEnvio(e){
    e.preventDefault();
}

botonEnviar.addEventListener("click", PreventEnvio);
document.querySelectorAll(".tipo-reporte-opcion").forEach((e) => {
    e.addEventListener("click", () => {
        tipo_reporte = e.id;
        elementos = [];
        CerrarReporte();
        idUserBlocked = false;

        switch(tipo_reporte){
            case "tipo_reporte2":
                // MostrarReporte("#reporte-documento", "585px");
            case "tipo_reporte1":
            case "tipo_reporte3":
            case "tipo_reporte5":
                MostrarReporte("#seleccionar-periodo", "161px");
            case "tipo_reporte4":
                if(tipo_reporte == "tipo_reporte4"){
                    idUserBlocked = true;
                    document.querySelector("#seleccion-usuario").classList.add('blocked');
                    document.querySelectorAll("#seleccion-usuario label, #seleccion-usuario label > *").forEach(e =>{
                        e.addEventListener("click", PreventEnvio);
                    });
                    document.querySelectorAll("#seleccion-usuario label").forEach(e=>{
                        e.classList.remove("label-seleccionable");
                    });
                }else{
                    document.querySelector("#seleccion-usuario").classList.remove('blocked');
                    if(!document.querySelectorAll("#seleccion-usuario label")[0].classList.contains("label-seleccionable")){
                        document.querySelectorAll("#seleccion-usuario label").forEach(e=>{
                            e.classList.add("label-seleccionable");
                        });
                    }
                    
                    document.querySelectorAll("#seleccion-usuario label, #seleccion-usuario label > *").forEach(e =>{
                        e.removeEventListener("click", PreventEnvio);
                    });
                }
                MostrarReporte("#seleccionar-usuario", "259px");
                break;
        }

        botonEnviar.removeEventListener("click", PreventEnvio);
        botonEnviar.classList.add("debloqueo-generar");
    });
});


//  Modificar Periodo de Tiempo
let reportePeriodo = document.querySelector("#seleccion-tiempo");
reportePeriodo.addEventListener("change", () => {
    if(reportePeriodo.value == "personalizado"){
        document.querySelector("#seleccion-fecha-personalizada").classList.remove("blocked");
        document.querySelector("#fecha-periodo-inicio").removeAttribute("readonly");
        document.querySelector("#fecha-periodo-final").removeAttribute("readonly");
    }else{
        if(!document.querySelector("#seleccion-fecha-personalizada").classList.contains("blocked")){
            document.querySelector("#seleccion-fecha-personalizada").classList.add("blocked");
        }
        document.querySelector("#fecha-periodo-inicio").setAttribute("readonly", true);
        document.querySelector("#fecha-periodo-final").setAttribute("readonly", true);

        let fechaInicio = new Date();
        switch(reportePeriodo.value){
            case "todos":
            case "hoy":
                fechaInicio = new Date();
                break;
            case "semana":
                fechaInicio.setDate(fechaInicio.getDate() - 7);
                break;
            case "mes":
                fechaInicio.setMonth(fechaInicio.getMonth() - 1);
                break;
            case "trimestre":
                fechaInicio.setMonth(fechaInicio.getMonth() - 3);
                break;
            case "semestre":
                fechaInicio.setMonth(fechaInicio.getMonth() - 6);
                break;
            case "anno":
                fechaInicio.setFullYear(fechaInicio.getFullYear() - 1);
                break;
        }

        function formatDate(date) {
            let year = date.getFullYear();
            let month = ('0' + (date.getMonth() + 1)).slice(-2);
            let day = ('0' + date.getDate()).slice(-2);
            return `${year}-${month}-${day}`;
        }

        document.querySelector("#fecha-periodo-inicio").value = formatDate(fechaInicio);
        document.querySelector("#fecha-periodo-final").value = formatDate(new Date());
    }
});


//  Modificar Usuario
function AgregarOpciones(objeto = usuarios, reset = true){
    if(reset){
        document.querySelector("#seleccion-usuario").innerHTML = "";
        let opcion = document.createElement("label");
        opcion.setAttribute("for", "0");
        let checked = document.createElement("input");
        checked.setAttribute("type", "checkbox");
        checked.name = "id_user[]";
        checked.value = "0";
        checked.id = '0';
        checked.checked = true;
        opcion.appendChild(checked);
        let span = document.createElement("span");
        span.textContent = "Todos";
        opcion.appendChild(span);
        document.querySelector("#seleccion-usuario").appendChild(opcion);
    }

    objeto.forEach((e) => {
        let opcion = document.createElement("label");
        opcion.setAttribute("for", e.id_user);
        let checked = document.createElement("input");
        checked.setAttribute("type", "checkbox");
        checked.name = "id_user[]";
        checked.value = e.id_user;
        checked.id = e.id_user;
        opcion.appendChild(checked);
        let span = document.createElement("span");
        span.textContent = `${e.nombre} ${e.apellido}`;
        opcion.appendChild(span);
        document.querySelector("#seleccion-usuario").appendChild(opcion);
    });
}

let visitante = document.querySelector("#visitante");
let admin = document.querySelector("#admin");
let mod = document.querySelector("#mod");

AgregarOpciones(usuarios);

visitante.addEventListener("change", () => {
    if(visitante.checked){
        if(!admin.checked && !mod.checked){
            AgregarOpciones(visitantes);
        }else if(!admin.checked && mod.checked){
            AgregarOpciones(mods);
            AgregarOpciones(visitantes, false);
        }else if(admin.checked && !mod.checked){
            AgregarOpciones(admins);
            AgregarOpciones(visitantes, false);
        }else{
            AgregarOpciones(mods);
            AgregarOpciones(admins, false);
            AgregarOpciones(visitantes, false);
        }
    }else{
        if(!admin.checked && !mod.checked){
            AgregarOpciones(usuarios);
        }else if(!admin.checked && mod.checked){
            AgregarOpciones(mods);
        }else if(admin.checked && !mod.checked){
            AgregarOpciones(admins);
        }else{
            AgregarOpciones(mods);
            AgregarOpciones(admins, false);
        }
    }
});
admin.addEventListener("change", () => {
    if(admin.checked){
        if(!visitante.checked && !mod.checked){
            AgregarOpciones(admins);
        }else if(!visitante.checked && mod.checked){
            AgregarOpciones(mods);
            AgregarOpciones(admins, false);
        }else if(visitante.checked && !mod.checked){
            AgregarOpciones(admins);
            AgregarOpciones(visitantes, false);
        }else{
            AgregarOpciones(mods);
            AgregarOpciones(admins, false);
            AgregarOpciones(visitantes, false);
        }
    }else{
        if(!visitante.checked && !mod.checked){
            AgregarOpciones(usuarios);
        }else if(!visitante.checked && mod.checked){
            AgregarOpciones(mods);
        }else if(visitante.checked && !mod.checked){
            AgregarOpciones(visitantes);
        }else{
            AgregarOpciones(mods);
            AgregarOpciones(visitantes, false);
        }
    }
});
mod.addEventListener("change", () => {
    if(mod.checked){
        if(!visitante.checked && !admin.checked){
            AgregarOpciones(mods);
        }else if(!visitante.checked && admin.checked){
            AgregarOpciones(mods);
            AgregarOpciones(admins, false);
        }else if(visitante.checked && !mod.checked){
            AgregarOpciones(mods);
            AgregarOpciones(visitantes, false);
        }else{
            AgregarOpciones(mods);
            AgregarOpciones(admins, false);
            AgregarOpciones(visitantes, false);
        }
    }else{
        if(!visitante.checked && !admin.checked){
            AgregarOpciones(usuarios);
        }else if(!visitante.checked && admin.checked){
            AgregarOpciones(admins);
        }else if(visitante.checked && !admin.checked){
            AgregarOpciones(visitantes);
        }else{
            AgregarOpciones(admins);
            AgregarOpciones(visitantes, false);
        }
    }
});

document.querySelectorAll('#seleccion-usuario input').forEach(element => {
    element.addEventListener("click", () => {
        if(element.id != '0'){
            document.getElementById("0").checked = false;
        }

        if(element.id == '0'){
            document.querySelectorAll('#seleccion-usuario input').forEach(element1 => element1.checked = false);
            element.checked = true;
        }
    });
});

//  Modificar Documento