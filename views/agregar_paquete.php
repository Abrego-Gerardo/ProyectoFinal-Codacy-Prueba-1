<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}

// Generar nonce CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje = ""; // Variable para almacenar mensajes

// Procesar la creación del nuevo paquete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_STRING);
    if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
        die("Solicitud inválida (CSRF detectado).");
    }

    // Validar entradas
    $nombre        = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $tipo_destino  = filter_input(INPUT_POST, 'tipo_destino', FILTER_SANITIZE_STRING);
    $precio_nino   = filter_input(INPUT_POST, 'precio_nino', FILTER_VALIDATE_FLOAT);
    $precio_adulto = filter_input(INPUT_POST, 'precio_adulto', FILTER_VALIDATE_FLOAT);
    $precio_mayor  = filter_input(INPUT_POST, 'precio_mayor', FILTER_VALIDATE_FLOAT);
    $detalles      = filter_input(INPUT_POST, 'detalles', FILTER_SANITIZE_STRING) ?? '';

    if ($nombre && $tipo_destino && $precio_nino !== false && $precio_adulto !== false && $precio_mayor !== false) {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto = basename($_FILES['foto']['name']);
            $target_dir = "../uploads/";
            $target_file = $target_dir . $foto;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Verificar si el archivo es una imagen
            $check = getimagesize($_FILES['foto']['tmp_name']);
            if ($check !== false) {
                // Permitir solo ciertos formatos
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageFileType, $allowed_types, true)) {
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                        // Consulta segura con prepared statement
                        $stmt = $conn->prepare("INSERT INTO destinos (city, tipo_destino, precio_nino, precio_adulto, precio_mayor, detalles, foto) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssddsss", $nombre, $tipo_destino, $precio_nino, $precio_adulto, $precio_mayor, $detalles, $target_file);

                        if ($stmt->execute()) {
                            $mensaje = "Paquete creado correctamente.";
                        } else {
                            $mensaje = "Error al crear el paquete: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8');
                        }
                        $stmt->close();
                    } else {
                        $mensaje = "Error al subir la imagen.";
                    }
                } else {
                    $mensaje = "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
                }
            } else {
                $mensaje = "El archivo no es una imagen.";
            }
        } else {
            $mensaje = "No se subió ninguna imagen válida.";
        }
    } else {
        $mensaje = "Datos inválidos en el formulario.";
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
        <h1>Agregar Detalles del Paquete</h1>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="agregar_paquete.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

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

