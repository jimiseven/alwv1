<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
requireLogin();

$tipoCuentaActual = isset($_GET['tipo_cuenta']) ? trim($_GET['tipo_cuenta']) : '';
$tiposCuenta = [];

$sqlTiposCuenta = "SELECT DISTINCT tipo_cuenta FROM cuentas WHERE tipo_cuenta IS NOT NULL AND tipo_cuenta <> '' ORDER BY tipo_cuenta";
$resTiposCuenta = mysqli_query($conn, $sqlTiposCuenta);

if ($resTiposCuenta) {
    while ($tipoRow = mysqli_fetch_assoc($resTiposCuenta)) {
        $tiposCuenta[] = $tipoRow['tipo_cuenta'];
    }
}

if ($tipoCuentaActual !== '' && !in_array($tipoCuentaActual, $tiposCuenta, true)) {
    $tipoCuentaActual = '';
}
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

        .table-container {
            max-height: 600px;
            overflow-y: auto;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            padding: 1rem;
        }

        .ventas-toolbar {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
            margin-bottom: 1.5rem;
        }

        .ventas-toolbar .toolbar-header {
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
            margin-bottom: 1.25rem;
        }

        .ventas-toolbar h2 {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
            font-size: 1.75rem;
        }

        .ventas-toolbar .toolbar-main {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 1.5rem;
            align-items: end;
        }

        .ventas-toolbar .toolbar-search {
            min-width: 280px;
        }

        .ventas-toolbar .toolbar-search .input-group {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .ventas-toolbar .toolbar-search .input-group-text {
            border: 1px solid #e2e8f0;
            background: white;
        }

        .ventas-toolbar .toolbar-search .form-control {
            border: 1px solid #e2e8f0;
            padding: 0.65rem 1rem;
        }

        .ventas-toolbar .toolbar-search .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .ventas-toolbar .toolbar-filters {
            display: flex;
            gap: 1rem;
            align-items: end;
        }

        .ventas-toolbar .filter-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            min-width: 200px;
        }

        .ventas-toolbar .filter-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            margin: 0;
        }

        .ventas-toolbar .filter-item .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.65rem 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.2s;
        }

        .ventas-toolbar .filter-item .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .ventas-toolbar #limpiarFiltros {
            padding: 0.65rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .ventas-toolbar #limpiarFiltros:hover {
            background-color: #dc2626;
            border-color: #dc2626;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .ventas-toolbar .toolbar-action .btn-primary {
            padding: 0.75rem 2rem;
            border-radius: 0.9rem;
            font-weight: 600;
            font-size: 1.05rem;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
            transition: all 0.2s;
        }

        .ventas-toolbar .toolbar-action .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        @media (max-width: 1400px) {
            .ventas-toolbar .toolbar-main {
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }

            .ventas-toolbar .toolbar-filters {
                flex-wrap: wrap;
            }
        }

        .ventas-toolbar-mobile {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .ventas-toolbar-mobile .toolbar-block {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .ventas-toolbar-mobile .input-group > .input-group-text {
            border-radius: 0.75rem 0 0 0.75rem;
            border-right: none;
        }

        .ventas-toolbar-mobile .input-group > .form-control,
        .ventas-toolbar-mobile .input-group > .btn,
        .ventas-toolbar-mobile .form-select {
            border-radius: 0.75rem;
        }

        .ventas-toolbar-mobile .toolbar-action .btn {
            padding: 0.75rem 1rem;
            border-radius: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
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
                left: -260px !important;
                transform: translateX(0) !important;
                transition: left 0.3s ease;
                box-shadow: none;
            }

            .sidebar.show {
                left: 0 !important;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            .main-content {
                margin-left: 0;
                padding: 85px 12px 20px 12px;
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

            .ventas-toolbar-mobile {
                margin-top: 0.5rem;
                margin-bottom: 1.25rem;
                padding: 1rem;
                box-shadow: 0 8px 20px rgba(15, 23, 42, 0.1);
            }

            .ventas-toolbar-mobile .toolbar-block {
                gap: 0.6rem;
            }

            .ventas-toolbar-mobile .form-label {
                font-size: 0.9rem;
                font-weight: 600;
                color: #334155;
                margin-bottom: 0.4rem;
            }

            .ventas-toolbar-mobile .input-group,
            .ventas-toolbar-mobile .form-select {
                font-size: 0.95rem;
            }

            .ventas-toolbar-mobile .toolbar-action {
                margin-top: 0.25rem;
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

            .mobile-only {
                display: none !important;
            }

            .desktop-only {
                display: block !important;
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

        .modal-header {
            pointer-events: auto !important;
            background: white !important;
            position: relative;
            z-index: 2;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-body {
            pointer-events: auto !important;
            background: white !important;
            position: relative;
            z-index: 2;
            padding: 1.5rem;
        }

        .modal-body .row {
            margin: 0;
            row-gap: 1.25rem;
        }

        .modal-body .form-label {
            font-weight: 600;
            color: #1e293b;
        }

        .modal-footer {
            pointer-events: auto !important;
            background: white !important;
            position: relative;
            z-index: 2;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .modal-footer .btn {
            font-size: 1.1rem;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }

        .modal-footer .btn-primary {
            background: #2563eb !important;
            color: #fff !important;
            border: none !important;
        }
        .modal-footer .btn-primary:hover {
            background: #1d4ed8 !important;
            color: #fff !important;
            box-shadow: 0 4px 16px rgba(37,99,235,0.15);
        }
        .modal-footer .btn-secondary {
            background: #f3f4f6 !important;
            color: #1e293b !important;
            border: 1px solid #cbd5e1 !important;
        }
        .modal-footer .btn-secondary:hover {
            background: #e5e7eb !important;
            color: #111827 !important;
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

        @media (max-width: 991.98px) {
            .mobile-only {
                display: block !important;
            }

            .desktop-only {
                display: none !important;
            }
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

    @media (max-width: 767px) {
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
    <div class="container-fluid p-0">
        <!-- Navbar móvil -->
        <div class="mobile-navbar mobile-only">
            <button class="btn btn-link text-dark p-0" id="btnSidebarMobile" type="button">
                <i class="bi bi-list"></i>
            </button>
            <h2 class="mb-0"><i class="bi bi-cart"></i> Ventas</h2>
        </div>
        <div class="sidebar-mobile-backdrop" id="sidebarMobileBackdrop"></div>

        <div class="d-flex">
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
            <main class="main-content p-4">
        <!-- Toolbar móvil -->
        <div class="ventas-toolbar-mobile mobile-only">
          <div class="toolbar-block">
            <label class="form-label mb-2" for="mobileSearch">Buscar por número</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
              <input type="text" id="mobileSearch" class="form-control" placeholder="Buscar por número...">
              <button type="button" class="btn btn-outline-secondary" id="clearMobileSearchInput" title="Borrar búsqueda">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
          <div class="toolbar-block">
            <label class="form-label mb-2" for="mobileFiltroTipo">Tipo de cuenta</label>
            <select class="form-select" id="mobileFiltroTipo">
              <option value="">Todos los tipos</option>
              <?php foreach ($tiposCuenta as $tipo): ?>
                <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo $tipo === $tipoCuentaActual ? 'selected' : ''; ?>>
                  <?php echo ucfirst(htmlspecialchars($tipo)); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="toolbar-block">
            <label class="form-label mb-2" for="mobileFiltroCuenta">Cuenta</label>
            <select class="form-select" id="mobileFiltroCuenta">
              <option value="">Todas las cuentas</option>
              <?php
              $sqlCuentasMobile = "SELECT id, correo, estado FROM cuentas ORDER BY estado DESC, correo";
              $resCuentasMobile = mysqli_query($conn, $sqlCuentasMobile);
              while ($cuenta = mysqli_fetch_assoc($resCuentasMobile)) {
                $color = $cuenta['estado'] === 'activa' ? 'text-success' : 'text-danger';
                $selected = (isset($_GET['cuenta_id']) && $_GET['cuenta_id'] == $cuenta['id']) ? 'selected' : '';
                echo "<option value='{$cuenta['id']}' class='{$color}' {$selected}>" .
                     htmlspecialchars($cuenta['correo']) .
                     ($cuenta['estado'] === 'activa' ? '' : ' (Inactiva)') .
                     "</option>";
              }
              ?>
            </select>
          </div>
          <div class="toolbar-action">
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
              <i class="bi bi-plus-circle"></i> Nueva venta
            </button>
          </div>
        </div>

        <!-- Desktop: barra de búsqueda y filtros -->
        <div class="ventas-toolbar desktop-only">
          <div class="toolbar-header">
            <h2 class="mb-0"><i class="bi bi-cart"></i> Listado de Ventas</h2>
          </div>
          <div class="toolbar-main">
            <div class="toolbar-search">
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Buscar por número...">
                <button type="button" class="btn btn-outline-secondary" id="clearSearchInput" title="Borrar búsqueda">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>
            <div class="toolbar-filters">
              <div class="filter-item">
                <label class="filter-label">Cuenta</label>
                <select class="form-select" id="filtroCuenta">
                  <option value="">Todas las cuentas</option>
                  <?php
                  $sqlCuentas = "SELECT id, correo, estado FROM cuentas ORDER BY estado DESC, correo";
                  $resCuentas = mysqli_query($conn, $sqlCuentas);
                  while ($cuenta = mysqli_fetch_assoc($resCuentas)) {
                    $color = $cuenta['estado'] === 'activa' ? 'text-success' : 'text-danger';
                    $selected = (isset($_GET['cuenta_id']) && $_GET['cuenta_id'] == $cuenta['id']) ? 'selected' : '';
                    echo "<option value='{$cuenta['id']}' class='{$color}' {$selected}>" .
                         htmlspecialchars($cuenta['correo']) .
                         ($cuenta['estado'] === 'activa' ? '' : ' (Inactiva)') .
                         "</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="filter-item">
                <label class="filter-label">Tipo</label>
                <select class="form-select" id="filtroTipoCuenta">
                  <option value="">Todos</option>
                  <?php foreach ($tiposCuenta as $tipo): ?>
                    <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo $tipo === $tipoCuentaActual ? 'selected' : ''; ?>>
                      <?php echo ucfirst(htmlspecialchars($tipo)); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php if(isset($_GET['cuenta_id']) || $tipoCuentaActual !== '') { ?>
                <button class="btn btn-outline-danger" id="limpiarFiltros" type="button" title="Limpiar filtros">
                  <i class="bi bi-x-circle"></i> Limpiar
                </button>
              <?php } ?>
            </div>
            <div class="toolbar-action">
              <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
                <i class="bi bi-plus-circle"></i> Nueva venta
              </button>
            </div>
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

            $filtroTipoCuenta = "";
            if ($tipoCuentaActual !== '') {
                $tipoCuentaEscaped = mysqli_real_escape_string($conn, $tipoCuentaActual);
                $filtroTipoCuenta = "AND c.tipo_cuenta = '" . $tipoCuentaEscaped . "'";
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
            WHERE 1=1 $filtroCuenta $filtroTipoCuenta";
            
            // Filtrar por rol: si no es admin, mostrar solo sus ventas
            if (!isAdmin()) {
                $sql .= " AND v.vendedor_id = " . $_SESSION['user_id'];
            }
            
            $sql .= " ORDER BY dias_restantes $ordenDias, v.fecha_inicio DESC";

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
              url.searchParams.delete('tipo_cuenta');
              window.location.href = url.toString();
            });

            // Filtro por cuenta
            const filtroCuentaSelect = document.getElementById('filtroCuenta');
            if (filtroCuentaSelect) {
              filtroCuentaSelect.addEventListener('change', function() {
                const cuentaId = this.value;
                const url = new URL(window.location.href);

                if (cuentaId) {
                  url.searchParams.set('cuenta_id', cuentaId);
                } else {
                  url.searchParams.delete('cuenta_id');
                }

                window.location.href = url.toString();
              });
            }

            // Filtro por tipo (desktop)
            const filtroTipoSelect = document.getElementById('filtroTipoCuenta');
            if (filtroTipoSelect) {
              filtroTipoSelect.addEventListener('change', function() {
                const tipo = this.value;
                const url = new URL(window.location.href);

                if (tipo) {
                  url.searchParams.set('tipo_cuenta', tipo);
                } else {
                  url.searchParams.delete('tipo_cuenta');
                }

                window.location.href = url.toString();
              });
            }

            // Filtro por tipo (móvil)
            const mobileFiltroTipo = document.getElementById('mobileFiltroTipo');
            if (mobileFiltroTipo) {
              mobileFiltroTipo.addEventListener('change', function() {
                const tipo = this.value;
                const url = new URL(window.location.href);

                if (tipo) {
                  url.searchParams.set('tipo_cuenta', tipo);
                } else {
                  url.searchParams.delete('tipo_cuenta');
                }

                window.location.href = url.toString();
              });
            }

            // Filtro por cuenta (móvil)
            const mobileFiltroCuenta = document.getElementById('mobileFiltroCuenta');
            if (mobileFiltroCuenta) {
              mobileFiltroCuenta.addEventListener('change', function() {
                const cuentaId = this.value;
                const url = new URL(window.location.href);

                if (cuentaId) {
                  url.searchParams.set('cuenta_id', cuentaId);
                } else {
                  url.searchParams.delete('cuenta_id');
                }

                window.location.href = url.toString();
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
                      $sqlCuentas = "SELECT c.id, c.correo, c.tipo_cuenta, COUNT(v.id) as ventas_count 
                                   FROM cuentas c 
                                   LEFT JOIN ventas v ON c.id = v.cuenta_id 
                                   WHERE c.estado='activa' 
                                   GROUP BY c.id, c.correo, c.tipo_cuenta 
                                   ORDER BY ventas_count ASC, c.correo ASC";
                      $resCuentas = mysqli_query($conn, $sqlCuentas);
                      
                      if (!$resCuentas) {
                        echo "<option value=''>Error al cargar cuentas</option>";
                      } else {
                        while ($cuenta = mysqli_fetch_assoc($resCuentas)) {
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
                          
                          echo "<option value='{$cuenta['id']}'>". 
                               $tipoLetra . " " .
                               htmlspecialchars($cuenta['correo']) . 
                               " (" . $cuenta['ventas_count'] . " ventas)</option>";
                        }
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

        <script>
          // Sidebar móvil - Script único y definitivo
          (function() {
              const sidebar = document.querySelector('.sidebar');
              const sidebarBackdrop = document.getElementById('sidebarMobileBackdrop');
              const btnSidebarMobile = document.getElementById('btnSidebarMobile');

              console.log('Inicializando sidebar móvil...');
              console.log('Elementos encontrados:', {
                  sidebar: !!sidebar,
                  backdrop: !!sidebarBackdrop,
                  button: !!btnSidebarMobile
              });

              if (!btnSidebarMobile || !sidebar || !sidebarBackdrop) {
                  console.error('No se encontraron todos los elementos del sidebar');
                  return;
              }

              // Toggle sidebar
              btnSidebarMobile.addEventListener('click', function(e) {
                  e.preventDefault();
                  e.stopPropagation();
                  
                  const isShowing = sidebar.classList.contains('show');
                  console.log('Toggle sidebar - Estado actual:', isShowing ? 'visible' : 'oculto');
                  
                  if (isShowing) {
                      sidebar.classList.remove('show');
                      sidebarBackdrop.classList.remove('show');
                      document.body.style.overflow = '';
                  } else {
                      sidebar.classList.add('show');
                      sidebarBackdrop.classList.add('show');
                      document.body.style.overflow = 'hidden';
                  }
                  
                  console.log('Nuevo estado:', sidebar.classList.contains('show') ? 'visible' : 'oculto');
              });

              // Cerrar sidebar al hacer click fuera de él
              document.addEventListener('click', function(e) {
                  if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
                      // Si el click no es en el sidebar ni en el botón de toggle
                      if (!sidebar.contains(e.target) && !btnSidebarMobile.contains(e.target)) {
                          console.log('Click fuera del sidebar - cerrando');
                          sidebar.classList.remove('show');
                          sidebarBackdrop.classList.remove('show');
                          document.body.style.overflow = '';
                      }
                  }
              });

              // Cerrar al hacer clic en enlaces (solo móvil)
              const sidebarLinks = sidebar.querySelectorAll('.nav-link');
              console.log('Enlaces del sidebar encontrados:', sidebarLinks.length);
              
              sidebarLinks.forEach(function(link) {
                  link.addEventListener('click', function(e) {
                      if (window.innerWidth < 992) {
                          console.log('Click en enlace - cerrando sidebar y navegando');
                          // No prevenir el comportamiento por defecto para permitir navegación
                          sidebar.classList.remove('show');
                          sidebarBackdrop.classList.remove('show');
                          document.body.style.overflow = '';
                          // Permitir que el enlace navegue normalmente
                      }
                  });
              });

              // Cerrar sidebar cuando se abre cualquier modal de Bootstrap
              document.addEventListener('show.bs.modal', function() {
                  console.log('Modal abierto - cerrando sidebar si está visible');
                  if (sidebar.classList.contains('show')) {
                      sidebar.classList.remove('show');
                      sidebarBackdrop.classList.remove('show');
                      document.body.style.overflow = '';
                  }
              });

              console.log('Sidebar móvil inicializado correctamente');
          })();
        </script>

</body>

</html>
