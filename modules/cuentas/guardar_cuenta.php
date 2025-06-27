<?php
// IMPORTANTE: No debe haber NINGÚN espacio ni línea antes de <?php
require_once '../../config/db.php';
require_once '../../config/config.php';

// Configura cabeceras para JSON
header('Content-Type: application/json');

// Procesa los datos del formulario
try {
    // Obtener datos
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $contrasena_correo = isset($_POST['contrasena_correo']) ? trim($_POST['contrasena_correo']) : '';
    $contrasena_gpt = isset($_POST['contrasena_gpt']) ? trim($_POST['contrasena_gpt']) : '';
    $codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : null;
    $fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : '';
    $fecha_fin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : '';
    $costo = isset($_POST['costo']) ? floatval($_POST['costo']) : 0;
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'activa';
    $usuarios = 0; // Siempre inicia con 0 usuarios
    
    // Validar datos
    if (empty($correo)) {
        throw new Exception("El correo es obligatorio");
    }
    if (empty($contrasena_correo)) {
        throw new Exception("La contraseña del correo es obligatoria");
    }
    if (empty($contrasena_gpt)) {
        throw new Exception("La contraseña de GPT es obligatoria");
    }
    if (empty($fecha_inicio)) {
        throw new Exception("La fecha de inicio es obligatoria");
    }
    
    // Insertar en base de datos
    $sql = "INSERT INTO cuentas (correo, contrasena_correo, contrasena_gpt, codigo, fecha_inicio, fecha_fin, costo, estado, usuarios) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssdsi", $correo, $contrasena_correo, $contrasena_gpt, $codigo, $fecha_inicio, $fecha_fin, $costo, $estado, $usuarios);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error en la base de datos: " . mysqli_error($conn));
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
