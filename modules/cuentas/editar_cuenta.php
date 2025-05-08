<?php
require_once '../../config/db.php';
require_once '../../config/config.php';

// Recoger y validar datos
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$correo = trim($_POST['correo'] ?? '');
$contrasena_correo = trim($_POST['contrasena_correo'] ?? '');
$contrasena_gpt = trim($_POST['contrasena_gpt'] ?? '');
$codigo = trim($_POST['codigo'] ?? '');
$fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
$costo = floatval($_POST['costo'] ?? 0);
$estado = trim($_POST['estado'] ?? '');

if ($id < 1 || !$correo || !$contrasena_correo || !$contrasena_gpt || !$fecha_inicio || !$estado) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Actualizar cuenta
$sql = "UPDATE cuentas SET correo=?, contrasena_correo=?, contrasena_gpt=?, codigo=?, fecha_inicio=?, costo=?, estado=? WHERE id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssssdsi", $correo, $contrasena_correo, $contrasena_gpt, $codigo, $fecha_inicio, $costo, $estado, $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
?>
