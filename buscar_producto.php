<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

// Inicializar variable de búsqueda
$buscar = "";
$sql = "SELECT * FROM productos";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["buscar"])) {
    $buscar = trim($_GET["buscar"]);

    // Buscar por nombre del producto (LIKE)
    $sql = "SELECT * FROM productos WHERE nombre LIKE ?";
}

// Prepara y ejecuta la consulta
$stmt = $conn->prepare($sql);
if (!empty($buscar)) {
    $like = "%" . $buscar . "%";
    $stmt->bind_param("s", $like);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Productos</title>
</head>
<body>
<div class="contenedor-navegacion">
    <nav>
        <ul>
            <li><a href="inicio.php">Inicio</a></li>
            <li><a href="registro_producto.php">Registrar Productos</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li><a href="reportes.php">Reportes</a></li>
        </ul>
    </nav>
</div>

<div class="contenedor-busqueda">
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
        <input type="text" name="buscar" placeholder="Buscar productos" value="<?php echo htmlspecialchars($buscar); ?>">
        <input type="submit" value="Buscar"><br><br>
    </form>
</div>

<div class="contenedor-productos">
    <h1>Productos</h1>
    <div class="productos">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="producto">
                    <h2><?php echo htmlspecialchars($row["nombre"]); ?></h2>
                    <img src="<?php echo "productos/imagenes/" . htmlspecialchars($row["imagen"]); ?>" alt="<?php echo htmlspecialchars($row["nombre"]); ?>" style="max-width: 100%; height: auto;">
                    <p>Precio: <?php echo htmlspecialchars($row["precio"]); ?> Bs</p>
                    <p>Cantidad: <?php echo htmlspecialchars($row["cantidad"]); ?></p>
                    <p>Descripción: <?php echo htmlspecialchars($row["descripcion"]); ?></p>
                    <a href="editar_producto.php?nombre=<?php echo urlencode($row['nombre']); ?>">Editar Producto</a>
                </div>
                <?php
            }
        } else {
            echo "<p>No hay productos disponibles</p>";
        }
        ?>
    </div>
</div>
</body>
</html>