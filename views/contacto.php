<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte y Contacto - Agencia de Viajes</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div class="header">
        <div class="left">Soporte y Contacto</div>
        <div class="right">
        <?php
            session_start();
            if (isset($_SESSION['username'])) {
                echo "Usuario: " . htmlspecialchars($_SESSION['username']);
            } else {
                echo "<a href='login_form.php' style='color: white;'>Iniciar Sesión</a>";
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
        <h1>Soporte y Contacto</h1>
        <form action="enviar_contacto.php" method="post">
            <label for="busqueda">¿En qué te podemos ayudar?</label>
            <input type="text" id="busqueda" name="busqueda" placeholder="Escribe tu consulta aquí" required>
            <button type="submit">Buscar</button>
        </form>
        <h2>Contactos</h2>
        <div class="contenido-blanco">
            <p><strong>Correos Electrónicos:</strong></p>
            <ul>
                <li><a href="mailto:hugoarmandorc2002@gmail.com">hugoarmandorc2002@gmail.com</a></li>
                <li><a href="mailto:delgadokev13@gmail.com">delgadokev@gmail.com</a></li>
                <li><a href="mailto:abrego010702@gmail.com">abrego010702@gmail.com.com</a></li>
            </ul>
            <p><strong>Números de Teléfono:</strong></p>
            <ul>
                <li><a href="tel:+50762914621">+507 6291-4621</a></li>
                <li><a href="tel:+50768128315">+507 6812-8315</a></li>
                <li><a href="tel:+50761317547">+507 6131-7547</a></li>
            </ul>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>
