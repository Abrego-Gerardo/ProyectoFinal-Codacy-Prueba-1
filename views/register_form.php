<?php
session_start();

// Generar nonce CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Gestión de Usuarios</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <h1>Gestión de Usuarios</h1>

        <?php
        if (isset($_POST["submit"])) {
            // Validar token CSRF
            $csrf_token = filter_input(INPUT_POST, "csrf_token", FILTER_SANITIZE_STRING);
            if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
                print "<div>Solicitud inválida (CSRF detectado)</div>";
            } else {
                // Validar entradas
                $usuario        = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                $email          = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
                $password       = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
                $passwordRepeat = filter_input(INPUT_POST, "confirm_password", FILTER_SANITIZE_STRING);

                $errors = [];

                if (empty($usuario) || empty($email) || empty($password) || empty($passwordRepeat)) {
                    $errors[] = "Todos los campos son requeridos";
                }
                if (!$email) {
                    $errors[] = "Email introducido no es válido";
                }
                if (strlen($password) < 8) {
                    $errors[] = "La contraseña debe tener más de 8 caracteres";
                }
                if ($password !== $passwordRepeat) {
                    $errors[] = "Las contraseñas no coinciden";
                }

                require_once __DIR__ . "/../database.php";

                // Consulta segura para verificar si el correo ya existe
                $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $errors[] = "Este correo ya está siendo utilizado";
                }
                $stmt->close();

                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        print "<div>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</div>";
                    }
                } else {
                    // Insertar usuario con prepared statement
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    if ($stmt) {
                        $stmt->bind_param("sss", $usuario, $email, $passwordHash);
                        $stmt->execute();
                        print "<div>Se ha registrado satisfactoriamente</div>";
                        $stmt->close();
                    } else {
                        exit("Error al preparar la consulta.");
                    }
                }
            }
        }
        ?>
        <form action="../views/register_form.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-group">
                <input type="text" name="username" placeholder="Nombre de Usuario" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo Electrónico" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Ingresar Contraseña" required>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required>
            </div>
            <div class="form-btn">
                <button type="submit" value="Register" name="submit">Registrarse</button>
            </div>
        </form>
        <a href="login_form.php">¿Ya tienes cuenta? Iniciar Sesión</a>
        <a href="../index.php">Volver a Inicio</a>
    </div>
</body>
</html>

