<?php
// Forzar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../../config/db.php';
require_once '../../config/config.php';

// Establecer timezone explícito
date_default_timezone_set('America/La_Paz');

// Limpiar buffer de salida
ob_start();

// Configurar headers para JSON UTF-8
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Verificar si hay errores de conexión a DB
if (!$conn || mysqli_connect_errno()) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . mysqli_connect_error()]);
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

    // Formatear fechas para el mensaje (usando formato consistente)
    $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    $dia_ini = date('d', strtotime($fecha_inicio));
    $mes_ini = $meses[date('n', strtotime($fecha_inicio)) - 1];
    $ano_ini = date('Y', strtotime($fecha_inicio));
    $dia_fin = date('d', strtotime($fecha_fin));
    $mes_fin = $meses[date('n', strtotime($fecha_fin)) - 1];
    $ano_fin = date('Y', strtotime($fecha_fin));
    
    $fecha_ini = "$dia_ini $mes_ini $ano_ini";
    $fecha_end = "$dia_fin $mes_fin $ano_fin";

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

    // Limpiar todos los niveles de buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $response = [
        'success' => true,
        'message' => 'Venta actualizada correctamente',
        'clipboardText' => $mensaje
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
} else {
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $errorMsg = htmlspecialchars(mysqli_error($conn), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');
    echo json_encode([
        'success' => false, 
        'error' => 'Error al actualizar: ' . $errorMsg
    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
?>
