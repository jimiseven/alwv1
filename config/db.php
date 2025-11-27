<?php
// Configuración de conexión a la base de datos
// Detectar automáticamente si estamos en localhost (XAMPP) o en cPanel

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // Configuración para XAMPP local
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'v4');
} else {
    // Configuración para cPanel
    define('DB_SERVER', '5.134.116.204');
    define('DB_USERNAME', 'alwsgine_root_alws');
    define('DB_PASSWORD', 'j+k)Q*2A{wc.');
    define('DB_NAME', 'alwsgine_bd_alws');
}

// Intentar conectar a la base de datos MySQL
$conn = mysqli_init();
mysqli_options($conn, MYSQLI_INIT_COMMAND, 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci');
mysqli_options($conn, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
mysqli_real_connect($conn, DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if(!$conn){
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}

// Forzar el charset después de conectar por si acaso
mysqli_set_charset($conn, "utf8mb4");
