<?php
// Iniciar sesión en todas las páginas
session_start();

// Definir la URL base del sitio (ajustar según el entorno)
define('BASE_URL', '/alwv1/');

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para redireccionar si no está logueado
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit;
    }
}
?>
