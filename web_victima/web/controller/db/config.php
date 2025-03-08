<?php
// Obtener las variables de entorno
$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');
$dbName = getenv('DB_NAME');

// Configuración de la base de datos
try {
    $connection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}