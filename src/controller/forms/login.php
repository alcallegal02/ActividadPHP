<?php
include('../db/config.php');
session_start();

// Verifica si ya está logueado
if (isset($_SESSION['user_id'])) {
    header('Location: ../../templates/product/product_list.php');
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $connection->prepare("SELECT * FROM users WHERE USERNAME=:username");
    $query->bindParam("username", $username, PDO::PARAM_STR);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);

    if (!$result || !password_verify($password, $result['password_hash'])) {
        echo '<p class="error">¡Credenciales incorrectas!, Inténtalo de nuevo...</p>';
    } else {
        // Si la autenticación es exitosa, establecemos la sesión
        $_SESSION['user_id'] = $result['id'];  // Asegúrate de que el valor sea el ID del usuario o algo que identifique al usuario
        header('Location: ../../templates/product/product_list.php');
        exit();
    }
}
?>
