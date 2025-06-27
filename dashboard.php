<?php
require_once 'config/db.php';
require_once 'config/config.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// Consultar estadísticas (solo cuentas activas)
$sql_cuentas = "SELECT COUNT(*) as total_cuentas,
            SUM(costo) as gastos_totales,
            SUM(ganancia) as ganancias_totales,
            (SELECT COUNT(*) FROM ventas WHERE fecha_fin >= CURDATE() AND cuenta_id = cuentas.id) as total_usuarios
            FROM cuentas
            WHERE estado = 'activa'";
$resultado_cuentas = mysqli_query($conn, $sql_cuentas);
$datos_cuentas = mysqli_fetch_assoc($resultado_cuentas);

$sql_usuarios = "SELECT COUNT(DISTINCT cuenta_id) as total_usuarios
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        /* Estilos personalizados para mejorar UI y UX */
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #f8f9fa;
            --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Diseño responsive mejorado */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: -100%;
                width: 80%;
                transition: all 0.3s ease;
                z-index: 1000;
                background: white;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }

            .sidebar.active {
                left: 0;
            }

            .content-padding-top {
                padding-top: 20px;
            }

            /* Métricas en móvil */
            .metric-card {
                margin-bottom: 1rem;
            }

            /* Tablas responsive */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin-bottom: 1rem;
            }

            table {
                font-size: 0.85rem;
                min-width: 600px;
            }

            /* Botón menú móvil */
            .mobile-menu-btn {
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 1001;
                background: var(--primary-color);
                color: white;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }
        }

        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 999;
            display: none;
        }

        .main-content {
            margin-left: 0 !important;
        }
        }

        @media (min-width: 992px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                width: 260px;
                background: white;
                padding-top: 1rem;
                overflow-y: auto;
            }

            .main-content {
                margin-left: 260px;
            }

            .mobile-nav {
                display: none;
            }
        }

        /* Contenedores de tablas con scroll independiente */
        .table-scroll-container {
            max-height: 60vh;
            overflow-y: auto;
            background: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        .table-scroll-container thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 2;
        }

        /* Scrollbar personalizada */
        .table-scroll-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-scroll-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .table-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .table-scroll {
            overflow-x: auto;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Diseño de tarjetas de métricas */
        .metric-card {
            background: white;
            border-radius: 8px;
            padding: 1.25rem;
            height: 100%;
            box-shadow: var(--card-shadow);
            transition: transform var(--transition-speed);
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .metric-title {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .metric-value {
            font-size: 1.75rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        /* Botones mejorados */
        .btn {
            min-height: 44px;
            min-width: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Tarjetas de métricas mejoradas */
        .metric-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0.5rem 0;
        }

        /* Mejora para tablas responsive */
        .table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 1;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        /* Navegación móvil */
        .mobile-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: white;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            padding: 0 1rem;
            z-index: 990;
        }

        .content-padding-top {
            padding-top: 70px;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <button class="mobile-menu-btn d-lg-none" id="mobileMenuBtn">
            <i class="bi bi-list"></i>
        </button>
        <div class="d-flex">
            <?php include 'includes/sidebar.php'; ?>
            <main class="flex-grow-1 p-4" style="overflow-y: auto; height: 100vh;">
                <!-- Métricas -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-3">
                        <div class="metric-card">
                            <div class="text-muted small">Cuentas activas</div>
                            <div class="h2"><?= $datos_cuentas['total_cuentas'] ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="metric-card">
                            <div class="text-muted small">Usuarios activos</div>
                            <div class="h2"><?= $datos_usuarios['total_usuarios'] ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="metric-card">
                            <div class="text-muted small">Gasto total</div>
                            <div class="h2">$<?= number_format($datos_cuentas['gastos_totales'] ?? 0, 2) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="metric-card">
                            <div class="text-muted small">Ganancia total</div>
                            <div class="h2">$<?= number_format($datos_cuentas['ganancias_totales'] ?? 0, 2) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Tabla Cuentas -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Cuentas registradas</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nuevaCuentaModal">
                        <i class="bi bi-plus-circle"></i> Nueva cuenta
                    </button>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 sticky-header">
                        <thead class="sticky-top bg-light">
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
                            $sql = "SELECT c.id, c.correo,
                                   (SELECT COUNT(DISTINCT numero_celular) FROM ventas WHERE cuenta_id = c.id) as usuarios,
                                   c.costo,
                                   (SELECT SUM(pago) FROM ventas WHERE cuenta_id = c.id) as ganancia_total
                                   FROM cuentas c
                                   ORDER BY c.id DESC";
                            $resultado = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>
                                            <td>{$fila['id']}</td>
                                            <td>" . htmlspecialchars($fila['correo']) . "</td>
                                            <td>{$fila['usuarios']}</td>
                                            <td>$" . number_format($fila['costo'], 2) . "</td>
                                            <td>$" . number_format($fila['ganancia_total'] ?? 0, 2) . "</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No hay cuentas registradas</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tabla Ventas -->
                <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                    <h5>Ventas activas</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
                        <i class="bi bi-cart-plus"></i> Nueva venta
                    </button>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle sticky-header">
                        <thead class="sticky-top bg-light">
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
            </main>
        </div>
    </div>
    <!-- Modales -->
    <!-- Modal Nueva Cuenta -->
    <div class="modal fade" id="nuevaCuentaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nueva Cuenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNuevaCuenta">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">contrasena Correo</label>
                            <input type="password" class="form-control" name="contrasena_correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">contrasena GPT</label>
                            <input type="password" class="form-control" name="contrasena_gpt" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Código</label>
                            <input type="text" class="form-control" name="codigo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Costo</label>
                            <input type="number" step="0.01" class="form-control" name="costo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado" required>
                                <option value="activa" selected>Activa</option>
                                <option value="inactiva">Inactiva</option>
                                <option value="suspendida">Suspendida</option>
                            </select>
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

    <!-- Modal Nueva Venta -->
    <div class="modal fade" id="nuevaVentaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-cart-check me-2"></i>Nueva Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                // Consulta corregida
                                $sql = "SELECT c.id, c.correo, COUNT(v.id) AS total_ventas 
            FROM cuentas c 
            LEFT JOIN ventas v ON v.cuenta_id = c.id 
            WHERE c.estado = 'activa' 
            GROUP BY c.id 
            ORDER BY c.correo";

                                $resultado = mysqli_query($conn, $sql);

                                if (!$resultado) {
                                    die("Error en la consulta: " . mysqli_error($conn));
                                }

                                while ($cuenta = mysqli_fetch_assoc($resultado)) {
                                    echo "<option value='{$cuenta['id']}'>"
                                        . htmlspecialchars($cuenta['correo'])
                                        . " (" . $cuenta['total_ventas'] . " ventas)</option>";
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
        document.addEventListener('DOMContentLoaded', function () {
            // Manejar envío de formularios
            document.getElementById('formNuevaCuenta').addEventListener('submit', function (e) {
                e.preventDefault();
                enviarFormulario(this, 'modules/cuentas/guardar_cuenta.php');
            });

            document.getElementById('formNuevaVenta').addEventListener('submit', function (e) {
                e.preventDefault();
                enviarFormulario(this, 'modules/ventas/guardar_venta.php');
            });

            async function enviarFormulario(form, url) {
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        if (data.clipboardText) {
                            try {
                                await navigator.clipboard.writeText(data.clipboardText);
                                mostrarNotificacion('¡Copiado!', 'Los datos se copiaron al portapapeles', 'success');
                            } catch (err) {
                                mostrarNotificacion(
                                    'Atención',
                                    'No se pudo copiar automáticamente. Copia manualmente:<br><textarea class="form-control mt-2">' + data.clipboardText + '</textarea>',
                                    'warning'
                                );
                            }
                        }
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        mostrarNotificacion('Error', data.error || 'Ocurrió un error', 'danger');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarNotificacion('Error', 'Error de conexión', 'danger');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Guardar';
                }
            }

            function mostrarNotificacion(titulo, mensaje, tipo) {
                const container = document.createElement('div');
                container.innerHTML = `
                <div class="toast align-items-center text-white bg-${tipo} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <strong>${titulo}</strong><br>${mensaje}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

                document.body.appendChild(container);
                new bootstrap.Toast(container.querySelector('.toast'), { autohide: true, delay: 5000 }).show();
                setTimeout(() => container.remove(), 6000);
            }
        });
        // Menú móvil
        document.getElementById('mobileMenuBtn').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>

</html>
