<?php
session_start();
session_unset();   // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión
header('Location: ../../templates/forms/login.php'); // Redirige al login
exit();
?>
