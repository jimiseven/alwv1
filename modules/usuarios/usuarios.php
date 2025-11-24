<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
requireLogin();
requireAdmin(); // Solo administradores pueden gestionar usuarios
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Sistema ALW</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Reset y base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            margin: 0;
            padding: 0;
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
                transform: translateX(0);
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
        }

        @media (min-width: 992px) {
            .mobile-nav {
                display: none;
            }

            .sidebar-mobile-backdrop {
                display: none !important;
            }
        }

        /* Modales */
        .modal-backdrop {
            z-index: 1040 !important;
            pointer-events: none !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .modal {
            z-index: 1055 !important;
        }

        .modal-content {
            pointer-events: auto !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
        }

        body.modal-open .sidebar-mobile-backdrop {
            display: none !important;
        }

        body.modal-open .sidebar {
            pointer-events: none !important;
        }

        .sidebar .nav-link {
            pointer-events: auto !important;
            cursor: pointer;
        }

        /* Tabla de usuarios */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
            border: none;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .btn {
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            border: none;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.75rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
            <h2 class="mb-0"><i class="bi bi-people"></i> Usuarios</h2>
        </div>
        <div class="sidebar-mobile-backdrop" id="sidebarMobileBackdrop"></div>

        <div class="d-flex">
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
            <main class="main-content p-4">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2"><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios</h2>
                            <p class="mb-0 opacity-75">Administra los usuarios del sistema</p>
                        </div>
                        <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                            <i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario
                        </button>
                    </div>
                </div>

                <!-- Tabla de usuarios -->
                <div class="table-container">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Fecha Creación</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT id, usuario, rol, activo, created_at FROM vendedores ORDER BY created_at DESC";
                            $resultado = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($resultado) > 0) {
                                while ($usuario = mysqli_fetch_assoc($resultado)) {
                                    $estadoBadge = $usuario['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';
                                    $rolBadge = $usuario['rol'] === 'admin' ? '<span class="badge bg-primary">Admin</span>' : '<span class="badge bg-secondary">Vendedor</span>';
                                    $fechaCreacion = date('d/m/Y', strtotime($usuario['created_at']));
                                    
                                    echo "<tr>
                                        <td class='text-center fw-bold'>#{$usuario['id']}</td>
                                        <td><i class='bi bi-person-circle me-2'></i>" . htmlspecialchars($usuario['usuario']) . "</td>
                                        <td>{$rolBadge}</td>
                                        <td class='text-center'>{$estadoBadge}</td>
                                        <td class='text-center'>{$fechaCreacion}</td>
                                        <td class='text-center'>
                                            <button class='btn btn-sm btn-warning me-1 edit-usuario' 
                                                data-id='{$usuario['id']}'
                                                data-usuario='" . htmlspecialchars($usuario['usuario']) . "'
                                                data-rol='{$usuario['rol']}'
                                                data-activo='{$usuario['activo']}'>
                                                <i class='bi bi-pencil-fill'></i>
                                            </button>
                                            <button class='btn btn-sm btn-danger toggle-estado-usuario me-1' 
                                                data-id='{$usuario['id']}'
                                                data-usuario='" . htmlspecialchars($usuario['usuario']) . "'
                                                data-activo='{$usuario['activo']}'>
                                                <i class='bi bi-" . ($usuario['activo'] ? 'x-circle-fill' : 'check-circle-fill') . "'></i>
                                            </button>
                                            <button class='btn btn-sm btn-outline-danger delete-usuario-confirm' 
                                                data-id='{$usuario['id']}'
                                                data-usuario='" . htmlspecialchars($usuario['usuario']) . "'
                                                title='Eliminar permanentemente'>
                                                <i class='bi bi-trash-fill'></i>
                                            </button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-4'>No hay usuarios registrados</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Nuevo Usuario -->
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formNuevoUsuario">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Crear Nuevo Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nuevo_usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nuevo_usuario" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuevo_password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="nuevo_password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuevo_rol" class="form-label">Rol</label>
                            <select class="form-select" id="nuevo_rol" name="rol" required>
                                <option value="vendedor">Vendedor</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nuevo_activo" class="form-label">Estado</label>
                            <select class="form-select" id="nuevo_activo" name="activo" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditarUsuario">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="edit_usuario" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Nueva Contraseña (dejar vacío para no cambiar)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="edit_rol" class="form-label">Rol</label>
                            <select class="form-select" id="edit_rol" name="rol" required>
                                <option value="vendedor">Vendedor</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_activo" class="form-label">Estado</label>
                            <select class="form-select" id="edit_activo" name="activo" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div class="modal fade" id="eliminarUsuarioModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Eliminación Permanente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>¡Advertencia!</strong> Esta acción es irreversible. Se eliminará el usuario y TODAS sus ventas asociadas.
                    </div>
                    
                    <h6 class="mb-3">Usuario a eliminar: <strong id="delete-usuario-name"></strong></h6>
                    
                    <div id="ventas-info">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando información de ventas...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminar-btn" disabled>
                        <i class="bi bi-trash-fill me-2"></i>Eliminar Permanentemente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar móvil
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarBackdrop = document.getElementById('sidebarMobileBackdrop');
            const btnSidebarMobile = document.getElementById('btnSidebarMobile');

            if (btnSidebarMobile && sidebar && sidebarBackdrop) {
                btnSidebarMobile.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarBackdrop.classList.toggle('show');
                    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                });

                document.addEventListener('click', function(e) {
                    if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
                        if (!sidebar.contains(e.target) && !btnSidebarMobile.contains(e.target)) {
                            sidebar.classList.remove('show');
                            sidebarBackdrop.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                    }
                });

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

            // Crear nuevo usuario
            document.getElementById('formNuevoUsuario').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('<?php echo BASE_URL; ?>api/usuarios/crear.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Usuario creado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al crear usuario');
                });
            });

            // Editar usuario
            document.querySelectorAll('.edit-usuario').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('edit_id').value = this.dataset.id;
                    document.getElementById('edit_usuario').value = this.dataset.usuario;
                    document.getElementById('edit_rol').value = this.dataset.rol;
                    document.getElementById('edit_activo').value = this.dataset.activo;
                    document.getElementById('edit_password').value = '';
                    
                    new bootstrap.Modal(document.getElementById('editarUsuarioModal')).show();
                });
            });

            document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('<?php echo BASE_URL; ?>api/usuarios/editar.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Usuario actualizado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar usuario');
                });
            });

            // Mostrar modal de eliminación con información de ventas
            let usuarioIdToDelete = null;
            document.querySelectorAll('.delete-usuario-confirm').forEach(btn => {
                btn.addEventListener('click', function() {
                    const usuarioId = this.dataset.id;
                    const usuarioNombre = this.dataset.usuario;
                    usuarioIdToDelete = usuarioId;
                    
                    // Actualizar nombre en el modal
                    document.getElementById('delete-usuario-name').textContent = usuarioNombre;
                    
                    // Resetear contenido de ventas
                    document.getElementById('ventas-info').innerHTML = `
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando información de ventas...</p>
                        </div>
                    `;
                    
                    // Deshabilitar botón de eliminar
                    document.getElementById('confirmar-eliminar-btn').disabled = true;
                    
                    // Mostrar modal
                    new bootstrap.Modal(document.getElementById('eliminarUsuarioModal')).show();
                    
                    // Cargar información de ventas
                    fetch('<?php echo BASE_URL; ?>api/usuarios/get_ventas.php?id=' + usuarioId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                mostrarVentas(data.ventas, data.total_ventas, data.total_ingresos);
                            } else {
                                document.getElementById('ventas-info').innerHTML = `
                                    <div class="alert alert-danger">
                                        <i class="bi bi-x-circle me-2"></i>Error al cargar ventas: ${data.message}
                                    </div>
                                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('ventas-info').innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="bi bi-x-circle me-2"></i>Error al cargar información de ventas
                                </div>
                            `;
                        });
                });
            });

            function mostrarVentas(ventas, totalVentas, totalIngresos) {
                let html = '';
                
                if (ventas.length === 0) {
                    html = `
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Este usuario no tiene ventas registradas. Se puede eliminar sin afectar datos.
                        </div>
                    `;
                } else {
                    html = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Este usuario tiene ${totalVentas} venta(s) registrada(s)</strong> por un total de <strong>$${totalIngresos}</strong>
                        </div>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-striped">
                                <thead class="sticky-top bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Cuenta</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    ventas.forEach(venta => {
                        html += `
                            <tr>
                                <td>${venta.id}</td>
                                <td>${venta.numero_celular}</td>
                                <td>${venta.cuenta_correo}</td>
                                <td>${venta.fecha_inicio}</td>
                                <td>${venta.fecha_fin}</td>
                                <td><strong>$${venta.pago}</strong></td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                document.getElementById('ventas-info').innerHTML = html;
                // Habilitar botón de eliminar
                document.getElementById('confirmar-eliminar-btn').disabled = false;
            }

            // Confirmar eliminación
            document.getElementById('confirmar-eliminar-btn').addEventListener('click', function() {
                if (!usuarioIdToDelete) return;
                
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...';
                
                const formData = new FormData();
                formData.append('id', usuarioIdToDelete);
                
                fetch('<?php echo BASE_URL; ?>api/usuarios/eliminar.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Usuario y sus ventas eliminados exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                        this.disabled = false;
                        this.innerHTML = '<i class="bi bi-trash-fill me-2"></i>Eliminar Permanentemente';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar usuario');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-trash-fill me-2"></i>Eliminar Permanentemente';
                });
            });

            // Activar/Desactivar usuario
            document.querySelectorAll('.toggle-estado-usuario').forEach(btn => {
                btn.addEventListener('click', function() {
                    const activo = this.dataset.activo === '1';
                    const accion = activo ? 'desactivar' : 'activar';
                    const nuevoEstado = activo ? 0 : 1;
                    
                    if (confirm('¿Estás seguro de ' + accion + ' al usuario ' + this.dataset.usuario + '?')) {
                        const formData = new FormData();
                        formData.append('id', this.dataset.id);
                        formData.append('activo', nuevoEstado);

                        fetch('<?php echo BASE_URL; ?>api/usuarios/toggle_estado.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Usuario ' + (nuevoEstado ? 'activado' : 'desactivado') + ' exitosamente');
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al cambiar estado del usuario');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
