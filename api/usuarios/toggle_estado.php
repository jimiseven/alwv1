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
    $activo = intval($_POST['activo'] ?? 0);

    // Validaciones
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        exit;
    }

    // No permitir desactivar el propio usuario
    if ($id == $_SESSION['user_id'] && $activo == 0) {
        echo json_encode(['success' => false, 'message' => 'No puedes desactivar tu propio usuario']);
        exit;
    }

    // Verificar si el usuario existe
    $sql_check = "SELECT id, usuario FROM vendedores WHERE id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);
    
    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }

    $usuario = mysqli_fetch_assoc($result);

    // Actualizar estado del usuario
    $sql = "UPDATE vendedores SET activo = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $activo, $id);

    if (mysqli_stmt_execute($stmt)) {
        $accion = $activo ? 'activado' : 'desactivado';
        echo json_encode([
            'success' => true, 
            'message' => "Usuario '{$usuario['usuario']}' {$accion} exitosamente"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al cambiar estado: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

mysqli_close($conn);
