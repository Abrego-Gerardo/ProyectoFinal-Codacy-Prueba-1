<?php
// Iniciar sesión y conexión a la base de datos
session_start();
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}

// Generar nonce CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener los filtros enviados desde el formulario de manera segura
$tipo_destino = filter_input(INPUT_POST, 'destino', FILTER_SANITIZE_STRING);
$precio_max   = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_INT);

$result = false;
if ($tipo_destino && $precio_max !== false) {
    // Filtrar destinos según los criterios
    $sql = "SELECT * FROM destinos WHERE tipo_destino = ? AND 
            precio_nino <= ? AND 
            precio_adulto <= ? AND 
            precio_mayor <= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $tipo_destino, $precio_max, $precio_max, $precio_max);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}

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
        <h1>Paquetes Disponibles</h1>
        <div class="destinos-container">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id           = (int)$row['id'];
                    $foto         = htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8');
                    $city         = htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8');
                    $precio_nino  = htmlspecialchars($row['precio_nino'], ENT_QUOTES, 'UTF-8');
                    $precio_adulto= htmlspecialchars($row['precio_adulto'], ENT_QUOTES, 'UTF-8');
                    $precio_mayor = htmlspecialchars($row['precio_mayor'], ENT_QUOTES, 'UTF-8');

                    print "<form action='detalles_viaje.php' method='get'>";
                    print "<input type='hidden' name='id' value='{$id}'>";
                    print "<input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . "'>";
                    print "<button type='submit' class='destino' style='background-image: url({$foto});'>";
                    print "<h3>{$city}</h3>";
                    print "<p>Precios: Niño \${$precio_nino}, Adulto \${$precio_adulto}, Mayor \${$precio_mayor}</p>";
                    print "</button>";
                    print "</form>";
                }
            } else {
                print "<p>No se encontraron paquetes con los filtros seleccionados.</p>";
            }
            ?>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>


