<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
requireLogin();
requireAdmin(); // Solo administradores pueden gestionar cuentas
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$estado = isset($_POST['estado']) ? $_POST['estado'] : '';

if ($id < 1 || !in_array($estado, ['activa', 'inactiva'])) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

// Actualizar estado
$sql = "UPDATE cuentas SET estado = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $estado, $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos']);
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
?>
