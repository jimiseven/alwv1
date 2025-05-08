<?php
require_once 'config/db.php';
require_once 'config/config.php';
requireLogin();

// Consultar estadísticas
$sql_cuentas = "SELECT COUNT(*) as total_cuentas, 
                SUM(costo) as gastos_totales, 
                SUM(ganancia) as ganancias_totales 
                FROM cuentas";
$resultado_cuentas = mysqli_query($conn, $sql_cuentas);
$datos_cuentas = mysqli_fetch_assoc($resultado_cuentas);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Estilos personalizados para mejorar UI y UX */
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #f8f9fa;
            --card-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            --transition-speed: 0.3s;
        }
        
        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        /* Mejora para sidebar en móvil */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 1000;
                width: 260px;
                background: white;
                transform: translateX(-100%);
                transition: transform var(--transition-speed) ease;
                overflow-y: auto;
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.3);
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
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
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
        
        /* Mejoras en botones para touch */
        .btn {
            min-height: 44px;
            min-width: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all var(--transition-speed);
        }
        
        /* Mejora para tablas responsive */
        .table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 1;
            box-shadow: 0 1px 0 rgba(0,0,0,0.1);
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
            box-shadow: 0 1px 0 rgba(0,0,0,0.1);
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
    <!-- Navegación móvil -->
    <nav class="mobile-nav d-lg-none">
        <button class="btn btn-link text-dark p-0 me-3" id="sidebarToggle">
            <i class="bi bi-list" style="font-size: 1.5rem;"></i>
        </button>
        <div class="fw-bold fs-4">ALW</div>
    </nav>

    <!-- Sidebar y backdrop para móvil -->
    <div class="sidebar-backdrop d-lg-none"></div>
    <div class="sidebar">
        <div class="p-3 border-bottom">
            <h3 class="mb-0">ALW</h3>
        </div>
        <ul class="nav flex-column p-3">
            <li class="nav-item mb-2">
                <a class="nav-link active d-flex align-items-center" href="#">
                    <i class="bi bi-speedometer2 me-2"></i> Centralizador
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center" href="#">
                    <i class="bi bi-person-badge me-2"></i> Cuentas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center" href="#">
                    <i class="bi bi-cart me-2"></i> Ventas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center" href="#">
                    <i class="bi bi-graph-up me-2"></i> Estado económico
                </a>
            </li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <main class="main-content content-padding-top p-3 p-lg-4">
        <!-- Tarjetas de métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="metric-card">
                    <div class="metric-title">Cuentas</div>
                    <div class="metric-value"><?= $datos_cuentas['total_cuentas'] ?? 0 ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-card">
                    <div class="metric-title">Usuarios activos</div>
                    <div class="metric-value"><?= $datos_usuarios['total_usuarios'] ?? 0 ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-card">
                    <div class="metric-title">Gasto total</div>
                    <div class="metric-value">$<?= number_format($datos_cuentas['gastos_totales'] ?? 0, 2) ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-card">
                    <div class="metric-title">Ganancia total</div>
                    <div class="metric-value">$<?= number_format($datos_cuentas['ganancias_totales'] ?? 0, 2) ?></div>
                </div>
            </div>
        </div>
        <!-- Sección Cuentas -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Cuentas registradas</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaCuentaModal">
                <i class="bi bi-plus-circle me-2"></i>Nueva cuenta
            </button>
        </div>
        
        <div class="table-responsive-custom">
            <table class="table table-hover align-middle mb-0">
                    <thead>
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
        </div>
        
        <!-- Sección Ventas -->
        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <h5 class="mb-0">Ventas activas</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
                <i class="bi bi-cart-plus me-2"></i>Nueva venta
            </button>
        </div>
        
        <div class="table-responsive-custom">
            <table class="table table-hover align-middle mb-0">
                    <thead>
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
        </div>
    </main>

    <!-- Modal Nueva Cuenta -->
    <div class="modal fade" id="nuevaCuentaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nueva Cuenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNuevaCuenta">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control form-control-lg" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña Correo</label>
                            <input type="password" class="form-control form-control-lg" name="contrasena_correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña GPT</label>
                            <input type="password" class="form-control form-control-lg" name="contrasena_gpt" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control form-control-lg" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Costo</label>
                                <input type="number" step="0.01" class="form-control form-control-lg" name="costo" required>
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
                            <input type="text" class="form-control form-control-lg" name="numero_celular" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cuenta</label>
                            <select class="form-select form-select-lg" name="cuenta_id" required>
                                <option value="">Seleccionar cuenta...</option>
                                <?php
                                $sql = "SELECT id, correo, usuarios FROM cuentas WHERE estado = 'activa' ORDER BY correo";
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
                                <input type="date" class="form-control form-control-lg" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control form-control-lg" name="fecha_fin" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pago</label>
                            <input type="number" step="0.01" class="form-control form-control-lg" name="pago" required>
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
        // Manejo del sidebar en móvil
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const backdrop = document.querySelector('.sidebar-backdrop');
        
        function toggleSidebar() {
            sidebar.classList.toggle('show');
            if (sidebar.classList.contains('show')) {
                backdrop.style.display = 'block';
                document.body.style.overflow = 'hidden';
            } else {
                backdrop.style.display = 'none';
                document.body.style.overflow = '';
            }
        }
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
        
        if (backdrop) {
            backdrop.addEventListener('click', toggleSidebar);
        }

        // Manejar envío de formularios
        document.getElementById('formNuevaCuenta').addEventListener('submit', function(e) {
            e.preventDefault();
            enviarFormulario(this, 'modules/cuentas/guardar_cuenta.php');
        });

        document.getElementById('formNuevaVenta').addEventListener('submit', function(e) {
            e.preventDefault();
            enviarFormulario(this, 'modules/ventas/guardar_venta.php');
        });

        function enviarFormulario(form, url) {
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Deshabilitar el botón y mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando...';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar notificación de éxito
                    mostrarNotificacion('Operación exitosa', data.message || 'Se guardó correctamente', 'success');
                    form.reset();
                    
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                    if (modal) modal.hide();
                    
                    // Copiar datos al portapapeles si existen
                    if (data.datos_cuenta) {
                        navigator.clipboard.writeText(data.datos_cuenta)
                            .then(() => {
                                mostrarNotificacion('Copiado', 'Los datos de la cuenta se copiaron al portapapeles', 'info');
                            })
                            .catch(err => {
                                console.error('Error al copiar:', err);
                            });
                    }
                    
                    // Recargar datos
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    mostrarNotificacion('Error', data.error || 'Ocurrió un error', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error', 'Ocurrió un error al procesar la solicitud', 'danger');
            })
            .finally(() => {
                // Restaurar el botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Guardar';
            });
        }
        
        // Sistema de notificaciones
        function mostrarNotificacion(titulo, mensaje, tipo) {
            // Crear elemento de notificación
            const notif = document.createElement('div');
            notif.className = `toast align-items-center text-white bg-${tipo} border-0`;
            notif.setAttribute('role', 'alert');
            notif.setAttribute('aria-live', 'assertive');
            notif.setAttribute('aria-atomic', 'true');
            
            notif.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${titulo}</strong>: ${mensaje}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            `;
            
            // Agregar al contenedor de notificaciones
            let container = document.querySelector('.toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                document.body.appendChild(container);
            }
            
            container.appendChild(notif);
            
            // Inicializar y mostrar toast
            const toast = new bootstrap.Toast(notif, {
                autohide: true,
                delay: 5000
            });
            
            toast.show();
        }
    });
    </script>
</body>
</html>
