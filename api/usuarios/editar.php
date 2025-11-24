<?php
require_once '../../config/db.php';
require_once '../../config/config.php';

header('Content-Type: application/json');

// Verificar que sea admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'vendedor';
    $activo = intval($_POST['activo'] ?? 1);

    // Validaciones
    if ($id <= 0 || empty($usuario)) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        exit;
    }

    // Verificar si el usuario existe
    $sql_check = "SELECT id FROM vendedores WHERE id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) === 0) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }

    // Verificar si el nuevo nombre de usuario ya existe (en otro usuario)
    $sql_check_usuario = "SELECT id FROM vendedores WHERE usuario = ? AND id != ?";
    $stmt_check_usuario = mysqli_prepare($conn, $sql_check_usuario);
    mysqli_stmt_bind_param($stmt_check_usuario, "si", $usuario, $id);
    mysqli_stmt_execute($stmt_check_usuario);
    mysqli_stmt_store_result($stmt_check_usuario);

    if (mysqli_stmt_num_rows($stmt_check_usuario) > 0) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso']);
        exit;
    }

    // Actualizar usuario
    if (!empty($password)) {
        // Si se proporciona nueva contraseña
        if (strlen($password) < 4) {
            echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 4 caracteres']);
            exit;
        }
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE vendedores SET usuario = ?, contrasena = ?, rol = ?, activo = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssii", $usuario, $hashed_password, $rol, $activo, $id);
    } else {
        // Sin cambiar contraseña
        $sql = "UPDATE vendedores SET usuario = ?, rol = ?, activo = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssii", $usuario, $rol, $activo, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

mysqli_close($conn);
