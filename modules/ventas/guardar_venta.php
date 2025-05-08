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
    dias, 
    created_at, 
    updated_at
) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Error en preparación: ' . mysqli_error($conn)]);
    exit;
}

// Bind de parámetros
mysqli_stmt_bind_param(
    $stmt, 
    "sisssdi",
    $numero_celular,
    $vendedor_id,
    $fecha_inicio,
    $fecha_fin,
    $pago,
    $cuenta_id,
    $dias
);

// Ejecutar consulta
if (mysqli_stmt_execute($stmt)) {
    // Obtener datos de la cuenta
    $sql_cuenta = "SELECT correo, contrasena_gpt FROM cuentas WHERE id = ?";
    $stmt_cuenta = mysqli_prepare($conn, $sql_cuenta);
    mysqli_stmt_bind_param($stmt_cuenta, "i", $cuenta_id);
    mysqli_stmt_execute($stmt_cuenta);
    mysqli_stmt_bind_result($stmt_cuenta, $correo, $contrasena_gpt);
    mysqli_stmt_fetch($stmt_cuenta);
    mysqli_stmt_close($stmt_cuenta);

    // Formatear fechas
    $fecha_ini = date('d/m/Y', strtotime($fecha_inicio));
    $fecha_end = date('d/m/Y', strtotime($fecha_fin));

    // Construir mensaje para portapapeles
    $mensaje = <<<EOD
Datos para ingresar a la cuenta de Chat GPT

Cuenta Chat GPT Plus (tiempo en días: $dias días)
Correo: $correo
Contraseña: $contrasena_gpt

Fecha ini: $fecha_ini
Fecha end: $fecha_end

Cambio de datos para la cuenta de Chat Gpt Plus, ingresa y me confirma por favor

Reglas para el uso de la cuenta:
* No modificar ningún dato de la cuenta. En caso de modificar algún dato, se retirará la cuenta del grupo de trabajo y se perderá el acceso. No se cubrirá la garantía ni el tiempo de servicio.
* Evitar salir de la cuenta.
* Preferentemente, usar la aplicación móvil en el celular y en computadora el navegador Google Chrome (NO usar pestaña de incógnito)

Ingresa ahora por favor y te paso los códigos de activación
EOD;

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
