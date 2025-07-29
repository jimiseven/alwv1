<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuentas - Sistema ALW</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .table-container {
            max-height: 500px;
            overflow-y: auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
        }

    /* Sidebar responsive */
    @media (max-width: 767px) {
        .sidebar {
            position: fixed;
            top: 0;
            left: -260px;
            bottom: 0;
            width: 260px;
            z-index: 1050;
            transition: left 0.3s;
        }

        .sidebar.show {
            left: 0 !important;
        }

        .sidebar-mobile-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1040;
        }

        .sidebar-mobile-backdrop.show {
            display: block;
        }

        .mobile-navbar {
            display: flex;
            align-items: center;
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 0 1rem;
            margin-bottom: 1rem;
            position: sticky;
            top: 0;
            z-index: 1060;
        }

        .mobile-navbar .btn {
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .mobile-navbar h2 {
            font-size: 1.2rem;
            margin: 0;
        }
    }

    @media (min-width: 768px) {
        .mobile-only {
            display: none !important;
        }
    }

    .sidebar {
        min-height: 100vh;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

        .nav-link:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.1) !important;
            transform: translateX(5px);
        }

        .bg-danger-hover:hover {
            background-color: #dc3545 !important;
        }

        .active.bg-primary {
            color: #fff !important;
        }
        
        .cuenta-vencida {
            border: 2px solid #dc3545;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Navbar móvil -->
        <div class="mobile-navbar mobile-only">
            <button class="btn btn-link text-dark p-0" id="btnSidebarMobile" type="button">
                <i class="bi bi-list"></i>
            </button>
            <h2 class="mb-0"><i class="bi bi-person"></i> Cuentas</h2>
        </div>
        <div class="sidebar-mobile-backdrop" id="sidebarMobileBackdrop"></div>

        <div class="row">
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="bi bi-person me-2"></i>Listado de cuentas</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaCuentaModal">
                        <i class="bi bi-plus-circle"></i> Nueva cuenta
                    </button>
                </div>
                <div class="table-container">
                    <table class="table table-hover align-middle">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Tipo</th>
                                <th>Correo</th>
                                <th>
                                    Fecha inicio 
                                    <button class="btn btn-sm btn-link p-0 ms-1 sort-btn" data-column="fecha_inicio">
                                        <i class="bi bi-arrow-down-up"></i>
                                    </button>
                                </th>
                                <th>Fecha fin</th>
                                <th>
                                    Días
                                    <button class="btn btn-sm btn-link p-0 ms-1 sort-btn" data-column="dias">
                                        <i class="bi bi-arrow-down-up"></i>
                                    </button>
                                </th>
                                <th>
                                    Usuarios Activos
                                    <button class="btn btn-sm btn-link p-0 ms-1 sort-btn" data-column="usuarios_activos">
                                        <i class="bi bi-arrow-down-up"></i>
                                    </button>
                                </th>
                                <th>Usuarios Inactivos</th>
                                <th>Total Usuarios</th>
                                <th>Gasto</th>
                                <th>Ganancia</th>
                                <th>Estado</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $column = $_GET['column'] ?? 'id';
                            $order = $_GET['order'] ?? 'desc';
                            
                            // Mapear columnas a campos de la base de datos
                            $fieldMap = [
                                'fecha_inicio' => 'fecha_inicio',
                                'dias' => 'fecha_fin', // Ordenamos por fecha_fin para los días
                                'usuarios_activos' => 'usuarios_activos',
                                'id' => 'id'
                            ];
                            
                            $field = $fieldMap[$column] ?? 'id';
                            $orderBy = "$field $order";
                            
                            $sql = "SELECT c.*, 
                                   (SELECT COUNT(DISTINCT numero_celular) FROM ventas WHERE cuenta_id = c.id AND fecha_fin >= CURDATE()) as usuarios_activos,
                                   (SELECT COUNT(DISTINCT numero_celular) FROM ventas WHERE cuenta_id = c.id AND fecha_fin < CURDATE()) as usuarios_inactivos,
                                   (SELECT COUNT(*) FROM ventas WHERE cuenta_id = c.id) as total_ventas,
                                   (SELECT SUM(pago) FROM ventas WHERE cuenta_id = c.id) - c.costo as ganancia,
                                   CASE 
                                     WHEN c.tipo_cuenta = 'gpt' THEN 'c'
                                     WHEN c.tipo_cuenta = 'gemini' THEN 'g' 
                                     WHEN c.tipo_cuenta = 'perplexity' THEN 'p'
                                     ELSE 'x'
                                   END as tipo_cuenta_abrev
                                   FROM cuentas c
                                   ORDER BY $orderBy";
                            $resultado = mysqli_query($conn, $sql);
                            $total_gasto = 0;
                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    $fecha_ini = $fila['fecha_inicio'];
                                    $fecha_fin = $fila['fecha_fin'];
                                    if (!$fecha_fin && $fecha_ini) {
                                        $fecha_fin_calc = date('Y-m-d', strtotime($fecha_ini . ' +30 days'));
                                        $fecha_fin_mostrar = date('d/m/Y', strtotime($fecha_fin_calc));
                                    } elseif ($fecha_fin) {
                                        $fecha_fin_mostrar = date('d/m/Y', strtotime($fecha_fin));
                                    } else {
                                        $fecha_fin_mostrar = '';
                                    }
                                    $dias = '';
                                    if ($fecha_ini && ($fecha_fin || isset($fecha_fin_calc))) {
                                        $fin = $fecha_fin ?: $fecha_fin_calc;
                                        $dias = (strtotime($fin) - time()) / (60 * 60 * 24);
                                        $dias = floor($dias); // Redondear hacia abajo
                                    }
                                    $total_gasto += floatval($fila['costo']);
                                    $estado_activo = $fila['estado'] === 'activa';
                                    $estado_btn = $estado_activo ? 'btn-success' : 'btn-secondary';
                                    $estado_txt = $estado_activo ? 'Activa' : 'Inactiva';
                                    $fecha_fin_comparar = $fila['fecha_fin'] ?: date('Y-m-d', strtotime($fila['fecha_inicio'] . ' +30 days'));
                                    $claseVencida = strtotime($fecha_fin_comparar) < time() ? 'cuenta-vencida' : '';
                                    echo "<tr class='$claseVencida'>
                                    <td>{$fila['id']}</td>
                                    <td class='text-center'>{$fila['tipo_cuenta_abrev']}</td>
                                    <td>" . htmlspecialchars($fila['correo']) . "</td>
                                    <td>" . ($fecha_ini ? date('d/m/Y', strtotime($fecha_ini)) : '') . "</td>
                                    <td>$fecha_fin_mostrar</td>
                                    <td>" . ($dias !== '' ? intval($dias) : '') . "</td>
                                    <td>{$fila['usuarios_activos']}</td>
                                    <td>{$fila['usuarios_inactivos']}</td>
                                    <td>{$fila['total_ventas']}</td>
                                    <td>$" . number_format($fila['costo'], 2) . "</td>
                                    <td>$" . number_format($fila['ganancia'] ?? 0, 2) . "</td>
                                    <td>
                                        <button class='btn btn-sm $estado_btn toggle-estado' 
                                            data-id='{$fila['id']}' data-estado='{$fila['estado']}'>
                                            $estado_txt
                                        </button>
                                    </td>
                                    <td>
                                        <button class='btn btn-sm btn-warning edit-cuenta' 
                                            data-id='{$fila['id']}'
                                            data-correo='" . htmlspecialchars($fila['correo'], ENT_QUOTES) . "'
                                            data-contrasena-correo='" . htmlspecialchars($fila['contrasena_correo'], ENT_QUOTES) . "'
                                            data-contrasena-gpt='" . htmlspecialchars($fila['contrasena_gpt'], ENT_QUOTES) . "'
                                            data-codigo='" . htmlspecialchars($fila['codigo'] ?? '', ENT_QUOTES) . "'
                                            data-fecha_inicio='{$fila['fecha_inicio']}'
                data-costo='{$fila['costo']}'
                data-estado='{$fila['estado']}'
                data-tipo_cuenta='{$fila['tipo_cuenta']}'
                                        >
                                            <i class='bi bi-pencil'></i>
                                        </button>
                                    </td>
                                    <td>
                                        <button class='btn btn-sm btn-danger delete-cuenta' data-id='{$fila['id']}'>
                                            <i class='bi bi-trash'></i>
                                        </button>
                                    </td>
                                  </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='12' class='text-center'>No hay cuentas registradas</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="9" class="text-end">Total gasto:</th>
                                <th colspan="5" class="text-start">$<?php echo number_format($total_gasto, 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </main>
        </div>

        <!-- Modal de confirmación para eliminar cuenta -->
        <div class="modal fade" id="modalEliminarCuenta" tabindex="-1" aria-labelledby="modalEliminarCuentaLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEliminarCuentaLabel">Eliminar cuenta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que deseas eliminar esta cuenta? Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="btnConfirmarEliminarCuenta">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Editar Cuenta -->
    <div class="modal fade" id="editarCuentaModal" tabindex="-1" aria-labelledby="editarCuentaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditarCuenta">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarCuentaModalLabel"><i class="bi bi-pencil me-2"></i>Editar Cuenta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" name="correo" id="edit-correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña Correo</label>
                            <input type="text" class="form-control" name="contrasena_correo" id="edit-contrasena-correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña GPT</label>
                            <input type="text" class="form-control" name="contrasena_gpt" id="edit-contrasena-gpt" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Código</label>
                            <input type="text" class="form-control" name="codigo" id="edit-codigo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="edit-fecha_inicio" required>
                            <input type="hidden" name="fecha_fin" id="edit-fecha_fin">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Costo</label>
                            <input type="number" step="0.01" class="form-control" name="costo" id="edit-costo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Cuenta</label>
                            <select class="form-select" name="tipo_cuenta" id="edit-tipo_cuenta">
                                <option value="gpt">ChatGPT (c)</option>
                                <option value="gemini">Gemini (g)</option>
                                <option value="perplexity">Perplexity (p)</option>
                                <option value="">Ninguno (x)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado" id="edit-estado">
                                <option value="activa">Activa</option>
                                <option value="inactiva">Inactiva</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Manejar ordenamiento por columnas
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const column = this.dataset.column;
                const url = new URL(window.location.href);
                const currentOrder = url.searchParams.get('order');
                const currentColumn = url.searchParams.get('column');
                
                // Alternar entre asc y desc si es la misma columna
                if (currentColumn === column) {
                    const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
                    url.searchParams.set('order', newOrder);
                } else {
                    // Nueva columna, orden ascendente por defecto
                    url.searchParams.set('column', column);
                    url.searchParams.set('order', 'asc');
                }
                
                window.location.href = url.toString();
            });
        });

        // Resaltar columna ordenada actual
        const currentColumn = '<?php echo $_GET["column"] ?? ""; ?>';
        const currentOrder = '<?php echo $_GET["order"] ?? ""; ?>';
        if (currentColumn) {
            const btn = document.querySelector(`.sort-btn[data-column="${currentColumn}"] i`);
            if (btn) {
                btn.className = currentOrder === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
            }
        }

        // Botón de activar/desactivar estado
        document.querySelectorAll('.toggle-estado').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const estadoActual = this.dataset.estado;
                const nuevoEstado = estadoActual === 'activa' ? 'inactiva' : 'activa';
                fetch('toggle_estado.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${id}&estado=${nuevoEstado}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) window.location.reload();
                        else alert('Error al cambiar estado');
                    });
            });
        });

        // Botón de editar
        document.querySelectorAll('.edit-cuenta').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-correo').value = this.dataset.correo;
                document.getElementById('edit-contrasena-correo').value = this.dataset.contrasenaCorreo;
                document.getElementById('edit-contrasena-gpt').value = this.dataset.contrasenaGpt;
                document.getElementById('edit-codigo').value = this.dataset.codigo;
                document.getElementById('edit-fecha_inicio').value = this.dataset.fecha_inicio;
                document.getElementById('edit-costo').value = this.dataset.costo;
                document.getElementById('edit-estado').value = this.dataset.estado;
                document.getElementById('edit-tipo_cuenta').value = this.dataset.tipo_cuenta || '';
                var modal = new bootstrap.Modal(document.getElementById('editarCuentaModal'));
                modal.show();
            });
        });

        // Recalcular fecha fin al cambiar fecha inicio
        document.getElementById('edit-fecha_inicio').addEventListener('change', function() {
            if (this.value) {
                const fechaInicio = new Date(this.value);
                const fechaFin = new Date(fechaInicio);
                fechaFin.setDate(fechaFin.getDate() + 30);
                const fechaFinStr = fechaFin.toISOString().split('T')[0];
                document.getElementById('edit-fecha_fin').value = fechaFinStr;
            }
        });

        // Calcular fecha fin al cargar el modal
        document.querySelectorAll('.edit-cuenta').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-correo').value = this.dataset.correo;
                document.getElementById('edit-contrasena-correo').value = this.dataset.contrasenaCorreo;
                document.getElementById('edit-contrasena-gpt').value = this.dataset.contrasenaGpt;
                document.getElementById('edit-codigo').value = this.dataset.codigo;
                document.getElementById('edit-fecha_inicio').value = this.dataset.fecha_inicio;
                document.getElementById('edit-costo').value = this.dataset.costo;
                document.getElementById('edit-estado').value = this.dataset.estado;
                
                // Calcular fecha fin inicial
                if (this.dataset.fecha_inicio) {
                    const fechaInicio = new Date(this.dataset.fecha_inicio);
                    const fechaFin = new Date(fechaInicio);
                    fechaFin.setDate(fechaFin.getDate() + 30);
                    document.getElementById('edit-fecha_fin').value = fechaFin.toISOString().split('T')[0];
                }
                
                var modal = new bootstrap.Modal(document.getElementById('editarCuentaModal'));
                modal.show();
            });
        });

        // Mostrar notificación
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show fixed-top mx-3 mt-3`;
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.prepend(alertDiv);
            setTimeout(() => alertDiv.remove(), 3000);
        }

        // Guardar cambios de edición
        document.getElementById('formEditarCuenta').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('editar_cuenta.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Cuenta editada correctamente', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showAlert('Error: ' + (data.error || 'Error al editar cuenta'), 'danger');
                    }
                });
        });

        // Guardar nueva cuenta
        document.getElementById('formNuevaCuenta').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('guardar_cuenta.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Cuenta creada correctamente', 'success');
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('nuevaCuentaModal')).hide();
                            window.location.reload();
                        }, 1000);
                    } else {
                        showAlert('Error: ' + (data.error || 'Error al crear cuenta'), 'danger');
                    }
                });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminarCuenta'));
            let cuentaAEliminar = null;

            // Delegación de eventos para botón eliminar
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.delete-cuenta');
                if (btn) {
                    cuentaAEliminar = btn.closest('tr');
                    document.getElementById('btnConfirmarEliminarCuenta').dataset.id = btn.dataset.id;
                    modalEliminar.show();
                }
            });

            // Confirmar eliminación
            document.getElementById('btnConfirmarEliminarCuenta').addEventListener('click', function() {
                const id = this.dataset.id;

                fetch('eliminar_cuenta.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + encodeURIComponent(id)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Mostrar alerta de éxito
                            showAlert('Cuenta eliminada exitosamente', 'success');

                            // Cerrar modal
                            modalEliminar.hide();

                            // Eliminar fila de la tabla
                            if (cuentaAEliminar) {
                                cuentaAEliminar.remove();
                                updateTotalGasto(data.deleted_amount || 0);
                            }
                        } else {
                            showAlert('Error al eliminar: ' + (data.error || 'Error desconocido'), 'danger');
                        }
                    })
                    .catch(error => {
                        showAlert('Error en la conexión', 'danger');
                        console.error('Error:', error);
                    });
            });

            // Función para mostrar notificaciones
            function showAlert(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show fixed-top mx-3 mt-3`;
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

                document.body.prepend(alertDiv);

                // Eliminar automáticamente después de 3 segundos
                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);
            }

            // Función para actualizar total (ajusta los selectores según tu estructura)
            function updateTotalGasto(amountToSubtract) {
                const totalElement = document.querySelector('tfoot th:nth-child(4)');
                if (totalElement) {
                    const currentTotal = parseFloat(totalElement.textContent.replace('$', '').replace(/,/g, ''));
                    totalElement.textContent = '$' + (currentTotal - amountToSubtract).toFixed(2);
                }
            }
        });
    </script>


    <!-- Modal Nueva Cuenta -->
    <div class="modal fade" id="nuevaCuentaModal" tabindex="-1" aria-labelledby="nuevaCuentaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formNuevaCuenta" method="post" action="guardar_cuenta.php" autocomplete="off">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevaCuentaModalLabel"><i class="bi bi-person-plus me-2"></i>Nueva Cuenta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" name="correo" id="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena_correo" class="form-label">Contraseña Correo</label>
                            <input type="text" class="form-control" name="contrasena_correo" id="contrasena_correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena_gpt" class="form-label">Contraseña GPT</label>
                            <input type="text" class="form-control" name="contrasena_gpt" id="contrasena_gpt" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo date('Y-m-d'); ?>" required>
                            <input type="hidden" name="fecha_fin" id="fecha_fin" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="costo" class="form-label">Costo</label>
                            <input type="number" step="0.01" class="form-control" name="costo" id="costo" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuarios" class="form-label">Usuarios</label>
                            <input type="number" min="0" class="form-control" name="usuarios" id="usuarios" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" name="estado" id="estado" required>
                                <option value="activa" selected>Activa</option>
                                <option value="inactiva">Inactiva</option>
                            </select>
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

</body>

</html>
