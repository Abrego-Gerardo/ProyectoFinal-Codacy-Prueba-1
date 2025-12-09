<?php
// Iniciar sesión y conexión a la base de datos
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "agencia_db");
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    echo "<div>Conexión fallida: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</div>";
    exit();
}

// Generar nonce CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener detalles del viaje de manera segura
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$row = null;
if ($id !== false && $id !== null) {
    $stmt = $conn->prepare("SELECT * FROM destinos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Viaje - Agencia de Viajes</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div class="header">
        <div class="left">Detalles del Viaje</div>
        <div class="right">
        <?php
        if (isset($_SESSION['user']) && is_string($_SESSION['user'])) {
            $usuario = htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8');
            echo "Usuario: {$usuario}";
            echo ' <a href="logout.php">Cerrar sesión</a>';
        } else {
            echo '<a href="login_form.php" style="color: white;">Iniciar Sesión</a>';
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
        <h1>Detalles del Viaje</h1>
        <?php if ($row !== null) : ?>
            <div class='detalle-viaje'>
                <?php
                $foto          = htmlspecialchars($row["foto"], ENT_QUOTES, 'UTF-8');
                $city          = htmlspecialchars($row["city"], ENT_QUOTES, 'UTF-8');
                $pais          = htmlspecialchars($row["pais"], ENT_QUOTES, 'UTF-8');
                $tipo_destino  = htmlspecialchars($row["tipo_destino"], ENT_QUOTES, 'UTF-8');
                $precio_nino   = htmlspecialchars($row["precio_nino"], ENT_QUOTES, 'UTF-8');
                $precio_adulto = htmlspecialchars($row["precio_adulto"], ENT_QUOTES, 'UTF-8');
                $precio_mayor  = htmlspecialchars($row["precio_mayor"], ENT_QUOTES, 'UTF-8');
                $detalles      = isset($row["detalles"]) ? nl2br(htmlspecialchars($row["detalles"], ENT_QUOTES, 'UTF-8')) : "No hay detalles disponibles";
                ?>
                <img src="../<?php echo $foto; ?>" alt="<?php echo $city; ?>">
                <h2><?php echo "{$city}, {$pais}"; ?></h2>
                <p>Tipo de Destino: <?php echo $tipo_destino; ?></p>
                <p>Precio Niño: $<?php echo $precio_nino; ?></p>
                <p>Precio Adulto: $<?php echo $precio_adulto; ?></p>
                <p>Precio Mayor: $<?php echo $precio_mayor; ?></p>
                <p>Detalles: <?php echo $detalles; ?></p>
                <form action="procesar_reserva.php" method="post">
                    <input type="hidden" name="id_viaje" value="<?php echo (int)$row['id']; ?>">
                    <?php if (isset($_SESSION['csrf_token'])): ?>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    <button type="submit">Reservar</button>
                </form>
            </div>
        <?php else : ?>
            <p>No se encontraron detalles para este viaje.</p>
        <?php endif; ?>
    </div>
    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>



