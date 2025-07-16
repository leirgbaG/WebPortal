let botonBorrar = document.querySelectorAll('.eliminar');
let botonBlock  = document.querySelectorAll('.bloquear');


let outModalCuenta  = document.querySelector('#outModalCuenta');
let modalCuenta     = document.querySelector('#modalCuenta');

function mostrarModal(msg, clase, id){
    outModalCuenta.style.display = 'block';
    modalCuenta.style.display = 'flex';

    modalCuenta.querySelector('#tituloModalCuenta').innerHTML = msg;
    
    modalCuenta.querySelector('#aceptar_Modalcuenta').innerHTML = clase;
    modalCuenta.querySelector('#aceptar_Modalcuenta').addEventListener('click', () => window.location.href = id)

    setTimeout(()=>{
        outModalCuenta.style.opacity = 1;
        modalCuenta.style.opacity = 1;
    }, 5);
}

function ocultarModal(){
    outModalCuenta.style.opacity = 0;
    modalCuenta.style.opacity = 0;

    modalCuenta.classList = "";

    setTimeout(()=>{
        outModalCuenta.style.display = 'none';
        modalCuenta.style.display = 'none';
    }, 300);
}

botonBorrar.forEach((e) => {
    e.addEventListener('click', () => mostrarModal('¿Desea eliminar esta cuenta?', 'Eliminar', e.id));
});

botonBlock.forEach((e2)=>{
    let mensaje = e2.id.match(/1$/) ? '¿Desea bloquear esta cuenta?' : '¿Desea desbloquear esta cuenta?';
    let aceptar = e2.id.match(/1$/) ? 'Bloquear' : 'Desbloquear';
    e2.addEventListener('click', () => mostrarModal(mensaje, aceptar, e2.id));
});

outModalCuenta.addEventListener('click', ocultarModal);
modalCuenta.querySelector('#cancelar_Modalcuenta').addEventListener('click', ocultarModal);