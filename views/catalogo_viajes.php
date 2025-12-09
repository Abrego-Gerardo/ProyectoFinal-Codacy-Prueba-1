<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Viajes - Agencia de Viajes</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <style>
        .slider-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .slider {
            width: 100%;
            height: 15px;
            background: #d3d3d3;
            outline: none;
            opacity: 0.7;
            transition: opacity .2s;
        }
        .slider:hover {
            opacity: 1;
            background-color: #83070b;
        }
        .slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 25px;
            height: 25px;
            color: #83070b;
            cursor: pointer;
        }
        .slider::-moz-range-thumb {
            width: 25px;
            height: 25px;
            color: #83070b;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">Catálogo de Viajes</div>
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
        <h1>¿A dónde quieres ir?</h1>
        <form action="buscar_viajes.php" method="post">
            <label for="origen">Origen:</label>
            <select id="origen" name="origen" required>
                <option value="Nacional">Nacional</option>
                <option value="Internacional">Internacional</option>
            </select>
            <label for="destino">Destino:</label>
            <select id="destino" name="destino" required>
                <option value="Nacional">Nacional</option>
                <option value="Internacional">Internacional</option>
            </select>
            <label for="fecha_salida">Fecha de Salida:</label>
            <input type="date" id="fecha_salida" name="fecha_salida" required onchange="validarFechaRegreso()">
            <label for="fecha_regreso">Fecha de Regreso:</label>
            <input type="date" id="fecha_regreso" name="fecha_regreso" required>
            <label for="tipo_viaje">Tipo de Viaje:</label>
            <select id="tipo_viaje" name="tipo_viaje" required>
                <option value="aire">Aire</option>
                <option value="mar">Mar</option>
                <option value="tierra">Tierra</option>
            </select>
            <div class="slider-container">
                <label for="precio">Rango de precio máximo por persona:</label>
                <input type="range" id="precio" name="precio" class="slider" min="0" max="1000" step="10" oninput="this.nextElementSibling.value = '$' + this.value">
                <output>$500</output>
            </div>
            <button type="submit">Buscar Viaje</button>
        </form>
    </div>
    <div class="footer">
        <p>&copy; 2024 Agencia de Viajes. Todos los derechos reservados.</p>
    </div>
</body>
</html>


