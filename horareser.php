<?php
session_start();

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'barberia';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Obtener la lista de barberos
$queryBarberos = "SELECT idbarber, username FROM barberos";
$resultBarberos = $con->query($queryBarberos);
$barberos = [];
while ($row = $resultBarberos->fetch_assoc()) {
    $barberos[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $diaReserva = $_POST['dia'];
    $horaReserva = $_POST['hora'];
    $fechaHoraReserva = new DateTime("$diaReserva $horaReserva");
    $fechaHoraFormateada = $fechaHoraReserva->format('Y-m-d H:i:s');

    if ($action === 'reservar') {
        // Resto del código de reserva
        // ...
    }
}

$currentWeekNumber = isset($_GET['week']) ? intval($_GET['week']) : date('W');
$currentYear = date('Y');
$monday = new DateTime();
$monday->setISODate($currentYear, $currentWeekNumber);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Horas en Peluquería</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .calendar {
            display: inline-block;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .available {
            background-color: #aaffaa;
            cursor: pointer;
        }
        .reserved {
            background-color: #ffaaaa;
        }
        .booking-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            z-index: 1000;
        }
        .booking-popup label {
            display: block;
            margin-bottom: 10px;
        }
        .title {
            text-align: center;
            font-size: 20px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="title">
        Reserva de Horas en Peluquería <br>
        Semana <?php echo $monday->format('d') . ' de ' . $monday->format('F'); ?>
    </div>
    
    <!-- Agregar selección de barbero -->
    <form method="POST" action="">
        <label for="barbero">Seleccione un barbero:</label>
        <select id="barbero" name="barbero" required>
            <?php foreach ($barberos as $barbero) : ?>
                <option value="<?= $barbero['idbarber'] ?>"><?= $barbero['username'] ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Seleccionar</button>
    </form>

    <!-- Mostrar horarios -->
    <div class="calendar">
        <table>
            <tr>
                <th>Hora</th>
                <th>Lunes</th>
                <th>Martes</th>
                <th>Miércoles</th>
                <th>Jueves</th>
                <th>Viernes</th>
                <th>Sábado</th>
            </tr>
            <?php
            $hours = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00'];
            foreach ($hours as $hour) {
                echo '<tr>';
                echo '<td>' . $hour . '</td>';
                for ($i = 0; $i < 6; $i++) {
                    $currentDay = clone $monday;
                    $currentDay->add(new DateInterval('P' . $i . 'D'));
                    $formattedDate = $currentDay->format('Y-m-d');
                    $horadisp2 = ($hour . ":00");

                    // Obtener el id del barbero seleccionado
                    $idBarberoSeleccionado = isset($_POST['barbero']) ? $_POST['barbero'] : 1;

                    $queryReserva = "SELECT * FROM reservas WHERE idbarber = $idBarberoSeleccionado AND hora = '$formattedDate $horadisp2'";
                    $resultReserva = $con->query($queryReserva);
                    $isReserved = $resultReserva->num_rows > 0;

                    if ($isReserved) {
                        echo '<td class="available" onclick="mostrarPopup(\'' . $formattedDate . '\', \'' . $horadisp2 . '\')"></td>';
                    } else {
                        echo '<td class="reserved"></td>';
                    }
                }
                echo '</tr>';
            }
            ?>
        </table>
    </div>

    <div id="bookingPopup" class="booking-popup">
        <!-- Contenido del popup (mismo que en el código original) -->
    </div>

    <script>
        function mostrarPopup(dia, hora) {
            document.getElementById('horaReserva').value = dia + ' ' + hora;
            document.getElementById('bookingPopup').style.display = 'block';
        }

        function ocultarPopup() {
            document.getElementById('bookingPopup').style.display = 'none';
        }
    </script>
</body>
</html>

<?php
$con->close();
?>

