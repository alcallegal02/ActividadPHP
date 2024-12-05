<?php
session_start();  // Al principio de todo

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    // Redirige al login si no está autenticado
    header('Location: ../templates/forms/login.php');
    exit();  // No debe ejecutar más código después de la redirección
}

// Incluir la configuración de la base de datos
include('../../controller/db/config.php');

// Lógica de eliminación del producto
if (isset($_GET['delete_id'])) {
    $product_id = $_GET['delete_id'];
    $query = $connection->prepare("DELETE FROM products WHERE id = :id");
    $query->bindParam(':id', $product_id, PDO::PARAM_INT);
    
    if ($query->execute()) {
        header('Location: product_list.php'); // Redirige después de eliminar
        exit();
    } else {
        echo "Error al eliminar el producto.";
    }
}

// Obtener la lista de productos
$query = $connection->prepare("SELECT * FROM products ORDER BY created_at DESC");
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
</head>
<body>
    <h1>Lista de Productos</h1>

    <!-- Formulario para cerrar sesión -->
    <form method="POST" action="../../controller/forms/logout.php">
        <button type="submit">Cerrar sesión</button>
    </form>

    <!-- Enlace para crear un nuevo producto -->
    <a href="create_product.php">Crear Nuevo Producto</a>

    <!-- Tabla de productos -->
    <table border="1">
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
                            <!-- Enlace de edición -->
                            <a href="edit_product.php?id=<?php echo htmlspecialchars($product['id']); ?>">Editar</a> | 
                            <!-- Enlace de eliminación con confirmación -->
                            <a href="product_list.php?delete_id=<?php echo htmlspecialchars($product['id']); ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">Eliminar</a>
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
