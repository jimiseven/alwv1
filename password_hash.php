<?php

// ===============================================
// ADVERTENCIA DE SEGURIDAD EXTREMA:
// ESTE SCRIPT ES PARA USO ÚNICO Y DEBE SER ELIMINADO
// DE TU SERVIDOR INMEDIATAMENTE DESPUÉS DE SU EJECUCIÓN EXITOSA.
// NUNCA UTILICES ESTE TIPO DE SCRIPT EN UN ENTORNO DE PRODUCCIÓN.
// ===============================================


// Definición de las constantes de la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Contraseña vacía, como indicaste
define('DB_NAME', 'v4');   // Nombre de la base de datos, como indicaste

// Contraseña que queremos cifrar para el usuario joe1
$contrasena_plana = '123qwe';

// ID del usuario joe1 en la tabla
$id_usuario_joe1 = 7;

// Nombre de la tabla de usuarios (AJUSTA ESTO SI ES NECESARIO)
// Basado en la imagen, parece que tu tabla de usuarios no tiene un nombre visible,
// pero a menudo se llaman 'usuarios', 'users', etc.
// ASUME QUE SE LLAMA 'users' o 'usuarios'
$nombre_tabla_usuarios = 'vendedores'; // <--- CAMBIA ESTO SI TU TABLA TIENE OTRO NOMBRE (ej. 'usuarios')


echo "<h1>Actualizando Contraseña Cifrada para Usuario 'joe1'</h1>";
echo "<p>Contraseña a cifrar: <code>" . htmlspecialchars($contrasena_plana) . "</code></p>";
echo "<p>ID del usuario a actualizar: <code>" . $id_usuario_joe1 . "</code></p>";
echo "<p>Tabla de usuarios: <code>" . htmlspecialchars($nombre_tabla_usuarios) . "</code></p>";

// Cifrar la contraseña usando password_hash con PASSWORD_BCRYPT
$contrasena_cifrada = password_hash($contrasena_plana, PASSWORD_BCRYPT);

echo "<p>Contraseña cifrada generada (HASH): <code>" . htmlspecialchars($contrasena_cifrada) . "</code></p>";

// Conexión a la base de datos
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($mysqli->connect_error) {
    die("ERROR: No se pudo conectar a la base de datos. " . $mysqli->connect_error);
}

// Preparar la consulta SQL para actualizar la contraseña
// Usamos prepared statements para mayor seguridad, aunque aquí los valores son fijos.
$sql = "UPDATE `" . $nombre_tabla_usuarios . "` SET `contrasena` = ? WHERE `id` = ?";

if ($stmt = $mysqli->prepare($sql)) {
    // Vincular parámetros
    $stmt->bind_param("si", $contrasena_cifrada, $id_usuario_joe1);
    // "s" para string (el hash), "i" para integer (el ID)

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "<h2 style='color: green;'>¡ÉXITO!</h2>";
        echo "<p style='color: green;'>La contraseña para el usuario con ID " . $id_usuario_joe1 . " ha sido actualizada y cifrada correctamente en la tabla `" . htmlspecialchars($nombre_tabla_usuarios) . "`.</p>";
        echo "<p>Ahora puedes intentar iniciar sesión con 'joe1' y '123qwe'.</p>";
    } else {
        echo "<h2 style='color: red;'>ERROR al ejecutar la consulta.</h2>";
        echo "<p style='color: red;'>Detalles del error: " . $stmt->error . "</p>";
    }

    // Cerrar el statement
    $stmt->close();
} else {
    echo "<h2 style='color: red;'>ERROR al preparar la consulta.</h2>";
    echo "<p style='color: red;'>Detalles del error: " . $mysqli->error . "</p>";
}

// Cerrar la conexión a la base de datos
$mysqli->close();

echo "<hr>";
echo "<h3>¡MUY IMPORTANTE!</h3>";
echo "<p style='font-weight: bold; color: red;'>ELIMINA ESTE ARCHIVO (`actualizar_contrasena_joe1.php`) DE TU SERVIDOR INMEDIATAMENTE DESPUÉS DE VERIFICAR QUE FUNCIONÓ.</p>";
echo "<p>Dejarlo expuesto es un riesgo de seguridad.</p>";

?>