<?php
// Start session at the beginning
session_start();

// Conexión a la base de datos
// Allow injection of $conn for testing purposes
if (!isset($conn)) {
    $conn = new mysqli("localhost", "root", "", "agencia_db");
}
if ($conn->connect_error) {
    die("Conexión fallida: " . htmlspecialchars($conn->connect_error));
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error: Token CSRF inválido");
    }

    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        // Procesar Edición - Validate and sanitize inputs
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if ($id === false) {
            die("Error: ID inválido");
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $tipo_destino = trim($_POST['tipo_destino'] ?? '');
        $precio_nino = filter_var($_POST['precio_nino'], FILTER_VALIDATE_FLOAT);
        $precio_adulto = filter_var($_POST['precio_adulto'], FILTER_VALIDATE_FLOAT);
        $precio_mayor = filter_var($_POST['precio_mayor'], FILTER_VALIDATE_FLOAT);
        $detalles = trim($_POST['detalles'] ?? '');

        // Validate required fields
        if (empty($nombre) || empty($tipo_destino) || $precio_nino === false || $precio_adulto === false || $precio_mayor === false) {
            die("Error: Campos requeridos inválidos");
        }

        // Validate tipo_destino is one of allowed values
        if (!in_array($tipo_destino, ['Nacional', 'Internacional'], true)) {
            die("Error: Tipo de destino inválido");
        }

        // Use prepared statement to prevent SQL injection
        $sql = "UPDATE destinos SET city=?, tipo_destino=?, precio_nino=?, precio_adulto=?, precio_mayor=?, detalles=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ssdddsi", $nombre, $tipo_destino, $precio_nino, $precio_adulto, $precio_mayor, $detalles, $id);
        if (!$stmt->execute()) {
            die("Error al actualizar: " . htmlspecialchars($stmt->error));
        }
        $stmt->close();

    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        // Procesar Eliminación - Validate input
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if ($id === false) {
            die("Error: ID inválido");
        }

        // Use prepared statement to prevent SQL injection
        $sql = "DELETE FROM destinos WHERE id=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            die("Error al eliminar: " . htmlspecialchars($stmt->error));
        }
        $stmt->close();
    }
}

// Consulta para obtener todos los destinos
$sql = "SELECT * FROM destinos";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Paquetes - Agencia de Viajes</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div class="header">
        <div class="left">Administración de Paquetes</div>
        <div class="right">
            <?php
            // Check user authentication and authorization
            if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
                header("Location: login_form.php");
                exit();
            }
            if (isset($_SESSION['user'])) {
                echo "Usuario: " . htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8');
                echo " <a href='logout.php'>Cerrar sesión</a>";
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
        <h1>Administración de Paquetes</h1>
        
        <!-- Botón para Redirigir a la Página de Crear Paquete -->
        <div class="contenido-blanco">
            <h2>Crear Paquete</h2>
            <a href="agregar_paquete.php"><button type="button">Crear Paquete</button></a>
        </div>

        <!-- Formulario para Modificar Paquetes -->
        <div class="contenido-blanco">
            <h2>Modificar Paquetes</h2>
            <div class="paquetes">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Sanitize all output to prevent XSS
                        $id_safe = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                        $city_safe = htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8');
                        $tipo_destino_safe = htmlspecialchars($row['tipo_destino'], ENT_QUOTES, 'UTF-8');
                        $precio_nino_safe = htmlspecialchars($row['precio_nino'], ENT_QUOTES, 'UTF-8');
                        $precio_adulto_safe = htmlspecialchars($row['precio_adulto'], ENT_QUOTES, 'UTF-8');
                        $precio_mayor_safe = htmlspecialchars($row['precio_mayor'], ENT_QUOTES, 'UTF-8');
                        $detalles_safe = htmlspecialchars($row['detalles'] ?? '', ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class='paquete'>
                            <form action='administracion.php' method='post'>
                                <input type='hidden' name='csrf_token' value='<?php echo $_SESSION['csrf_token']; ?>'>
                                <input type='hidden' name='id' value='<?php echo $id_safe; ?>'>
                                <input type='hidden' name='action' value='edit'>
                                <label for='nombre_<?php echo $id_safe; ?>'>Nombre:</label>
                                <input type='text' id='nombre_<?php echo $id_safe; ?>' name='nombre' value='<?php echo $city_safe; ?>' required>
                                <label for='tipo_destino_<?php echo $id_safe; ?>'>Tipo de Destino:</label>
                                <select id='tipo_destino_<?php echo $id_safe; ?>' name='tipo_destino' required>
                                    <option value='Nacional' <?php echo $tipo_destino_safe === 'Nacional' ? 'selected' : ''; ?>>Nacional</option>
                                    <option value='Internacional' <?php echo $tipo_destino_safe === 'Internacional' ? 'selected' : ''; ?>>Internacional</option>
                                </select>
                                <label for='precio_nino_<?php echo $id_safe; ?>'>Precio Niño:</label>
                                <input type='number' id='precio_nino_<?php echo $id_safe; ?>' name='precio_nino' value='<?php echo $precio_nino_safe; ?>' step='0.01' required>
                                <label for='precio_adulto_<?php echo $id_safe; ?>'>Precio Adulto:</label>
                                <input type='number' id='precio_adulto_<?php echo $id_safe; ?>' name='precio_adulto' value='<?php echo $precio_adulto_safe; ?>' step='0.01' required>
                                <label for='precio_mayor_<?php echo $id_safe; ?>'>Precio Mayor:</label>
                                <input type='number' id='precio_mayor_<?php echo $id_safe; ?>' name='precio_mayor' value='<?php echo $precio_mayor_safe; ?>' step='0.01' required>
                                <label for='detalles_<?php echo $id_safe; ?>'>Detalles:</label>
                                <textarea id='detalles_<?php echo $id_safe; ?>' name='detalles' required><?php echo $detalles_safe; ?></textarea>
                                <button type='submit'>Guardar Cambios</button>
                            </form>
                            <form action='administracion.php' method='post' style='display:inline;' onsubmit='return confirm("¿Está seguro de que desea eliminar este paquete?");'>
                                <input type='hidden' name='csrf_token' value='<?php echo $_SESSION['csrf_token']; ?>'>
                                <input type='hidden' name='id' value='<?php echo $id_safe; ?>'>
                                <input type='hidden' name='action' value='delete'>
                                <button type='submit' style='background-color: #ff4444;'>Eliminar</button>
                            </form>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No hay paquetes disponibles.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2023 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>