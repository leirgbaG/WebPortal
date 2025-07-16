const poseeNums = /\d+/;
const iniciaNums = /^\d+/;
const poseeEspacios = /\s+/;


// Datos Personales

document.getElementById("prenom").addEventListener("keyup", function(e){

    let nombre= document.getElementById('prenom').value;

    if(poseeEspacios.test(nombre)){
        document.getElementById('nameerr1').style.display= 'block';
    }else{
        document.getElementById('nameerr1').style.display= 'none';
    }
    
    if(poseeNums.test(nombre)){
        document.getElementById('nameerr2').style.display= 'block';
    }else{
        document.getElementById('nameerr2').style.display= 'none';
    }
});

document.getElementById('nom').addEventListener("keyup", function(e){

    let apellido= document.getElementById('nom').value;

    if(poseeEspacios.test(apellido)){
        document.getElementById('nameerr4').style.display= 'block';
    }else{
        document.getElementById('nameerr4').style.display= 'none';
    }

    if(poseeNums.test(apellido)){
        document.getElementById('nameerr5').style.display= 'block';
    }else{
        document.getElementById('nameerr5').style.display= 'none';
    }
});

document.getElementById("cargo").addEventListener("keyup", function(e){
    let cargo = document.getElementById('cargo').value;

    if(poseeNums.test(cargo)){
        document.getElementById("cargerr1").style.display= 'block';
    }else{
        document.getElementById("cargerr1").style.display= 'none';
    }
})

// Datos de la cuenta

document.getElementById('user').addEventListener("keyup", function(e){
    let usuario = document.getElementById('user').value;

    if(usuario == null || usuario == undefined || usuario.length < 8 || usuario.length > 16){
        document.getElementById('usererr1').style.display= 'block';
    }else{
        document.getElementById('usererr1').style.display= 'none';
    }

    if(iniciaNums.test(usuario)){
        document.getElementById('usererr2').style.display= 'block';
    }else{
        document.getElementById('usererr2').style.display= 'none';
    }

    if(poseeEspacios.test(usuario)){
        document.getElementById('usererr3').style.display= 'block';
    }else{
        document.getElementById('usererr3').style.display= 'none';
    }

});

document.getElementById('pw1').addEventListener("keyup", function(e){
    let contrasegna = document.getElementById('pw1').value;

    if(contrasegna == null || contrasegna == undefined || contrasegna.length < 8 || contrasegna.length > 16){
        document.getElementById('pwerr1').style.display= 'block';
    }else{
        document.getElementById('pwerr1').style.display= 'none';
    }

    if(iniciaNums.test(contrasegna)){
        document.getElementById('pwerr2').style.display= 'block';
    }else{
        document.getElementById('pwerr2').style.display= 'none';
    }

    if(poseeEspacios.test(contrasegna)){
        document.getElementById('pwerr3').style.display= 'block';
    }else{
        document.getElementById('pwerr3').style.display= 'none';
    }
});

document.getElementById('pw2').addEventListener("keyup", function(){
    let contrasegna2 = document.getElementById('pw2').value;
    let contrasegna1 = document.getElementById('pw1').value;
    if(contrasegna1 != contrasegna2){
        document.getElementById('pwerr4').style.display= 'block';
    }else{
        document.getElementById('pwerr4').style.display= 'none';
    }
})

document.getElementById('enviar').addEventListener("click", function(e){

    let nombre      = document.getElementById('prenom').value;
    let apellido    = document.getElementById('nom').value;
    let cargo       = document.getElementById('cargo').value;
    let usuario     = document.getElementById('user').value;
    let contrasegna = document.getElementById('pw1').value;
    let contrasegna2= document.getElementById('pw2').value;

    if(nombre == null || nombre == undefined || nombre.length < 2 || nombre.length > 20 || poseeEspacios.test(nombre) || poseeNums.test(nombre) || apellido == null || apellido == undefined || apellido.length < 2 || apellido.length > 20 || poseeEspacios.test(apellido) || poseeNums.test(apellido) || poseeNums.test(cargo) || usuario == null || usuario == undefined || usuario.length < 8 || usuario.length > 16 || poseeEspacios.test(usuario) || iniciaNums.test(usuario) || contrasegna == null || contrasegna == undefined || contrasegna.length < 8 || contrasegna.length > 16 || poseeEspacios.test(contrasegna) || iniciaNums.test(contrasegna) || contrasegna != contrasegna2){
        e.preventDefault();
        alert("Por favor, complete los campos correctamente");
    }
});