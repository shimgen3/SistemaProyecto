<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Barbería</title>
</head>
<body>

<?php
// Establecer conexión con la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'barberia';

$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para obtener el número de clientes atendidos por todos los barberos en un intervalo de tiempo
function obtenerClientesPorBarbero($inicio, $fin) {
    global $conn;

    $sql = "SELECT barberos.idbarber, barberos.username, COUNT(*) as totalClientes FROM reservas
            JOIN barberos ON reservas.idbarber = barberos.idbarber
            WHERE realizada = TRUE AND hora BETWEEN '$inicio' AND '$fin'
            GROUP BY barberos.idbarber";
    $result = $conn->query($sql);

    $clientesPorBarbero = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $clientesPorBarbero[] = array(
                'idbarber' => $row['idbarber'],
                'username' => $row['username'],
                'totalClientes' => $row['totalClientes']
            );
        }
    }

    return $clientesPorBarbero;
}

// Función para obtener el dinero generado por todos los barberos en un intervalo de tiempo
function obtenerDineroPorBarbero($inicio, $fin) {
    global $conn;

    $sql = "SELECT barberos.idbarber, barberos.username, SUM(servicios.precio) as totalDinero FROM reservas
            JOIN barberos ON reservas.idbarber = barberos.idbarber
            JOIN servicios ON reservas.idservicio = servicios.idservicio
            WHERE realizada = TRUE AND hora BETWEEN '$inicio' AND '$fin'
            GROUP BY barberos.idbarber";
    $result = $conn->query($sql);

    $dineroPorBarbero = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dineroPorBarbero[] = array(
                'idbarber' => $row['idbarber'],
                'username' => $row['username'],
                'totalDinero' => $row['totalDinero']
            );
        }
    }

    return $dineroPorBarbero;
}

// Función para obtener el registro de clientes y cuántas veces han ido, ordenado de mayor a menor
function obtenerRegistroClientes($inicio, $fin) {
    global $conn;

    $sql = "SELECT clientes.idcliente, clientes.username, COUNT(*) as totalVisitas FROM reservas
            JOIN clientes ON reservas.idcliente = clientes.idcliente
            WHERE realizada = TRUE AND hora BETWEEN '$inicio' AND '$fin'
            GROUP BY clientes.idcliente
            ORDER BY totalVisitas DESC";
    $result = $conn->query($sql);

    $registroClientes = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $registroClientes[] = array(
                'idcliente' => $row['idcliente'],
                'username' => $row['username'],
                'totalVisitas' => $row['totalVisitas']
            );
        }
    }

    return $registroClientes;
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $funcionSeleccionada = $_POST['funcion'];
    $intervaloSeleccionado = $_POST['intervalo'];

    // Obtener la fecha actual
    $fechaActual = date('Y-m-d');

    switch ($intervaloSeleccionado) {
        case 'diario':
            $inicio = $fechaActual . ' 00:00:00';
            $fin = $fechaActual . ' 23:59:59';
            break;
        case 'semanal':
            $inicio = date('Y-m-d', strtotime('-1 week', strtotime($fechaActual))) . ' 00:00:00';
            $fin = $fechaActual . ' 23:59:59';
            break;
        case 'mensual':
            $inicio = date('Y-m-01', strtotime($fechaActual)) . ' 00:00:00';
            $fin = date('Y-m-t', strtotime($fechaActual)) . ' 23:59:59';
            break;
        case '3meses':
            $inicio = date('Y-m-d', strtotime('-3 months', strtotime($fechaActual))) . ' 00:00:00';
            $fin = $fechaActual . ' 23:59:59';
            break;
        case '6meses':
            $inicio = date('Y-m-d', strtotime('-6 months', strtotime($fechaActual))) . ' 00:00:00';
            $fin = $fechaActual . ' 23:59:59';
            break;
        case 'anual':
            $inicio = date('Y-01-01', strtotime($fechaActual)) . ' 00:00:00';
            $fin = date('Y-12-31', strtotime($fechaActual)) . ' 23:59:59';
            break;
        default:
            echo "Intervalo no válido.";
            exit();
    }

    switch ($funcionSeleccionada) {
        case 'clientesPorBarbero':
            $resultado = obtenerClientesPorBarbero($inicio, $fin);
            echo "<h2>Clientes por Barbero</h2>";
            echo "<table border='1'>
                    <tr>
                        <th>Barbero</th>
                        <th>Clientes Atendidos</th>
                    </tr>";
            foreach ($resultado as $barbero) {
                echo "<tr>
                        <td>" . $barbero['username'] . "</td>
                        <td>" . $barbero['totalClientes'] . "</td>
                      </tr>";
            }
            echo "</table>";
            break;

        case 'dineroGenerado':
            $resultado = obtenerDineroPorBarbero($inicio, $fin);
            echo "<h2>Dinero Generado por Barbero</h2>";
            echo "<table border='1'>
                    <tr>
                        <th>Barbero</th>
                        <th>Dinero Generado</th>
                    </tr>";
            foreach ($resultado as $barbero) {
                echo "<tr>
                        <td>" . $barbero['username'] . "</td>
                        <td>" . $barbero['totalDinero'] . "</td>
                      </tr>";
            }
            echo "</table>";
            break;

        case 'registroClientes':
            $resultado = obtenerRegistroClientes($inicio, $fin);
            echo "<h2>Registro de Clientes</h2>";
            echo "<table border='1'>
                    <tr>
                        <th>Cliente</th>
                        <th>Total Visitas</th>
                    </tr>";
            foreach ($resultado as $cliente) {
                echo "<tr>
                        <td>" . $cliente['username'] . "</td>
                        <td>" . $cliente['totalVisitas'] . "</td>
                      </tr>";
            }
            echo "</table>";
            break;

        default:
            echo "Función no válida.";
            break;
    }
}

// Cerrar la conexión
$conn->close();
?>

<!-- Formulario HTML -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="funcion">Seleccione una función:</label>
    <select name="funcion" id="funcion">
        <option value="clientesPorBarbero">Clientes por Barbero</option>
        <option value="dineroGenerado">Dinero Generado por Barbero</option>
        <option value="registroClientes">Registro de Clientes</option>
    </select>
    <br>

    <label for="intervalo">Seleccione un intervalo de tiempo:</label>
    <select name="intervalo" id="intervalo">
        <option value="diario">Diario</option>
        <option value="semanal">Semanal</option>
        <option value="mensual">Mensual</option>
        <option value="3meses">3 Meses</option>
        <option value="6meses">6 Meses</option>
        <option value="anual">Anual</option>
    </select>
    <br>

    <input type="submit" value="Consultar">
</form>

</body>
</html>
