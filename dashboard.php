<?php

session_start();                       // Inicia una nueva sesión o reanuda la existente, es necesario para acceder a las variables de sesión, como $_SESSION["usuario"].
if (!isset($_SESSION["usuario"])) {    // Verifica si la variable de sesión "usuario" NO está definida, esto normalmente significa que el usuario no ha iniciado sesión correctamente.
    header("Location: login.php");     // Si no hay sesión activa, redirige al archivo login.php para que inicie sesión.
    exit();                            // Detiene la ejecución del script después de la redirección para evitar que se ejecute código adicional.
}

$host = "sql308.infinityfree.com";
$username = "if0_38936820";
$password = "ZN2l3TBeoM8";
$database = "if0_38936820_reservasHawai";

$conn = new mysqli($host, $username, $password, $database);
$conn->set_charset("utf8");

// Obtener estadísticas generales
$estadisticas_generales = $conn->query("
  SELECT COUNT(*) AS total_reservas,
         SUM(personas) AS total_personas,
         AVG(personas) AS promedio_general
  FROM reservas
")->fetch_assoc();

// Obtener promedios por tipo de paquete
$estadisticas_paquetes = $conn->query("
  SELECT paquete, AVG(personas) AS promedio_personas
  FROM reservas
  GROUP BY paquete
");

$promedios_paquete = [];
while ($fila = $estadisticas_paquetes->fetch_assoc()) {
    $promedios_paquete[$fila['paquete']] = round($fila['promedio_personas'], 0);
}


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$paqueteQuery = $conn->query("SELECT paquete, COUNT(*) as total, SUM(personas) as total_personas FROM reservas GROUP BY paquete");
$paquetes = [];
$reservas = [];
$personas = [];
while ($row = $paqueteQuery->fetch_assoc()) {
    $paquetes[] = $row['paquete'];
    $reservas[] = $row['total'];
    $personas[] = $row['total_personas'];
}


$limite = isset($_GET['limite']) ? $_GET['limite'] : 5;

if ($limite === 'all') {
  $sql = "SELECT * FROM reservas ORDER BY id DESC";
} else {
  $limite = intval($limite);
  $sql = "SELECT * FROM reservas ORDER BY id DESC LIMIT $limite";
}

$datos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Completo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        h1, h2 { text-align: center; color: #C4512C; }
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 40px;
        }
        .card {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 30%;
        }
        canvas { width: 100% !important; height: auto !important; }
        table { width: 100%; margin-top: 30px; background: white; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background-color: #C4512C; color: white; }
        .acciones {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; padding: 0 10px;
        }
        .boton {
            background-color: #C4512C; color: white; padding: 10px 20px;
            text-decoration: none; border-radius: 6px; font-weight: bold;
        }
        .boton:hover {
            background-color: #FFD700; color: black;
        }
    </style>
</head>
<body>

<div class="acciones">
    <a href="formulario_reserva.html" class="boton">➕ Gestionar reservas</a>
    <a href="logout.php" class="boton" style="background-color: #b22222;">⎋ Cerrar sesión</a>
</div>

<h1>Dashboard Aloha Ohana Travels</h1>

<h2 style="text-align: center; color: #C4512C;">Estadísticas Generales</h2>
<div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin: 20px 0;">
  <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center;">
    <strong>Total de reservas:</strong><br><?= $estadisticas_generales['total_reservas'] ?>
  </div>
  <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center;">
    <strong>Total de personas:</strong><br><?= $estadisticas_generales['total_personas'] ?>
  </div>
  <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center;">
    <strong>Promedio general por reserva:</strong><br><?= round($estadisticas_generales['promedio_general'], 0) ?>
  </div>
  <?php foreach ($promedios_paquete as $tipo => $promedio): ?>
    <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center;">
      <strong><?= htmlspecialchars(ucfirst($tipo)) ?>:</strong><br><?= $promedio ?> personas por reserva
    </div>
  <?php endforeach; ?>
</div>


<div class="chart-container">
    <div class="card">
        <h2>Reservas por Paquete</h2>
        <canvas id="tortaPaquetes"></canvas>
    </div>
    <div class="card">
        <h2>Total de Personas por Paquete</h2>
        <canvas id="barrasPersonas"></canvas>
    </div>
</div>

<h2>Reservas Registradas</h2>

<form method="GET" style="margin-bottom: 20px;">
  <label for="limite">Mostrar los últimos:</label>
  <select name="limite" id="limite" onchange="this.form.submit()">
    <option value="5" <?= (!isset($_GET['limite']) || $_GET['limite'] == 5) ? 'selected' : '' ?>>5</option>
    <option value="10" <?= (isset($_GET['limite']) && $_GET['limite'] == 10) ? 'selected' : '' ?>>10</option>
    <option value="50" <?= (isset($_GET['limite']) && $_GET['limite'] == 50) ? 'selected' : '' ?>>50</option>
    <option value="100" <?= (isset($_GET['limite']) && $_GET['limite'] == 100) ? 'selected' : '' ?>>100</option>
    <option value="all" <?= (isset($_GET['limite']) && $_GET['limite'] === 'all') ? 'selected' : '' ?>>Todos</option>
  </select> registros:
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Hora registro</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Teléfono</th>
        <th>Paquete</th>
        <th>Fecha Llegada</th>
        <th>Fecha Salida</th>
        <th>Personas</th>
        <th>Comentarios</th>
        <th>Acciones</th>

    </tr>
    <?php while($fila = $datos->fetch_assoc()): ?>
    <tr>
        <td><?= $fila['id'] ?></td>
        <td><?= $fila['fecha_hora_registro'] ?></td>
        <td><?= htmlspecialchars($fila['nombre']) ?></td>
        <td><?= htmlspecialchars($fila['email']) ?></td>
        <td><?= htmlspecialchars($fila['telefono']) ?></td>
        <td><?= htmlspecialchars($fila['paquete']) ?></td>
        <td><?= $fila['fecha_llegada'] ?></td>
        <td><?= $fila['fecha_salida'] ?></td>
        <td><?= $fila['personas'] ?></td>
        <td><?= nl2br(htmlspecialchars($fila['comentarios'])) ?></td>
        <td> <a href="editar.php?id=<?= $fila['id'] ?>">✏️ Editar</a> |
             <a href="eliminar.php?id=<?= $fila['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?')">🗑 Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
const etiquetas = <?= json_encode($paquetes) ?>;
const datosReservas = <?= json_encode($reservas) ?>;
const datosPersonas = <?= json_encode($personas) ?>;

new Chart(document.getElementById('tortaPaquetes'), {
    type: 'pie',
    data: {
        labels: etiquetas,
        datasets: [{
            data: datosReservas,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Cantidad de Reservas por Tipo de Paquete' }
        }
    }
});

new Chart(document.getElementById('barrasPersonas'), {
    type: 'bar',
    data: {
        labels: etiquetas,
        datasets: [{
            label: 'Total de Personas',
            data: datosPersonas,
            backgroundColor: '#36A2EB'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Personas Totales por Tipo de Paquete' }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>
<?php $conn->close(); ?>