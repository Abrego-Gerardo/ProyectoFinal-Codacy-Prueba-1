<?php
session_start();

// Generar nonce CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Reserva - Agencia de Viajes</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div class="header">
        <div class="left">Confirmar Reserva</div>
        <div class="right">
        <?php
        if (isset($_SESSION['user']) && is_string($_SESSION['user'])) {
            $usuario = htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8');
            print "Usuario: {$usuario}";
            print '<a href="logout.php">Cerrar sesión</a>';
        } else {
            print '<a href="login_form.php" style="color: white;">Iniciar Sesión</a>';
        }
        ?>
        </div>
    </div>
    <div class="nav">
        <a href="../index.php">Inicio</a>
        <a href="catalogo_viajes.php">Catálogo de Viajes</a>
        <a href="detalles_reservas.php">Reservas</a>
        <a href="administracion.php">Administración</a>
        <a href="contacto.php">Soporte y Contacto</a>
    </div>
    <div class="main-content">
        <h1>Confirmar Reserva</h1>
        <form action="procesar_reserva.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="email">Ingrese su correo para confirmar su reserva:</label>
            <input type="email" id="email" name="email" placeholder="Correo electrónico" required>
            <button type="submit">Confirmar Reserva</button>
        </form>
    </div>
    <div class="footer">
        <p>&copy; 2023 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>




