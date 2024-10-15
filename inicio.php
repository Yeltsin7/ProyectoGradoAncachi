<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="productos.php">Productos</a></li>
            <li><a href="registro_producto.php">Registrar Productos</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li><a href="reportes.php">Reportes</a></li>
            <li><a href="cerrarsesion.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION["username"]); ?></h1>
</body>
</html>