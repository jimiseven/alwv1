<?php
// Archivo de prueba para verificar configuración
require_once 'config/config.php';
require_once 'config/db.php';

echo "<h1>Prueba de Configuración - Sistema ALW</h1>";
echo "<h3>Información del Servidor:</h3>";
echo "<pre>";
echo "BASE_URL: " . BASE_URL . "\n";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'No') . "\n";
echo "</pre>";

echo "<h3>Información de Base de Datos:</h3>";
echo "<pre>";
echo "DB_SERVER: " . DB_SERVER . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "Conexión: " . ($conn ? "EXITOSA" : "FALLIDA") . "\n";
echo "</pre>";

echo "<h3>Prueba de Rutas:</h3>";
echo "<ul>";
echo "<li><a href='" . BASE_URL . "auth/login.php'>Login</a></li>";
echo "<li><a href='" . BASE_URL . "dashboard.php'>Dashboard</a></li>";
echo "<li><a href='" . BASE_URL . "dashboard_admin.php'>Dashboard Admin</a></li>";
echo "</ul>";

echo "<h3>Variables de Sesión:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

mysqli_close($conn);
?>
