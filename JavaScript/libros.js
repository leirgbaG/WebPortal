const libros = document.querySelectorAll('.libro');

libros.forEach(libro => {
    const id_libro = libro.getAttribute('id');
    const outModal = document.getElementById("outModal" + id_libro);
    const modal = document.getElementById("modal" + id_libro);


    libro.addEventListener("click", ()=>{
        outModal.style.display = "block";
        modal.style.display = "grid";
        
        setTimeout(()=>{
            outModal.classList.add("show");
            modal.classList.add("show");
        }, 5);
    });

    outModal.addEventListener("click", ()=>{
        outModal.classList.remove("show");
        modal.classList.remove("show");

        setTimeout(()=>{
            outModal.style.display = "none";
            modal.style.display = "none";
        }, 500);
    });
});



updateSelectedLabel();
// Agregar evento a cada input radio
document.querySelectorAll('input[type="radio"][name="tipo"]').forEach(function(input) {
    input.addEventListener('change', updateSelectedLabel);
});

function updateSelectedLabel() {
    // Remover los estilos de todos los labels
    document.querySelectorAll('.extra_search').forEach(function(label) {
        label.style.fontWeight = 'normal';
        label.style.textDecoration = 'none';
    });

    // Añadir los estilos al label del input seleccionado
    const selectedInput = document.querySelector('input[type="radio"][name="tipo"]:checked');
    if (selectedInput) {
        const selectedLabel = document.querySelector('label[for="' + selectedInput.id + '"]');
        if (selectedLabel) {
            selectedLabel.style.fontWeight = 'bold';
            selectedLabel.style.textDecoration = 'underline';
        }
    }
}




const outModalEliminar = document.querySelector('#outModalBiblioteca');
const modalEliminar = document.querySelector('#modalEliminarLibro');
const botonRegresar = document.querySelector('#cancelar_modalEliminar');
const botonEliminar = document.querySelector('#eliminar_modalEliminar');
const botonEliminarLibro = document.querySelectorAll('.botonEliminarLibro');

function MostrarModal(){
    outModalEliminar.style.display = "block";
    modalEliminar.style.display = "flex";

    setTimeout(()=>{
        outModalEliminar.style.opacity = 1;
        modalEliminar.style.opacity = 1;
    }, 5);
}

function OcultarModal(){
    outModalEliminar.style.opacity = 0;
    modalEliminar.style.opacity = 0;

    setTimeout(()=>{
        outModalEliminar.style.display = "none";
        modalEliminar.style.display = "none";
    }, 300);
}

botonEliminarLibro.forEach((e)=>{
    e.addEventListener("click", ()=>{
        MostrarModal();
        botonEliminar.addEventListener('click', ()=>{
            window.location.href = e.id;
        })
    });
});

outModalEliminar.addEventListener('click', OcultarModal);
botonRegresar.addEventListener('click', OcultarModal);

