<?php
require_once 'config/db.php';
require_once 'config/config.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// Redirigir según el rol del usuario
if ($_SESSION['user_rol'] === 'admin') {
    header("Location: " . BASE_URL . "dashboard_admin.php");
    exit;
} elseif ($_SESSION['user_rol'] === 'vendedor') {
    header("Location: " . BASE_URL . "dashboard_vendedor.php");
    exit;
} else {
    // Si no tiene rol definido, cerrar sesión
    session_destroy();
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}
?>
