<?php
session_start();

$DATABASE_HOST = 'optideve.com';
$DATABASE_USER = 'optideve_login';
$DATABASE_PASS = 'log1605log';
$DATABASE_NAME = 'optideve_Test';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $diaReserva = $_POST['dia'];
    $horaReserva = $_POST['hora'];
    $fechaHoraReserva = new DateTime("$diaReserva $horaReserva");
    $fechaHoraFormateada = $fechaHoraReserva->format('Y-m-d H:i:s');

    if ($action === 'reservar') {
        $nombreCliente = $_POST['nombre'];
        $rutCliente = $_POST['rut'];
        $telefonoCliente = $_POST['telefono'];
        $servicioSeleccionado = $_POST['servicio'];

        // Verificar si el cliente ya existe
        $queryClienteExistente = "SELECT * FROM clientes WHERE rut = '$rutCliente'";
        $resultClienteExistente = $con->query($queryClienteExistente);

        if ($resultClienteExistente->num_rows == 0) {
            // El cliente no existe, lo agregamos
            $insertClienteQuery = "INSERT INTO clientes (username, rut, telefono) VALUES ('$nombreCliente', '$rutCliente', '$telefonoCliente')";
            $con->query($insertClienteQuery);
        }

        // Obtener el id del cliente
        $queryIdCliente = "SELECT idcliente FROM clientes WHERE rut = '$rutCliente'";
        $resultIdCliente = $con->query($queryIdCliente);
        $rowIdCliente = $resultIdCliente->fetch_assoc();
        $idCliente = $rowIdCliente['idcliente'];

        // Obtener el id del servicio
        $queryIdServicio = "SELECT idservicio FROM servicios WHERE name = '$servicioSeleccionado'";
        $resultIdServicio = $con->query($queryIdServicio);
        $rowIdServicio = $resultIdServicio->fetch_assoc();
        $idServicio = $rowIdServicio['idservicio'];

        // Insertar reserva en la tabla de reservas
        $insertReservaQuery = "INSERT INTO reservas (hora, idbarber, idcliente, idservicio, realizada) VALUES ('$fechaHoraFormateada', 1, $idCliente, $idServicio, TRUE)";
        $con->query($insertReservaQuery);
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

                    $queryReserva = "SELECT * FROM reservas WHERE idbarber = 1 AND hora = '$formattedDate $horadisp2'";
                    $resultReserva = $con->query($queryReserva);
                    $isReserved = $resultReserva->num_rows > 0;

                    if ($isReserved) {
                        echo '<td class="available" onclick="mostrarPopup(\'' . $formattedDate . '\', \'' . $horadisp2 . '\')"></td>';
                    } elseif {
                        echo '<td class="reserved"></td>';
                    } elseif {
                        echo '<td class="reserved"></td>';
                    }
                }
                echo '</tr>';
            }
            ?>
        </table>
    </div>
    
    <div id="bookingPopup" class="booking-popup">
        <h3>Reservar Hora</h3>
        <form method="POST" action="">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br>

            <label for="rut">RUT:</label>
            <input type="text" id="rut" name="rut" required><br>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required><br>

            <label for="servicio">Servicio:</label>
            <select id="servicio" name="servicio" required>
                <option value="Corte de cabello">Corte de cabello</option>
                <option value="Afeitado">Afeitado</option>
                <option value="Corte y afeitado">Corte y afeitado</option>
            </select><br>

            <input type="hidden" id="horaReserva" name="hora">
            <input type="hidden" name="action" value="reservar">

            <button type="submit">Reservar</button>
            <button type="button" onclick="ocultarPopup()">Cancelar</button>
        </form>
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
