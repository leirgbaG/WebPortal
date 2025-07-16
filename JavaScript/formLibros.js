// Mensaje al Subir Archivo
let doc = document.querySelector('#docx');
doc.addEventListener('change', ()=>{
    if(doc.files.length > 0){
        const fileName = doc.files[0].name; 
        document.querySelector('#texto_docx').textContent = `Archivo seleccionado: ${fileName}`;
        document.querySelector('#docx_btn').textContent = "Cambiar Archivo";
    }else{
        document.querySelector('#texto_docx').textContent = `No hay archivo seleccionado`;
        document.querySelector('#docx_btn').textContent = "Seleccionar Archivo";
    }
});

// Separar Palabras Clave 
const input = document.querySelector('#kw');
const spanContainer = document.querySelector('#palabra');

input.addEventListener('input', function() {
    const words = input.value.trim().split(/\s+/);
    spanContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos spans

    words.forEach(word => {
        if (word.length > 0) {
            const span = document.createElement('span');
            span.textContent = word;
            spanContainer.appendChild(span);
        }
    });
});

// Reemplazar saltos de Línea 
const textInput = document.querySelector('#descripcion');
const outputContainer = document.querySelector('#enviable');

textInput.addEventListener('input', function() {
    const input = textInput.value;
    const formattedText = input.replace(/\n/g, '<br>');
    outputContainer.innerHTML = formattedText;
});


// **
// **   Procesos para Autores
// **

// Crear Inputs
function CrearAutores(){
    let contenedor = document.querySelector('#autores');
    let autor = document.createElement('section');
    autor.innerHTML =  `<label>
    <input type="text" name="nombre_autor[]" placeholder="Nombre del Autor" class="autor autor_prenom">
    <input type="text" name="apellido_autor[]" placeholder="Apellido del Autor" class="autor autor_nom">
    </label>`;
    
    contenedor.appendChild(autor);
}

// Evento
function Eventos(){
    let prenom = document.querySelectorAll('.autor_prenom');
    let nom = document.querySelectorAll('.autor_nom');

    prenom.forEach((elemento, i) => {
        elemento.addEventListener('input', ()=>{
            if((elemento.value.length > 0 && nom[i].value.length > 0) && (prenom.every((element)=>{if(element.value.length > 0) return true; else return false;}) && nom.every((element) =>{if(element.value.length > 0) return true; else return false;}))){
                CrearAutores();
            }
        });
    });

    nom.forEach((elemento, i) => {
        elemento.addEventListener('input', ()=>{
            if((elemento.value.length > 0 && prenom[i].value.length > 0) && (prenom.every((element)=>{if(element.value.length > 0) return true; else return false;}) && nom.every((element) =>{if(element.value.length > 0) return true; else return false;}))){
                CrearAutores();
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', Eventos);