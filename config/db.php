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
    define('DB_SERVER', '185.140.33.19');
    define('DB_USERNAME', 'carlacom_test1');
    define('DB_PASSWORD', '&J%[zy*=8tR3Bp1i');
    define('DB_NAME', 'carlacom_rootes');
}

// Intentar conectar a la base de datos MySQL
$conn = mysqli_init();
mysqli_options($conn, MYSQLI_INIT_COMMAND, 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci');
mysqli_options($conn, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$connected = mysqli_real_connect($conn, DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if (!$connected) {
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}

// Forzar el charset después de conectar por si acaso
mysqli_set_charset($conn, "utf8mb4");
