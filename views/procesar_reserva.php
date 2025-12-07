<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "agencia_db");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener detalles del destino seleccionado
$id_viaje = $_POST['id_viaje'];
$sql = "SELECT * FROM destinos WHERE id = $id_viaje";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $destino = $result->fetch_assoc();
} else {
    die("Destino no encontrado.");
}
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
        background-color: #83070b; /* Color rojo */
        color: white;
        border: none;
        border-radius: 3px;
        font-size: 18px;
        cursor: pointer;
    }

    .contador button:hover {
        background-color: #83070b; /* Color rojo más oscuro al pasar el mouse */
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
            session_start();
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
        <h1>Procesar Reserva</h1>
        <div class="detalle-reserva">
            <h2><?php echo $destino["city"] . ", " . $destino["pais"]; ?></h2>
            <p>Precio Niño: $<?php echo $destino["precio_nino"]; ?></p>
            <p>Precio Adulto: $<?php echo $destino["precio_adulto"]; ?></p>
            <p>Precio Mayor: $<?php echo $destino["precio_mayor"]; ?></p>

            <!-- Precio total dinámico -->
            <p class="precio-final">Precio Total: $<span id="precio_total">0</span></p>

            <form action="confirmar_reserva.php" method="post">
                <input type="hidden" name="id_viaje" value="<?php echo $destino['id']; ?>">

                <!-- Contador para niños -->
                <label for="cantidad_ninos">Cantidad de Niños:</label>
                <div class="contador">
                    <button type="button" onclick="actualizarCantidad('cantidad_ninos', -1, <?php echo $destino['precio_nino']; ?>)">-</button>
                    <input type="number" id="cantidad_ninos" name="cantidad_ninos" value="0" min="0" readonly>
                    <button type="button" onclick="actualizarCantidad('cantidad_ninos', 1, <?php echo $destino['precio_nino']; ?>)">+</button>
                </div>

                <!-- Contador para adultos -->
                <label for="cantidad_adultos">Cantidad de Adultos:</label>
                <div class="contador">
                    <button type="button" onclick="actualizarCantidad('cantidad_adultos', -1, <?php echo $destino['precio_adulto']; ?>)">-</button>
                    <input type="number" id="cantidad_adultos" name="cantidad_adultos" value="0" min="0" readonly>
                    <button type="button" onclick="actualizarCantidad('cantidad_adultos', 1, <?php echo $destino['precio_adulto']; ?>)">+</button>
                </div>

                <!-- Contador para mayores -->
                <label for="cantidad_mayores">Cantidad de Mayores:</label>
                <div class="contador">
                    <button type="button" onclick="actualizarCantidad('cantidad_mayores', -1, <?php echo $destino['precio_mayor']; ?>)">-</button>
                    <input type="number" id="cantidad_mayores" name="cantidad_mayores" value="0" min="0" readonly>
                    <button type="button" onclick="actualizarCantidad('cantidad_mayores', 1, <?php echo $destino['precio_mayor']; ?>)">+</button>
                </div>

                <button type="submit">Confirmar Reserva</button>
            </form>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>

    <script>
        let totalPrecio = 0;

        function actualizarCantidad(id, cambio, precio) {
            const input = document.getElementById(id);
            const valorActual = parseInt(input.value);
            const nuevoValor = Math.max(0, valorActual + cambio); // Evitar valores negativos
            input.value = nuevoValor;

            // Actualizar precio total
            totalPrecio += cambio * precio;
            totalPrecio = Math.max(0, totalPrecio); // Evitar precios negativos
            document.getElementById('precio_total').textContent = totalPrecio.toFixed(2);
        }
    </script>
</body>
</html>

