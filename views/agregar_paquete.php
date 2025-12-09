<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = ""; // Variable para almacenar mensajes

// Procesar la creación del nuevo paquete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $tipo_destino = $_POST['tipo_destino'];
    $precio_nino = $_POST['precio_nino'];
    $precio_adulto = $_POST['precio_adulto'];
    $precio_mayor = $_POST['precio_mayor'];
    $detalles = $_POST['detalles'];
    $foto = $_FILES['foto']['name'];

    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($foto);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo es una imagen
    $check = getimagesize($_FILES['foto']['tmp_name']);
    if ($check !== false) {

        // Permitir solo ciertos formatos
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($imageFileType, $allowed_types)) {

            // Verificar directorio
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {

                $sql = "INSERT INTO destinos (city, tipo_destino, precio_nino, precio_adulto, precio_mayor, detalles, foto) 
                        VALUES ('$nombre', '$tipo_destino', '$precio_nino', '$precio_adulto', '$precio_mayor', '$detalles', '$target_file')";

                if ($conn->query($sql) === TRUE) {
                    $mensaje = "Paquete creado correctamente.";
                } else {
                    $mensaje = "Error al crear el paquete: " . $conn->error;
                }

            } else {
                $mensaje = "Error al subir la imagen.";
            }

        } else {
            $mensaje = "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
        }

    } else {
        $mensaje = "El archivo no es una imagen.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Paquete - Agencia de Viajes</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">

    <style>
        .alert {
            background: #ffd966;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
            border: 1px solid #d4a017;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">Agregar Paquete</div>
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
        <h1>Agregar Detalles del Paquete</h1>

        <!-- Mostrar mensaje sin usar echo en el proceso PHP -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert">
                <?= htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form action="agregar_paquete.php" method="post" enctype="multipart/form-data">
            <label for="nombre">Nombre del Paquete:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre del Paquete" required>

            <label for="tipo_destino">Tipo de Destino:</label>
            <select id="tipo_destino" name="tipo_destino" required>
                <option value="Nacional">Nacional</option>
                <option value="Internacional">Internacional</option>
            </select>

            <label for="precio_nino">Precio Niño:</label>
            <input type="number" id="precio_nino" name="precio_nino" placeholder="Precio Niño" required>

            <label for="precio_adulto">Precio Adulto:</label>
            <input type="number" id="precio_adulto" name="precio_adulto" placeholder="Precio Adulto" required>

            <label for="precio_mayor">Precio Mayor:</label>
            <input type="number" id="precio_mayor" name="precio_mayor" placeholder="Precio Mayor" required>

            <label for="detalles">Detalles:</label>
            <textarea id="detalles" name="detalles" placeholder="Detalles del Paquete" required></textarea>

            <label for="foto">Imagen del Paquete:</label>
            <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.gif" required>

            <button type="submit">Crear Paquete</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>
