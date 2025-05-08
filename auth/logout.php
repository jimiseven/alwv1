<?php
require_once '../config/config.php';
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: " . BASE_URL . "auth/login.php");
exit;
?>