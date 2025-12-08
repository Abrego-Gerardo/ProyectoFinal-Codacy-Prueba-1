<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if ($action === 'edit') {
        // Procesar Edición
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $tipo_destino = filter_input(INPUT_POST, 'tipo_destino', FILTER_SANITIZE_STRING);
        $precio_nino = filter_input(INPUT_POST, 'precio_nino', FILTER_VALIDATE_FLOAT);
        $precio_adulto = filter_input(INPUT_POST, 'precio_adulto', FILTER_VALIDATE_FLOAT);
        $precio_mayor = filter_input(INPUT_POST, 'precio_mayor', FILTER_VALIDATE_FLOAT);
        $detalles = filter_input(INPUT_POST, 'detalles', FILTER_SANITIZE_STRING) ?? '';

        if ($id !== false) {
            $stmt = $conn->prepare("UPDATE destinos 
                SET city=?, tipo_destino=?, precio_nino=?, precio_adulto=?, precio_mayor=?, detalles=? 
                WHERE id=?");
            $stmt->bind_param("ssdddis", $nombre, $tipo_destino, $precio_nino, $precio_adulto, $precio_mayor, $detalles, $id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($action === 'delete') {
        // Procesar Eliminación
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id !== false) {
            $stmt = $conn->prepare("DELETE FROM destinos WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
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
            if (isset($_SESSION['user']) && is_string($_SESSION['user'])) {
                $usuario = htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8');
                print "Usuario: {$usuario}";
                print '<a href="logout.php">Cerrar sesión</a>';
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
                        print "<div class='paquete'>";
                        print "<form action='administracion.php' method='post'>";
                        print "<input type='hidden' name='id' value='" . (int)$row['id'] . "'>";
                        print "<input type='hidden' name='action' value='edit'>";
                        print "<label for='nombre_" . (int)$row['id'] . "'>Nombre:</label>";
                        print "<input type='text' id='nombre_" . (int)$row['id'] . "' name='nombre' value='" . htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8') . "' required>";
                        print "<label for='tipo_destino_" . (int)$row['id'] . "'>Tipo de Destino:</label>";
                        print "<select id='tipo_destino_" . (int)$row['id'] . "' name='tipo_destino' required>";
                        print "<option value='Nacional' " . ($row['tipo_destino'] === 'Nacional' ? 'selected' : '') . ">Nacional</option>";
                        print "<option value='Internacional' " . ($row['tipo_destino'] === 'Internacional' ? 'selected' : '') . ">Internacional</option>";
                        print "</select>";
                        print "<label for='precio_nino_" . (int)$row['id'] . "'>Precio Niño:</label>";
                        print "<input type='number' id='precio_nino_" . (int)$row['id'] . "' name='precio_nino' value='" . htmlspecialchars($row['precio_nino'], ENT_QUOTES, 'UTF-8') . "' required>";
                        print "<label for='precio_adulto_" . (int)$row['id'] . "'>Precio Adulto:</label>";
                        print "<input type='number' id='precio_adulto_" . (int)$row['id'] . "' name='precio_adulto' value='" . htmlspecialchars($row['precio_adulto'], ENT_QUOTES, 'UTF-8') . "' required>";
                        print "<label for='precio_mayor_" . (int)$row['id'] . "'>Precio Mayor:</label>";
                        print "<input type='number' id='precio_mayor_" . (int)$row['id'] . "' name='precio_mayor' value='" . htmlspecialchars($row['precio_mayor'], ENT_QUOTES, 'UTF-8') . "' required>";
                        print "<label for='detalles_" . (int)$row['id'] . "'>Detalles:</label>";
                        print "<textarea id='detalles_" . (int)$row['id'] . "' name='detalles' required>" . htmlspecialchars($detalles, ENT_QUOTES, 'UTF-8') . "</textarea>";
                        print "<button type='submit'>Guardar Cambios</button>";
                        print "</form>";
                        print "<form action='administracion.php' method='post' style='display:inline;'>";
                        print "<input type='hidden' name='id' value='" . (int)$row['id'] . "'>";
                        print "<input type='hidden' name='action' value='delete'>";
                        print "<button type='submit'>Eliminar</button>";
                        print "</form>";
                        print "</div>";
                    }
                } else {
                    print "No hay paquetes disponibles.";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
