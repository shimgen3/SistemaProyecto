<?php
// Iniciamos la sesión
session_start();

// Si el usuario no ha iniciado sesión, redirigimos a la página de inicio de sesión
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

// Configuración de la conexión a la base de datos
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'barberia';

// Conexión a la base de datos
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Consulta para obtener la información del barbero
$stmt = $con->prepare('SELECT * FROM barberos WHERE idbarber = ?');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($idbarber, $username, $email, $rut, $password);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Perfil del Barbero</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Nombre del Sitio Web</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Perfil</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Cerrar Sesión</a>
				<a href="horadisp.php"><i class="fas fa-clock"></i>Horarios Disponibles</a>
			</div>
		</nav>
		<div class="content">
			<h2>Página de Perfil del Barbero</h2>
			<div>
				<p>Los detalles de tu cuenta son los siguientes:</p>
				<table>
					<tr>
						<td>ID del Barbero:</td>
						<td><?=$idbarber?></td>
					</tr>
					<tr>
						<td>Username:</td>
						<td><?=$username?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
					<tr>
						<td>RUT:</td>
						<td><?=$rut?></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><?=$password?></td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
