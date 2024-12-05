<?php
session_start();

// Función para verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Redirige al usuario al login si no está autenticado
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: ../../templates/forms/login.php');  // Redirige al login si no está autenticado
        exit();
    }
}
?>
