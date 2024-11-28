<?php
include('../controller/db/auth.php');
requireAuth(); // Verifica que el usuario esté autenticado
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido</title>
</head>
<body>
    <h1>¡Bienvenido al sistema, usuario ID: <?php echo $_SESSION['user_id']; ?>!</h1>
    <p><a href="product_list.php">Ir al CRUD de productos</a></p>
    <p><a href="../controller/forms/logout.php">Cerrar sesión</a></p>
</body>
</html>
