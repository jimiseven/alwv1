<?php
require_once 'config/db.php';

// Crear un usuario de prueba
$usuario = "test";
$password = "test123";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO vendedores (usuario, contraseña, activo) VALUES (?, ?, 1)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $usuario, $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    echo "Usuario creado con éxito. Usuario: test, Contraseña: test123";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
