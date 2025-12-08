<?php
session_start();

// Conexión a la base de datos con manejo seguro de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "agencia_db");
    $conn->set_charset("utf8mb4"); // Charset explícito recomendado
} catch (Exception $e) {
    echo "<div>Error de conexión: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</div>";
    exit();
}

// Consultas para obtener destinos nacionales e internacionales
$sql_nacionales = "SELECT * FROM destinos WHERE tipo_destino = 'Nacional'";
$result_nacionales = $conn->query($sql_nacionales);

$sql_internacionales = "SELECT * FROM destinos WHERE tipo_destino = 'Internacional'";
$result_internacionales = $conn->query($sql_internacionales);

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Agencia de Viajes</title>
    <link rel="stylesheet" href="public/assets/css/style.css">
    <style>
        .destino {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            background-size: cover;
            background-position: center;
            border: none;
            width: 200px;
            height: 200px;
            margin: 10px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            cursor: pointer;
        }
        .destino img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        .destino h3 {
            margin: 0;
            padding: 5px;
            background-color: rgba(0, 0, 0, 0.6);
            width: 100%;
            text-align: center;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">Inicio</div>
        <div class="right">
        <?php
        if (isset($_SESSION['user']) && is_string($_SESSION['user'])) {
            echo "Usuario: " . htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8');
            echo " <a href='views/logout.php'>Cerrar sesión</a>";
        } else {
            echo "<a href='views/login_form.php' style='color: white;'>Iniciar Sesión</a>";
        }
        ?>
        </div>
    </div>
    <div class="nav">
        <a href="index.php">Inicio</a>
        <a href="views/catalogo_viajes.php">Catálogo de Viajes</a>
        <a href="views/detalles_reservas.php">Reservas</a>
        <a href="views/administracion.php">Administración</a>
        <a href="views/contacto.php">Soporte y Contacto</a>
    </div>
    <div class="main-content">
        <h1>Bienvenido a la Agencia de Viajes</h1>
        <div class="destinos">
            <h2>Destinos Nacionales</h2>
            <div class="destinos-container">
                <?php
                if ($result_nacionales !== false && $result_nacionales->num_rows > 0) {
                    while ($row = $result_nacionales->fetch_assoc()) {
                        $id   = (int)$row['id'];
                        $foto = htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8');
                        $city = htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8');

                        echo "<form action='views/detalles_viaje.php' method='get'>";
                        echo "<input type='hidden' name='id' value='{$id}'>";
                        echo "<button type='submit' class='destino'>";
                        echo "<img src='{$foto}' alt='{$city}'>";
                        echo "<h3>{$city}</h3>";
                        echo "</button>";
                        echo "</form>";
                    }
                } else {
                    echo "<div>No hay destinos nacionales disponibles.</div>";
                }
                ?>
            </div>

            <h2>Destinos Internacionales</h2>
            <div class="destinos-container">
                <?php
                if ($result_internacionales !== false && $result_internacionales->num_rows > 0) {
                    while ($row = $result_internacionales->fetch_assoc()) {
                        $id   = (int)$row['id'];
                        $foto = htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8');
                        $city = htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8');

                        echo "<form action='views/detalles_viaje.php' method='get'>";
                        echo "<input type='hidden' name='id' value='{$id}'>";
                        echo "<button type='submit' class='destino'>";
                        echo "<img src='{$foto}' alt='{$city}'>";
                        echo "<h3>{$city}</h3>";
                        echo "</button>";
                        echo "</form>";
                    }
                } else {
                    echo "<div>No hay destinos internacionales disponibles.</div>";
                }
                ?>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>

