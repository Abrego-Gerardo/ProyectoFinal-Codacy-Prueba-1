<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "agencia_db";

$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Error de conexión");
}
$mysqli = new mysqli($hostName, $dbUser, $dbPassword, $dbName);
return $mysqli;
?>