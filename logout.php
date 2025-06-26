<?php
ini_set('display_errors', 1);              // Muestra los errores en pantalla
ini_set('display_startup_errors', 1);      // Muestra errores durante el arranque de PHP
error_reporting(E_ALL);                    // Reporta todos los errores y advertencias

session_start();                 // Iniciar sesión
session_unset();                 // Vaciar variables de sesión
session_destroy();               // Destruir la sesión
header("Location: index.html");  // Redirigir a la página principal
exit();
?>