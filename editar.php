<?php

session_start();                       // Inicia una nueva sesión o reanuda la existente, es necesario para acceder a las variables de sesión, como $_SESSION["usuario"].
if (!isset($_SESSION["usuario"])) {    // Verifica si la variable de sesión "usuario" NO está definida, esto normalmente significa que el usuario no ha iniciado sesión correctamente.
    header("Location: login.php");     // Si no hay sesión activa, redirige al archivo login.php para que inicie sesión.
    exit();                            // Detiene la ejecución del script después de la redirección para evitar que se ejecute código adicional.
}

// Conexión a la base de datos
$conn = new mysqli("sql308.infinityfree.com", "if0_38936820", "ZN2l3TBeoM8", "if0_38936820_reservasHawai");
$conn->set_charset("utf8mb4");

$mensaje_exito = false;

// Obtener datos si hay un ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM reservas WHERE id = $id");
    $fila = $res->fetch_assoc();
}

// Guardar cambios
if (isset($_POST['actualizar'])) {
    $stmt = $conn->prepare("UPDATE reservas SET nombre=?, email=?, telefono=?, paquete=?, fecha_llegada=?, fecha_salida=?, personas=?, comentarios=? WHERE id=?");
    $stmt->bind_param(
        "ssssssisi",
        $_POST['nombre'],
        $_POST['email'],
        $_POST['telefono'],
        $_POST['paquete'],
        $_POST['fecha_llegada'],
        $_POST['fecha_salida'],
        $_POST['personas'],
        $_POST['comentarios'],
        $_POST['id']
    );
    $stmt->execute();
    $stmt->close();
    $mensaje_exito = true;

    // Recargar los datos actualizados
    $id = intval($_POST['id']);
    $res = $conn->query("SELECT * FROM reservas WHERE id = $id");
    $fila = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar reserva</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 40px;
    }

    .formulario-edicion {
      background: #fff;
      padding: 30px;
      max-width: 600px;
      margin: auto;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .formulario-edicion h2 {
      text-align: center;
      color: #C4512C;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-top: 15px;
      color: #333;
      font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background-color: #C4512C;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #a63e20;
    }

    .volver {
      display: block;
      text-align: center;
      margin-top: 20px;
      text-decoration: none;
      color: #555;
    }

    .volver:hover {
      color: #C4512C;
    }

    .exito {
      text-align: center;
      background: #d4edda;
      color: #155724;
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 20px;
      border: 1px solid #c3e6cb;
    }
  </style>
</head>
<body>

<div class="formulario-edicion">
  <h2>Editar reserva</h2>

  <?php if ($mensaje_exito): ?>
    <div class="exito">✅ ¡Reserva actualizada con éxito!</div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="id" value="<?= $fila['id'] ?>">

    <label>Nombre:</label>
    <input name="nombre" value="<?= htmlspecialchars($fila['nombre']) ?>">

    <label>Email:</label>
    <input name="email" type="email" value="<?= htmlspecialchars($fila['email']) ?>">

    <label>Teléfono:</label>
    <input name="telefono" value="<?= htmlspecialchars($fila['telefono']) ?>">

    <label>Paquete:</label>
    <select name="paquete">
      <option <?= $fila['paquete'] === 'Familiar' ? 'selected' : '' ?>>Familiar</option>
      <option <?= $fila['paquete'] === 'Romántico' ? 'selected' : '' ?>>Romántico</option>
      <option <?= $fila['paquete'] === 'Aventura' ? 'selected' : '' ?>>Aventura</option>
    </select>

    <label>Fecha de llegada:</label>
    <input type="date" name="fecha_llegada" value="<?= $fila['fecha_llegada'] ?>">

    <label>Fecha de salida:</label>
    <input type="date" name="fecha_salida" value="<?= $fila['fecha_salida'] ?>">

    <label>Personas:</label>
    <input type="number" name="personas" value="<?= $fila['personas'] ?>">

    <label>Comentarios:</label>
    <textarea name="comentarios"><?= htmlspecialchars($fila['comentarios']) ?></textarea>

    <button type="submit" name="actualizar">Guardar cambios</button>
  </form>

  <a href="dashboard.php" class="volver">← Volver al dashboard</a>
</div>

</body>
</html>
