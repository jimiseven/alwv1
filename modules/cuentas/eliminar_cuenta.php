<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
requireLogin();
requireAdmin(); // Solo administradores pueden gestionar cuentas

header('Content-Type: application/json');

try {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if (!$id || $id < 1) {
        throw new Exception("ID inválido");
    }

    // Iniciar transacción
    mysqli_begin_transaction($conn);

    // 1. Obtener monto para actualizar el total
    $sql_select = "SELECT costo FROM cuentas WHERE id = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select);
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $costo = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_select))['costo'] ?? 0;

    // 2. Eliminar ventas relacionadas
    $sql_delete_ventas = "DELETE FROM ventas WHERE cuenta_id = ?";
    $stmt_ventas = mysqli_prepare($conn, $sql_delete_ventas);
    mysqli_stmt_bind_param($stmt_ventas, "i", $id);
    
    if (!mysqli_stmt_execute($stmt_ventas)) {
        throw new Exception("Error al eliminar ventas: " . mysqli_error($conn));
    }

    // 3. Eliminar la cuenta
    $sql_delete_cuenta = "DELETE FROM cuentas WHERE id = ?";
    $stmt_cuenta = mysqli_prepare($conn, $sql_delete_cuenta);
    mysqli_stmt_bind_param($stmt_cuenta, "i", $id);
    
    if (!mysqli_stmt_execute($stmt_cuenta)) {
        throw new Exception("Error al eliminar cuenta: " . mysqli_error($conn));
    }

    // Confirmar transacción
    mysqli_commit($conn);

    echo json_encode([
        'success' => true,
        'deleted_amount' => $costo
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
