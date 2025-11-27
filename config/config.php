<?php
// Manejo centralizado de sesiones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir la URL base del sitio (ajustar según el entorno)
// Detectar automáticamente si estamos en localhost (XAMPP) o en cPanel
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // Para XAMPP local
    define('BASE_URL', '/alwv1/');
} else {
    // Para cPanel
    define('BASE_URL', '/');
}

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

// Funciones de verificación de roles
function isAdmin() {
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin';
}

function isVendedor() {
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'vendedor';
}

// Función para requerir rol específico
function requireRole($rol) {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit;
    }
    
    if ($_SESSION['user_rol'] !== $rol) {
        // Redirigir al dashboard correspondiente
        if ($_SESSION['user_rol'] === 'admin') {
            header("Location: " . BASE_URL . "dashboard_admin.php");
        } elseif ($_SESSION['user_rol'] === 'vendedor') {
            header("Location: " . BASE_URL . "dashboard_vendedor.php");
        } else {
            // Si no tiene rol válido, cerrar sesión
            session_destroy();
            header("Location: " . BASE_URL . "auth/login.php");
        }
        exit;
    }
}

// Función para requerir rol de admin
function requireAdmin() {
    requireRole('admin');
}

// Función para requerir rol de vendedor
function requireVendedor() {
    requireRole('vendedor');
}
?>
