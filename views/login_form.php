<?php
    session_start();
    if (isset($_SESSION['user'])) {
        header("Location: ../index.php");
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Gestión de Usuarios</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Gestión de Usuarios</h1>
        <?php 
            if (isset($_POST["login"])) {
                $email = $_POST["email"];
                $password = $_POST["password"];
                require_once "database.php";
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $result = mysqli_query($conn, $sql);
                $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                if ($user) {
                    if (password_verify($password, $user["password"])) {
                        if ($user["usertype"]=="usuario") {
                            session_start();
                            $_SESSION["user"] = $user["username"];
                            $_SESSION["usertype"] = $user["usertype"];
                            header("Location: ../index.php");
                            die(); 
                        }else{
                            session_start();
                            $_SESSION["user"] = $user["username"];
                            $_SESSION["usertype"] = $user["usertype"];
                            header("Location: ../views/administracion.php");
                            die();
                        }
                        
                    }else{
                        echo "<div>El correo/contraseña fue incorrecto</div>";
                    }
                }else{
                    echo "<div>No existe una cuenta asociada a ese correo</div>";
                }
            }
        ?>
        <form action="../views/login_form.php" method="post">
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo electrónico" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <div class="form-group">
                <button type="submit" value="Login" name="login">Iniciar Sesión</button>
            </div>
        </form>
        <a href="register_form.php">Registrarse</a>
        <a href="recover_password_form.php">¿No recuerdas tu contraseña? Recuperar Contraseña</a>
        <a href="../index.php">Volver a Inicio</a>
    </div>
</body>
</html>
