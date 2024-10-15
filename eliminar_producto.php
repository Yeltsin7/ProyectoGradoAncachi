<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET["id"];
    $sql = "DELETE FROM productos WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Producto eliminado con éxito";
    } else {
        echo "Error al eliminar producto: " . $conn->error;
    }
}
?>