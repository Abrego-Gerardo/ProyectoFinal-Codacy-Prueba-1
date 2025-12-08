<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    exit("Conexión fallida: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}

// Generar nonce CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener detalles del destino seleccionado de manera segura
$id_viaje = filter_input(INPUT_POST, 'id_viaje', FILTER_VALIDATE_INT);

$destino = null;
if ($id_viaje !== false && $id_viaje !== null) {
    $stmt = $conn->prepare("SELECT * FROM destinos WHERE id = ?");
    $stmt->bind_param("i", $id_viaje);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $destino = $result->fetch_assoc();
    } else {
        exit("Destino no encontrado.");
    }
    $stmt->close();
} else {
    exit("Solicitud inválida.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Reserva - Agencia de Viajes</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <style>
    .contador {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    .contador button {
        width: 30px;
        height: 30px;
        margin: 0 5px;
        background-color: #83070b;
        color: white;
        border: none;
        border-radius: 3px;
        font-size: 18px;
        cursor: pointer;
    }
    .contador button:hover {
        background-color: #5a0508;
    }
    .contador input {
        width: 50px;
        text-align: center;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 3px;
        margin: 0 5px;
    }
    .precio-final {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-top: 20px;
    }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">Procesar Reserva</div>
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
        <h1>Procesar Reserva</h1>
        <?php if ($destino) : ?>
        <div class="detalle-reserva">
            <?php
            $city          = htmlspecialchars($destino["city"], ENT_QUOTES, 'UTF-8');
            $pais          = htmlspecialchars($destino["pais"], ENT_QUOTES, 'UTF-8');
            $precio_nino   = (float)$destino["precio_nino"];
            $precio_adulto = (float)$destino["precio_adulto"];
            $precio_mayor  = (float)$destino["precio_mayor"];
            ?>
            <h2><?php print "{$city}, {$pais}"; ?></h2>
            <p>Precio Niño: $<?php print htmlspecialchars($precio_nino, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Precio Adulto: $<?php print htmlspecialchars($precio_adulto, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Precio Mayor: $<?php print htmlspecialchars($precio_mayor, ENT_QUOTES, 'UTF-8'); ?></p>

            <!-- Precio total dinámico -->
            <p class="precio-final">Precio Total: $<span id="precio_total">0</span></p>

            <form action="confirmar_reserva.php" method="post">
                <input type="hidden" name="id_viaje" value="<?php print (int)$destino['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <!-- Contador para niños -->
                <label for="cantidad_ninos">Cantidad de Niños:</label>
                <div class="contador">
                    <button type="button" onclick="actualizarCantidad('cantidad_ninos', -1, <?php print $precio_nino; ?>)">-</button>
                    <input type="number" id="cantidad_ninos" name="cantidad_ninos" value="0" min="0" readonly>
                    <button type="button" onclick="actualizarCantidad('cantidad_ninos', 1, <?php print $precio_nino; ?>)">+</button>
                </div>

                <!-- Contador para adultos -->
                <label for="cantidad_adultos">Cantidad de Adultos:</label>
                <div class="contador">
                    <button type="button" onclick="actualizarCantidad('cantidad_adultos', -1, <?php print $precio_adulto; ?>)">-</button>
                    <input type="number" id="cantidad_adultos" name="cantidad_adultos" value="0" min="0" readonly>
                    <button type="button" onclick="actualizarCantidad('cantidad_adultos', 1, <?php print $precio_adulto; ?>)">+</button>
                </div>

                <!-- Contador para mayores -->
                <label for="cantidad_mayores">Cantidad de Mayores:</label>
                <div class="contador">
                    <button type="button" onclick="actualizarCantidad('cantidad_mayores', -1, <?php print $precio_mayor; ?>)">-</button>
                    <input type="number" id="cantidad_mayores" name="cantidad_mayores" value="0" min="0" readonly>
                    <button type="button" onclick="actualizarCantidad('cantidad_mayores', 1, <?php print $precio_mayor; ?>)">+</button>
                </div>

                <button type="submit">Confirmar Reserva</button>
            </form>
        </div>
        <?php else : ?>
            <p>No se encontraron detalles para este destino.</p>
        <?php endif; ?>
    </div>
    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>

    <script>
        let totalPrecio = 0;
        function actualizarCantidad(id, cambio, precio) {
            const input = document.getElementById(id);
            const valorActual = parseInt(input.value);
            const nuevoValor = Math.max(0, valorActual + cambio);
            input.value = nuevoValor;

            totalPrecio += cambio * precio;
            totalPrecio = Math.max(0, totalPrecio);
            document.getElementById('precio_total').textContent = totalPrecio.toFixed(2);
        }
    </script>
</body>
</html>


