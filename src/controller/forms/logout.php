<?php
// Iniciar la sesión
session_start();

// Destruir la sesión y las cookies asociadas
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión actual

// Borrar la cookie PHPSESSID del navegador
setcookie('PHPSESSID', '', [
    'expires' => time() - 3600, // Establece la fecha de expiración en el pasado (1 hora antes)
    'path' => '/', // Ruta donde la cookie es válida (en este caso, todo el dominio)
    'secure' => false, // No requiere HTTPS para enviar la cookie
    'httponly' => false // Permite acceso a la cookie desde JavaScript
]);

// Redirigir al usuario a la página principal
header('Location: ../../index.php'); // Redirección a la página de inicio
exit(); // Detiene la ejecución del script después de la redirección
?>