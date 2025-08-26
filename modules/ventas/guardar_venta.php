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
$numero_celular = trim($_POST['numero_celular'] ?? '');
$cuenta_id = intval($_POST['cuenta_id'] ?? 0);
$fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
$fecha_fin = trim($_POST['fecha_fin'] ?? '');
$pago = floatval($_POST['pago'] ?? 0);
$vendedor_id = intval($_POST['vendedor_id'] ?? 0);

// Validación robusta
$errores = [];
if (empty($numero_celular)) $errores[] = "Número celular requerido";
if ($cuenta_id < 1) $errores[] = "Selecciona una cuenta válida";
if (empty($fecha_inicio)) $errores[] = "Fecha inicio requerida";
if (empty($fecha_fin)) $errores[] = "Fecha fin requerida";
if ($pago <= 0) $errores[] = "Pago debe ser mayor a 0";
if ($vendedor_id < 1) $errores[] = "Vendedor inválido";

if (!empty($errores)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => implode(", ", $errores)]);
    exit;
}

// Calcular días en PHP
$dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
$dias = intval($dias);

// Consulta SQL
$sql = "INSERT INTO ventas (
    numero_celular, 
    vendedor_id, 
    fecha_inicio, 
    fecha_fin, 
    pago, 
    cuenta_id, 
    created_at, 
    updated_at
) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Error en preparación: ' . mysqli_error($conn)]);
    exit;
}

// Bind de parámetros
mysqli_stmt_bind_param(
    $stmt, 
    "sisssi",
    $numero_celular,
    $vendedor_id,
    $fecha_inicio,
    $fecha_fin,
    $pago,
    $cuenta_id
);

// Ejecutar consulta
if (mysqli_stmt_execute($stmt)) {
    // Obtener datos de la cuenta (incluye tipo_cuenta para mensaje dinamico)
    $sql_cuenta = "SELECT correo, contrasena_gpt, tipo_cuenta FROM cuentas WHERE id = ?";
    $stmt_cuenta = mysqli_prepare($conn, $sql_cuenta);
    mysqli_stmt_bind_param($stmt_cuenta, "i", $cuenta_id);
    mysqli_stmt_execute($stmt_cuenta);
    mysqli_stmt_bind_result($stmt_cuenta, $correo, $contrasena_gpt, $tipo_cuenta);
    mysqli_stmt_fetch($stmt_cuenta);
    mysqli_stmt_close($stmt_cuenta);

    // Formatear fechas como en la tabla (ej: 05 oct 2024)
    $meses = [
        1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun',
        7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic'
    ];
    $dt_ini = new DateTime($fecha_inicio);
    $dt_fin = new DateTime($fecha_fin);
    $fecha_ini = $dt_ini->format('d') . ' ' . $meses[(int)$dt_ini->format('n')] . ' ' . $dt_ini->format('Y');
    $fecha_end = $dt_fin->format('d') . ' ' . $meses[(int)$dt_fin->format('n')] . ' ' . $dt_fin->format('Y');

    // Determinar nombre dinamico de cuenta como en copy-btn
    $tipo_raw = strtolower(trim($tipo_cuenta ?? 'gpt'));
    if (strpos($tipo_raw, 'gemini') !== false) {
        $nombreCuenta = 'Gemini Advanced';
    } elseif (strpos($tipo_raw, 'perplex') !== false) {
        $nombreCuenta = 'Perplexity Pro';
    } else {
        $nombreCuenta = 'Chat Gpt Plus';
    }

    // Construir mensaje para portapapeles (alineado con copy-btn)
    $mensaje = "Datos para ingresar a la cuenta de {$nombreCuenta}\n\n" .
               "Cuenta {$nombreCuenta} ({$dias} dias)\n" .
               "Correo: {$correo}\n" .
               "Contrasena: {$contrasena_gpt}\n\n" .
               "Fecha ini: {$fecha_ini}\n" .
               "Fecha end: {$fecha_end}\n\n" .
               "Reglas para el uso de la cuenta:\n\n" .
               "- No modificar ningun dato de la cuenta, en caso de modificar algun dato de la cuenta, retiro la cuenta del grupo de trabajo y te quitare el acceso, no cubrire la garantia y el tiempo de servicio.\n" .
               "- Evita salirte de la cuenta.\n" .
               "- Referentemente, usa la aplicacion movil en el celular y en computadora navegador Google Chrome NO PESTAnA INCoGNITO \n" .
               "- Link para pc preferentemente la pagina oficial en tu navegador\n\n" .
               "Ingresa ahora por favor y te paso los codigos de activacion";

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Venta guardada correctamente',
        'clipboardText' => $mensaje
    ]);
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Error al guardar: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
?>
