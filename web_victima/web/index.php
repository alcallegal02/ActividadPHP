<?php
include('controller/db/config.php');

// Comprobar si el usuario ya está autenticado
session_start();

if (isset($_SESSION['user_id'])) {
    // Si ya hay una sesión activa, redirigir al usuario a la página de productos
    header('Location: templates/product/product_list.php');
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para obtener el usuario
    $query = $connection->prepare("SELECT * FROM users WHERE USERNAME=:username");
    $query->bindParam("username", $username, PDO::PARAM_STR);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);

    // Verificar si las credenciales son correctas
    if (!$result || !password_verify($password, $result['password_hash'])) {
        echo '<p class="error">¡Credenciales incorrectas!, Inténtalo de nuevo...</p>';
    } else {
        // Crear sesión solo si el usuario es válido
        $_SESSION['user_id'] = $result['id'];  // Guarda el ID del usuario
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Redirigir al usuario a la página de lista de productos
        header('Location: templates/product/product_list.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="templates/auth/styles/styleform.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="POST" action="" name="signin-form">
            <div class="form-element">
                <label>Username</label>
                <input type="text" name="username" pattern="[a-zA-Z0-9]+" required />
            </div>
            <div class="form-element">
                <label>Password</label>
                <input type="password" name="password" required />
            </div>
            <button type="submit" name="login" value="login">Log In</button>
        </form>
        <p>¿No tienes cuenta? <a href="templates/auth/register.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
