<?php
include('../db/config.php');

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Verificar si el email ya está registrado
    $query = $connection->prepare("SELECT * FROM users WHERE EMAIL=:email");
    $query->bindParam("email", $email, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        echo '<p class="error">¡La dirección de correo ya existe!</p>';
    } else {
        // Insertar nuevo usuario
        try {
            $query = $connection->prepare("INSERT INTO users(USERNAME, PASSWORD_HASH, EMAIL) VALUES (:username, :password_hash, :email)");
            $query->bindParam("username", $username, PDO::PARAM_STR);
            $query->bindParam("password_hash", $password_hash, PDO::PARAM_STR);
            $query->bindParam("email", $email, PDO::PARAM_STR);
            $result = $query->execute();

            if ($result) {
                // Redirigir al usuario sin iniciar sesión
                header('Location: ../../index.php');
                exit(); // Asegúrate de que el script se detenga después de la redirección
            } else {
                echo '<p class="error">¡Algo fue mal al registrar el usuario!</p>';
            }
        } catch (PDOException $e) {
            echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
        }
    }
}
?>