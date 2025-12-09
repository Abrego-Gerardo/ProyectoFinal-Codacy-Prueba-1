<?php
// Conexión a la base de datos
$conn = @new mysqli("localhost", "root", "", "agencia_db");

if ($conn->connect_error) {
    $safeError = htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8');
    die("Conexión fallida: " . $safeError);
}

$sql_nacionales = "SELECT * FROM destinos WHERE tipo_destino='Nacional'";
$result_nacionales = $conn->query($sql_nacionales);

$sql_internacionales = "SELECT * FROM destinos WHERE tipo_destino='Internacional'";
$result_internacionales = $conn->query($sql_internacionales);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars("Inicio - Agencia de Viajes", ENT_QUOTES, 'UTF-8'); ?></title>
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
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
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
            background-color: rgba(0,0,0,0.6);
            width: 100%;
            text-align: center;
            font-size: 1em;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="left"><?php echo htmlspecialchars("Inicio", ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="right">
            <a href="views/login_form.php" style="color: white;"><?php echo htmlspecialchars("Iniciar Sesión", ENT_QUOTES, 'UTF-8'); ?></a>
        </div>
    </div>

    <div class="nav">
        <a href="index.php"><?php echo htmlspecialchars("Inicio", ENT_QUOTES, 'UTF-8'); ?></a>
        <a href="views/catalogo_viajes.php"><?php echo htmlspecialchars("Catálogo de Viajes", ENT_QUOTES, 'UTF-8'); ?></a>
        <a href="views/detalles_reservas.php"><?php echo htmlspecialchars("Reservas", ENT_QUOTES, 'UTF-8'); ?></a>
        <a href="views/administracion.php"><?php echo htmlspecialchars("Administración", ENT_QUOTES, 'UTF-8'); ?></a>
        <a href="views/contacto.php"><?php echo htmlspecialchars("Soporte y Contacto", ENT_QUOTES, 'UTF-8'); ?></a>
    </div>

    <div class="main-content">
        <h1><?php echo htmlspecialchars("Bienvenido a la Agencia de Viajes", ENT_QUOTES, 'UTF-8'); ?></h1>

        <div class="destinos">
            <h2><?php echo htmlspecialchars("Destinos Nacionales", ENT_QUOTES, 'UTF-8'); ?></h2>

            <div class="destinos-container">
                <?php if ($result_nacionales && $result_nacionales->num_rows > 0): ?>
                    <?php while ($row = $result_nacionales->fetch_assoc()): 
                        $id    = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                        $city  = htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8');
                        $foto  = htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8');
                    ?>
                        <form action="views/detalles_viaje.php" method="get">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <button type="submit" class="destino">
                                <img src="<?php echo $foto; ?>" alt="<?php echo $city; ?>">
                                <h3><?php echo $city; ?></h3>
                            </button>
                        </form>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p><?php echo htmlspecialchars("No hay destinos nacionales disponibles.", ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>

            <h2><?php echo htmlspecialchars("Destinos Internacionales", ENT_QUOTES, 'UTF-8'); ?></h2>

            <div class="destinos-container">
                <?php if ($result_internacionales && $result_internacionales->num_rows > 0): ?>
                    <?php while ($row = $result_internacionales->fetch_assoc()): 
                        $id    = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                        $city  = htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8');
                        $foto  = htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8');
                    ?>
                        <form action="views/detalles_viaje.php" method="get">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <button type="submit" class="destino">
                                <img src="<?php echo $foto; ?>" alt="<?php echo $city; ?>">
                                <h3><?php echo $city; ?></h3>
                            </button>
                        </form>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p><?php echo htmlspecialchars("No hay destinos internacionales disponibles.", ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><?php echo htmlspecialchars("© 2024 Agencia de Viajes. Todos los derechos reservados.", ENT_QUOTES, 'UTF-8'); ?></p>
    </div>

</body>
</html>

<?php $conn->close(); ?>
