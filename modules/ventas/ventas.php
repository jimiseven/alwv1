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
  <!-- En el <head> -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- Antes de </body> -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    .venta-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 1rem;
      padding: 1rem;
    }

    .venta-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #eee;
      padding-bottom: 0.5rem;
      margin-bottom: 0.5rem;
    }

    .venta-body p {
      margin-bottom: 0.3rem;
      font-size: 0.9rem;
    }

    .mobile-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 1rem;
    }

    .action-btn {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
    }

    .sidebar-mobile-backdrop {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 1049;
    }

    @media (min-width: 768px) {
      .mobile-only {
        display: none !important;
      }

      .desktop-only {
        display: block !important;
      }
    }

    @media (max-width: 767px) {
      .desktop-only {
        display: none !important;
      }

      .mobile-only {
        display: block !important;
      }

      .sidebar {
        position: fixed !important;
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

      .sidebar-mobile-backdrop.show {
        display: block !important;
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

    /* vista movil - diseno mejorado */
    .venta-card-custom {
      background: #ffffff;
      border: none;
      border-radius: 16px;
      padding: 18px 16px;
      margin-bottom: 16px;
      position: relative;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      overflow: hidden;
    }

    .venta-card-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .venta-card-custom::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(to bottom, #4e73ff, #224dff);
    }

    .venta-card-custom .edit-btn {
      position: absolute;
      top: 12px;
      right: 12px;
      z-index: 2;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 50%;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.2s ease;
    }

    .venta-card-custom .edit-btn:hover {
      transform: scale(1.1);
    }

    .venta-card-custom .venta-datos {
      background: #f8faff;
      border-radius: 12px;
      padding: 16px;
      margin: 16px 0;
      display: block;
      border: 1px solid #e0e8ff;
    }

    .venta-card-custom .venta-datos div {
      margin-bottom: 8px;
      color: #2d3748;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .venta-card-custom .venta-datos i {
      color: #4e73ff;
      font-size: 1.1rem;
      min-width: 20px;
    }

    .venta-card-custom .venta-btns {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-top: 12px;
    }

    .venta-card-custom .venta-btns button {
      width: 50%;
      font-weight: 600;
      border-radius: 8px;
      padding: 10px 0;
      font-size: 0.95rem;
      transition: all 0.2s ease;
      border-width: 2px;
    }

    .venta-card-custom .venta-btns .btn-outline-dark {
      border-color: #4e73ff;
      color: #4e73ff;
      background: transparent;
    }

    .venta-card-custom .venta-btns .btn-outline-dark:hover {
      background: #4e73ff;
      color: white;
    }

    .venta-card-custom .text-start {
      font-size: 1.2rem;
      font-weight: 700;
      color: #1a237e;
      margin-bottom: 4px;
      padding-left: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .venta-card-custom .text-start::before {
      content: '📱';
      font-size: 1.1rem;
    }

    @media (max-width: 400px) {
      .venta-card-custom {
        padding: 14px 12px;
        border-radius: 14px;
      }
      
      .venta-card-custom .venta-datos {
        padding: 12px;
        margin: 12px 0;
      }
      
      .venta-card-custom .venta-btns button {
        padding: 8px 0;
        font-size: 0.9rem;
      }
    }

    @media (max-width: 400px) {
      .venta-card-custom {
        padding: 10px 3px 10px 3px;
      }

      .venta-card-custom .venta-datos {
        background: #f4f6fb;
        /* Cambia aqui el color de fondo */
        border-radius: 8px;
        padding: 14px 14px 10px 14px;
        margin: 18px 0 18px 0;
        display: block;
        box-shadow: 0 1px 4px 0 rgba(120, 120, 120, .07);
        font-size: 1rem;
      }

    }

    /* vista movil */
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Boton hamburguesa y backdrop solo en movil -->
      <div class="mobile-navbar mobile-only">
        <button class="btn btn-link text-dark p-0" id="btnSidebarMobile" type="button">
          <i class="bi bi-list"></i>
        </button>
        <h2 class="mb-0"><i class="bi bi-cart"></i> Ventas</h2>
      </div>
      <div class="sidebar-mobile-backdrop" id="sidebarMobileBackdrop"></div>
      <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-3">
        <!-- Busqueda y boton en movil -->
        <div class="mobile-only mb-3">
          <div class="input-group mb-3">
            <input type="text" id="mobileSearch" class="form-control" placeholder="Buscar por numero...">
            <button type="button" class="btn btn-outline-secondary" id="clearMobileSearchInput" title="Borrar busqueda">
              <i class="bi bi-x-lg"></i>
            </button>
            <span class="input-group-text"><i class="bi bi-search"></i></span>
          </div>
          <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
            <i class="bi bi-plus-circle"></i> Nueva venta
          </button>
        </div>

        <!-- Desktop: barra de busqueda y boton -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 desktop-only">
          <h2><i class="bi bi-cart"></i> Listado de Ventas</h2>
          <div class="d-flex gap-2">
            <div class="input-group">
              <input type="text" id="searchInput" class="form-control" placeholder="Buscar por numero...">
              <button type="button" class="btn btn-outline-secondary" id="clearSearchInput" title="Borrar busqueda">
                <i class="bi bi-x-lg"></i>
              </button>
              <span class="input-group-text"><i class="bi bi-search"></i></span>
            </div>
            <div class="input-group" style="width: 350px;">
              <select class="form-select" id="filtroCuenta">
                <option value="">Todas las cuentas</option>
              <?php
              $sqlCuentas = "SELECT id, correo, estado FROM cuentas ORDER BY estado DESC, correo";
              $resCuentas = mysqli_query($conn, $sqlCuentas);
              while ($cuenta = mysqli_fetch_assoc($resCuentas)) {
                $color = $cuenta['estado'] === 'activa' ? 'text-success' : 'text-danger';
                echo "<option value='{$cuenta['id']}' class='{$color}'>" . 
                     htmlspecialchars($cuenta['correo']) . 
                     ($cuenta['estado'] === 'activa' ? '' : ' (Inactiva)') . 
                     "</option>";
              }
              ?>
              </select>
              <?php if(isset($_GET['cuenta_id'])) { ?>
                <button class="btn btn-outline-secondary" id="limpiarFiltros" type="button" title="Limpiar filtros">
                  <i class="bi bi-x-lg"></i>
                </button>
              <?php } ?>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
              <i class="bi bi-plus-circle"></i> Nueva venta
            </button>
          </div>
        </div>

        <!-- vista escritorio -->
        <?php
        $meses = [
          1 => 'ene',
          2 => 'feb',
          3 => 'mar',
          4 => 'abr',
          5 => 'may',
          6 => 'jun',
          7 => 'jul',
          8 => 'ago',
          9 => 'sep',
          10 => 'oct',
          11 => 'nov',
          12 => 'dic'
        ];
        ?>

        <!-- Vista desktop -->
        <div class="table-container desktop-only">
          <div class="table-responsive">
            <table class="table table-hover align-middle text-nowrap mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-center">TC</th>
                  <th class="text-center">Pago</th>
                  <th class="text-center">Numero celular</th>
                  <th class="text-center">Cuenta</th>
                  <th class="text-center">Fecha inicio</th>
                  <th class="text-center">Fecha fin</th>
                  <th class="text-center">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                      Dias restantes
                      <button type="button" class="btn btn-sm btn-link p-0 border-0" id="ordenarDias">
                        <i class="bi bi-arrow-down-up"></i>
                      </button>
                    </div>
                  </th>
                  <th class="text-center">Opciones</th>
                </tr>
              </thead>
              <tbody>
                <?php
            $filtroCuenta = "";
            if (isset($_GET['cuenta_id']) && $_GET['cuenta_id'] !== '') {
                $filtroCuenta = "AND v.cuenta_id = " . intval($_GET['cuenta_id']);
            }

            $ordenDias = isset($_GET['orden_dias']) && $_GET['orden_dias'] === 'asc' ? 'ASC' : 'DESC';
            
            $sql = "SELECT 
                v.id, 
                v.numero_celular, 
                v.fecha_inicio, 
                v.fecha_fin, 
                v.dias, 
                v.pago, 
                v.cuenta_id,
                c.correo AS cuenta_correo,
                c.contrasena_gpt,
                c.tipo_cuenta,
                DATEDIFF(v.fecha_fin, CURDATE()) AS dias_restantes
            FROM ventas v
            INNER JOIN cuentas c ON v.cuenta_id = c.id
            WHERE 1=1 $filtroCuenta
            ORDER BY dias_restantes $ordenDias, v.fecha_inicio DESC";

                $resultado = mysqli_query($conn, $sql);

                if (mysqli_num_rows($resultado) > 0) {
                  while ($fila = mysqli_fetch_assoc($resultado)) {
                    // Formateo de fechas
              try {
                $fechaInicio = new DateTime($fila['fecha_inicio']);
                $fechaFin = new DateTime($fila['fecha_fin']);
                
                if (!$fechaInicio || !$fechaFin) {
                  throw new Exception('Fecha invalida');
                }
              } catch (Exception $e) {
                error_log("Error al parsear fechas para venta ID {$fila['id']}: " . $e->getMessage());
                continue; // Saltar esta fila si hay error
              }

                    $diaInicio = $fechaInicio->format('d');
                    $mesInicio = $meses[(int)$fechaInicio->format('n')];
                    $anoInicio = $fechaInicio->format('Y');

                    $diaFin = $fechaFin->format('d');
                    $mesFin = $meses[(int)$fechaFin->format('n')];
                    $anoFin = $fechaFin->format('Y');

                    // Calculo de dias
                    $diasContratados = $fechaFin->diff($fechaInicio)->days;
                    $hoy = new DateTime();
                    $diasRestantes = (int)$hoy->diff($fechaFin)->format('%r%a'); // Convertir a entero

                    // Determinar clase y valor a mostrar
                    if ($diasRestantes < 1) {
                      $claseDias = 'text-danger';
                      $diasMostrar = $diasRestantes; // Mostrar valor negativo si corresponde
                    } else {
                      $claseDias = '';
                      $diasMostrar = $diasRestantes;
                    }

                    // Normalizar tipo de cuenta para JS
                    $tipoRaw = strtolower(trim($fila['tipo_cuenta'] ?? 'gpt'));
                    if (strpos($tipoRaw, 'gemini') !== false) {
                      $tipoKey = 'gemini';
                    } elseif (strpos($tipoRaw, 'perplex') !== false) {
                      $tipoKey = 'perplexity';
                    } else {
                      $tipoKey = 'gpt';
                    }
                    $tipoNombreMap = [
                      'gpt' => 'Chat Gpt Plus',
                      'gemini' => 'Gemini Advanced',
                      'perplexity' => 'Perplexity Pro'
                    ];
                    $tipoNombre = $tipoNombreMap[$tipoKey];

                    echo "<tr>
    <td class='text-center'>{$diasContratados}</td>
    <td class='text-center'>$" . number_format($fila['pago'], 2) . "</td>
    <td class='text-center'>" . htmlspecialchars($fila['numero_celular']) . "</td>
    <td class='text-center'>" . htmlspecialchars($fila['cuenta_correo']) . "</td>
    <td class='text-center'>{$diaInicio} {$mesInicio} {$anoInicio}</td>
    <td class='text-center'>{$diaFin} {$mesFin} {$anoFin}</td>
    <td class='text-center {$claseDias}'>{$diasMostrar}</td>
    <td class='text-center'>
        <button class='btn btn-sm btn-warning edit-venta me-1' 
            data-id='{$fila['id']}'
                data-numero_celular='{$fila['numero_celular']}'
                data-fecha_inicio='{$fechaInicio->format('Y-m-d')}'
                data-fecha_fin='{$fechaFin->format('Y-m-d')}'
                data-pago='{$fila['pago']}'
                data-cuenta_id='{$fila['cuenta_id']}'
                data-vendedor_id='{$_SESSION['user_id']}'>
            <i class='bi bi-pencil'></i>
        </button>
        <button class='btn btn-sm btn-danger delete-venta me-1' 
                data-id='{$fila['id']}'>
            <i class='bi bi-trash'></i>
        </button>
                <button class='btn btn-sm btn-info copy-btn' 
                data-correo='" . htmlspecialchars($fila['cuenta_correo'], ENT_QUOTES) . "'
                data-contrasena='" . htmlspecialchars($fila['contrasena_gpt'], ENT_QUOTES) . "'
                data-inicio='{$diaInicio} {$mesInicio} {$anoInicio}'
                data-fin='{$diaFin} {$mesFin} {$anoFin}'
                data-dias='{$diasContratados}'
                data-tipo_cuenta='" . htmlspecialchars($fila['tipo_cuenta'] ?? 'gpt', ENT_QUOTES) . "'
                data-tipo_cuenta_key='{$tipoKey}'
                data-tipo_cuenta_nombre='" . htmlspecialchars($tipoNombre, ENT_QUOTES) . "'>
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
        </div>

        <!-- Vista movil -->
        <div class="mobile-only">
          <?php
          mysqli_data_seek($resultado, 0);
          if (mysqli_num_rows($resultado) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
              // Formateo de fechas
              $fechaInicio = new DateTime($fila['fecha_inicio']);
              $fechaFin = new DateTime($fila['fecha_fin']);

              $diaInicio = $fechaInicio->format('d');
              $mesInicio = $meses[(int)$fechaInicio->format('n')];
              $anoInicio = $fechaInicio->format('Y');

              $diaFin = $fechaFin->format('d');
              $mesFin = $meses[(int)$fechaFin->format('n')];
              $anoFin = $fechaFin->format('Y');

              // Calculos
              $diasContratados = $fechaFin->diff($fechaInicio)->days;
              $hoy = new DateTime();
              $diasRestantes = (int)$hoy->diff($fechaFin)->format('%r%a');

              // Determinar clase y valor
              if ($diasRestantes < 1) {
                $claseBadge = 'bg-danger';
                $diasMostrar = $diasRestantes;
              } else {
                $claseBadge = 'bg-primary';
                $diasMostrar = $diasRestantes;
              }

              // Normalizar tipo de cuenta para JS (movil)
              $tipoRawM = strtolower(trim($fila['tipo_cuenta'] ?? 'gpt'));
              if (strpos($tipoRawM, 'gemini') !== false) {
                $tipoKeyM = 'gemini';
              } elseif (strpos($tipoRawM, 'perplex') !== false) {
                $tipoKeyM = 'perplexity';
              } else {
                $tipoKeyM = 'gpt';
              }
              $tipoNombreMapM = [
                'gpt' => 'Chat Gpt Plus',
                'gemini' => 'Gemini Advanced',
                'perplexity' => 'Perplexity Pro'
              ];
              $tipoNombreM = $tipoNombreMapM[$tipoKeyM];

              echo "<div class='venta-card-custom mb-3'>
        <div class='edit-btn'>
            <button class='btn btn-warning btn-sm edit-venta'
                data-id='{$fila['id']}'
                data-numero_celular='{$fila['numero_celular']}'
                data-fecha_inicio='{$fechaInicio->format('Y-m-d')}'
                data-fecha_fin='{$fechaFin->format('Y-m-d')}'
                data-pago='{$fila['pago']}'
                data-cuenta_id='{$fila['cuenta_id']}'
                data-vendedor_id='{$_SESSION['user_id']}'>
                <i class='bi bi-pencil'></i>
            </button>
        </div>
        <div class='text-start fw-bold fs-5 mb-2' style='padding-left:8px;'>" . htmlspecialchars($fila['numero_celular']) . "</div>
        <div class='venta-datos mx-auto mb-3' style='background:#fff; border-radius:4px; padding:10px 12px; display:inline-block;'>
            <div><strong>TC:</strong> {$diasContratados}</div>
            <div><i class='bi bi-cash-coin me-2'></i>$" . number_format($fila['pago'], 2) . "</div>
            <div><i class='bi bi-envelope me-2'></i>" . htmlspecialchars($fila['cuenta_correo']) . "</div>
            <div><i class='bi bi-calendar me-2'></i>{$diaInicio} {$mesInicio} {$anoInicio}</div>
            <div><i class='bi bi-calendar-check me-2'></i>{$diaFin} {$mesFin} {$anoFin}</div>
        </div>
        <div class='venta-btns d-flex justify-content-between gap-2 px-2 pb-2'>
            <button class='btn btn-outline-dark w-50 delete-venta' data-id='{$fila['id']}'>Eliminar</button>
                <button class='btn btn-outline-dark w-50 copy-btn'
                data-correo='" . htmlspecialchars($fila['cuenta_correo'], ENT_QUOTES) . "'
                data-contrasena='" . htmlspecialchars($fila['contrasena_gpt'], ENT_QUOTES) . "'
                data-inicio='{$diaInicio} {$mesInicio} {$anoInicio}'
                data-fin='{$diaFin} {$mesFin} {$anoFin}'
                data-dias='{$diasContratados}'
                data-tipo_cuenta='" . htmlspecialchars($fila['tipo_cuenta'] ?? 'gpt', ENT_QUOTES) . "'
                data-tipo_cuenta_key='{$tipoKeyM}'
                data-tipo_cuenta_nombre='" . htmlspecialchars($tipoNombreM, ENT_QUOTES) . "'>
                Copiar
            </button>
        </div>
    </div>";
            }
          } else {
            echo "<div class='text-center p-4'>No hay ventas registradas</div>";
          }
          ?>
        </div>

        <!-- Modal Editar Venta - Nueva Version -->
        <div class="modal fade" id="editarVentaModal" tabindex="-1" aria-labelledby="editarVentaModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form id="formEditarVenta" autocomplete="off">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title" id="editarVentaModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Editar Venta
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" id="edit-id">
                  
                  <div class="row g-3">
                    <!-- Numero Celular -->
                    <div class="col-md-6">
                      <label for="edit-numero_celular" class="form-label">Numero Celular</label>
                      <input type="text" class="form-control" name="numero_celular" id="edit-numero_celular" 
                             pattern="[0-9]{7,20}" title="Solo numeros, minimo 7 digitos" required>
                    </div>

                    <!-- Cuenta -->
                    <div class="col-md-6">
                      <label for="edit-cuenta_id" class="form-label">Cuenta</label>
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

                    <!-- Fechas -->
                    <div class="col-md-6">
                      <label for="edit-fecha_inicio" class="form-label">Fecha Inicio</label>
                      <input type="date" class="form-control" name="fecha_inicio" id="edit-fecha_inicio" required>
                      <div class="invalid-feedback">Fecha invalida</div>
                    </div>
                    <div class="col-md-6">
                      <label for="edit-fecha_fin" class="form-label">Fecha Fin</label>
                      <input type="date" class="form-control" name="fecha_fin" id="edit-fecha_fin" required>
                      <div class="invalid-feedback">Fecha invalida</div>
                    </div>

                    <!-- Pago -->
                    <div class="col-md-6">
                      <label for="edit-pago" class="form-label">Pago (Bs)</label>
                      <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" class="form-control" 
                               name="pago" id="edit-pago" required>
                      </div>
                    </div>

                    <!-- Vendedor (oculto) -->
                    <input type="hidden" name="vendedor_id" id="edit-vendedor_id" value="<?php echo $_SESSION['user_id']; ?>">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                  </button>
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar Cambios
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Manejar clic en boton editar
          document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('.edit-venta');
            if (btn) {
              // Validar y cargar datos
              const fechaInicio = btn.dataset.fecha_inicio;
              const fechaFin = btn.dataset.fecha_fin;
              
              if (!fechaInicio || !fechaFin || 
                  !fechaInicio.match(/^\d{4}-\d{2}-\d{2}$/) || 
                  !fechaFin.match(/^\d{4}-\d{2}-\d{2}$/)) {
                console.error('Error: Fechas invalidas', {fechaInicio, fechaFin});
                alert('Error: No se pueden cargar los datos de esta venta. Contacte al administrador.');
                return;
              }

              // Cargar datos en el formulario
              document.getElementById('edit-id').value = btn.dataset.id || '';
              document.getElementById('edit-numero_celular').value = btn.dataset.numero_celular || '';
              document.getElementById('edit-fecha_inicio').value = fechaInicio;
              document.getElementById('edit-fecha_fin').value = fechaFin;
              document.getElementById('edit-pago').value = btn.dataset.pago || '';
              document.getElementById('edit-cuenta_id').value = btn.dataset.cuenta_id || '';
              document.getElementById('edit-vendedor_id').value = btn.dataset.vendedor_id || '';

              // Mostrar modal
              new bootstrap.Modal(document.getElementById('editarVentaModal')).show();
            }
          });

          // Validacion del formulario
          document.getElementById('formEditarVenta').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar fechas
            const fechaInicio = new Date(this.elements['fecha_inicio'].value);
            const fechaFin = new Date(this.elements['fecha_fin'].value);
            
            if (fechaFin <= fechaInicio) {
              alert('Error: La fecha fin debe ser posterior a la fecha inicio');
              return;
            }

            // Enviar datos
            const formData = new FormData(this);
            fetch('editar_venta.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // alert('Venta actualizada correctamente');
                location.reload();
              } else {
                alert('Error: ' + (data.error || 'Error desconocido'));
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('Error al procesar la solicitud');
            });
          });
        });
        </script>
        <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            // Ordenar por dias
            const btnOrdenarDias = document.getElementById('ordenarDias');
            if (btnOrdenarDias) {
              btnOrdenarDias.addEventListener('click', function() {
                const url = new URL(window.location.href);
                const currentOrder = url.searchParams.get('orden_dias');
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
                url.searchParams.set('orden_dias', newOrder);
                
                // Cambiar icono segun el orden
                const icon = this.querySelector('i');
                if (icon) {
                  icon.className = newOrder === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
                }
                
                window.location.href = url.toString();
              });
              
              // Establecer icono inicial
              const url = new URL(window.location.href);
              const currentOrder = url.searchParams.get('orden_dias');
              const icon = btnOrdenarDias.querySelector('i');
              if (icon) {
                icon.className = currentOrder === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
              }
            }

            // Limpiar filtros
            document.getElementById('limpiarFiltros')?.addEventListener('click', function() {
              const url = new URL(window.location.href);
              url.searchParams.delete('cuenta_id');
              window.location.href = url.toString();
            });

            // Filtro por cuenta
            document.getElementById('filtroCuenta').addEventListener('change', function() {
              const cuentaId = this.value;
              const url = new URL(window.location.href);
              
              if (cuentaId) {
                url.searchParams.set('cuenta_id', cuentaId);
              } else {
                url.searchParams.delete('cuenta_id');
              }
              
              window.location.href = url.toString();
            });

            // Sidebar movil
            const sidebar = document.querySelector('.sidebar');
            const sidebarBackdrop = document.getElementById('sidebarMobileBackdrop');
            const btnSidebarMobile = document.getElementById('btnSidebarMobile');
            if (btnSidebarMobile && sidebar && sidebarBackdrop) {
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

            // Delegacion de eventos para botones de acciones
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
                // El modal se abre automaticamente por data-bs-toggle/data-bs-target
              }

              // Eliminar
              if (event.target.closest('.delete-venta')) {
                const btn = event.target.closest('.delete-venta');
                if (confirm('¿Seguro que quieres eliminar esta venta?')) {
                  const id = btn.dataset.id;
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
                        // Recargar la pagina completa
                        window.location.reload();
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
                // Deteccion robusta del tipo de cuenta segun el valor recibido
                const tipoRaw = (btn.dataset.tipo_cuenta || '').toLowerCase();
                let nombreCuenta = 'Chat Gpt Plus';
                if (tipoRaw.includes('gemini')) {
                  nombreCuenta = 'Gemini Advanced';
                } else if (tipoRaw.includes('perplex')) {
                  nombreCuenta = 'Perplexity Pro';
                } else if (tipoRaw.includes('gpt') || tipoRaw.includes('chat')) {
                  nombreCuenta = 'Chat Gpt Plus';
                }

                const mensaje = `Datos para ingresar a la cuenta de ${nombreCuenta}

Cuenta ${nombreCuenta} (${btn.dataset.dias} dias)
Correo: ${btn.dataset.correo}
Contrasena: ${btn.dataset.contrasena}

Fecha ini: ${btn.dataset.inicio}
Fecha end: ${btn.dataset.fin}

Reglas para el uso de la cuenta:

- No modificar ningun dato de la cuenta, en caso de modificar algun dato de la cuenta, retiro la cuenta del grupo de trabajo y te quitare el acceso, no cubrire la garantia y el tiempo de servicio.
- Evita salirte de la cuenta.
- Referentemente, usa la aplicacion movil en el celular y en computadora navegador Google Chrome NO PESTAnA INCoGNITO 
- Link para pc preferentemente la pagina oficial en tu navegador

Ingresa ahora por favor y te paso los codigos de activacion`;

                navigator.clipboard.writeText(mensaje)
                  .then(() => {
                    // Crear y mostrar alerta de Bootstrap
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    alertDiv.style.zIndex = '9999';
                    alertDiv.role = 'alert';
                    alertDiv.innerHTML = `
                      <strong>Mensaje copiado al portapeles</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    
                    // Cerrar automaticamente después de 3 segundos
                    setTimeout(() => {
                      const bsAlert = new bootstrap.Alert(alertDiv);
                      bsAlert.close();
                    }, 3000);
                  })
                  .catch(err => {
                    console.error('Error al copiar:', err);
                    alert('No se pudo copiar automaticamente');
                  });
              }
            });

          // Guardar cambios edicion - Usando SweetAlert2 para mejor experiencia
          document.getElementById('formEditarVenta').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            let processingAlert;
            
            // Mostrar alerta de procesamiento sin timer
            processingAlert = Swal.fire({
              title: 'Procesando...',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
                
                // Forzar duración mínima de 3 segundos
                setTimeout(() => {
                  if (Swal.isVisible()) {
                    Swal.close();
                  }
                }, 3000);
              }
            });

            fetch('editar_venta.php', {
                method: 'POST',
                body: formData
              })
              .then(res => res.json())
              .then(data => {
                Swal.close();
                if (data.success) {
                  Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Venta actualizada correctamente',
                    confirmButtonText: 'Aceptar'
                  }).then(() => {
                    window.location.reload();
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al actualizar: ' + (data.error || 'Error desconocido')
                  });
                }
              })
              .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Error al procesar la solicitud'
                });
              });
          });

            // Busqueda dinamica para ambas vistas
            function handleSearch(value) {
              // Desktop
              document.querySelectorAll('.table tbody tr').forEach(row => {
                const celular = row.children[1].textContent.toLowerCase();
                row.style.display = celular.includes(value) ? '' : 'none';
              });
              // Movil
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
        <script>
          // Busqueda dinamica
          document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            // Para version desktop
            document.querySelectorAll('.table tbody tr').forEach(row => {
              const celular = row.children[2].textContent.toLowerCase();
              row.style.display = celular.includes(searchTerm) ? '' : 'none';
            });

            // Para version movil
            document.querySelectorAll('.venta-card').forEach(card => {
              const celular = card.querySelector('.title').textContent.toLowerCase();
              card.style.display = celular.includes(searchTerm) ? 'block' : 'none';
            });
          });
        </script>
        <!-- Modal Nueva Venta -->
        <div class="modal fade" id="nuevaVentaModal" tabindex="-1" aria-labelledby="nuevaVentaModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form id="formNuevaVenta" autocomplete="off">
                <div class="modal-header">
                  <h5 class="modal-title" id="nuevaVentaModalLabel">Registrar Nueva Venta</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="numero_celular" class="form-label">Numero celular</label>
                    <input type="text" class="form-control" name="numero_celular" id="numero_celular" required>
                  </div>
                  <div class="mb-3">
                    <label for="cuenta_id" class="form-label">Cuenta</label>
                    <select class="form-select" name="cuenta_id" id="cuenta_id" required>
                      <option value="">Selecciona una cuenta...</option>
                      <?php
                      $sqlCuentas = "SELECT c.id, c.correo, COUNT(v.id) as ventas_count 
                                   FROM cuentas c 
                                   LEFT JOIN ventas v ON c.id = v.cuenta_id 
                                   WHERE c.estado='activa' 
                                   GROUP BY c.id 
                                   ORDER BY c.correo";
                      $resCuentas = mysqli_query($conn, $sqlCuentas);
                      while ($cuenta = mysqli_fetch_assoc($resCuentas)) {
                        echo "<option value='{$cuenta['id']}'>" . 
                             htmlspecialchars($cuenta['correo']) . 
                             " (" . $cuenta['ventas_count'] . " ventas)</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <div class="row">
                    <div class="col-6 mb-3">
                      <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                      <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required>
                    </div>
                    <div class="col-6 mb-3">
                      <label for="duracion" class="form-label">Duracion</label>
                      <select class="form-select" id="duracion" required>
                        <option value="30">30 dias</option>
                        <option value="60">60 dias</option>
                        <option value="90">90 dias</option>
                      </select>
                    </div>
                  </div>
                  <input type="hidden" name="fecha_fin" id="fecha_fin">
                  <div class="mb-3">
                    <label for="pago" class="form-label">Pago</label>
                    <input type="number" step="0.01" class="form-control" name="pago" id="pago" required>
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

        <script>
          // Calcular fecha fin automaticamente
          document.addEventListener('DOMContentLoaded', function() {
            const fechaInicio = document.getElementById('fecha_inicio');
            const duracion = document.getElementById('duracion');
            const fechaFin = document.getElementById('fecha_fin');
            
            function calcularFechaFin() {
              if (fechaInicio.value) {
                const fecha = new Date(fechaInicio.value);
                fecha.setDate(fecha.getDate() + parseInt(duracion.value));
                fechaFin.value = fecha.toISOString().split('T')[0];
              }
            }
            
            fechaInicio.addEventListener('change', calcularFechaFin);
            duracion.addEventListener('change', calcularFechaFin);
            
            // Calcular fecha inicial si hay valores
            if (fechaInicio.value) {
              calcularFechaFin();
            }
          });

          document.getElementById('formNuevaVenta').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            fetch('guardar_venta.php', {
                method: 'POST',
                body: formData
              })
              .then(res => res.json())
              .then(response => {
                if (response.success) {
                  // Cerrar modal
                  const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaVentaModal'));
                  if (modal) modal.hide();

                  // Intentar copiar el mensaje predeterminado al portapapeles
                  const texto = response.clipboardText || '';
                  if (texto) {
                    navigator.clipboard.writeText(texto)
                      .then(() => {
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                        alertDiv.style.zIndex = '9999';
                        alertDiv.role = 'alert';
                        alertDiv.innerHTML = `
                          <strong>Mensaje copiado al portapapeles</strong>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.body.appendChild(alertDiv);
                        setTimeout(() => {
                          const bsAlert = new bootstrap.Alert(alertDiv);
                          bsAlert.close();
                        }, 3000);
                      })
                      .catch(() => {
                        // Silencioso: si falla el copiado, igual continuamos con el éxito
                      });
                  }

                  // Mostrar aviso no intrusivo y recargar sin pedir confirmación
                  const alertDiv2 = document.createElement('div');
                  alertDiv2.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                  alertDiv2.style.zIndex = '9999';
                  alertDiv2.role = 'alert';
                  alertDiv2.innerHTML = `
                    <strong>Venta guardada - Mensaje copiado</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  `;
                  document.body.appendChild(alertDiv2);

                  setTimeout(() => {
                    try {
                      const bsAlert2 = new bootstrap.Alert(alertDiv2);
                      bsAlert2.close();
                    } catch (e) {}
                  }, 3000);

                  // Recargar para actualizar la tabla sin interacción adicional
                  setTimeout(() => {
                    location.reload();
                  }, 800);
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Error al guardar la venta'
                  });
                }
              })
              .catch(() => {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Error en la conexion o al procesar la solicitud'
                });
              });
          });
        </script>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            // Funcion de busqueda unificada
            function handleSearch(searchTerm) {
              const term = searchTerm.toLowerCase().trim();

              // Filtrar tabla desktop
              document.querySelectorAll('.table tbody tr').forEach(row => {
                const celular = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                row.style.display = celular.includes(term) ? '' : 'none';
              });

              // Filtrar cards moviles (version actualizada)
              document.querySelectorAll('.venta-card-custom').forEach(card => {
                const celular = card.querySelector('.text-start').textContent.toLowerCase(); // Cambiado a .text-start
                card.style.display = celular.includes(term) ? 'block' : 'none';
              });
            }

            // Eventos de busqueda
            document.getElementById('searchInput').addEventListener('input', function() {
              handleSearch(this.value);
            });

            document.getElementById('mobileSearch').addEventListener('input', function() {
              handleSearch(this.value);
            });

            // Botones para limpiar busqueda
            document.getElementById('clearSearchInput').addEventListener('click', function() {
              document.getElementById('searchInput').value = '';
              handleSearch('');
              document.getElementById('searchInput').focus();
            });

            document.getElementById('clearMobileSearchInput').addEventListener('click', function() {
              document.getElementById('mobileSearch').value = '';
              handleSearch('');
              document.getElementById('mobileSearch').focus();
            });
          });
        </script>

        <script>
          document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', function(e) {
              const btn = e.target.closest('.edit-venta');
              if (btn) {
                console.log('Datos del boton:', {
                  id: btn.dataset.id,
                  numero: btn.dataset.numero_celular,
                  inicio: btn.dataset.fecha_inicio,
                  fin: btn.dataset.fecha_fin,
                  pago: btn.dataset.pago,
                  cuenta: btn.dataset.cuenta_id,
                  vendedor: btn.dataset.vendedor_id
                });

                // Validar fechas antes de asignar
                const fechaInicio = btn.dataset.fecha_inicio || '';
                const fechaFin = btn.dataset.fecha_fin || '';
                
                if (!fechaInicio.match(/^\d{4}-\d{2}-\d{2}$/) || !fechaFin.match(/^\d{4}-\d{2}-\d{2}$/)) {
                  console.error('Formato de fecha invalido:', {fechaInicio, fechaFin});
                  alert('Error: Formato de fecha invalido. Contacte al administrador.');
                  return;
                }

                // Rellenar campos del modal
                document.getElementById('edit-id').value = btn.dataset.id || '';
                document.getElementById('edit-numero_celular').value = btn.dataset.numero_celular || '';
                document.getElementById('edit-fecha_inicio').value = fechaInicio;
                document.getElementById('edit-fecha_fin').value = fechaFin;
                document.getElementById('edit-pago').value = btn.dataset.pago || '';
                document.getElementById('edit-cuenta_id').value = btn.dataset.cuenta_id || '';
                document.getElementById('edit-vendedor_id').value = btn.dataset.vendedor_id || '';

                // Mostrar el modal de edicion
                const modal = new bootstrap.Modal(document.getElementById('editarVentaModal'));
                modal.show();
              }
            });
          });
        </script>

</body>

</html>
