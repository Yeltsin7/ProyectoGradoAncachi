<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanear y validar datos
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $cantidad = intval($_POST["cantidad"]);
    $precio = floatval($_POST["precio"]);
    
    // Verificar que se subió una imagen
    if (isset($_FILES["productImage"]) && $_FILES["productImage"]["error"] == 0) {
        $directorio_imagenes = "productos/imagenes/";

        // Crear el directorio si no existe
        if (!is_dir($directorio_imagenes)) {
            mkdir($directorio_imagenes, 0755, true);
        }

        // Generar un nombre único para la imagen
        $nombre_imagen = uniqid() . "_" . basename($_FILES["productImage"]["name"]);
        $ruta_imagen = $directorio_imagenes . $nombre_imagen;

        // Verificar el tipo de archivo
        $imageFileType = strtolower(pathinfo($ruta_imagen, PATHINFO_EXTENSION));
        $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
        
        if (in_array($imageFileType, $tipos_permitidos)) {
            // Mover la imagen subida al servidor
            if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $ruta_imagen)) {
                // La imagen se subió correctamente, ahora insertar el producto
                $sql = "INSERT INTO productos (nombre, descripcion, cantidad, precio, imagen) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssids", $nombre, $descripcion, $cantidad, $precio, $nombre_imagen);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Producto registrado con éxito</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al registrar producto: " . $stmt->error . "</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error al subir la imagen.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Solo se permiten imágenes (jpg, jpeg, png, gif).</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Por favor selecciona una imagen válida.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Registrar Producto Nuevo</title>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Registrar Producto Nuevo</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="mt-4" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nombre">Nombre del producto:</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción del producto:</label>
            <input type="text" name="descripcion" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" name="cantidad" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="precio">Precio:</label>
            <input type="number" name="precio" class="form-control" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="productImage">Imagen del Producto:</label>
            <input type="file" id="productImage" name="productImage" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar producto</button>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='inicio.php';">Cancelar Registro</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
