<?php
require_once 'config/db.php';
require_once 'config/config.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    // Usar ruta absoluta para evitar problemas de redirección
    $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . BASE_URL . "auth/login.php";
    header("Location: " . $redirect_url);
    exit;
}

// Redirigir según el rol del usuario
if ($_SESSION['user_rol'] === 'admin') {
    $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . BASE_URL . "dashboard_admin.php";
    header("Location: " . $redirect_url);
    exit;
} elseif ($_SESSION['user_rol'] === 'vendedor') {
    $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . BASE_URL . "dashboard_vendedor.php";
    header("Location: " . $redirect_url);
    exit;
} else {
    // Si no tiene rol definido, cerrar sesión
    session_destroy();
    $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . BASE_URL . "auth/login.php";
    header("Location: " . $redirect_url);
    exit;
}
?>
