<?php
session_start([
    'cookie_lifetime' => 86400,
    'cookie_httponly' => true, 
    'cookie_secure' => isset($_SERVER['HTTPS']), 
    'cookie_samesite' => 'Strict',
    'name' => 'PHPSESSID',
    'use_strict_mode' => true
]);

// Regenerar sesión periódicamente
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} elseif (time() - $_SESSION['CREATED'] > 1800) { // Regenerar cada 30 minutos
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

// Comprobar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

// Expirar sesión si está inactiva más de 30 minutos
$session_lifetime = 1800;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_lifetime)) {
    session_unset();
    session_destroy();
    header('Location: /controller/auth/logout.php?timeout=true');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Generar y asociar CSRF token a la sesión si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Función para validar CSRF
function validar_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Bloquear accesos sin CSRF token válido en POST y acciones sensibles en GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validar_csrf($_POST['csrf_token'])) {
        session_unset();
        session_destroy();
        header('Location: /controller/auth/logout.php');
        exit();
    }
}

if (isset($_GET['delete_id'])) {
    if (!isset($_GET['csrf_token']) || !validar_csrf($_GET['csrf_token'])) {
        session_unset();
        session_destroy();
        header('Location: /controller/auth/logout.php');
        exit();
    }
}
?>
