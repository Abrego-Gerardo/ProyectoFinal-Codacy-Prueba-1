<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'edit') {
        // Procesar Edición
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $tipo_destino = $_POST['tipo_destino'];
        $precio_nino = $_POST['precio_nino'];
        $precio_adulto = $_POST['precio_adulto'];
        $precio_mayor = $_POST['precio_mayor'];
        $detalles = $_POST['detalles'] ?? '';

        $sql = "UPDATE destinos SET city='$nombre', tipo_destino='$tipo_destino', precio_nino='$precio_nino', precio_adulto='$precio_adulto', precio_mayor='$precio_mayor', detalles='$detalles' WHERE id=$id";
        $conn->query($sql);
    } elseif ($_POST['action'] === 'delete') {
        // Procesar Eliminación
        $id = $_POST['id'];
        $sql = "DELETE FROM destinos WHERE id=$id";
        $conn->query($sql);
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
            session_start();
            if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
                header("Location: login_form.php");
                exit();
            }
            if (isset($_SESSION['user'])) {
                echo "Usuario: " . htmlspecialchars($_SESSION['user']);
                echo "<a href='logout.php'>Cerrar sesión</a>";
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
                        $detalles = $row['detalles'] ?? ''; // Verificar si el campo detalles existe
                        echo "<div class='paquete'>";
                        echo "<form action='administracion.php' method='post'>";
                        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                        echo "<input type='hidden' name='action' value='edit'>";
                        echo "<label for='nombre_" . $row['id'] . "'>Nombre:</label>";
                        echo "<input type='text' id='nombre_" . $row['id'] . "' name='nombre' value='" . $row['city'] . "' required>";
                        echo "<label for='tipo_destino_" . $row['id'] . "'>Tipo de Destino:</label>";
                        echo "<select id='tipo_destino_" . $row['id'] . "' name='tipo_destino' required>";
                        echo "<option value='Nacional' " . ($row['tipo_destino'] == 'Nacional' ? 'selected' : '') . ">Nacional</option>";
                        echo "<option value='Internacional' " . ($row['tipo_destino'] == 'Internacional' ? 'selected' : '') . ">Internacional</option>";
                        echo "</select>";
                        echo "<label for='precio_nino_" . $row['id'] . "'>Precio Niño:</label>";
                        echo "<input type='number' id='precio_nino_" . $row['id'] . "' name='precio_nino' value='" . $row['precio_nino'] . "' required>";
                        echo "<label for='precio_adulto_" . $row['id'] . "'>Precio Adulto:</label>";
                        echo "<input type='number' id='precio_adulto_" . $row['id'] . "' name='precio_adulto' value='" . $row['precio_adulto'] . "' required>";
                        echo "<label for='precio_mayor_" . $row['id'] . "'>Precio Mayor:</label>";
                        echo "<input type='number' id='precio_mayor_" . $row['id'] . "' name='precio_mayor' value='" . $row['precio_mayor'] . "' required>";
                        echo "<label for='detalles_" . $row['id'] . "'>Detalles:</label>";
                        echo "<textarea id='detalles_" . $row['id'] . "' name='detalles' required>" . htmlspecialchars($detalles) . "</textarea>";
                        echo "<button type='submit'>Guardar Cambios</button>";
                        echo "</form>";
                        echo "<form action='administracion.php' method='post' style='display:inline;'>";
                        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
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
    <div class="footer">
        <p>&copy; 2023 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>
