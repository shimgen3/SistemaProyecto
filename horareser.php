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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    $diaReserva = isset($_POST['dia']) ? $_POST['dia'] : null;
    $horaReserva = isset($_POST['hora']) ? $_POST['hora'] : null;

    if ($action === 'reservar') {
        // Datos del cliente
        $nombreCliente = $_POST['nombre'];
        $rutCliente = $_POST['rut'];
        $telefonoCliente = $_POST['telefono'];
        $servicioSeleccionado = $_POST['servicio'];

        // Verificar si el cliente ya existe
        $queryClienteExistente = "SELECT idcliente FROM clientes WHERE rut = '$rutCliente'";
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
        $queryIdServicio = "SELECT idservicio FROM servicios WHERE servicename = '$servicioSeleccionado'";
        $resultIdServicio = $con->query($queryIdServicio);
        $rowIdServicio = $resultIdServicio->fetch_assoc();
        $idServicio = $rowIdServicio['idservicio'];

        // Actualizar reserva en la tabla de reservas
        $updateReservaQuery = "UPDATE reservas SET idcliente = $idCliente, idservicio = $idServicio, realizada = TRUE WHERE idbarber = 5 AND hora = '$diaReserva $horaReserva'";
        $con->query($updateReservaQuery);
    }
}

// Obtener la semana actual o la seleccionada
$currentWeekNumber = isset($_GET['week']) ? intval($_GET['week']) : date('W');
$currentYear = date('Y');
$monday = new DateTime();
$monday->setISODate($currentYear, $currentWeekNumber);

// Obtener la lista de barberos
$queryBarberos = "SELECT idbarber, username FROM barberos";
$resultBarberos = $con->query($queryBarberos);
$barberos = [];
while ($row = $resultBarberos->fetch_assoc()) {
    $barberos[] = $row;
}

// Obtener el id del barbero seleccionado
$idBarberoSeleccionado = isset($_POST['barbero']) ? $_POST['barbero'] : (isset($_GET['barbero']) ? $_GET['barbero'] : 1);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Horas en Peluquería</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
        label, select, button {
            margin-bottom: 10px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
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
        .blank {
            background-color: #FFFFFF;
        }
        .week-navigation {
            text-align: center;
            margin-top: 10px;
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
        .booking-popup h3 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .booking-popup button {
            display: inline-block;
            background-color: #333;
            color: #fff;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        .booking-popup button.cancel {
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <div class="title">
        Reserva de Horas en Peluquería <br>
        Semana <?php echo $monday->format('d') . ' de ' . $monday->format('F'); ?>
    </div>
    
    <!-- Agregar selección de barbero y semana -->
    <form method="POST" action="">
        <label for="barbero">Seleccione un barbero:</label>
        <select id="barbero" name="barbero" required>
            <?php foreach ($barberos as $barberoItem) : ?>
                <option value="<?= $barberoItem['idbarber'] ?>" <?php echo $idBarberoSeleccionado == $barberoItem['idbarber'] ? 'selected' : ''; ?>>
                    <?= $barberoItem['username'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        
        
        <input type="hidden" name="action" value="seleccionar">
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

                    $queryReserva = "SELECT * FROM reservas WHERE idbarber = $idBarberoSeleccionado AND hora = '$formattedDate $horadisp2' and idcliente IS NULL";
                    $resultReserva = $con->query($queryReserva);
                    $isReserved = $resultReserva->num_rows > 0;
                    $queryReserva2 = "SELECT * FROM reservas WHERE idbarber = $idBarberoSeleccionado AND hora = '$formattedDate $horadisp2' and idcliente IS NOT NULL";
                    $resultReserva2 = $con->query($queryReserva2);
                    $isReserved2 = $resultReserva2->num_rows > 0;
                    

                    if ($isReserved) {
                        echo '<td class="available" onclick="mostrarPopup(\'' . $formattedDate . '\', \'' . $horadisp2 . '\')"></td>';
                    } elseif ($isReserved2){
                        echo '<td class="reserved"></td>';
                    } else
                    {
                        echo '<td class="blank"></td>';
                    }
                }
                echo '</tr>';
            }
            ?>
        </table>
    </div>
    
    <div style="text-align: center; margin-top: 10px;">
        <a href="?week=<?php echo $currentWeekNumber - 1; ?>&barbero=<?php echo $idBarberoSeleccionado; ?>">Semana anterior</a>
        <a href="?week=<?php echo $currentWeekNumber + 1; ?>&barbero=<?php echo $idBarberoSeleccionado; ?>">Siguiente semana</a><br>
        <button onclick="volverAotraPagina()">Volver al inicio</button>
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
        function volverAotraPagina() {
            
            window.location.href = "profile.php";
        }
    </script>
</body>
</html>

<?php
$con->close();
?>
