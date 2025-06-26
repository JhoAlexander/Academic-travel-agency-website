<?php


// Parámetros de conexión a la base de datos en InfinityFree
$host = "sql308.infinityfree.com";         // Servidor de la base de datos
$username = "if0_38936820";                // Nombre de usuario de la base de datos de Alex en infinity
$password = "ZN2l3TBeoM8";                 // Contraseña del usuario Alex en infinity
$database = "if0_38936820_reservasHawai";  // Nombre de la base de datos para las reservas de viajes para Hawai

$conn = new mysqli($host, $username, $password, $database); // Crea una nueva conexión con MySQL
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {                             // Si hay error en la conexión
    mostrarMensaje("Conexión fallida: " . $conn->connect_error, false); // Mostrar mensaje de error
    exit();                                              // Detener ejecución
}

$nombre = htmlspecialchars($_POST['nombre']);           // Sanitiza y obtiene el nombre
$email = htmlspecialchars($_POST['email']);             // Sanitiza y obtiene el email
$telefono = htmlspecialchars($_POST['telefono']);       // Sanitiza y obtiene el teléfono
$paquete = htmlspecialchars($_POST['paquete']);         // Sanitiza y obtiene el tipo de paquete
$fecha_llegada = htmlspecialchars($_POST['fecha-llegada']); // Sanitiza y obtiene fecha de llegada
$fecha_salida = htmlspecialchars($_POST['fecha-salida']);   // Sanitiza y obtiene fecha de salida
$personas = (int)$_POST['personas'];                    // Convierte a entero el número de personas
$comentarios = htmlspecialchars($_POST['comentarios']); // Sanitiza los comentarios

$sql = "INSERT INTO reservas (nombre, email, telefono, paquete, fecha_llegada, fecha_salida, personas, comentarios)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";               // Consulta SQL con placeholders (segura contra inyecciones)

$stmt = $conn->prepare($sql);                           // Prepara la consulta SQL para ejecución segura

if ($stmt === false) {                                  // Si falla la preparación de la consulta
    mostrarMensaje("Error al preparar la consulta: " . $conn->error, false); // Mostrar error
    exit();                                              // Detener ejecución
}

$stmt->bind_param("ssssssis",                            // Enlaza los parámetros a la consulta SQL
    $nombre,                                             // Nombre
    $email,                                              // Email
    $telefono,                                           // Teléfono
    $paquete,                                            // Tipo de paquete
    $fecha_llegada,                                      // Fecha de llegada
    $fecha_salida,                                       // Fecha de salida
    $personas,                                           // Número de personas
    $comentarios                                         // Comentarios
);

if ($stmt->execute()) {                                 // Ejecuta la consulta SQL
    mostrarMensaje("¡Reserva guardada con éxito! 🌺", true); // Si todo va bien, muestra mensaje exitoso
} else {
    mostrarMensaje("Error al guardar la reserva: " . $stmt->error, false); // Si algo falla, muestra error
}

$stmt->close();                                          // Cierra el statement
$conn->close();                                          // Cierra la conexión a la base de datos

// Función para mostrar mensajes estilizados y un botón de regreso
function mostrarMensaje($mensaje, $exito) {
    $color = $exito ? "#28a745" : "#dc3545";             // Verde si es éxito, rojo si es error
    echo "                                               
    <html lang='es'>                                     
    <head>
        <meta charset='UTF-8'>
        <title>Resultado de reserva</title>
        <style>
            body {
                font-family: Arial, sans-serif;          
                text-align: center;                      
                background-color: #f9f9f9;               
                padding: 50px;                           
            }
            .mensaje {
                color: $color;                           
                font-size: 22px;                        
                margin-bottom: 30px;                     
            }
            .volver {
                background-color: #C4512C;               
                color: white;                            
                padding: 12px 25px;                      
                border: none;                            
                border-radius: 8px;                      
                font-size: 18px;                         
                text-decoration: none;                   
                transition: background-color 0.3s;       
            }
            .volver:hover {
                background-color: #FFD700;               
                color: black;                            
            }
        </style>
    </head>
    <body>
        <div class='mensaje'>$mensaje</div>              
        <a class='volver' href='formulario_reserva.html'>← Volver al formulario</a> 
    </body>
    </html>";                                            // Fin de la página HTML
}
?>
