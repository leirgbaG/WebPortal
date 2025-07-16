const iniciaNum = /^\d/;
const poseeEspacios = /^\S+$/;

document.getElementById('user').addEventListener('keyup', function(event){
    let usuario = document.getElementById('user').value;

    if(usuario == null || usuario == undefined || usuario.length < 7 || usuario.length > 16){
        document.getElementById('usererr1').style.display= "block";
    }else{
        document.getElementById('usererr1').style.display= "none";
    }

    if(iniciaNum.test(usuario)){
        document.getElementById('usererr2').style.display= "block";
    }else{
        document.getElementById('usererr2').style.display= "none";
    }

    if(!poseeEspacios.test(usuario)){
        document.getElementById('usererr3').style.display= "block";
    }else{
        document.getElementById('usererr3').style.display= "none";
    }
});

document.getElementById('pw').addEventListener('keyup', function(event){
    let password = document.getElementById('pw').value;

    if(password == null || password.length == undefined || password.length < 7 || password.length > 16){
        document.getElementById('pwerr1').style.display= "block";
    }else{
        document.getElementById('pwerr1').style.display= "none";
    }

    if(iniciaNum.test(password) || !poseeEspacios.test(password)){
        document.getElementById('pwerr2').style.display= "block";
    }else{
        document.getElementById('pwerr2').style.display= "none";
    }
});

document.getElementById('enviar').addEventListener('click', function(event){
    let usuario = document.getElementById('user').value;
    let password = document.getElementById('pw').value;

    if(usuario == null || usuario == undefined || usuario.length < 8 || usuario.length > 16 || password == null || password.length == undefined || password.length < 8 || password.length > 16 || iniciaNum.test(usuario) || !poseeEspacios.test(usuario) || iniciaNum.test(password) || !poseeEspacios.test(password)){
        event.preventDefault();
        alert("Por favor, complete los campos correctamente");
    }

});