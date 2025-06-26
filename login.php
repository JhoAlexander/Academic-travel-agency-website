<?php
ini_set('display_errors', 1);              // Muestra los errores en pantalla
ini_set('display_startup_errors', 1);      // Muestra errores durante el arranque de PHP
error_reporting(E_ALL);                    // Reporta todos los errores y advertencias

session_start(); // Iniciar sesión para manejar logins

if (isset($_SESSION["usuario"])) {
    header("Location: dashboard.php"); // Ya está logueado, lo enviamos al dashboard directamente.
    exit();
}

// Datos de conexión
$host = "sql308.infinityfree.com";
$username = "if0_38936820";
$password = "ZN2l3TBeoM8";
$database = "if0_38936820_reservasHawai";

// Conectar a la base de datos
$conn = new mysqli($host, $username, $password, $database);
$conn->set_charset("utf8");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"]; // Captura el nombre de usuario
    $contraseña = $_POST["contraseña"]; // Captura la contraseña

    // Buscar el usuario en la base de datos
    $stmt = $conn->prepare("SELECT id, password_hash FROM administradores WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    // Verifica si encontró el usuario
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $password_hash);
        $stmt->fetch();

        // Verifica la contraseña con hash
        if (password_verify($contraseña, $password_hash)) {
            $_SESSION["usuario_id"] = $id;          // Guardar el ID en sesión
            $_SESSION["usuario"] = $usuario;        // Guardar nombre de usuario
            header("Location: dashboard.php");      // Redirigir al dashboard
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
}

$conn->close(); // Cierra conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Aloha Ohana Travels</title>
    <style>
        body {
            background-color: #f3f3f3;
            font-family: Arial, sans-serif;
            padding: 40px;
        }

        .login-container {
            background-color: white;
            max-width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #C4512C;
        }

        input[type="text"], input[type="password"] {
            width: 95%;
            padding: 12px 12px 12px 12px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            background-color: #C4512C;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            font-size: 16px;
        }

        button:hover {
            background-color: #FFD700;
            color: black;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Ingresar a Dashboard</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="contraseña" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>
