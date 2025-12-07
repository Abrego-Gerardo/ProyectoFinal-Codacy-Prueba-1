<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener detalles del viaje
$id = $_GET['id'];
$sql = "SELECT * FROM destinos WHERE id=$id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    $row = null;
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
            session_start();
            if (isset($_SESSION['user'])) {
                echo "Usuario: " . htmlspecialchars($_SESSION['user']);
                echo "<a href='logout.php'>Cerrar sesión</a>";
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
        <h1>Detalles del Viaje</h1>
        <?php if ($row): ?>
            <div class='detalle-viaje'>
                <img src='../<?php echo $row["foto"]; ?>' alt='<?php echo $row["city"]; ?>'>
                <h2><?php echo $row["city"] . ", " . $row["pais"]; ?></h2>
                <p>Tipo de Destino: <?php echo $row["tipo_destino"]; ?></p>
                <p>Precio Niño: $<?php echo $row["precio_nino"]; ?></p>
                <p>Precio Adulto: $<?php echo $row["precio_adulto"]; ?></p>
                <p>Precio Mayor: $<?php echo $row["precio_mayor"]; ?></p>
                <p>Detalles: <?php echo isset($row["detalles"]) ? nl2br(htmlspecialchars($row["detalles"])) : "No hay detalles disponibles"; ?></p>
                <form action="procesar_reserva.php" method="post">
                    <input type="hidden" name="id_viaje" value="<?php echo $row['id']; ?>">
                    <button type="submit">Reservar</button>
                </form>
            </div>
        <?php else: ?>
            <p>No se encontraron detalles para este viaje.</p>
        <?php endif; ?>
    </div>
    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>

