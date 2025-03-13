<?php
include('../../controller/db/config.php');
include('../../controller/auth/session_check.php');

$user_id = $_SESSION['user_id'];

$message = ""; // Variable para almacenar mensajes

// Obtener los datos del usuario (función para reutilizar después)
function obtenerUsuario($connection, $user_id) {
    $query = $connection->prepare("SELECT username, email, password_hash FROM users WHERE id = :id");
    $query->bindParam(":id", $user_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

$user = obtenerUsuario($connection, $user_id);

if (!$user) {
    $message = "<p class='error'>Error al cargar los datos del usuario.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];

    // Actualizar los datos del usuario
    $updateQuery = $connection->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
    $updateQuery->bindParam(":username", $new_username, PDO::PARAM_STR);
    $updateQuery->bindParam(":email", $new_email, PDO::PARAM_STR);
    $updateQuery->bindParam(":id", $user_id, PDO::PARAM_INT);

    if ($updateQuery->execute()) {
        $_SESSION['username'] = $new_username; // Actualizar la sesión
        $message .= "<p class='success'>Datos actualizados correctamente.</p>";

        // Volver a obtener los datos actualizados del usuario
        $user = obtenerUsuario($connection, $user_id);
    } else {
        $message .= "<p class='error'>Error al actualizar los datos.</p>";
    }

    // Cambio de contraseña
    if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (password_verify($current_password, $user['password_hash'])) {
            if ($new_password === $confirm_password) {
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
                $updatePasswordQuery = $connection->prepare("UPDATE users SET password_hash = :password WHERE id = :id");
                $updatePasswordQuery->bindParam(":password", $new_password_hash, PDO::PARAM_STR);
                $updatePasswordQuery->bindParam(":id", $user_id, PDO::PARAM_INT);

                if ($updatePasswordQuery->execute()) {
                    $message .= "<p class='success'>Contraseña actualizada correctamente.</p>";
                } else {
                    $message .= "<p class='error'>Error al actualizar la contraseña.</p>";
                }
            } else {
                $message .= "<p class='error'>Las nuevas contraseñas no coinciden.</p>";
            }
        } else {
            $message .= "<p class='error'>La contraseña actual es incorrecta.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="styles/styleform.css">
</head>
<body>

    <div class="container">
        <h1>Editar Perfil</h1>

        <form method="POST" action="">
            <div class="form-element">
                <label for="username">Nombre de usuario:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-element">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <h3>Cambiar Contraseña</h3>

            <div class="form-element">
                <label for="current_password">Contraseña Actual:</label>
                <input type="password" name="current_password">
            </div>

            <div class="form-element">
                <label for="new_password">Nueva Contraseña:</label>
                <input type="password" name="new_password">
            </div>

            <div class="form-element">
                <label for="confirm_password">Confirmar Nueva Contraseña:</label>
                <input type="password" name="confirm_password">
            </div>

            <button type="submit">Actualizar</button>
        </form>

        <p><a href="../product/product_list.php">Volver</a></p>

        <div class="message">
            <?php echo $message; ?>
        </div>
    </div>

</body>
</html>
