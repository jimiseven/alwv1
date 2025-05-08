<?php
require_once '../../config/db.php';
require_once '../../config/config.php';

header('Content-Type: application/json');

// Validar datos recibidos
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$numero_celular = trim($_POST['numero_celular'] ?? '');
$cuenta_id = isset($_POST['cuenta_id']) ? intval($_POST['cuenta_id']) : 0;
$fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
$fecha_fin = trim($_POST['fecha_fin'] ?? '');
$pago = isset($_POST['pago']) ? floatval($_POST['pago']) : 0;
$vendedor_id = isset($_POST['vendedor_id']) ? intval($_POST['vendedor_id']) : 0;

// Validaciones básicas
if ($id < 1 || empty($numero_celular) || $cuenta_id < 1 || 
    empty($fecha_inicio) || empty($fecha_fin) || $pago <= 0) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos o inválidos']);
    exit;
}

try {
    // Calcular días automáticamente
    $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
    
    $sql = "UPDATE ventas SET 
            numero_celular = ?, 
            cuenta_id = ?, 
            fecha_inicio = ?, 
            fecha_fin = ?, 
            dias = ?, 
            pago = ?, 
            vendedor_id = ?, 
            updated_at = NOW() 
            WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sissdiii", 
        $numero_celular,
        $cuenta_id,
        $fecha_inicio,
        $fecha_fin,
        $dias,
        $pago,
        $vendedor_id,
        $id
    );
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error en la actualización: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn);
exit;
?>
