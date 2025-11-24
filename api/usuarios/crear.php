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
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'vendedor';
    $activo = intval($_POST['activo'] ?? 1);

    // Validaciones
    if (empty($usuario) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son requeridos']);
        exit;
    }

    if (strlen($password) < 4) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 4 caracteres']);
        exit;
    }

    // Verificar si el usuario ya existe
    $sql_check = "SELECT id FROM vendedores WHERE usuario = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $usuario);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        echo json_encode(['success' => false, 'message' => 'El usuario ya existe']);
        exit;
    }

    // Hashear contraseña
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insertar usuario
    $sql = "INSERT INTO vendedores (usuario, contrasena, rol, activo) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $usuario, $hashed_password, $rol, $activo);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'id' => mysqli_insert_id($conn)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear usuario: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

mysqli_close($conn);
