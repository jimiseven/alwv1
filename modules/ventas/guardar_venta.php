<?php
// No debe haber ningún espacio ni línea antes de <?php
require_once '../../config/db.php';
require_once '../../config/config.php';

header('Content-Type: application/json');

// Validar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Recoger y limpiar los datos del formulario
$numero_celular = isset($_POST['numero_celular']) ? trim($_POST['numero_celular']) : null;
$cuenta_id      = isset($_POST['cuenta_id']) ? intval($_POST['cuenta_id']) : null;
$fecha_inicio   = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
$fecha_fin      = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : null;
$pago           = isset($_POST['pago']) ? floatval($_POST['pago']) : null;
$vendedor_id    = isset($_POST['vendedor_id']) ? intval($_POST['vendedor_id']) : null;

// Validar campos requeridos
if (
    empty($numero_celular) ||
    empty($cuenta_id) ||
    empty($fecha_inicio) ||
    empty($fecha_fin) ||
    empty($pago) ||
    empty($vendedor_id)
) {
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
    exit;
}

// Calcular los días de la venta
$dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
$dias = intval($dias);

// Insertar la venta en la base de datos
$sql = "INSERT INTO ventas 
        (numero_celular, vendedor_id, fecha_inicio, fecha_fin, pago, cuenta_id, dias, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sissdii", 
        $numero_celular, $vendedor_id, $fecha_inicio, $fecha_fin, $pago, $cuenta_id, $dias
    );
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Venta guardada correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar la venta: ' . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la consulta']);
}

mysqli_close($conn);
exit;
?>
