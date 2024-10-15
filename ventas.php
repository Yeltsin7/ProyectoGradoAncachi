<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

$sql = "SELECT * FROM productos";
$resultado = $conn->query($sql);

$productos = array();
while ($fila = $resultado->fetch_assoc()) {
    $productos[] = $fila;
}

echo '<div class="container">
        <form action="" method="post" id="ventaForm">
            <div class="form-group">
                <label for="producto">Producto:</label>
                <select name="producto" class="form-control">';
foreach ($productos as $producto) {
    echo '<option value="' . $producto["nombre"] . '">' . $producto["nombre"] . '</option>';
}
echo '</select>
            </div>
            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidad" value="0" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Realizar venta</button>
            <button type="button" class="btn btn-danger" onclick="location.href=\'inicio.php\'">Cancelar venta</button>
        </form>
    </div>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto_nombre = $_POST["producto"];
    $cantidad = (int)$_POST["cantidad"];

    if (empty($producto_nombre) || $cantidad <= 0) {
        echo '<script>alert("Por favor, selecciona un producto y una cantidad válida.");</script>';
    } else {

        $sql = "SELECT cantidad FROM productos WHERE nombre = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $producto_nombre);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();

        if ($fila) {
            $cantidad_actual = $fila["cantidad"];
            if ($cantidad > $cantidad_actual) {
                echo '<script>alert("No hay suficiente stock disponible.");</script>';
            } else {
                $nueva_cantidad = $cantidad_actual - $cantidad;
                $sql = "UPDATE productos SET cantidad = ? WHERE nombre = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $nueva_cantidad, $producto_nombre);
                $stmt->execute();

                echo '<script>alert("Venta realizada con éxito!");</script>';
            }
        } else {
            echo '<script>alert("El producto seleccionado no es válido.");</script>';
        }
    }
}
?>

<script>
document.querySelector('#ventaForm').addEventListener('submit', function(event) {
    var producto = document.querySelector('select[name="producto"]').value;
    var cantidad = document.querySelector('input[name="cantidad"]').value;

    if (producto === "" || cantidad <= 0) {
        alert("Por favor, selecciona un producto y una cantidad válida.");
        event.preventDefault();
    }
});
</script>

<link rel="stylesheet" type="text/css" href="estilos.css">
