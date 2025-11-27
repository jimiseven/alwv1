<?php
require_once 'config/db.php';
require_once 'config/config.php';

// Verificar sesión y rol de admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// Consultar estadísticas (todas las cuentas)
$sql_cuentas = "SELECT 
            COUNT(*) as total_cuentas,
            SUM(costo) as gastos_totales,
            (SELECT SUM(pago) FROM ventas) - SUM(costo) as ganancias_totales
            FROM cuentas";
$resultado_cuentas = mysqli_query($conn, $sql_cuentas);
$datos_cuentas = mysqli_fetch_assoc($resultado_cuentas);

$sql_usuarios = "SELECT COUNT(DISTINCT numero_celular) as total_usuarios
            FROM ventas
            WHERE fecha_fin >= CURDATE()";
$resultado_usuarios = mysqli_query($conn, $sql_usuarios);
$datos_usuarios = mysqli_fetch_assoc($resultado_usuarios);

// Estadísticas por vendedor
$sql_vendedores = "SELECT 
                   v.usuario,
                   COUNT(ven.id) as total_ventas,
                   SUM(ven.pago) as total_ingresos,
                   COUNT(DISTINCT ven.numero_celular) as clientes_unicos,
                   SUM(CASE WHEN c.tipo_cuenta LIKE '%perplex%' THEN 1 ELSE 0 END) as ventas_p,
                   SUM(CASE WHEN c.tipo_cuenta LIKE '%gemini%' THEN 1 ELSE 0 END) as ventas_g,
                   SUM(CASE WHEN c.tipo_cuenta NOT LIKE '%perplex%' AND c.tipo_cuenta NOT LIKE '%gemini%' THEN 1 ELSE 0 END) as ventas_c
                   FROM vendedores v
                   LEFT JOIN ventas ven ON v.id = ven.vendedor_id
                   LEFT JOIN cuentas c ON ven.cuenta_id = c.id
                   GROUP BY v.id, v.usuario
                   ORDER BY total_ingresos DESC";
$resultado_vendedores = mysqli_query($conn, $sql_vendedores);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistema ALW</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        /* Reset y base */
        * {
            box-sizing: border-box;
        }

        :root {
            --primary-color: #0d6efd;
            --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .admin-badge {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

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

        /* Sidebar base */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a2530 100%);
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
            width: calc(100% - 260px);
            background: white;
            position: relative;
            z-index: 1;
        }

        /* Mobile styles */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                left: -260px;
                transition: left 0.3s ease;
                box-shadow: none;
            }

            .sidebar.show {
                left: 0;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            .main-content {
                margin-left: 0;
                padding: 70px 15px 15px 15px;
                width: 100%;
                transition: transform 0.3s ease;
            }

            .sidebar.show ~ .main-content {
                transform: translateX(260px);
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
            }

            .mobile-navbar .admin-badge {
                font-size: 0.6rem !important;
                padding: 0.15rem 0.5rem !important;
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

            /* Asegurar que el sidebar esté por encima del backdrop */
            .sidebar {
                z-index: 1000 !important;
                pointer-events: auto !important;
            }
        }

        @media (min-width: 992px) {
            .mobile-nav {
                display: none;
            }

            .sidebar-mobile-backdrop {
                display: none !important;
            }
        }

        /* Asegurar que los modales de Bootstrap estén por encima de todo */
        /* El backdrop debe estar DETRÁS del modal */
        .modal-backdrop {
            z-index: 1040 !important;
            pointer-events: none !important;  /* No debe bloquear clics */
            background-color: rgba(0, 0, 0, 0.5) !important;  /* Semi-transparente */
        }

        .modal-backdrop.show {
            opacity: 0.5 !important;  /* Opacidad reducida */
        }

        .modal {
            z-index: 1055 !important;
            pointer-events: none !important;  /* Solo el contenido debe recibir clics */
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
            box-shadow: 0 10px 30px rgba(0,0,0,.8) !important;  /* Sombra más fuerte */
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
            background: white !important;
        }

        .modal .form-control,
        .modal .form-select {
            background-color: white !important;
            border: 1px solid #ced4da !important;
        }

        .modal .form-control:focus,
        .modal .form-select:focus {
            background-color: white !important;
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        /* Asegurar que el sidebar-backdrop no interfiera con modales */
        .sidebar-mobile-backdrop {
            z-index: 1030 !important;
        }

        /* Cuando hay un modal abierto, ocultar completamente el sidebar-backdrop */
        body.modal-open .sidebar-mobile-backdrop {
            display: none !important;
            pointer-events: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }

        /* Asegurar que el sidebar no interfiera cuando hay modal */
        body.modal-open .sidebar {
            pointer-events: none !important;
        }

        /* Pero permitir clicks en los enlaces del sidebar siempre */
        .sidebar .nav-link {
            pointer-events: auto !important;
            cursor: pointer;
        }

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
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <!-- Navbar móvil -->
        <div class="mobile-navbar mobile-only">
            <button class="btn btn-link text-dark p-0" id="btnSidebarMobile" type="button">
                <i class="bi bi-list"></i>
            </button>
            <h2 class="mb-0"><i class="bi bi-speedometer2"></i> Dashboard Admin <span class="admin-badge">ADMIN</span></h2>
        </div>
        <div class="sidebar-mobile-backdrop" id="sidebarMobileBackdrop"></div>

        <div class="d-flex">
            <?php include 'includes/sidebar.php'; ?>
            <main class="main-content p-4">
                <!-- Header Admin -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1">Panel de Administración</h2>
                        <p class="text-muted">Vista completa del sistema</p>
                    </div>
                    <span class="admin-badge">
                        <i class="bi bi-shield-check"></i> ADMINISTRADOR
                    </span>
                </div>

                <!-- Métricas Generales -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-3">
                        <div class="metric-card">
                            <div class="text-muted small">Total cuentas</div>
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

                <!-- Estadísticas por Vendedor -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="bi bi-people"></i> Rendimiento por Vendedor</h5>
                </div>
                <div class="table-scroll-container">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>Vendedor</th>
                                <th>Total Ventas</th>
                                <th>Ingresos</th>
                                <th>Clientes Únicos</th>
                                <th>ChatGPT Plus (C)</th>
                                <th>Perplexity Pro (P)</th>
                                <th>Gemini (G)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($resultado_vendedores) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado_vendedores)) {
                                    echo "<tr>
                                            <td><strong>" . htmlspecialchars($fila['usuario']) . "</strong></td>
                                            <td>{$fila['total_ventas']}</td>
                                            <td>$" . number_format($fila['total_ingresos'] ?? 0, 2) . "</td>
                                            <td>{$fila['clientes_unicos']}</td>
                                            <td><span class='badge bg-primary'>{$fila['ventas_c']}</span></td>
                                            <td><span class='badge bg-warning text-dark'>{$fila['ventas_p']}</span></td>
                                            <td><span class='badge bg-success'>{$fila['ventas_g']}</span></td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No hay datos de vendedores</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tabla Cuentas -->
                <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                    <h5><a href="modules/cuentas/cuentas.php" class="text-decoration-none"><i class="bi bi-wallet2"></i> Cuentas registradas</a></h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nuevaCuentaModal">
                        <i class="bi bi-plus-circle"></i> Nueva cuenta
                    </button>
                </div>
                <div class="table-scroll-container">
                    <table class="table table-hover align-middle mb-0 sticky-header">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>N°</th>
                                <th>CUENTA</th>
                                <th>USERS ACTIVOS</th>
                                <th>USERS INACTIVOS</th>
                                <th>TOTAL USERS</th>
                                <th>GASTO</th>
                                <th>REC</th>
                                <th>GAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT c.id, c.correo,
                                   (SELECT COUNT(DISTINCT numero_celular) FROM ventas WHERE cuenta_id = c.id AND fecha_fin >= CURDATE()) as usuarios_activos,
                                   (SELECT COUNT(DISTINCT numero_celular) FROM ventas WHERE cuenta_id = c.id AND fecha_fin < CURDATE()) as usuarios_inactivos,
                                   (SELECT COUNT(DISTINCT numero_celular) FROM ventas WHERE cuenta_id = c.id) as usuarios_total,
                                   c.costo,
                                   (SELECT SUM(pago) FROM ventas WHERE cuenta_id = c.id) as recaudado,
                                   (SELECT SUM(pago) FROM ventas WHERE cuenta_id = c.id) - c.costo as ganancia_neta
                                   FROM cuentas c
                                   ORDER BY c.id DESC";
                            $resultado = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>
                                            <td>{$fila['id']}</td>
                                            <td>" . htmlspecialchars($fila['correo']) . "</td>
                                            <td>{$fila['usuarios_activos']}</td>
                                            <td>{$fila['usuarios_inactivos']}</td>
                                            <td>{$fila['usuarios_total']}</td>
                                            <td>$" . number_format($fila['costo'], 2) . "</td>
                                            <td>$" . number_format($fila['recaudado'] ?? 0, 2) . "</td>
                                            <td>$" . number_format($fila['ganancia_neta'] ?? 0, 2) . "</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No hay cuentas registradas</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tabla Ventas -->
                <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                    <h5><a href="modules/ventas/ventas.php" class="text-decoration-none"><i class="bi bi-cart-check"></i> Ventas activas</a></h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
                        <i class="bi bi-cart-plus"></i> Nueva venta
                    </button>
                </div>
                <div class="table-scroll-container">
                    <table class="table table-hover align-middle sticky-header">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>N°</th>
                                <th>CUENTA</th>
                                <th>VENDEDOR</th>
                                <th>NUM</th>
                                <th>F INI</th>
                                <th>F END</th>
                                <th>DIAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.id, c.correo, ven.usuario as vendedor, v.numero_celular, 
                                   v.fecha_inicio, v.fecha_fin, v.dias 
                                   FROM ventas v 
                                   JOIN cuentas c ON v.cuenta_id = c.id 
                                   JOIN vendedores ven ON v.vendedor_id = ven.id
                                   WHERE v.fecha_fin >= CURDATE()
                                   ORDER BY v.fecha_fin ASC";
                            $resultado = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>
                                            <td>{$fila['id']}</td>
                                            <td>" . htmlspecialchars($fila['correo']) . "</td>
                                            <td><span class='badge bg-info'>" . htmlspecialchars($fila['vendedor']) . "</span></td>
                                            <td>" . htmlspecialchars($fila['numero_celular']) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "</td>
                                            <td>{$fila['dias']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No hay ventas activas</td></tr>";
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
                        // Si el click no es en el sidebar ni en el botón de toggle
                        if (!sidebar.contains(e.target) && !btnSidebarMobile.contains(e.target)) {
                            sidebar.classList.remove('show');
                            sidebarBackdrop.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                    }
                });

                // Cerrar sidebar al hacer clic en un enlace del menú (solo en móvil)
                const sidebarLinks = sidebar.querySelectorAll('.nav-link');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        // Solo cerrar en vista móvil
                        if (window.innerWidth < 992) {
                            sidebar.classList.remove('show');
                            sidebarBackdrop.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                    });
                });

                // Cerrar sidebar cuando se abre cualquier modal de Bootstrap
                document.addEventListener('show.bs.modal', function() {
                    if (sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        sidebarBackdrop.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            }

            document.querySelectorAll('.metric-card, .table-responsive').forEach(el => {
                el.style.maxWidth = '100%';
                el.style.width = '100%';
            });
        });
    </script>
</body>
</html>
