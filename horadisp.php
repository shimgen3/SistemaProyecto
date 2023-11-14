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
    $usuario = $_SESSION['username'];
    $action = $_POST['action'];

    $diaReserva = $_POST['dia'];
    $horaReserva = $_POST['hora'];
    $fechaHoraReserva = new DateTime("$diaReserva $horaReserva");
    $fechaHoraFormateada = $fechaHoraReserva->format('Y-m-d H:i:s');

    if ($action === 'agregar') {
        $insertReservaQuery = "INSERT INTO reservas (hora, idbarber, idservicio, realizada) VALUES ('$fechaHoraFormateada', 1, 1, FALSE)";
        $con->query($insertReservaQuery);
    } elseif ($action === 'eliminar') {
        $eliminarReservaQuery = "DELETE FROM reservas WHERE idbarber = 1 AND hora = '$fechaHoraFormateada'";
        $con->query($eliminarReservaQuery);
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
        }
        .reserved {
            background-color: #ffaaaa;
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
        Edición de horarios disponibles <br>
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

                    if (!$isReserved) {
                        echo '<td class="blank" onclick="accionReserva(\'' . $formattedDate . '\', \'' . $horadisp2 . '\', \'agregar\')"></td>';
                    } else {
                        echo '<td class="reserved" onclick="accionReserva(\'' . $formattedDate . '\', \'' . $horadisp2 . '\', \'eliminar\')"></td>';
                    }
                }
                echo '</tr>';
            }
            ?>
        </table>
    </div>
    
    <div style="text-align: center; margin-top: 10px;">
        <a href="?week=<?php echo $currentWeekNumber - 1; ?>">Semana anterior</a>
        <a href="?week=<?php echo $currentWeekNumber + 1; ?>">Siguiente semana</a>
    </div>

    <script>
        function accionReserva(dia, hora, action) {
            var form = document.createElement("form");
            form.method = "POST";
            form.action = "";

            var inputDia = document.createElement("input");
            inputDia.type = "hidden";
            inputDia.name = "dia";
            inputDia.value = dia;
            form.appendChild(inputDia);

            var inputHora = document.createElement("input");
            inputHora.type = "hidden";
            inputHora.name = "hora";
            inputHora.value = hora;
            form.appendChild(inputHora);

            var inputAction = document.createElement("input");
            inputAction.type = "hidden";
            inputAction.name = "action";
            inputAction.value = action;
            form.appendChild(inputAction);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>

<?php
$con->close();
?>
