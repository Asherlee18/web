function validarProducto(){

    let stock=document.getElementById("stock").value;

    if(stock<0){
        alert("Stock inválido");
        return false;
    }

    return true;
}