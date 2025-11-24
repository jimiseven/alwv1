<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
requireLogin();

header('Content-Type: application/json');

// Validar ID recibido
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id < 1) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    $sql = "DELETE FROM ventas WHERE id = ?";
    
    // Si no es admin, agregar restricción de vendedor_id
    if (!isAdmin()) {
        $sql .= " AND vendedor_id = " . $_SESSION['user_id'];
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Verificar si se eliminó alguna fila
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se encontró la venta o no tienes permisos para eliminarla']);
        }
    } else {
        throw new Exception('Error al eliminar: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn);
exit;
?>
