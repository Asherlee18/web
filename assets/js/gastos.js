function validarGasto(){

    let monto=document.getElementById("monto").value;

    if(monto<=0){
        alert("Monto inválido");
        return false;
    }

    return true;
}