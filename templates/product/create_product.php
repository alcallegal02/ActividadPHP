<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../templates/forms/login.php');
    exit();
}

include('../../controller/db/config.php');

// Verifica si se ha enviado el formulario
if (isset($_POST['create'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Insertar el nuevo producto
    $query = $connection->prepare("INSERT INTO products (name, description, price, stock) VALUES (:name, :description, :price, :stock)");
    $query->bindParam(':name', $name);
    $query->bindParam(':description', $description);
    $query->bindParam(':price', $price);
    $query->bindParam(':stock', $stock);

    if ($query->execute()) {
        header('Location: product_list.php'); // Redirige a la lista de productos
        exit();
    } else {
        echo "Error al crear el producto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
    <link rel="stylesheet" href="styles/styleproduct.css">
</head>
<body>
    <h1>Crear Producto</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="name" required />
        </div>
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="description" required></textarea>
        </div>
        <div class="form-group">
            <label>Precio</label>
            <input type="number" name="price" required />
        </div>
        <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" required />
        </div>
        <button type="submit" name="create" class="btn">Crear Producto</button>
    </form>
    <a href="product_list.php" class="btn">Volver a la lista de productos</a>
</body>
</html>
