<?php
require_once './config/db.php';
require_once './config/config.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Consultar estadísticas
$sql_cuentas = "SELECT COUNT(*) as total_cuentas, SUM(costo) as gastos_totales, SUM(ganancia) as ganancias_totales FROM cuentas";
$resultado_cuentas = mysqli_query($conn, $sql_cuentas);
$datos_cuentas = mysqli_fetch_assoc($resultado_cuentas);

$sql_usuarios = "SELECT COUNT(*) as total_usuarios FROM ventas WHERE fecha_fin >= CURDATE()";
$resultado_usuarios = mysqli_query($conn, $sql_usuarios);
$datos_usuarios = mysqli_fetch_assoc($resultado_usuarios);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Ventas</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/styles.css" rel="stylesheet">
</head>
<body>
<!-- Modal Nueva Cuenta -->
<div class="modal fade" id="nuevaCuentaModal" tabindex="-1" aria-labelledby="nuevaCuentaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevaCuentaModalLabel">Nueva Cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaCuenta">
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="pass_correo" class="form-label">Contraseña Correo</label>
                        <input type="password" class="form-control" id="pass_correo" name="pass_correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="pass_chatgpt" class="form-label">Contraseña ChatGPT</label>
                        <input type="password" class="form-control" id="pass_chatgpt" name="pass_chatgpt" required>
                    </div>
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_ini" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_ini" name="fecha_ini" required>
                    </div>
                    <div class="mb-3">
                        <label for="pago_bs" class="form-label">Pago (Bs)</label>
                        <input type="number" step="0.01" class="form-control" id="pago_bs" name="pago_bs" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCuenta">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <?php include './includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Centralizador</h1>
            </div>
            
            <!-- Tabla de cuentas -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>N°</th>
                            <th>CUENTA</th>
                            <th>USERS</th>
                            <th>GASTO</th>
                            <th>GAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT c.id, c.correo, c.usuarios, c.costo, c.ganancia FROM cuentas c ORDER BY c.id DESC";
                        $resultado = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($resultado) > 0) {
                            while ($fila = mysqli_fetch_assoc($resultado)) {
                                echo "<tr>";
                                echo "<td>" . $fila['id'] . "</td>";
                                echo "<td>" . $fila['correo'] . "</td>";
                                echo "<td>" . $fila['usuarios'] . "</td>";
                                echo "<td>$" . number_format($fila['costo'], 2) . "</td>";
                                echo "<td>$" . number_format($fila['ganancia'], 2) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No hay cuentas registradas</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Contador de cuentas -->
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h1 class="display-1"><?php echo $datos_cuentas['total_cuentas'] ?? 0; ?></h1>
                </div>
                <div>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nuevaCuentaModal">Nueva cuenta</button>
                </div>
            </div>
            
            <!-- Tabla de ventas activas -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>N°</th>
                            <th>CUENTA</th>
                            <th>NUM</th>
                            <th>F INI</th>
                            <th>F END</th>
                            <th>DIAS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT v.id, c.correo, v.numero_celular, v.fecha_inicio, v.fecha_fin, v.dias 
                                FROM ventas v 
                                JOIN cuentas c ON v.cuenta_id = c.id 
                                WHERE v.fecha_fin >= CURDATE()
                                ORDER BY v.fecha_fin ASC";
                        $resultado = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($resultado) > 0) {
                            while ($fila = mysqli_fetch_assoc($resultado)) {
                                echo "<tr>";
                                echo "<td>" . $fila['id'] . "</td>";
                                echo "<td>" . $fila['correo'] . "</td>";
                                echo "<td>" . $fila['numero_celular'] . "</td>";
                                echo "<td>" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "</td>";
                                echo "<td>" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "</td>";
                                echo "<td>" . $fila['dias'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No hay ventas activas</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Contador de usuarios -->
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h1 class="display-1"><?php echo $datos_usuarios['total_usuarios'] ?? 0; ?></h1>
                    <p class="h4">USUARIOS</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary" onclick="location.href='modules/ventas/nueva.php'">Nueva venta</button>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="./assets/js/bootstrap.bundle.min.js"></script>
<?php
// Función para guardar nueva cuenta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_cuenta'])) {
    require_once './config/db.php';
    require_once './config/config.php';

    header('Content-Type: application/json; charset=UTF-8');
    
    // Validar datos
    $required = ['correo', 'pass_correo', 'pass_chatgpt', 'codigo', 'fecha_ini', 'pago_bs'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "El campo $field es requerido"]);
            exit;
        }
    }

    // Procesar datos
    $correo = mysqli_real_escape_string($conn, $_POST['correo']);
    $pass_correo = mysqli_real_escape_string($conn, $_POST['pass_correo']);
    $pass_chatgpt = mysqli_real_escape_string($conn, $_POST['pass_chatgpt']);
    $codigo = mysqli_real_escape_string($conn, $_POST['codigo']);
    $fecha_ini = mysqli_real_escape_string($conn, $_POST['fecha_ini']);
    $pago_bs = floatval($_POST['pago_bs']);
    $costo = $pago_bs * 0.7;
    $ganancia = $pago_bs * 0.3;

    // Insertar en BD
    $sql = "INSERT INTO cuentas (correo, pass_correo, pass_chatgpt, codigo, fecha_ini, pago_bs, costo, ganancia)
            VALUES ('$correo', '$pass_correo', '$pass_chatgpt', '$codigo', '$fecha_ini', $pago_bs, $costo, $ganancia)";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Cuenta creada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear cuenta: ' . mysqli_error($conn)]);
    }
    exit;
}
?>

<script>
// Script para manejar el modal y el formulario
document.getElementById('btnGuardarCuenta').addEventListener('click', async function() {
    const form = document.getElementById('formNuevaCuenta');
    const btnGuardar = this;
    // Crear elemento de feedback si no existe
    let feedback = document.getElementById('feedback-message');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.id = 'feedback-message';
        feedback.className = 'mb-3';
        document.getElementById('formNuevaCuenta').appendChild(feedback);
    }
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Deshabilitar botón durante el envío
    btnGuardar.disabled = true;
    feedback.textContent = 'Guardando...';
    feedback.className = 'text-info';

    try {
        const formData = new FormData(form);
        // Agregar flag para identificar la acción
        formData.append('guardar_cuenta', 'true');
        
        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Error en el servidor');
        }

        feedback.textContent = '✓ Cuenta guardada exitosamente';
        feedback.className = 'text-success';
        
        // Cerrar modal y recargar después de 2 segundos
        setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('modalNuevaCuenta')).hide();
            location.reload();
        }, 2000);

    } catch (error) {
        console.error('Error:', error);
        feedback.textContent = `Error: ${error.message}`;
        feedback.className = 'text-danger';
    } finally {
        btnGuardar.disabled = false;
    }
});
</script>
</body>
</html>
