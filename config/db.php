// config/db.php
<?php
// Configuración de conexión a la base de datos para cPanel
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'v4');

// Intentar conectar a la base de datos MySQL
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if(!$conn){
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}

// Establecer el juego de caracteres (importante para caracteres especiales)
mysqli_set_charset($conn, "utf8");
?>
