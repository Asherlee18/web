<form action="../../controllers/ProductoController.php"
      method="POST">

    <input type="text"
           name="nombre"
           placeholder="Nombre">

    <input type="number"
           step="0.01"
           name="precio"
           placeholder="Precio">

    <input type="number"
           name="stock"
           placeholder="Stock">

    <button name="guardar">
        Guardar
    </button>

</form>