<?php
require_once '../../config/db.php';
require_once '../../config/config.php';

// Limpiar buffer de salida
ob_start();

header('Content-Type: application/json');

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Recoger y sanitizar datos
$id = intval($_POST['id'] ?? 0);
$numero_celular = trim($_POST['numero_celular'] ?? '');
$cuenta_id = intval($_POST['cuenta_id'] ?? 0);
$fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
$fecha_fin = trim($_POST['fecha_fin'] ?? '');
$pago = floatval($_POST['pago'] ?? 0);
$vendedor_id = intval($_POST['vendedor_id'] ?? 0);

// Validación robusta
$errores = [];
if ($id < 1) $errores[] = "ID de venta inválido";
if (empty($numero_celular)) $errores[] = "Número celular requerido";
if ($cuenta_id < 1) $errores[] = "Selecciona una cuenta válida";
if (empty($fecha_inicio) || !strtotime($fecha_inicio)) $errores[] = "Fecha inicio inválida";
if (empty($fecha_fin) || !strtotime($fecha_fin)) $errores[] = "Fecha fin inválida";
if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) $errores[] = "Fecha fin debe ser posterior a fecha inicio";
if ($pago <= 0) $errores[] = "Pago debe ser mayor a 0";
if ($vendedor_id < 1) $errores[] = "Vendedor inválido";

if (!empty($errores)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => implode(", ", $errores)]);
    exit;
}

// Consulta SQL para actualizar
$sql = "UPDATE ventas SET
    numero_celular = ?,
    vendedor_id = ?,
    fecha_inicio = ?,
    fecha_fin = ?,
    pago = ?,
    cuenta_id = ?,
    updated_at = NOW()
WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Error en preparación: ' . mysqli_error($conn)]);
    exit;
}

// Bind de parámetros
mysqli_stmt_bind_param(
    $stmt,
    "sisssii",
    $numero_celular,
    $vendedor_id,
    $fecha_inicio,
    $fecha_fin,
    $pago,
    $cuenta_id,
    $id
);

// Ejecutar consulta
if (mysqli_stmt_execute($stmt)) {
    // Obtener datos actualizados para el mensaje
    $sql_venta = "SELECT 
        v.*, 
        c.correo, 
        c.contrasena_gpt,
        DATEDIFF(v.fecha_fin, v.fecha_inicio) AS dias
    FROM ventas v
    INNER JOIN cuentas c ON v.cuenta_id = c.id
    WHERE v.id = ?";
    
    $stmt_venta = mysqli_prepare($conn, $sql_venta);
    mysqli_stmt_bind_param($stmt_venta, "i", $id);
    mysqli_stmt_execute($stmt_venta);
    mysqli_stmt_bind_result($stmt_venta, 
        $venta_id, $num_cel, $vend_id, $fec_ini, $fec_fin, 
        $venta_pago, $cta_id, $dias, $created, $updated,
        $correo, $contrasena, $dias_calc
    );
    mysqli_stmt_fetch($stmt_venta);
    mysqli_stmt_close($stmt_venta);

    // Formatear fechas para el mensaje
    $fecha_ini = date('d/m/Y', strtotime($fecha_inicio));
    $fecha_end = date('d/m/Y', strtotime($fecha_fin));

    // Construir mensaje para portapapeles
    $mensaje = <<<EOD
Datos actualizados para la cuenta de Chat GPT

Cuenta Chat GPT Plus (tiempo en días: $dias_calc días)
Correo: $correo
Contraseña: $contrasena

Fecha ini: $fecha_ini
Fecha end: $fecha_end

Cambio de datos para la cuenta de Chat Gpt Plus, ingresa y me confirma por favor

Reglas para el uso de la cuenta:
* No modificar ningún dato de la cuenta
* Evitar salir de la cuenta
* Preferentemente, usar la aplicación móvil en el celular y en computadora el navegador Google Chrome (NO usar pestaña de incógnito)

Ingresa ahora por favor y te paso los códigos de activación
EOD;

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Venta actualizada correctamente',
        'clipboardText' => $mensaje
    ]);
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
?>
