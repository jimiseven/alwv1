<?php
require_once '../../config/db.php';
require_once '../../config/config.php';

header('Content-Type: application/json');

// Verificar que sea admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);

    // Validaciones
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
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

    // Obtener ventas del usuario
    $sql_ventas = "SELECT v.id, v.numero_celular, v.fecha_inicio, v.fecha_fin, v.pago, c.correo as cuenta_correo
                   FROM ventas v
                   LEFT JOIN cuentas c ON v.cuenta_id = c.id
                   WHERE v.vendedor_id = ?
                   ORDER BY v.fecha_inicio DESC";
    
    $stmt_ventas = mysqli_prepare($conn, $sql_ventas);
    mysqli_stmt_bind_param($stmt_ventas, "i", $id);
    mysqli_stmt_execute($stmt_ventas);
    $result_ventas = mysqli_stmt_get_result($stmt_ventas);

    $ventas = [];
    $total_ingresos = 0;
    
    while ($venta = mysqli_fetch_assoc($result_ventas)) {
        $ventas[] = [
            'id' => $venta['id'],
            'numero_celular' => $venta['numero_celular'],
            'cuenta_correo' => $venta['cuenta_correo'] ?? 'N/A',
            'fecha_inicio' => date('d/m/Y', strtotime($venta['fecha_inicio'])),
            'fecha_fin' => date('d/m/Y', strtotime($venta['fecha_fin'])),
            'pago' => number_format($venta['pago'], 2)
        ];
        $total_ingresos += $venta['pago'];
    }

    echo json_encode([
        'success' => true,
        'ventas' => $ventas,
        'total_ventas' => count($ventas),
        'total_ingresos' => number_format($total_ingresos, 2)
    ]);

    mysqli_stmt_close($stmt_ventas);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

mysqli_close($conn);
