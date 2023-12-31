<?php
session_start();


if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
$idBarbero = $_SESSION['id'];
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'barberia';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$stmt = $con->prepare('SELECT * FROM barberos WHERE idbarber = ?');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($idbarber, $username, $email, $rut, $password);
$stmt->fetch();
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'];

    $diaReserva = $_POST['dia'];
    $horaReserva = $_POST['hora'];
    $fechaHoraReserva = new DateTime("$diaReserva $horaReserva");
    $fechaHoraFormateada = $fechaHoraReserva->format('Y-m-d H:i:s');

    if ($action === 'agregar') {
        
        $insertReservaQuery = "INSERT INTO reservas (hora, idbarber, idservicio, realizada) VALUES ('$fechaHoraFormateada', '$idBarbero', NULL, FALSE)";
        $con->query($insertReservaQuery);
    } elseif ($action === 'eliminar') {
          
        $eliminarReservaQuery = "DELETE FROM reservas WHERE idbarber = $idBarbero AND hora = '$fechaHoraFormateada'";
        $con->query($eliminarReservaQuery);}}

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
        }
        .reserved {
            background-color: #ffaaaa;
        }
        .free {
            background-color: #aaffaa;
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
        Edición de horarios disponibles <?=$_SESSION['name']?><br>
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

                    $queryReserva = "SELECT * FROM reservas WHERE idbarber = $idbarber AND hora = '$formattedDate $horadisp2' AND idcliente IS NULL";
                    $resultReserva = $con->query($queryReserva);
                    $isReserved = $resultReserva->num_rows > 0;

                    $queryReserva2 = "SELECT * FROM reservas WHERE idbarber = $idbarber AND hora = '$formattedDate $horadisp2' AND idcliente IS NOT NULL";
                    $resultReserva2 = $con->query($queryReserva2);
                    $isReserved2 = $resultReserva2->num_rows > 0;
                    
                    
                    if (!$isReserved) {
                        echo '<td class="blank" onclick="accionReserva(\'' . $formattedDate . '\', \'' . $horadisp2 . '\', \'agregar\')"></td>';
                    } elseif (!$isReserved2) {
                        echo '<td class="reserved" onclick="accionReserva(\'' . $formattedDate . '\', \'' . $horadisp2 . '\', \'eliminar\')"></td>';
                    } else {
                        echo '<td class="free"></td>';
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
        <br>
        <button onclick="volverAotraPagina()">Volver al inicio</button>
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
        function volverAotraPagina() {
            
            window.location.href = "profile.php";
        }
    </script>
</body>
</html>

<?php
$con->close();
?>

