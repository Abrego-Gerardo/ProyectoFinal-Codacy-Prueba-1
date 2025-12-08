<?php
session_start();

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Función segura para redireccionar
function safe_redirect(string $url): void {
    $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    if (!headers_sent()) {
        header("Location: $url");
        exit();
    } else {
        echo "<script>window.location.href='$url';</script>";
        exit();
    }
}

// Redirigir si ya está autenticado
if (isset($_SESSION['user'])) {
    safe_redirect("../index.php");
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
            // Validar token CSRF
            $csrf_token = filter_input(INPUT_POST, "csrf_token", FILTER_SANITIZE_STRING);
            if ($csrf_token === false || $csrf_token !== $_SESSION['csrf_token']) {
                echo "<div>Solicitud inválida (CSRF detectado)</div>";
            } else {
                // Validar entradas
                $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
                $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);

                if ($email !== false && $password !== false) {
                    require_once __DIR__ . "/../database.php";

                    // Consulta segura con prepared statement
                    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    $stmt->close();

                    if ($user) {
                        if (password_verify($password, $user["password"]) === true) {
                            $_SESSION["user"] = $user["username"];
                            $_SESSION["usertype"] = $user["usertype"];

                            if ($user["usertype"] === "usuario") {
                                safe_redirect("../index.php");
                            } else {
                                safe_redirect("../views/administracion.php");
                            }
                        } else {
                            echo "<div>El correo/contraseña fue incorrecto</div>";
                        }
                    } else {
                        echo "<div>No existe una cuenta asociada a ese correo</div>";
                    }
                } else {
                    echo "<div>Entrada inválida</div>";
                }
            }
        }
        ?>
        <form action="../views/login_form.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
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




