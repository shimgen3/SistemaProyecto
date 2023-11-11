<!DOCTYPE html>
<html>
<head>
    <title>Generador de Hash de Contraseña</title>
</head>
<body>
    <h1>Generador de Hash de Contraseña</h1>
    <form method="post" action="">
        <label for="password">Ingrese una Contraseña:</label>
        <input type="password" name="password" id="password" required>
        <input type="submit" value="Generar Hash">
    </form>
    <?php
    // Verifica si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtiene la contraseña ingresada por el usuario
        $password = $_POST["password"];
        
        // Crea el hash de la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Muestra el hash generado
        echo "<p>Hash de la Contraseña: $hash</p>";
    }
    ?>
</body>
</html>