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
    body { background-color: #f8f9fa; color: #343a40; }
    .table-container { max-height: 600px; overflow-y: auto; background: #fff; border-radius: 0.5rem; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.1); margin-bottom: 1.5rem; padding: 1rem; }
    .table thead th { position: sticky; top: 0; background-color: #e9ecef; z-index: 10; }
    .btn-sm { min-width: 36px; }
    .action-buttons .btn { margin-right: 0.25rem; }
    @media (max-width: 767.98px) {
      .table-responsive { overflow-x: auto; }
      .table-container { padding: 0.5rem; }
    }
    .venta-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 1rem; padding: 1rem; }
    .venta-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 0.5rem; }
    .venta-body p { margin-bottom: 0.3rem; font-size: 0.9rem; }
    .mobile-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 1rem; }
    .action-btn { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: none; }
    .sidebar-mobile-backdrop {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.4);
      z-index: 1049;
    }
    @media (min-width: 768px) { .mobile-only { display: none !important; } .desktop-only { display: block !important; } }
    @media (max-width: 767px) {
      .desktop-only { display: none !important; }
      .mobile-only { display: block !important; }
      .sidebar {
        position: fixed !important;
        top: 0; left: -260px; bottom: 0;
        width: 260px; z-index: 1050;
        transition: left 0.3s;
      }
      .sidebar.show { left: 0 !important; }
      .sidebar-mobile-backdrop.show { display: block !important; }
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
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Botón hamburguesa y backdrop solo en móvil -->
    <div class="mobile-navbar mobile-only">
      <button class="btn btn-link text-dark p-0" id="btnSidebarMobile" type="button">
        <i class="bi bi-list"></i>
      </button>
      <h2 class="mb-0"><i class="bi bi-cart"></i> Ventas</h2>
    </div>
    <div class="sidebar-mobile-backdrop" id="sidebarMobileBackdrop"></div>
    <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-3">
      <!-- Búsqueda y botón en móvil -->
      <div class="mobile-only mb-3">
        <div class="input-group mb-3">
          <input type="text" id="mobileSearch" class="form-control" placeholder="Buscar por número...">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
        </div>
        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
          <i class="bi bi-plus-circle"></i> Nueva venta
        </button>
      </div>
      <!-- Desktop: barra de búsqueda y botón -->
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 desktop-only">
        <h2><i class="bi bi-cart"></i> Listado de Ventas</h2>
        <div class="d-flex gap-2">
          <div class="input-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por número...">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
          </div>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
            <i class="bi bi-plus-circle"></i> Nueva venta
          </button>
        </div>
      </div>
      <!-- Vista desktop -->
      <div class="table-container desktop-only">
        <div class="table-responsive">
          <table class="table table-hover align-middle text-nowrap mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Número Celular</th>
                <th>Vendedor</th>
                <th>Cuenta</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Días</th>
                <th>Pago</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="ventas-tbody">
              <?php
              $sql = "SELECT v.id, v.numero_celular, v.fecha_inicio, v.fecha_fin, v.dias, v.pago, v.cuenta_id, v.vendedor_id,
                             c.correo AS cuenta_correo, c.contrasena_gpt,
                             u.usuario AS vendedor_usuario
                      FROM ventas v
                      INNER JOIN cuentas c ON v.cuenta_id = c.id
                      INNER JOIN vendedores u ON v.vendedor_id = u.id
                      ORDER BY v.fecha_inicio DESC";
              $resultado = mysqli_query($conn, $sql);
              if (mysqli_num_rows($resultado) > 0) {
                while ($fila = mysqli_fetch_assoc($resultado)) {
                  echo "<tr data-id='{$fila['id']}'>
                          <td>{$fila['id']}</td>
                          <td>" . htmlspecialchars($fila['numero_celular']) . "</td>
                          <td>" . htmlspecialchars($fila['vendedor_usuario']) . "</td>
                          <td>" . htmlspecialchars($fila['cuenta_correo']) . "</td>
                          <td>" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "</td>
                          <td>" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "</td>
                          <td>{$fila['dias']}</td>
                          <td>$" . number_format($fila['pago'], 2) . "</td>
                          <td class='action-buttons'>
                            <button type='button' class='btn btn-sm btn-warning edit-venta' 
                                    data-bs-toggle='modal' data-bs-target='#editarVentaModal'
                                    data-id='{$fila['id']}'
                                    data-numero_celular='" . htmlspecialchars($fila['numero_celular'], ENT_QUOTES) . "'
                                    data-cuenta_id='{$fila['cuenta_id']}'
                                    data-vendedor_id='{$fila['vendedor_id']}'
                                    data-fecha_inicio='{$fila['fecha_inicio']}'
                                    data-fecha_fin='{$fila['fecha_fin']}'
                                    data-pago='{$fila['pago']}'
                            >
                              <i class='bi bi-pencil'></i>
                            </button>
                            <button type='button' class='btn btn-sm btn-danger delete-venta' data-id='{$fila['id']}'>
                              <i class='bi bi-trash'></i>
                            </button>
                            <button type='button' class='btn btn-sm btn-info copy-btn' 
                                    data-correo='" . htmlspecialchars($fila['cuenta_correo'], ENT_QUOTES) . "'
                                    data-contrasena='" . htmlspecialchars($fila['contrasena_gpt'], ENT_QUOTES) . "'
                                    data-inicio='" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "'
                                    data-fin='" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "'
                                    data-dias='{$fila['dias']}'
                            >
                              <i class='bi bi-clipboard'></i>
                            </button>
                          </td>
                        </tr>";
                }
              } else {
                echo "<tr><td colspan='9' class='text-center'>No hay ventas registradas</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Vista móvil -->
      <div class="mobile-only">
        <?php
        mysqli_data_seek($resultado, 0); // Reiniciar el puntero del resultado
        if (mysqli_num_rows($resultado) > 0) {
          while ($fila = mysqli_fetch_assoc($resultado)) {
            echo "<div class='venta-card' data-id='{$fila['id']}'>
                    <div class='venta-header'>
                      <div class='title'>#" . $fila['id'] . " - " . htmlspecialchars($fila['numero_celular']) . "</div>
                      <div class='badge bg-primary'>" . $fila['dias'] . " días</div>
                    </div>
                    <div class='venta-body'>
                      <p><i class='bi bi-envelope me-2'></i>" . htmlspecialchars($fila['cuenta_correo']) . "</p>
                      <p><i class='bi bi-calendar me-2'></i>Inicio: " . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "</p>
                      <p><i class='bi bi-calendar-check me-2'></i>Fin: " . date('d/m/Y', strtotime($fila['fecha_fin'])) . "</p>
                      <p><i class='bi bi-cash-coin me-2'></i>$" . number_format($fila['pago'], 2) . "</p>
                      <div class='mobile-actions'>
                        <button type='button' class='action-btn bg-warning text-white edit-venta' 
                                data-bs-toggle='modal' data-bs-target='#editarVentaModal'
                                data-id='{$fila['id']}'
                                data-numero_celular='" . htmlspecialchars($fila['numero_celular'], ENT_QUOTES) . "'
                                data-cuenta_id='{$fila['cuenta_id']}'
                                data-vendedor_id='{$fila['vendedor_id']}'
                                data-fecha_inicio='{$fila['fecha_inicio']}'
                                data-fecha_fin='{$fila['fecha_fin']}'
                                data-pago='{$fila['pago']}'
                        >
                          <i class='bi bi-pencil'></i>
                        </button>
                        <button type='button' class='action-btn bg-danger text-white delete-venta' data-id='{$fila['id']}'>
                          <i class='bi bi-trash'></i>
                        </button>
                        <button type='button' class='action-btn bg-info text-white copy-btn' 
                                data-correo='" . htmlspecialchars($fila['cuenta_correo'], ENT_QUOTES) . "'
                                data-contrasena='" . htmlspecialchars($fila['contrasena_gpt'], ENT_QUOTES) . "'
                                data-inicio='" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "'
                                data-fin='" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "'
                                data-dias='{$fila['dias']}'
                        >
                          <i class='bi bi-clipboard'></i>
                        </button>
                      </div>
                    </div>
                  </div>";
          }
        } else {
          echo "<div class='text-center p-4'>No hay ventas registradas</div>";
        }
        ?>
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
                <input type="hidden" name="vendedor_id" id="edit-vendedor_id" value="<?php echo $_SESSION['user_id']; ?>">
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
      document.addEventListener('DOMContentLoaded', function() {
        // Sidebar móvil
        const sidebar = document.querySelector('.sidebar');
        const sidebarBackdrop = document.getElementById('sidebarMobileBackdrop');
        const btnSidebarMobile = document.getElementById('btnSidebarMobile');
        if(btnSidebarMobile && sidebar && sidebarBackdrop) {
          btnSidebarMobile.addEventListener('click', function() {
            sidebar.classList.add('show');
            sidebarBackdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
          });
          sidebarBackdrop.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarBackdrop.classList.remove('show');
            document.body.style.overflow = '';
          });
        }

        // Delegación de eventos para botones de acciones
        document.body.addEventListener('click', function(event) {
          // Editar
          if (event.target.closest('.edit-venta')) {
            const btn = event.target.closest('.edit-venta');
            document.getElementById('edit-id').value = btn.dataset.id;
            document.getElementById('edit-numero_celular').value = btn.dataset.numero_celular;
            document.getElementById('edit-fecha_inicio').value = btn.dataset.fecha_inicio;
            document.getElementById('edit-fecha_fin').value = btn.dataset.fecha_fin;
            document.getElementById('edit-pago').value = btn.dataset.pago;
            document.getElementById('edit-vendedor_id').value = btn.dataset.vendedor_id;
            document.getElementById('edit-cuenta_id').value = btn.dataset.cuenta_id;
            // El modal se abre automáticamente por data-bs-toggle/data-bs-target
          }

          // Eliminar
          if (event.target.closest('.delete-venta')) {
            const btn = event.target.closest('.delete-venta');
            if (confirm('¿Seguro que quieres eliminar esta venta?')) {
              const id = btn.dataset.id;
              fetch('eliminar_venta.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(id)
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  // Eliminar del DOM
                  const card = btn.closest('.venta-card');
                  if (card) card.remove();
                  const row = btn.closest('tr');
                  if (row) row.remove();
                } else {
                  alert('Error al eliminar la venta: ' + (data.error || 'Error desconocido'));
                }
              })
              .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
              });
            }
          }

          // Copiar al portapapeles
          if (event.target.closest('.copy-btn')) {
            const btn = event.target.closest('.copy-btn');
            const mensaje = `Datos para ingresar a la cuenta de Chat GPT

Cuenta Chat GPT Plus (${btn.dataset.dias} días)
Correo: ${btn.dataset.correo}
Contraseña: ${btn.dataset.contrasena}

Fecha ini: ${btn.dataset.inicio}
Fecha end: ${btn.dataset.fin}

Reglas para el uso de la cuenta:

- No modificar ningún dato de la cuenta, en caso de modificar algún dato de la cuenta, retiro la cuenta del grupo de trabajo y te quitaré el acceso, no cubriré la garantía y el tiempo de servicio.
- Evita salirte de la cuenta.
- Referentemente, usa la aplicación móvil en el celular y en computadora navegador Google Chrome NO PESTAÑA INCÓGNITO 
- Link para pc https://auth.openai.com/log-in

Ingresa ahora por favor y te paso los códigos de activación`;

            navigator.clipboard.writeText(mensaje)
              .then(() => alert('Mensaje copiado al portapapeles'))
              .catch(err => {
                console.error('Error al copiar:', err);
                alert('No se pudo copiar automáticamente');
              });
          }
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
          .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
          });
        });

        // Búsqueda dinámica para ambas vistas
        function handleSearch(value) {
          // Desktop
          document.querySelectorAll('.table tbody tr').forEach(row => {
            const celular = row.children[1].textContent.toLowerCase();
            row.style.display = celular.includes(value) ? '' : 'none';
          });
          // Móvil
          document.querySelectorAll('.venta-card').forEach(card => {
            const celular = card.querySelector('.title').textContent.toLowerCase();
            card.style.display = celular.includes(value) ? '' : 'none';
          });
        }
        document.getElementById('searchInput').addEventListener('input', function() {
          handleSearch(this.value.toLowerCase());
        });
        document.getElementById('mobileSearch').addEventListener('input', function() {
          handleSearch(this.value.toLowerCase());
        });
      });
      </script>
    </body>
</html>
