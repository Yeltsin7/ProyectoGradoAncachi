<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

// Inicializar variables
$row = null;
$producto_no_encontrado = false; // Variable para manejar el estado de búsqueda

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["nombre"])) {
    $nombre = trim($_GET["nombre"]); // Obtener el nombre del producto
    $sql = "SELECT * FROM productos WHERE nombre = ?";

    // Prepara la consulta
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        $producto_no_encontrado = true;
    }
}

// Manejo de la actualización y eliminación del producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    if (isset($_POST["eliminar"])) {
        $id = intval($_POST["id"]);
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Producto eliminado con éxito</div>";
            $row = null;
        } else {
            echo "<div class='alert alert-danger'>Error al eliminar producto: " . $conn->error . "</div>";
        }
    } else {
        $id = intval($_POST["id"]);
        $nombre = $_POST["nombre"];
        $descripcion = $_POST["descripcion"];
        $cantidad = $_POST["cantidad"];
        $precio = $_POST["precio"];
        
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == UPLOAD_ERR_OK) {
            $ruta_imagen = "Imágenes/" . basename($_FILES["imagen"]["name"]);
            move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_imagen);
            
            $sql = "UPDATE productos SET nombre = ?, descripcion = ?, cantidad = ?, precio = ?, imagen = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssissi", $nombre, $descripcion, $cantidad, $precio, $ruta_imagen, $id);
        } else {
            $sql = "UPDATE productos SET nombre = ?, descripcion = ?, cantidad = ?, precio = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisi", $nombre, $descripcion, $cantidad, $precio, $id);
        }

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Producto actualizado con éxito</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar producto: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="editarproducto">
        <div class="recuadro">
            <h1 class="text-center">Buscar Producto por Nombre</h1>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre del producto" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form>

                <button onclick="window.location.href='productos.php';" class="btn btn-secondary mb-3">Regresar a Productos</button>

                <div class="editar_producto">
                    <?php if ($producto_no_encontrado): ?>
                        <div class="alert alert-danger" role="alert">
                            No se encontró información del producto.
                        </div>
                    <?php elseif ($row): ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row["id"]); ?>">
                            <div class="form-group">
                                <label for="nombre">Nombre del producto:</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($row["nombre"]); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción del producto:</label>
                                <input type="text" name="descripcion" class="form-control" value="<?php echo htmlspecialchars($row["descripcion"]); ?>">
                            </div>
                            <div class="form-group">
                                <label for="cantidad">Cantidad:</label>
                                <input type="number" name="cantidad" class="form-control" value="<?php echo $row["cantidad"]; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="precio">Precio:</label>
                                <input type="number" name="precio" class="form-control" step="0.01" value="<?php echo $row["precio"]; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="imagen">Cargar imagen (opcional):</label>
                                <input type="file" name="imagen" class="form-control-file">
                            </div>
                            <input type="submit" value="Registrar Cambios" class="btn btn-success">
                            <button type="submit" name="eliminar" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar producto</button>
                        </form>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>