function validarLogin(){

    let usuario=document.getElementById("usuario").value;
    let clave=document.getElementById("clave").value;

    if(usuario==="" || clave===""){
        alert("Complete todos los campos");
        return false;
    }

    return true;
}