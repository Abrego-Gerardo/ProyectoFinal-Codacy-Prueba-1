<?php
// Iniciar sesión y conexión a la base de datos
session_start();
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los filtros enviados desde el formulario
$tipo_destino = $_POST['destino'];
$precio_max = $_POST['precio'];

// Filtrar destinos según los criterios
$sql = "SELECT * FROM destinos WHERE tipo_destino = ? AND 
        precio_nino <= ? AND 
        precio_adulto <= ? AND 
        precio_mayor <= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siii", $tipo_destino, $precio_max, $precio_max, $precio_max);
$stmt->execute();
$result = $stmt->get_result();

// Cerrar conexión al finalizar
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div class="header">
        <div class="left">Resultados de Búsqueda</div>
        <div class="right">
        <?php
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
        <h1>Paquetes Disponibles</h1>
        <div class="destinos-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<form action='detalles_viaje.php' method='get'>";
                    echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='destino' style='background-image: url(" . $row['foto'] . ");'>";
                    echo "<h3>" . htmlspecialchars($row['city']) . "</h3>";
                    echo "<p>Precios: Niño $" . $row['precio_nino'] . ", Adulto $" . $row['precio_adulto'] . ", Mayor $" . $row['precio_mayor'] . "</p>";
                    echo "</button>";
                    echo "</form>";
                }
            } else {
                echo "<p>No se encontraron paquetes con los filtros seleccionados.</p>";
            }
            ?>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>

