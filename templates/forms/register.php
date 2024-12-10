<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styles/styleform.css">
</head>
<body>
    <div class="container">
        <h1>Regístrate</h1>
        <form method="POST" action="../../controller/forms/registration.php" name="signup-form">
            <div class="form-element">
                <label>Username</label>
                <input type="text" name="username" pattern="[a-zA-Z0-9]+" required />
            </div>
            <div class="form-element">
                <label>Email</label>
                <input type="email" name="email" required />
            </div>
            <div class="form-element">
                <label>Password</label>
                <input type="password" name="password" required />
            </div>
            <button type="submit" name="register" value="register">Register</button>
        </form>
        <p><a href="login.php">Volver al Login</a></p>
    </div>
</body>
</html>
