<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $detalles = $row['detalles'] ?? '';
        echo "<div class='paquete'>";
        echo "<form action='administracion.php' method='post'>";
        printf("<input type='hidden' name='id' value='%d'>", (int)$row['id']);
        echo "<input type='hidden' name='action' value='edit'>";
        printf("<label for='nombre_%d'>Nombre:</label>", (int)$row['id']);
        printf("<input type='text' id='nombre_%d' name='nombre' value='%s' required>",
            (int)$row['id'], htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8'));
        printf("<label for='tipo_destino_%d'>Tipo de Destino:</label>", (int)$row['id']);
        printf("<select id='tipo_destino_%d' name='tipo_destino' required>", (int)$row['id']);
        echo "<option value='Nacional' " . ($row['tipo_destino'] === 'Nacional' ? 'selected' : '') . ">Nacional</option>";
        echo "<option value='Internacional' " . ($row['tipo_destino'] === 'Internacional' ? 'selected' : '') . ">Internacional</option>";
        echo "</select>";
        printf("<label for='precio_nino_%d'>Precio Niño:</label>", (int)$row['id']);
        printf("<input type='number' id='precio_nino_%d' name='precio_nino' value='%s' required>",
            (int)$row['id'], htmlspecialchars($row['precio_nino'], ENT_QUOTES, 'UTF-8'));
        printf("<label for='precio_adulto_%d'>Precio Adulto:</label>", (int)$row['id']);
        printf("<input type='number' id='precio_adulto_%d' name='precio_adulto' value='%s' required>",
            (int)$row['id'], htmlspecialchars($row['precio_adulto'], ENT_QUOTES, 'UTF-8'));
        printf("<label for='precio_mayor_%d'>Precio Mayor:</label>", (int)$row['id']);
        printf("<input type='number' id='precio_mayor_%d' name='precio_mayor' value='%s' required>",
            (int)$row['id'], htmlspecialchars($row['precio_mayor'], ENT_QUOTES, 'UTF-8'));
        printf("<label for='detalles_%d'>Detalles:</label>", (int)$row['id']);
        printf("<textarea id='detalles_%d' name='detalles' required>%s</textarea>",
            (int)$row['id'], htmlspecialchars($detalles, ENT_QUOTES, 'UTF-8'));
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

