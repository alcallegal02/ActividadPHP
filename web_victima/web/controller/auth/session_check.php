<?php
// Iniciar la sesión con configuraciones específicas
session_start([ 
    'cookie_lifetime' => 86400, // Duración de la cookie de sesión (1 día)
    'cookie_httponly' => false, // Permite acceso a la cookie desde JavaScript
    'cookie_secure' => false, // No requiere HTTPS para la cookie
    'cookie_samesite' => 'Lax', // Restringe el envío de cookies en solicitudes entre sitios
    'name' => 'PHPSESSID', // Nombre explícito de la cookie de sesión
    'use_strict_mode' => true // Genera siempre una nueva ID de sesión para mayor seguridad
]);

// Desactivar el caché del navegador para evitar problemas con datos obsoletos
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    // Redirige al login si no está autenticado
    header('Location: ../../index.php');
    exit(); // Detiene la ejecución del script después de la redirección
}

// Definir el tiempo máximo de inactividad (30 minutos)
$session_lifetime = 1800; // 30 minutos

// Verificar si la sesión ha expirado
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_lifetime)) {
    // Redirigir al archivo de logout.php para manejar la expiración de la sesión
    header('Location: /controller/auth/logout.php?timeout=true');
    exit();
}

// Actualizar el tiempo de actividad de la sesión
$_SESSION['LAST_ACTIVITY'] = time();
?>