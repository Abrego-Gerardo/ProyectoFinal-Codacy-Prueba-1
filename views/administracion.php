<?php
session_start();

// Verificación de acceso (admin)
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: login_form.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    exit("Conexión fallida: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if ($action === 'edit') {
        // Procesar Edición
        $id            = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombre        = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $tipo_destino  = filter_input(INPUT_POST, 'tipo_destino', FILTER_SANITIZE_STRING);
        $precio_nino   = filter_input(INPUT_POST, 'precio_nino', FILTER_VALIDATE_FLOAT);
        $precio_adulto = filter_input(INPUT_POST, 'precio_adulto', FILTER_VALIDATE_FLOAT);
        $precio_mayor  = filter_input(INPUT_POST, 'precio_mayor', FILTER_VALIDATE_FLOAT);
        $detalles      = filter_input(INPUT_POST, 'detalles', FILTER_SANITIZE_STRING) ?? '';

        if ($id !== false) {
            $stmt = $conn->prepare("UPDATE destinos 
                SET city = ?, tipo_destino = ?, precio_nino = ?, precio_adulto = ?, precio_mayor = ?, detalles = ? 
                WHERE id = ?");
            $stmt->bind_param("ssdddis", $nombre, $tipo_destino, $precio_nino, $precio_adulto, $precio_mayor, $detalles, $id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($action === 'delete') {
        // Procesar Eliminación
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id !== false) {
            $stmt = $conn->prepare("DELETE FROM destinos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Consulta para obtener todos los destinos
$sql = "SELECT * FROM destinos";
$result = $conn->query($sql);

// Nota: mantenemos la conexión abierta hasta terminar el render para evitar perder $result.
// Se cerrará al final del documento.
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
            if (isset($_SESSION['user']) && is_string($_SESSION['user'])) {
                $usuario = htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8');
                echo "Usuario: {$usuario}";
                echo '<a href="logout.php">Cerrar sesión</a>';
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
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $detalles = $row['detalles'] ?? '';
                        echo "<div class='paquete'>";
                        echo "<form action='administracion.php' method='post'>";
                        printf("<input type='hidden' name='id' value='%d'>", (int)$row['id']);
                        echo "<input type='hidden' name='action' value='edit'>";
                        printf("<label for='nombre_%d'>Nombre:</label>", (int)$row['id']);
                        printf(
                            "<input type='text' id='nombre_%d' name='nombre' value='%s' required>",
                            (int)$row['id'],
                            htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8')
                        );
                        printf("<label for='tipo_destino_%d'>Tipo de Destino:</label>", (int)$row['id']);
                        printf("<select id='tipo_destino_%d' name='tipo_destino' required>", (int)$row['id']);
                        echo "<option value='Nacional' " . ($row['tipo_destino'] === 'Nacional' ? 'selected' : '') . ">Nacional</option>";
                        echo "<option value='Internacional' " . ($row['tipo_destino'] === 'Internacional' ? 'selected' : '') . ">Internacional</option>";
                        echo "</select>";
                        printf("<label for='precio_nino_%d'>Precio Niño:</label>", (int)$row['id']);
                        printf(
                            "<input type='number' id='precio_nino_%d' name='precio_nino' value='%s' required>",
                            (int)$row['id'],
                            htmlspecialchars($row['precio_nino'], ENT_QUOTES, 'UTF-8')
                        );
                        printf("<label for='precio_adulto_%d'>Precio Adulto:</label>", (int)$row['id']);
                        printf(
                            "<input type='number' id='precio_adulto_%d' name='precio_adulto' value='%s' required>",
                            (int)$row['id'],
                            htmlspecialchars($row['precio_adulto'], ENT_QUOTES, 'UTF-8')
                        );
                        printf("<label for='precio_mayor_%d'>Precio Mayor:</label>", (int)$row['id']);
                        printf(
                            "<input type='number' id='precio_mayor_%d' name='precio_mayor' value='%s' required>",
                            (int)$row['id'],
                            htmlspecialchars($row['precio_mayor'], ENT_QUOTES, 'UTF-8')
                        );
                        printf("<label for='detalles_%d'>Detalles:</label>", (int)$row['id']);
                        printf(
                            "<textarea id='detalles_%d' name='detalles' required>%s</textarea>",
                            (int)$row['id'],
                            htmlspecialchars($detalles, ENT_QUOTES, 'UTF-8')
                        );
                        echo "<button type='submit'>Guardar Cambios</button>";
                        echo "</form>";

                        echo "<form action='administracion.php' method='post' style='display:inline;'>";
                        printf("<input type='hidden' name='id' value='%d'>", (int)$row['id']);
                        echo "<input type='hidden' name='action' value='delete'>";
                        echo "<button type='submit'>Eliminar</button>";
                        echo "</form>";

                        echo "</div>";
                    }
                } else {
                    echo "No hay paquetes disponibles.";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// Cerrar la conexión al final
$conn->close();
?>

