<?php

session_start();                       // Inicia una nueva sesión o reanuda la existente, es necesario para acceder a las variables de sesión, como $_SESSION["usuario"].
if (!isset($_SESSION["usuario"])) {    // Verifica si la variable de sesión "usuario" NO está definida, esto normalmente significa que el usuario no ha iniciado sesión correctamente.
    header("Location: login.php");     // Si no hay sesión activa, redirige al archivo login.php para que inicie sesión.
    exit();                            // Detiene la ejecución del script después de la redirección para evitar que se ejecute código adicional.
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $conn = new mysqli("sql308.infinityfree.com", "if0_38936820", "ZN2l3TBeoM8", "if0_38936820_reservasHawai");
    $conn->set_charset("utf8mb4");

    $stmt = $conn->prepare("DELETE FROM reservas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: dashboard.php");
    exit();
}
?>
