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
    </style>
</head>

<body>
    <div class="container-fluid">
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
                                <th>Correo</th>
                                <th>Contraseña correo</th>
                                <th>Contraseña GPT</th>
                                <th>Código</th>
                                <th>Fecha inicio</th>
                                <th>Fecha fin</th>
                                <th>Días</th>
                                <th>Usuarios</th>
                                <th>Gasto</th>
                                <th>Ganancia</th>
                                <th>Estado</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM cuentas ORDER BY id DESC";
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
                                    $dias = $fila['dias'] ?? '';
                                    if (!$dias && $fecha_ini && ($fecha_fin || isset($fecha_fin_calc))) {
                                        $fin = $fecha_fin ?: $fecha_fin_calc;
                                        $dias = (strtotime($fin) - strtotime($fecha_ini)) / (60 * 60 * 24);
                                    }
                                    $total_gasto += floatval($fila['costo']);
                                    $estado_activo = $fila['estado'] === 'activa';
                                    $estado_btn = $estado_activo ? 'btn-success' : 'btn-secondary';
                                    $estado_txt = $estado_activo ? 'Activa' : 'Inactiva';
                                    echo "<tr>
                                    <td>{$fila['id']}</td>
                                    <td>" . htmlspecialchars($fila['correo']) . "</td>
                                    <td>" . htmlspecialchars($fila['contrasena_correo']) . "</td>
                                    <td>" . htmlspecialchars($fila['contrasena_gpt']) . "</td>
                                    <td>" . htmlspecialchars($fila['codigo']) . "</td>
                                    <td>" . ($fecha_ini ? date('d/m/Y', strtotime($fecha_ini)) : '') . "</td>
                                    <td>$fecha_fin_mostrar</td>
                                    <td>" . intval($dias) . "</td>
                                    <td>{$fila['usuarios']}</td>
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
                                            data-contrasena_correo='" . htmlspecialchars($fila['contrasena_correo'], ENT_QUOTES) . "'
                                            data-contrasena_gpt='" . htmlspecialchars($fila['contrasena_gpt'], ENT_QUOTES) . "'
                                            data-codigo='" . htmlspecialchars($fila['codigo'], ENT_QUOTES) . "'
                                            data-fecha_inicio='{$fila['fecha_inicio']}'
                                            data-costo='{$fila['costo']}'
                                            data-estado='{$fila['estado']}'
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
                                echo "<tr><td colspan='14' class='text-center'>No hay cuentas registradas</td></tr>";
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
                            <label class="form-label">Contraseña correo</label>
                            <input type="text" class="form-control" name="contrasena_correo" id="edit-contrasena_correo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña GPT</label>
                            <input type="text" class="form-control" name="contrasena_gpt" id="edit-contrasena_gpt" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Código</label>
                            <input type="text" class="form-control" name="codigo" id="edit-codigo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="edit-fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Costo</label>
                            <input type="number" step="0.01" class="form-control" name="costo" id="edit-costo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado" id="edit-estado">
                                <option value="activa">Activa</option>
                                <option value="inactiva">Inactiva</option>
                                <option value="suspendida">Suspendida</option>
                                <option value="baneada">Baneada</option>
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
                document.getElementById('edit-contrasena_correo').value = this.dataset.contrasena_correo;
                document.getElementById('edit-contrasena_gpt').value = this.dataset.contrasena_gpt;
                document.getElementById('edit-codigo').value = this.dataset.codigo;
                document.getElementById('edit-fecha_inicio').value = this.dataset.fecha_inicio;
                document.getElementById('edit-costo').value = this.dataset.costo;
                document.getElementById('edit-estado').value = this.dataset.estado;
                var modal = new bootstrap.Modal(document.getElementById('editarCuentaModal'));
                modal.show();
            });
        });

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
                    if (data.success) window.location.reload();
                    else alert('Error al editar cuenta');
                });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', function(e) {
                const deleteBtn = e.target.closest('.delete-cuenta');

                if (deleteBtn) {
                    const id = deleteBtn.dataset.id; // Obtener el ID del botón
                    const confirmBtn = document.getElementById('btnConfirmarEliminarCuenta');

                    if (!id || !confirmBtn) {
                        alert("Error: No se pudo obtener el ID de la cuenta.");
                        return;
                    }

                    // Asignar ID al botón de confirmación
                    confirmBtn.dataset.id = id;

                    // Mostrar modal
                    new bootstrap.Modal(document.getElementById('modalEliminarCuenta')).show();
                }
            });

            // Confirmar eliminación
            document.getElementById('btnConfirmarEliminarCuenta').addEventListener('click', function() {
                const id = this.dataset.id;

                if (!id) {
                    alert("Error: ID no válido.");
                    return;
                }

                fetch('eliminar_cuenta.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${encodeURIComponent(id)}` // Enviar como parámetro POST
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Eliminar la fila de la tabla
                            document.querySelector(`.delete-cuenta[data-id="${id}"]`).closest('tr').remove();
                            // Actualizar total gasto (si aplica)
                            updateTotalGasto(data.deleted_amount);
                        } else {
                            alert('Error: ' + (data.error || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error en la conexión');
                    });
            });

            // Función para actualizar el total
            function updateTotalGasto(amount) {
                const totalElement = document.querySelector('tfoot th:nth-child(4)');
                if (totalElement) {
                    const currentTotal = parseFloat(totalElement.textContent.replace('$', '').replace(/,/g, ''));
                    totalElement.textContent = '$' + (currentTotal - amount).toFixed(2);
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
                            <label for="contrasena_correo" class="form-label">Contraseña correo</label>
                            <input type="text" class="form-control" name="contrasena_correo" id="contrasena_correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena_gpt" class="form-label">Contraseña GPT</label>
                            <input type="text" class="form-control" name="contrasena_gpt" id="contrasena_gpt" required>
                        </div>
                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text" class="form-control" name="codigo" id="codigo">
                        </div>
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="costo" class="form-label">Costo</label>
                            <input type="number" step="0.01" class="form-control" name="costo" id="costo" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuarios" class="form-label">Usuarios</label>
                            <input type="number" min="1" class="form-control" name="usuarios" id="usuarios" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" name="estado" id="estado" required>
                                <option value="activa" selected>Activa</option>
                                <option value="inactiva">Inactiva</option>
                                <option value="suspendida">Suspendida</option>
                                <option value="baneada">Baneada</option>
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