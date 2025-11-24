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

    // Contar ventas asociadas
    $sql_check_ventas = "SELECT COUNT(*) as total FROM ventas WHERE vendedor_id = ?";
    $stmt_check_ventas = mysqli_prepare($conn, $sql_check_ventas);
    mysqli_stmt_bind_param($stmt_check_ventas, "i", $id);
    mysqli_stmt_execute($stmt_check_ventas);
    $result = mysqli_stmt_get_result($stmt_check_ventas);
    $row = mysqli_fetch_assoc($result);
    $total_ventas = $row['total'];

    // Iniciar transacción
    mysqli_begin_transaction($conn);

    try {
        // Eliminar ventas asociadas primero
        if ($total_ventas > 0) {
            $sql_delete_ventas = "DELETE FROM ventas WHERE vendedor_id = ?";
            $stmt_delete_ventas = mysqli_prepare($conn, $sql_delete_ventas);
            mysqli_stmt_bind_param($stmt_delete_ventas, "i", $id);
            
            if (!mysqli_stmt_execute($stmt_delete_ventas)) {
                throw new Exception('Error al eliminar ventas: ' . mysqli_error($conn));
            }
            mysqli_stmt_close($stmt_delete_ventas);
        }

        // Eliminar usuario
        $sql = "DELETE FROM vendedores WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Error al eliminar usuario: ' . mysqli_error($conn));
        }

        // Confirmar transacción
        mysqli_commit($conn);
        
        $mensaje = 'Usuario eliminado exitosamente';
        if ($total_ventas > 0) {
            $mensaje .= ' junto con ' . $total_ventas . ' venta(s) asociada(s)';
        }
        
        echo json_encode(['success' => true, 'message' => $mensaje]);
        mysqli_stmt_close($stmt);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

mysqli_close($conn);
