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

    // Validaciones
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        exit;
    }

    // No permitir eliminar el propio usuario
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propio usuario']);
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

    // Verificar si el usuario tiene ventas asociadas
    $sql_check_ventas = "SELECT COUNT(*) as total FROM ventas WHERE vendedor_id = ?";
    $stmt_check_ventas = mysqli_prepare($conn, $sql_check_ventas);
    mysqli_stmt_bind_param($stmt_check_ventas, "i", $id);
    mysqli_stmt_execute($stmt_check_ventas);
    $result = mysqli_stmt_get_result($stmt_check_ventas);
    $row = mysqli_fetch_assoc($result);

    if ($row['total'] > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'No se puede eliminar el usuario porque tiene ' . $row['total'] . ' venta(s) asociada(s). Considera desactivarlo en su lugar.'
        ]);
        exit;
    }

    // Eliminar usuario
    $sql = "DELETE FROM vendedores WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar usuario: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

mysqli_close($conn);
