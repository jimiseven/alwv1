<?php
require_once 'config/db.php';
require_once 'config/config.php';

function crearVendedor($conn, $usuario, $password) {
    // Verificar si usuario ya existe
    $sql_check = "SELECT id FROM vendedores WHERE usuario = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $usuario);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        echo "\nError: El usuario ya existe\n";
        return false;
    }

    $activo = 1; // Por defecto activo

    // Hashear contraseña
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insertar en base de datos
    $sql = "INSERT INTO vendedores (usuario, contrasena, activo)
            VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $usuario, $hashed_password, $activo);

    if(mysqli_stmt_execute($stmt)) {
        echo "\nUsuario creado exitosamente!\n";
        echo "ID: ".mysqli_insert_id($conn)."\n";
        echo "Usuario: $usuario\n";
        return true;
    } else {
        echo "\nError al crear usuario: ".mysqli_error($conn)."\n";
        return false;
    }
}

// Crear usuario joe con contraseña pass
crearVendedor($conn, 'joe', 'pass');
mysqli_close($conn);
?>