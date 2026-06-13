function calcularTotal() {

    let precio = document.getElementById("precio").value;
    let cantidad = document.getElementById("cantidad").value;

    let total = precio * cantidad;

    document.getElementById("total").innerHTML =
        "Total: S/ " + total.toFixed(2);
}