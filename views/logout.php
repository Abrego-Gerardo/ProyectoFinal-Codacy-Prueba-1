<?php
session_start();
session_destroy();

// Función segura para redireccionar
function safe_redirect(string $url): void {
    $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    if (!headers_sent()) {
        header("Location: $url");
        exit();
    } else {
        echo "<script>window.location.href='$url';</script>";
        exit();
    }
}

// Redirigir al formulario de login
safe_redirect("../views/login_form.php");
