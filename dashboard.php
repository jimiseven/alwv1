<?php
require_once 'config/db.php';
require_once 'config/config.php';
requireLogin();

// Consultar estadísticas de cuentas
$sql_cuentas = "SELECT COUNT(*) as total_cuentas, 
                SUM(costo) as gastos_totales, 
                SUM(ganancia) as ganancias_totales 
                FROM cuentas";
$resultado_cuentas = mysqli_query($conn, $sql_cuentas);
$datos_cuentas = mysqli_fetch_assoc($resultado_cuentas);

// Consultar ventas activas
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
    <title>Dashboard - Sistema ALW</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Centralizador</h1>
                </div>

                <!-- Tabla de Cuentas -->
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
                            $sql = "SELECT id, correo, usuarios, costo, ganancia 
                                   FROM cuentas 
                                   ORDER BY id DESC";
                            $resultado = mysqli_query($conn, $sql);
                            
                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>
                                            <td>{$fila['id']}</td>
                                            <td>" . htmlspecialchars($fila['correo']) . "</td>
                                            <td>{$fila['usuarios']}</td>
                                            <td>$" . number_format($fila['costo'], 2) . "</td>
                                            <td>$" . number_format($fila['ganancia'] ?? 0, 2) . "</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No hay cuentas registradas</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Contador Cuentas -->
                <div class="d-flex justify-content-between mb-4">
                    <div>
                        <h1 class="display-1"><?= $datos_cuentas['total_cuentas'] ?? 0 ?></h1>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nuevaCuentaModal">
                            <i class="bi bi-plus-circle"></i> Nueva cuenta
                        </button>
                    </div>
                </div>

                <!-- Tabla de Ventas -->
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
                            $sql = "SELECT v.id, c.correo, v.numero_celular, 
                                   v.fecha_inicio, v.fecha_fin, v.dias 
                                   FROM ventas v 
                                   JOIN cuentas c ON v.cuenta_id = c.id 
                                   WHERE v.fecha_fin >= CURDATE()
                                   ORDER BY v.fecha_fin ASC";
                            $resultado = mysqli_query($conn, $sql);
                            
                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>
                                            <td>{$fila['id']}</td>
                                            <td>" . htmlspecialchars($fila['correo']) . "</td>
                                            <td>" . htmlspecialchars($fila['numero_celular']) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "</td>
                                            <td>{$fila['dias']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No hay ventas activas</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Contador Usuarios -->
                <div class="d-flex justify-content-between mb-4">
                    <div>
                        <h1 class="display-1"><?= $datos_usuarios['total_usuarios'] ?? 0 ?></h1>
                        <p class="h4">USUARIOS</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
                            <i class="bi bi-cart-plus"></i> Nueva venta
                        </button>
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
                    <h5 class="modal-title" id="nuevaCuentaModalLabel"><i class="bi bi-person-plus"></i> Nueva Cuenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="formNuevaCuenta">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña Correo</label>
                            <input type="password" class="form-control" name="contrasena_correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña GPT</label>
                            <input type="password" class="form-control" name="contrasena_gpt" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Costo</label>
                                <input type="number" step="0.01" class="form-control" name="costo" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Venta -->
    <div class="modal fade" id="nuevaVentaModal" tabindex="-1" aria-labelledby="nuevaVentaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevaVentaModalLabel"><i class="bi bi-cart-check"></i> Nueva Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="formNuevaVenta">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Número Celular</label>
                            <input type="text" class="form-control" name="numero_celular" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cuenta</label>
                            <select class="form-select" name="cuenta_id" required>
                                <option value="">Seleccionar cuenta...</option>
                                <?php
                                $sql = "SELECT id, correo, usuarios FROM cuentas ORDER BY correo";
                                $resultado = mysqli_query($conn, $sql);
                                while ($cuenta = mysqli_fetch_assoc($resultado)) {
                                    echo "<option value='{$cuenta['id']}'>" 
                                       . htmlspecialchars($cuenta['correo']) 
                                       . " ({$cuenta['usuarios']} usuarios)</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" name="fecha_fin" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pago</label>
                            <input type="number" step="0.01" class="form-control" name="pago" required>
                        </div>
                        <input type="hidden" name="vendedor_id" value="<?= $_SESSION['user_id'] ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar envío de nueva cuenta
        document.getElementById('formNuevaCuenta').addEventListener('submit', function(e) {
            e.preventDefault();
            enviarFormulario(this, 'modules/cuentas/guardar_cuenta.php');
        });

        // Manejar envío de nueva venta
        document.getElementById('formNuevaVenta').addEventListener('submit', function(e) {
            e.preventDefault();
            enviarFormulario(this, 'modules/ventas/guardar_venta.php');
        });

        function enviarFormulario(form, url) {
            const formData = new FormData(form);
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    form.reset();
                    window.location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        }
    });
    </script>
</body>
</html>
