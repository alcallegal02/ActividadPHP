<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../templates/forms/login.php');
    exit();
}

include('../../controller/db/config.php');

// Verifica si se pasó un ID de producto
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Buscar el producto en la base de datos
    $query = $connection->prepare("SELECT * FROM products WHERE id = :id");
    $query->bindParam(':id', $product_id, PDO::PARAM_INT);
    $query->execute();
    $product = $query->fetch(PDO::FETCH_ASSOC);

    // Si no existe el producto
    if (!$product) {
        echo "Producto no encontrado.";
        exit();
    }
} else {
    echo "ID de producto no proporcionado.";
    exit();
}

// Actualizar el producto
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Actualizar en la base de datos
    $query = $connection->prepare("UPDATE products SET name = :name, description = :description, price = :price, stock = :stock WHERE id = :id");
    $query->bindParam(':name', $name);
    $query->bindParam(':description', $description);
    $query->bindParam(':price', $price);
    $query->bindParam(':stock', $stock);
    $query->bindParam(':id', $product_id);
    
    if ($query->execute()) {
        header('Location: product_list.php'); // Redirige después de actualizar
        exit();
    } else {
        echo "Error al actualizar el producto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
</head>
<body>
    <h1>Editar Producto</h1>
    <form method="POST" action="">
        <div>
            <label>Nombre</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required />
        </div>
        <div>
            <label>Descripción</label>
            <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div>
            <label>Precio</label>
            <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required />
        </div>
        <div>
            <label>Stock</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required />
        </div>
        <button type="submit" name="update">Actualizar Producto</button>
    </form>
    <a href="product_list.php">Volver a la lista de productos</a>
</body>
</html>
