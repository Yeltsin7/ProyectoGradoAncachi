<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require_once 'conexion.php';

// Consultas para obtener ventas y adiciones
$sql_ventas = "SELECT movimientos.id, productos.nombre, movimientos.cantidad, movimientos.fecha 
               FROM movimientos 
               JOIN productos ON movimientos.producto_id = productos.id 
               WHERE movimientos.tipo_movimiento = 'salida' 
               ORDER BY movimientos.fecha DESC";
$result_ventas = $conn->query($sql_ventas);

$sql_adiciones = "SELECT movimientos.id, productos.nombre, movimientos.cantidad, movimientos.fecha 
                  FROM movimientos 
                  JOIN productos ON movimientos.producto_id = productos.id 
                  WHERE movimientos.tipo_movimiento IN ('entrada', 'añadido') 
                  ORDER BY movimientos.fecha DESC";
$result_adiciones = $conn->query($sql_adiciones);

// Verificar si las consultas fueron exitosas
if ($result_ventas === false || $result_adiciones === false) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Productos Vendidos y Añadidos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="nav-reportes">
        <nav>
            <ul>
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="registro_producto.php">Registrar Productos</a></li>
                <li><a href="ventas.php">Ventas</a></li>
                <li><a href="reportes.php">Reportes</a></li>
            </ul>
        </nav>
    </div>

    <div class="container">
        <!-- Reporte de ventas -->
        <h1 class="mt-5">Reporte de Productos Vendidos</h1>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad Vendida</th>
                    <th>Fecha de Venta</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_ventas->num_rows > 0): ?>
                    <?php while ($row = $result_ventas->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo $row['cantidad']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No se han realizado ventas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Reporte de adiciones -->
        <h1 class="mt-5">Reporte de Productos Añadidos</h1>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad Añadida</th>
                    <th>Fecha de Adición</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_adiciones->num_rows > 0): ?>
                    <?php while ($row = $result_adiciones->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo $row['cantidad']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No se han añadido productos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
