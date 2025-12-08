<?php
// Configuración de la base de datos
$hostName   = "localhost";
$dbUser     = "root";
$dbPassword = "";
$dbName     = "agencia_db";

// Reportar errores de mysqli como excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($hostName, $dbUser, $dbPassword, $dbName);
    $mysqli->set_charset("utf8mb4"); // Codacy recomienda definir charset explícito
} catch (Exception $e) {
    exit("Error de conexión: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

return $mysqli;
