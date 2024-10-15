<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: inicio.php"); 
    exit();
}

require_once 'conexion.php';

$messageUsername = ""; 
$messagePassword = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]); 
    $password = trim($_POST["password"]);

    if (empty($username)) {
        $messageUsername = "Por favor, ingrese su nombre de usuario."; 
    }

    if (empty($password)) {
        $messagePassword = "Por favor, ingrese su contraseña."; 
    }

    if (empty($messageUsername) && empty($messagePassword)) {
        $sql = "SELECT * FROM usuarios WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION["username"] = $username;
            header("Location: inicio.php");
            exit;
        } else {
            $messageUsername = "Ingrese datos correctos."; 
            $messagePassword = "Ingrese datos correctos."; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comercial Patty - Iniciar sesión</title>
    <link rel="stylesheet" href="estilos.css">
    <script>
        window.onload = function() {
            document.getElementById("username").value = "";
        };
    </script>
</head>
<body>
    <div class="container">
        <h1>Comercial Patty</h1>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" autocomplete="off">
            <input type="text" style="display:none;">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" name="username" id="username" autocomplete="new-username" value="<?php echo htmlspecialchars($username ?? ''); ?>"><br>
            <span style="color:red;"><?php echo $messageUsername; ?></span><br>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password"><br>
            <span style="color:red;"><?php echo $messagePassword; ?></span><br>

            <input type="submit" value="Iniciar sesión">
        </form>
    </div>
</body>
</html>
