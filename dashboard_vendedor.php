<?php
require_once 'config/db.php';
require_once 'config/config.php';

// Verificar sesión y rol de vendedor
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'vendedor') {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$vendedor_id = $_SESSION['user_id'];

// Consultar estadísticas personales del vendedor
$sql_ventas = "SELECT 
            COUNT(*) as total_ventas,
            SUM(pago) as ingresos_totales,
            COUNT(DISTINCT numero_celular) as clientes_unicos
            FROM ventas 
            WHERE vendedor_id = ?";
$stmt_ventas = mysqli_prepare($conn, $sql_ventas);
mysqli_stmt_bind_param($stmt_ventas, "i", $vendedor_id);
mysqli_stmt_execute($stmt_ventas);
$datos_ventas = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_ventas));

// Ventas activas del vendedor
$sql_activas = "SELECT COUNT(*) as ventas_activas
               FROM ventas 
               WHERE vendedor_id = ? AND fecha_fin >= CURDATE()";
$stmt_activas = mysqli_prepare($conn, $sql_activas);
mysqli_stmt_bind_param($stmt_activas, "i", $vendedor_id);
mysqli_stmt_execute($stmt_activas);
$datos_activas = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_activas));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Dashboard - Sistema ALW</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --vendor-color: #10b981;
            --vendor-dark: #059669;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition-speed: 0.3s;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
            color: #333;
            margin: 0;
            padding: 0;
        }

        .vendor-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .metric-card {
            background: white;
            border-radius: 1rem;
            padding: 1.75rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .metric-value {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1f2937;
            margin: 0.5rem 0;
            line-height: 1;
        }

        .metric-label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Sidebar base */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a2530 100%);
            z-index: 1000;
            transition: left 0.3s ease;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
            width: calc(100% - 260px);
            position: relative;
            z-index: 1;
        }

        /* Mobile styles */
        @media (max-width: 991.98px) {
            .sidebar {
                left: -260px;
            }

            .sidebar.show {
                left: 0;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            .main-content {
                margin-left: 0;
                padding: 85px 15px 20px 15px;
                width: 100%;
            }

            .mobile-navbar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1060;
                background: white;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                height: 60px;
                display: flex;
                align-items: center;
                padding: 0 1rem;
            }

            .mobile-navbar h2 {
                font-size: 1.1rem !important;
                margin: 0 !important;
                flex: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                font-weight: 700;
            }

            .mobile-navbar .vendor-badge {
                font-size: 0.65rem !important;
                padding: 0.3rem 0.6rem !important;
            }

            .mobile-only {
                display: block !important;
            }

            .sidebar-mobile-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
                pointer-events: none !important;
            }

            .sidebar-mobile-backdrop.show {
                display: block;
                pointer-events: none !important;
            }

            .sidebar {
                z-index: 1000 !important;
                pointer-events: auto !important;
            }

            .metric-card {
                margin-bottom: 1rem;
            }
        }

        @media (min-width: 992px) {
            .mobile-navbar {
                display: none;
            }

            .sidebar-mobile-backdrop {
                display: none !important;
            }

            .mobile-only {
                display: none !important;
            }

            .desktop-only {
                display: block !important;
            }
        }

        @media (max-width: 991.98px) {
            .desktop-only {
                display: none !important;
            }
        }

        /* Modales */
        .modal-backdrop {
            z-index: 1040 !important;
            pointer-events: none !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .modal-backdrop.show {
            opacity: 0.5 !important;
        }

        .modal {
            z-index: 1055 !important;
            pointer-events: none !important;
        }

        .modal.show {
            pointer-events: auto !important;
        }

        .modal-dialog {
            z-index: 1056 !important;
            position: relative;
            pointer-events: auto !important;
            margin: 1.75rem auto;
        }

        .modal-content {
            z-index: 1057 !important;
            position: relative;
            pointer-events: auto !important;
            background: white !important;
            border: 1px solid rgba(0,0,0,.2);
            box-shadow: 0 10px 30px rgba(0,0,0,.3) !important;
        }

        .modal-body,
        .modal-header,
        .modal-footer {
            pointer-events: auto !important;
            background: white !important;
            position: relative;
            z-index: 2;
        }

        .modal input,
        .modal select,
        .modal textarea,
        .modal button {
            pointer-events: auto !important;
            position: relative;
            z-index: 3;
        }

        body.modal-open .sidebar-mobile-backdrop {
            display: none !important;
            pointer-events: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }

        body.modal-open .sidebar {
            pointer-events: none !important;
        }

        .sidebar .nav-link {
            pointer-events: auto !important;
            cursor: pointer;
        }

        .table-scroll-container {
            max-height: 60vh;
            overflow-y: auto;
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        .table-scroll-container thead th {
            position: sticky;
            top: 0;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem;
            border: none;
            z-index: 2;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f0fdf4;
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

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

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-outline-primary {
            border-color: #10b981;
            color: #10b981;
        }

        .btn-outline-primary:hover {
            background-color: #10b981;
            border-color: #10b981;
            color: white;
        }

        .welcome-section {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 1.25rem;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.25);
            color: white;
        }

        .welcome-section h2 {
            color: white;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .welcome-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.05rem;
        }

        .section-header {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-header h5 {
            margin: 0;
            font-weight: 700;
            color: #1f2937;
            font-size: 1.25rem;
        }

        @media (max-width: 768px) {
            .welcome-section {
                padding: 1.5rem;
            }

            .welcome-section h2 {
                font-size: 1.5rem;
            }

            .section-header {
                padding: 1rem;
            }

            .section-header h5 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <!-- Navbar móvil -->
        <div class="mobile-navbar mobile-only">
            <button class="btn btn-link text-dark p-0" id="btnSidebarMobile" type="button">
                <i class="bi bi-list fs-3"></i>
            </button>
            <h2 class="mb-0"><i class="bi bi-speedometer2"></i> Mi Dashboard</h2>
            <span class="vendor-badge">VENDEDOR</span>
        </div>
        <div class="sidebar-mobile-backdrop" id="sidebarMobileBackdrop"></div>

        <div class="d-flex">
            <?php include 'includes/sidebar.php'; ?>
            <main class="main-content p-4">
                <!-- Bienvenida -->
                <div class="welcome-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">¡Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
                            <p class="text-muted mb-0">Este es tu panel personal de ventas</p>
                        </div>
                        <span class="vendor-badge">
                            <i class="bi bi-person-badge"></i> VENDEDOR
                        </span>
                    </div>
                </div>

                <!-- Métricas Personales -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="bi bi-cart-check-fill"></i>
                            </div>
                            <div class="metric-label">Total Ventas</div>
                            <div class="metric-value"><?= $datos_ventas['total_ventas'] ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="metric-label">Clientes Únicos</div>
                            <div class="metric-value"><?= $datos_ventas['clientes_unicos'] ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div class="metric-label">Ingresos Totales</div>
                            <div class="metric-value">$<?= number_format($datos_ventas['ingresos_totales'] ?? 0, 2) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="metric-card">
                            <div class="metric-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="metric-label">Ventas Activas</div>
                            <div class="metric-value"><?= $datos_activas['ventas_activas'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>

                <!-- Mis Ventas Activas -->
                <div class="section-header">
                    <h5><i class="bi bi-cart-check-fill me-2"></i>Mis Ventas Activas</h5>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
                        <i class="bi bi-plus-circle me-2"></i>Nueva Venta
                    </button>
                </div>
                <div class="table-scroll-container">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>N°</th>
                                <th>CUENTA</th>
                                <th>CLIENTE</th>
                                <th>F INI</th>
                                <th>F END</th>
                                <th>DIAS</th>
                                <th>PAGO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.id, c.correo, v.numero_celular, 
                                   v.fecha_inicio, v.fecha_fin, v.dias, v.pago
                                   FROM ventas v 
                                   JOIN cuentas c ON v.cuenta_id = c.id 
                                   WHERE v.vendedor_id = ? AND v.fecha_fin >= CURDATE()
                                   ORDER BY v.fecha_fin ASC";
                            $stmt = mysqli_prepare($conn, $sql);
                            mysqli_stmt_bind_param($stmt, "i", $vendedor_id);
                            mysqli_stmt_execute($stmt);
                            $resultado = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>
                                            <td>{$fila['id']}</td>
                                            <td>" . htmlspecialchars($fila['correo']) . "</td>
                                            <td>" . htmlspecialchars($fila['numero_celular']) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "</td>
                                            <td>{$fila['dias']}</td>
                                            <td><strong>$" . number_format($fila['pago'], 2) . "</strong></td>
                                            <td>
                                                <button class='btn btn-sm btn-outline-primary copy-btn' 
                                                        data-correo='" . htmlspecialchars($fila['correo']) . "' 
                                                        data-fecha-ini='" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "' 
                                                        data-fecha-fin='" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "' 
                                                        data-dias='{$fila['dias']}'>
                                                    <i class='bi bi-clipboard'></i>
                                                </button>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No tienes ventas activas</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Historial de Ventas -->
                <div class="section-header mt-4">
                    <h5><i class="bi bi-clock-history me-2"></i>Mi Historial de Ventas</h5>
                </div>
                <div class="table-scroll-container">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>N°</th>
                                <th>CUENTA</th>
                                <th>CLIENTE</th>
                                <th>F INI</th>
                                <th>F END</th>
                                <th>DIAS</th>
                                <th>PAGO</th>
                                <th>ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.id, c.correo, v.numero_celular, 
                                   v.fecha_inicio, v.fecha_fin, v.dias, v.pago,
                                   CASE WHEN v.fecha_fin >= CURDATE() THEN 'Activa' ELSE 'Finalizada' END as estado
                                   FROM ventas v 
                                   JOIN cuentas c ON v.cuenta_id = c.id 
                                   WHERE v.vendedor_id = ?
                                   ORDER BY v.fecha_fin DESC
                                   LIMIT 50";
                            $stmt = mysqli_prepare($conn, $sql);
                            mysqli_stmt_bind_param($stmt, "i", $vendedor_id);
                            mysqli_stmt_execute($stmt);
                            $resultado = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    $estado_badge = $fila['estado'] === 'Activa' ? 'bg-success' : 'bg-secondary';
                                    echo "<tr>
                                            <td>{$fila['id']}</td>
                                            <td>" . htmlspecialchars($fila['correo']) . "</td>
                                            <td>" . htmlspecialchars($fila['numero_celular']) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "</td>
                                            <td>{$fila['dias']}</td>
                                            <td><strong>$" . number_format($fila['pago'], 2) . "</strong></td>
                                            <td><span class='badge {$estado_badge}'>{$fila['estado']}</span></td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No tienes ventas registradas</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
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
                                $sql = "SELECT c.id, c.correo, c.tipo_cuenta, COUNT(v.id) AS total_ventas 
            FROM cuentas c 
            LEFT JOIN ventas v ON v.cuenta_id = c.id 
            WHERE c.estado = 'activa' 
            GROUP BY c.id, c.correo, c.tipo_cuenta 
            ORDER BY total_ventas ASC, c.correo ASC";

                                $resultado = mysqli_query($conn, $sql);

                                if (!$resultado) {
                                    echo "<option value=''>Error al cargar cuentas</option>";
                                } else {
                                    while ($cuenta = mysqli_fetch_assoc($resultado)) {
                                        // Determinar letra según tipo
                                        $tipoLetra = '';
                                        $tipoRaw = strtolower(trim($cuenta['tipo_cuenta'] ?? ''));
                                        
                                        if (strpos($tipoRaw, 'perplex') !== false) {
                                          $tipoLetra = 'p';
                                        } elseif (strpos($tipoRaw, 'gemini') !== false) {
                                          $tipoLetra = 'g';
                                        } else {
                                          $tipoLetra = 'c'; // ChatGPT por defecto
                                        }
                                        
                                        echo "<option value='{$cuenta['id']}'>"
                                            . $tipoLetra . " "
                                            . htmlspecialchars($cuenta['correo'])
                                            . " (" . $cuenta['total_ventas'] . " ventas)</option>";
                                    }
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
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Manejar envío de formulario
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
                                mostrarNotificacion('¡Venta registrada!', 'Datos copiados al portapapeles', 'success');
                            } catch (err) {
                                mostrarNotificación(
                                    'Venta registrada',
                                    'Copia manualmente los datos:<br><textarea class="form-control mt-2">' + data.clipboardText + '</textarea>',
                                    'warning'
                                );
                            }
                        }
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        mostrarNotificación('Error', data.error || 'Ocurrió un error', 'danger');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarNotificación('Error', 'Error de conexión', 'danger');
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

            // Botones de copiar
            document.addEventListener('click', function(e) {
                if (e.target.closest('.copy-btn')) {
                    const btn = e.target.closest('.copy-btn');
                    const texto = `Cuenta: ${btn.dataset.correo}
Fecha inicio: ${btn.dataset.fechaIni}
Fecha fin: ${btn.dataset.fechaFin}
Días: ${btn.dataset.dias}`;
                    
                    navigator.clipboard.writeText(texto).then(() => {
                        mostrarNotificación('¡Copiado!', 'Datos copiados al portapapeles', 'success');
                    }).catch(() => {
                        mostrarNotificación('Error', 'No se pudo copiar', 'danger');
                    });
                }
            });

            // Sidebar móvil
            const sidebar = document.querySelector('.sidebar');
            const sidebarBackdrop = document.getElementById('sidebarMobileBackdrop');
            const btnSidebarMobile = document.getElementById('btnSidebarMobile');

            if (btnSidebarMobile && sidebar && sidebarBackdrop) {
                btnSidebarMobile.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarBackdrop.classList.toggle('show');
                    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                });

                // Cerrar sidebar al hacer click fuera de él
                document.addEventListener('click', function(e) {
                    if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
                        if (!sidebar.contains(e.target) && !btnSidebarMobile.contains(e.target)) {
                            sidebar.classList.remove('show');
                            sidebarBackdrop.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                    }
                });

                // Cerrar sidebar al hacer clic en enlaces
                const sidebarLinks = sidebar.querySelectorAll('.nav-link');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 992) {
                            sidebar.classList.remove('show');
                            sidebarBackdrop.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                    });
                });
            }

            // Cerrar sidebar cuando se abre un modal
            document.addEventListener('show.bs.modal', function() {
                if (sidebar && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });

            // Ajustar ancho de elementos
            document.querySelectorAll('.metric-card, .table-responsive').forEach(el => {
                el.style.maxWidth = '100%';
                el.style.width = '100%';
            });
        });
    </script>
</body>
</html>
