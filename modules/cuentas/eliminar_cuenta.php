<?php
require_once '../../config/db.php';
require_once '../../config/config.php';

header('Content-Type: application/json');

// Validar ID
$id = isset($_POST['id']) ? $_POST['id'] : null;

if (!$id || !is_numeric($id) || $id < 1) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    // Obtener el costo antes de eliminar (para actualizar el total)
    $sql_select = "SELECT costo FROM cuentas WHERE id = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select);
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $result = mysqli_stmt_get_result($stmt_select);
    $costo = mysqli_fetch_assoc($result)['costo'] ?? 0;

    // Eliminar la cuenta
    $sql_delete = "DELETE FROM cuentas WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $id);

    if (mysqli_stmt_execute($stmt_delete)) {
        echo json_encode([
            'success' => true,
            'deleted_amount' => $costo
        ]);
    } else {
        throw new Exception("Error al eliminar: " . mysqli_error($conn));
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
