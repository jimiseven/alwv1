<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ventas - Sistema ALW</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }

        .table-container {
            max-height: 600px;
            overflow-y: auto;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            padding: 1rem;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #e9ecef;
            z-index: 10;
        }

        .btn-sm {
            min-width: 36px;
        }

        .action-buttons .btn {
            margin-right: 0.25rem;
        }

        @media (max-width: 767.98px) {
            .table-responsive {
                overflow-x: auto;
            }

            .table-container {
                padding: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Parte 1: Agregar barra de búsqueda y botón de copiado -->
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div class="input-group me-3" style="max-width: 300px;">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por número...">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
                            <i class="bi bi-plus-circle"></i> Nueva venta
                        </button>
                    </div>
                </div>

                <div class="table-responsive-custom">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Celular</th>
                                <th class="mobile-hidden">Cuenta</th>
                                <th>Inicio</th>
                                <th class="mobile-hidden">Fin</th>
                                <th>Días</th>
                                <th>Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT v.id, v.numero_celular, v.fecha_inicio, v.fecha_fin, v.dias, v.pago,
                               c.correo AS cuenta_correo, c.contrasena_gpt, u.usuario AS vendedor
                               FROM ventas v
                               JOIN cuentas c ON v.cuenta_id = c.id
                               JOIN vendedores u ON v.vendedor_id = u.id
                               ORDER BY v.id DESC";
                            $resultado = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>
                                        <td>{$fila['id']}</td>
                                        <td>" . htmlspecialchars($fila['numero_celular']) . "</td>
                                        <td class='mobile-hidden'>" . htmlspecialchars($fila['cuenta_correo']) . "</td>
                                        <td>" . date('d/m/y', strtotime($fila['fecha_inicio'])) . "</td>
                                        <td class='mobile-hidden'>" . date('d/m/y', strtotime($fila['fecha_fin'])) . "</td>
                                        <td>{$fila['dias']}</td>
                                        <td>$" . number_format($fila['pago'], 2) . "</td>
                                        <td>
                                            <button class='btn btn-sm btn-warning me-1'>
                                                <i class='bi bi-pencil'></i>
                                            </button>
                                            <button class='btn btn-sm btn-danger me-1'>
                                                <i class='bi bi-trash'></i>
                                            </button>
                                            <button class='btn btn-sm btn-info copy-btn' 
                                                data-correo='" . htmlspecialchars($fila['cuenta_correo']) . "'
                                                data-contrasena='" . htmlspecialchars($fila['contrasena_gpt']) . "'
                                                data-inicio='" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "'
                                                data-fin='" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "'
                                                data-dias='{$fila['dias']}'>
                                                <i class='bi bi-clipboard'></i>
                                            </button>
                                        </td>
                                      </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No hay ventas registradas</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>


                <!-- En el modal de Nueva Venta (dentro de ventas.php) -->
                <div class="modal fade" id="nuevaVentaModal" tabindex="-1" aria-labelledby="nuevaVentaModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form id="formNuevaVenta">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="nuevaVentaModalLabel"><i class="bi bi-cart-plus me-2"></i>Nueva Venta</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
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
                                            // Consulta modificada para obtener el conteo de ventas
                                            $sql_cuentas = "SELECT c.id, c.correo, COUNT(v.id) as ventas_count 
                                            FROM cuentas c 
                                            LEFT JOIN ventas v ON c.id = v.cuenta_id 
                                            WHERE c.estado = 'activa' 
                                            GROUP BY c.id 
                                            ORDER BY c.correo";
                                            $res_cuentas = mysqli_query($conn, $sql_cuentas);

                                            while ($cuenta = mysqli_fetch_assoc($res_cuentas)) {
                                                $ventas_count = $cuenta['ventas_count'];
                                                echo "<option value='{$cuenta['id']}'>"
                                                    . htmlspecialchars($cuenta['correo'])
                                                    . " ($ventas_count ventas)</option>";
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
                                    <input type="hidden" name="vendedor_id" value="<?php echo $_SESSION['user_id']; ?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- Modal Editar Venta -->
                <div class="modal fade" id="editarVentaModal" tabindex="-1" aria-labelledby="editarVentaModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form id="formEditarVenta">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editarVentaModalLabel"><i class="bi bi-pencil me-2"></i>Editar Venta</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" id="edit-id">
                                    <div class="mb-3">
                                        <label class="form-label">Número Celular</label>
                                        <input type="text" class="form-control" name="numero_celular" id="edit-numero_celular" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Cuenta</label>
                                        <select class="form-select" name="cuenta_id" id="edit-cuenta_id" required>
                                            <option value="">Seleccionar cuenta...</option>
                                            <?php
                                            $sql_cuentas = "SELECT id, correo FROM cuentas WHERE estado = 'activa' ORDER BY correo";
                                            $res_cuentas = mysqli_query($conn, $sql_cuentas);
                                            while ($cuenta = mysqli_fetch_assoc($res_cuentas)) {
                                                echo "<option value='{$cuenta['id']}'>" . htmlspecialchars($cuenta['correo']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Fecha Inicio</label>
                                            <input type="date" class="form-control" name="fecha_inicio" id="edit-fecha_inicio" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Fecha Fin</label>
                                            <input type="date" class="form-control" name="fecha_fin" id="edit-fecha_fin" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pago</label>
                                        <input type="number" step="0.01" class="form-control" name="pago" id="edit-pago" required>
                                    </div>
                                    <input type="hidden" name="vendedor_id" id="edit-vendedor_id">
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
                    // Guardar nueva venta
                    document.getElementById('formNuevaVenta').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        fetch('guardar_venta.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Venta guardada correctamente');
                                    window.location.reload();
                                } else {
                                    alert('Error: ' + (data.error || 'No se pudo guardar la venta'));
                                }
                            })
                            .catch(() => alert('Error al procesar la solicitud'));
                    });

                    // Abrir modal editar con datos cargados
                    document.querySelectorAll('.edit-venta').forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.getElementById('edit-id').value = this.dataset.id;
                            document.getElementById('edit-numero_celular').value = this.dataset.numero_celular;
                            document.getElementById('edit-fecha_inicio').value = this.dataset.fecha_inicio;
                            document.getElementById('edit-fecha_fin').value = this.dataset.fecha_fin;
                            document.getElementById('edit-pago').value = this.dataset.pago;
                            document.getElementById('edit-vendedor_id').value = this.dataset.vendedor_id;

                            // Seleccionar cuenta en el select
                            const cuentaSelect = document.getElementById('edit-cuenta_id');
                            for (let option of cuentaSelect.options) {
                                option.selected = (option.value == this.dataset.cuenta_id);
                            }

                            var modal = new bootstrap.Modal(document.getElementById('editarVentaModal'));
                            modal.show();
                        });
                    });

                    // Guardar cambios edición
                    document.getElementById('formEditarVenta').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        fetch('editar_venta.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Venta actualizada correctamente');
                                    window.location.reload();
                                } else {
                                    alert('Error al actualizar la venta: ' + (data.error || 'Error desconocido'));
                                }
                            })
                            .catch(() => alert('Error al procesar la solicitud'));
                    });

                    // Eliminar venta
                    document.querySelectorAll('.delete-venta').forEach(btn => {
                        btn.addEventListener('click', function() {
                            if (confirm('¿Seguro que quieres eliminar esta venta?')) {
                                const id = this.dataset.id;
                                fetch('eliminar_venta.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: 'id=' + encodeURIComponent(id)
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert('Venta eliminada correctamente');
                                            window.location.reload();
                                        } else {
                                            alert('Error al eliminar la venta');
                                        }
                                    })
                                    .catch(() => alert('Error al procesar la solicitud'));
                            }
                        });
                    });
                </script>
                <!-- Parte 2: Scripts para búsqueda y copiado -->
                <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
                <script>
                    // Búsqueda dinámica
                    document.getElementById('searchInput').addEventListener('keyup', function() {
                        const value = this.value.toLowerCase();
                        document.querySelectorAll('tbody tr').forEach(row => {
                            const celular = row.children[1].textContent.toLowerCase();
                            row.style.display = celular.includes(value) ? '' : 'none';
                        });
                    });

                    // Copiar mensaje al portapapeles
                    document.querySelectorAll('.copy-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const mensaje = `Datos para ingresar a la cuenta de Chat GPT

Cuenta Chat GPT Plus (${this.dataset.dias} días)
Correo: ${this.dataset.correo}
Contraseña: ${this.dataset.contrasena}

Fecha ini: ${this.dataset.inicio}
Fecha end: ${this.dataset.fin}

Reglas para el uso de la cuenta:

- No modificar ningún dato de la cuenta, en caso de modificar algún dato de la cuenta, retiro la cuenta del grupo de trabajo y te quitaré el acceso, no cubriré la garantía y el tiempo de servicio.
- Evita salirte de la cuenta.
- Referentemente, usa la aplicación móvil en el celular y en computadora navegador Google Chrome NO PESTAÑA INCÓGNITO
- Link para pc: https://auth.openai.com/log-in

Ingresa ahora por favor y te paso los códigos de activación`;

                            navigator.clipboard.writeText(mensaje)
                                .then(() => alert('Mensaje copiado al portapapeles'))
                                .catch(err => console.error('Error al copiar:', err));
                        });
                    });
                </script>

</body>

</html>