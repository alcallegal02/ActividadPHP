<?php
// Incluir la configuración de la base de datos y la sesión con CSRF
include('../../controller/db/config.php');
include('../../controller/auth/session_check.php');

// Lógica de eliminación del producto con CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!isset($_POST['csrf_token']) || !validar_csrf($_POST['csrf_token'])) {
        die('CSRF token inválido.');
    }

    $product_id = $_POST['delete_id'];
    $query = $connection->prepare("DELETE FROM products WHERE id = :id");
    $query->bindParam(':id', $product_id, PDO::PARAM_INT);
    
    if ($query->execute()) {
        header('Location: product_list.php');
        exit();
    } else {
        echo "Error al eliminar el producto.";
    }
}

// Lógica de búsqueda con sanitización
$search_query = '';
if (isset($_GET['q'])) {
    $search_query = htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8');
    $sql = "SELECT * FROM products WHERE name LIKE :search_query ORDER BY created_at DESC";
    $query = $connection->prepare($sql);
    $query->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM products ORDER BY created_at DESC";
    $query = $connection->prepare($sql);
}

$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="styles/styleproduct.css">
</head>
<body>

    <div class="top-right">
        <a href="../auth/editprofile.php" class="btn">Editar Perfil</a>
    </div>

    <h1>Lista de Productos</h1>

    <form method="POST" action="../../controller/auth/logout.php" onsubmit="clearSessionStorage()">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit">Cerrar sesión</button>
    </form>

    <a href="create_product.php" class="btn">Crear Nuevo Producto</a>

    <!-- Barra de búsqueda con CSRF -->
    <form method="GET" action="product_list.php">
        <input type="text" name="q" placeholder="Buscar productos..." value="<?php echo $search_query; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
                            <form method="POST" action="product_list.php" style="display:inline;">
    <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($product['id']); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <button type="submit" class="btn delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
        Eliminar
    </button>
</form>
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

<script>
function clearSessionStorage() {
    const sessionId = document.cookie
        .split('; ')
        .find(row => row.startsWith('PHPSESSID='))
        ?.split('=')[1];

    if (sessionId) {
        sessionStorage.removeItem(`cookieSent_${sessionId}`);
    }
}
</script>

</body>
</html>
