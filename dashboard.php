<?php
require_once 'config/db.php';
require_once 'config/config.php';
requireLogin(); // Asegurarse de que está logueado

// Consultar estadísticas de cuentas
$sql_cuentas = "SELECT COUNT(*) as total_cuentas, 
                      SUM(costo) as gastos_totales, 
                      SUM(ganancia) as ganancias_totales 
               FROM cuentas";
$resultado_cuentas = mysqli_query($conn, $sql_cuentas);
$datos_cuentas = mysqli_fetch_assoc($resultado_cuentas);

// Consultar total de usuarios (ventas activas)
$sql_usuarios = "SELECT COUNT(*) as total_usuarios 
                FROM ventas 
                WHERE fecha_fin >= CURDATE()";
$resultado_usuarios = mysqli_query($conn, $sql_usuarios);
$datos_usuarios = mysqli_fetch_assoc($resultado_usuarios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Gestión</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Incluir el sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Centralizador</h1>
                </div>
                
                <!-- Primera tabla - Resumen de cuentas -->
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
                            // Consultar cuentas con detalles
                            $sql = "SELECT id, correo, usuarios, costo, ganancia 
                                   FROM cuentas 
                                   ORDER BY id DESC";
                            $resultado = mysqli_query($conn, $sql);
                            
                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>";
                                    echo "<td>" . $fila['id'] . "</td>";
                                    echo "<td>" . htmlspecialchars($fila['correo']) . "</td>";
                                    echo "<td>" . $fila['usuarios'] . "</td>";
                                    echo "<td>$" . number_format($fila['costo'], 2) . "</td>";
                                    echo "<td>$" . number_format($fila['ganancia'] ?? 0, 2) . "</td>";
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
                
                <!-- Segunda tabla - Ventas activas -->
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
                            // Consultar ventas con detalles
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
                                    echo "<td>" . htmlspecialchars($fila['correo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($fila['numero_celular']) . "</td>";
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
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">Nueva venta</button>
                    </div>
                </div>
            </main>
        </div>
    </div>
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
                            <label for="contrasena_correo" class="form-label">Contraseña Correo</label>
                            <input type="password" class="form-control" id="contrasena_correo" name="contrasena_correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena_gpt" class="form-label">Contraseña GPT</label>
                            <input type="password" class="form-control" id="contrasena_gpt" name="contrasena_gpt" required>
                        </div>
                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text" class="form-control" id="codigo" name="codigo">
                        </div>
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="costo" class="form-label">Costo</label>
                            <input type="number" step="0.01" class="form-control" id="costo" name="costo" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="activa" selected>Activa</option>
                                <option value="inactiva">Inactiva</option>
                                <option value="suspendida">Suspendida</option>
                                <option value="baneada">Baneada</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarCuenta">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Nueva Venta -->
    <div class="modal fade" id="nuevaVentaModal" tabindex="-1" aria-labelledby="nuevaVentaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevaVentaModalLabel">Nueva Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaVenta">
                        <div class="mb-3">
                            <label for="numero_celular" class="form-label">Número Celular</label>
                            <input type="text" class="form-control" id="numero_celular" name="numero_celular" required>
                        </div>
                        <div class="mb-3">
                            <label for="cuenta_id" class="form-label">Cuenta</label>
                            <select class="form-select" id="cuenta_id" name="cuenta_id" required>
                                <option value="">Seleccionar cuenta...</option>
                                <?php
                                $sql_cuentas = "SELECT id, correo FROM cuentas WHERE estado = 'activa'";
                                $resultado_cuentas = mysqli_query($conn, $sql_cuentas);
                                while ($cuenta = mysqli_fetch_assoc($resultado_cuentas)) {
                                    echo "<option value='" . $cuenta['id'] . "'>" . htmlspecialchars($cuenta['correo']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio_venta" name="fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <div class="mb-3">
                            <label for="pago" class="form-label">Pago</label>
                            <input type="number" step="0.01" class="form-control" id="pago" name="pago" required>
                        </div>
                        <input type="hidden" name="vendedor_id" value="<?php echo $_SESSION['user_id']; ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarVenta">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botón para guardar nueva cuenta
        document.getElementById('btnGuardarCuenta').addEventListener('click', function() {
            let formData = new FormData(document.getElementById('formNuevaCuenta'));
            
            fetch('modules/cuentas/guardar_cuenta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cuenta guardada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al guardar la cuenta');
            });
        });

        // Botón para guardar nueva venta
        document.getElementById('btnGuardarVenta').addEventListener('click', function() {
            let formData = new FormData(document.getElementById('formNuevaVenta'));
            
            fetch('modules/ventas/guardar_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Venta guardada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al guardar la venta');
            });
        });
    });
    </script>
</body>
</html>
