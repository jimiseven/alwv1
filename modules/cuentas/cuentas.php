<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
requireLogin();
requireAdmin(); // Solo administradores pueden gestionar cuentas
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
            max-height: 500px;
            overflow-y: auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
        }

        .mobile-only {
            display: none !important;
        }

        .mobile-action-label {
            display: none;
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
                padding: 76px 12px 18px 12px !important;
                width: 100%;
                transition: transform 0.3s ease;
                background: #f5f7fa;
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

            .mobile-navbar .btn {
                min-width: 42px;
                min-height: 42px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.45rem;
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

            .mobile-navbar.mobile-only {
                display: flex !important;
            }

            .page-header {
                align-items: stretch !important;
                flex-direction: column;
                gap: 0.75rem;
                margin-bottom: 1rem !important;
            }

            .page-header h2 {
                display: none;
            }

            .page-header .btn {
                width: 100%;
                min-height: 44px;
                border-radius: 12px;
                font-weight: 600;
            }

            .mobile-tools {
                background: #fff;
                border: 1px solid #e9edf3;
                border-radius: 18px;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
                margin-bottom: 1rem;
                padding: 0.9rem;
            }

            .mobile-summary {
                display: grid !important;
                gap: 0.6rem;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                margin-bottom: 0.85rem;
            }

            .mobile-summary-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 0.65rem 0.75rem;
            }

            .mobile-summary-card span {
                color: #64748b;
                display: block;
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.02em;
                text-transform: uppercase;
            }

            .mobile-summary-card strong {
                color: #0f172a;
                display: block;
                font-size: 1rem;
                margin-top: 0.2rem;
            }

            .mobile-search-wrap {
                position: relative;
            }

            .mobile-search-wrap .bi-search {
                color: #64748b;
                left: 0.9rem;
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
            }

            .mobile-search-wrap .form-control {
                border-radius: 14px;
                min-height: 46px;
                padding-left: 2.4rem;
                padding-right: 2.4rem;
            }

            .mobile-search-clear {
                border: 0;
                background: transparent;
                color: #64748b;
                min-height: 38px;
                min-width: 38px;
                position: absolute;
                right: 0.25rem;
                top: 50%;
                transform: translateY(-50%);
            }

            .mobile-filter-chips {
                display: flex;
                gap: 0.5rem;
                margin: 0.75rem -0.9rem 0;
                overflow-x: auto;
                padding: 0 0.9rem 0.15rem;
                scrollbar-width: none;
            }

            .mobile-filter-chips::-webkit-scrollbar {
                display: none;
            }

            .mobile-filter-chip {
                background: #f8fafc;
                border: 1px solid #cbd5e1;
                border-radius: 999px;
                color: #334155;
                flex: 0 0 auto;
                font-size: 0.84rem;
                font-weight: 700;
                min-height: 38px;
                padding: 0 0.9rem;
            }

            .mobile-filter-chip.active {
                background: #0d6efd;
                border-color: #0d6efd;
                color: #fff;
            }

            .mobile-sort-wrap {
                margin-top: 0.75rem;
            }

            .mobile-sort-wrap label {
                color: #64748b;
                display: block;
                font-size: 0.76rem;
                font-weight: 700;
                letter-spacing: 0.02em;
                margin-bottom: 0.35rem;
                text-transform: uppercase;
            }

            .mobile-sort-wrap .form-select {
                border-radius: 14px;
                min-height: 44px;
            }

            .mobile-results-meta {
                color: #64748b;
                display: flex !important;
                font-size: 0.82rem;
                justify-content: space-between;
                margin-top: 0.65rem;
            }

            .mobile-no-results {
                background: #fff;
                border: 1px dashed #cbd5e1;
                border-radius: 16px;
                color: #64748b;
                display: none;
                margin-bottom: 1rem;
                padding: 1.25rem;
                text-align: center;
            }

            .mobile-no-results.show {
                display: block !important;
            }

            .table-container {
                max-height: none;
                overflow: visible;
                background: transparent;
                border-radius: 0;
                box-shadow: none;
                margin-bottom: 1rem;
            }

            .table-container table,
            .table-container thead,
            .table-container tbody,
            .table-container tfoot,
            .table-container tr,
            .table-container th,
            .table-container td {
                display: block;
                width: 100%;
            }

            .table-container thead {
                display: none;
            }

            .table-container tbody tr {
                background: #fff;
                border: 1px solid #e9edf3;
                border-radius: 12px;
                box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07);
                margin-bottom: 0.55rem;
                overflow: hidden;
                padding: 0.25rem 0;
            }

            .table-container tbody tr.filtered-hidden {
                display: none;
            }

            .table-container tbody tr.cuenta-vencida {
                border: 2px solid #dc3545;
            }

            .table-container tbody td {
                align-items: center;
                border: 0;
                border-bottom: 1px solid #f0f2f5;
                display: flex;
                justify-content: space-between;
                gap: 0.65rem;
                min-height: 30px;
                padding: 0.34rem 0.7rem;
                text-align: right !important;
                word-break: break-word;
            }

            .table-container tbody td[data-label="ID"],
            .table-container tbody td[data-label="Fecha inicio"],
            .table-container tbody td[data-label="Fecha fin"],
            .table-container tbody td[data-label="Usuarios inactivos"] {
                display: none;
            }

            .table-container tbody td:last-child {
                border-bottom: 0;
            }

            .table-container tbody td::before {
                color: #64748b;
                content: attr(data-label);
                flex: 0 0 40%;
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.02em;
                text-align: left;
                text-transform: uppercase;
            }

            .table-container tbody td[data-label="Correo"] {
                align-items: flex-start;
                flex-direction: column;
                font-size: 0.88rem;
                gap: 0.12rem;
                line-height: 1.15;
                padding-bottom: 0.45rem;
                padding-top: 0.48rem;
                text-align: left !important;
            }

            .table-container tbody td[data-label="Correo"]::before {
                flex: none;
            }

            .table-container tbody td[data-label="Estado"],
            .table-container tbody td[data-label="Editar"],
            .table-container tbody td[data-label="Eliminar"] {
                align-items: stretch;
                display: inline-flex;
                gap: 0.25rem;
                padding-left: 0.18rem;
                padding-right: 0.18rem;
                text-align: center !important;
                width: 33.333%;
            }

            .table-container tbody td[data-label="Estado"]::before,
            .table-container tbody td[data-label="Editar"]::before,
            .table-container tbody td[data-label="Eliminar"]::before {
                content: none;
            }

            .table-container tbody td .btn {
                min-height: 32px;
                width: 100%;
            }

            .table-container tbody td.mobile-action-cell {
                border-bottom: 0;
                padding-bottom: 0.26rem;
                padding-top: 0.26rem;
            }

            .table-container tbody td.mobile-action-cell .btn {
                align-items: center;
                border-radius: 9px;
                display: inline-flex;
                font-size: 0.78rem;
                font-weight: 700;
                justify-content: center;
                padding: 0.25rem 0.45rem;
            }

            .mobile-action-label {
                display: inline;
                margin-left: 0.22rem;
            }

            .table-container tbody td[data-label="Días"] {
                font-weight: 800;
            }

            .table-container tbody td[data-dias-status="ok"] {
                color: #198754;
            }

            .table-container tbody td[data-dias-status="soon"] {
                color: #b58100;
            }

            .table-container tbody td[data-dias-status="expired"] {
                color: #dc3545;
            }

            .table-container tbody tr.empty-row td {
                display: block;
                min-height: auto;
                padding: 1.25rem;
                text-align: center !important;
            }

            .table-container tbody tr.empty-row td::before {
                content: none;
            }

            .table-container tfoot tr {
                background: #111827;
                border-radius: 16px;
                color: #fff;
                display: flex;
                justify-content: space-between;
                padding: 0.9rem 1rem;
            }

            .table-container tfoot th {
                border: 0;
                color: inherit;
                display: block;
                padding: 0;
                text-align: left !important;
                width: auto;
            }

            .table-container tfoot th:last-child {
                font-weight: 800;
                text-align: right !important;
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
            .mobile-nav,
            .mobile-only {
                display: none;
            }

            .sidebar-mobile-backdrop {
                display: none !important;
            }
        }

        /* Asegurar que los modales de Bootstrap estén por encima de todo */
        /* El backdrop debe estar DETRÁS del modal */
        .modal-backdrop {
            z-index: 3000 !important;
            pointer-events: auto !important;
            background-color: rgba(0, 0, 0, 0.5) !important;  /* Semi-transparente */
        }

        .modal-backdrop.show {
            opacity: 0.5 !important;  /* Opacidad reducida */
        }

        .modal {
            z-index: 3010 !important;
            pointer-events: none !important;  /* Solo el contenido debe recibir clics */
        }

        .modal.show {
            pointer-events: auto !important;
        }

        .modal-dialog {
            z-index: 3011 !important;
            position: relative;
            pointer-events: auto !important;
            margin: 1.75rem auto;
        }

        .modal-content {
            z-index: 3012 !important;
            position: relative;
            pointer-events: auto !important;
            background: white !important;
            border: 1px solid rgba(0,0,0,.2);
            box-shadow: 0 10px 30px rgba(0,0,0,.8) !important;  /* Sombra más fuerte */
        }

        #modalEliminarCuenta {
            align-items: center;
            background: rgba(15, 23, 42, 0.55);
            bottom: 0;
            display: none;
            justify-content: center;
            left: 0;
            padding: 1rem;
            position: fixed !important;
            right: 0;
            top: 0;
            z-index: 9999 !important;
        }

        #modalEliminarCuenta.show,
        #modalEliminarCuenta.delete-modal-open {
            display: flex !important;
            opacity: 1 !important;
        }

        #modalEliminarCuenta .modal-dialog {
            margin: 0;
            max-width: 430px;
            transform: none !important;
            width: 100%;
            z-index: 10000 !important;
        }

        #modalEliminarCuenta .modal-content {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.55) !important;
            z-index: 10001 !important;
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
        .modal textarea {
            pointer-events: auto !important;
            position: relative;
            z-index: 3;
            background: white !important;
        }

        .modal button {
            pointer-events: auto !important;
            position: relative;
            z-index: 3;
        }

        .modal .btn-secondary {
            color: #fff !important;
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }

        .modal .btn-primary {
            color: #fff !important;
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .modal .btn-danger {
            color: #fff !important;
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        .modal-footer {
            gap: 0.5rem;
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

        @media (max-width: 575.98px) {
            .modal-dialog {
                margin: 0.75rem;
                max-width: calc(100% - 1.5rem);
            }

            .modal-content {
                max-height: calc(100vh - 1.5rem);
                overflow: hidden;
            }

            .modal-body {
                overflow-y: auto;
            }

            .modal-footer {
                align-items: stretch;
                flex-direction: column-reverse;
            }

            .modal-footer .btn {
                width: 100%;
                margin: 0 !important;
            }

            #modalEliminarCuenta .modal-body {
                font-size: 0.95rem;
                line-height: 1.35;
                padding: 1rem;
            }

            #modalEliminarCuenta .modal-footer {
                flex-direction: row;
                padding: 0.75rem 1rem 1rem;
            }

            #modalEliminarCuenta .modal-footer .btn {
                min-height: 42px;
            }

            #editarCuentaModal .modal-dialog {
                align-items: flex-end;
                display: flex;
                margin: 0;
                max-width: 100%;
                min-height: 100%;
                width: 100%;
            }

            #editarCuentaModal .modal-content {
                border: 0;
                border-radius: 18px 18px 0 0;
                max-height: calc(100vh - 12px);
            }

            #editarCuentaModal .modal-header {
                padding: 0.85rem 1rem;
            }

            #editarCuentaModal .modal-title {
                font-size: 1rem;
                font-weight: 800;
            }

            #editarCuentaModal .modal-body {
                display: grid;
                gap: 0.65rem;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                padding: 0.85rem 1rem;
            }

            #editarCuentaModal .modal-body .mb-3 {
                margin-bottom: 0 !important;
            }

            #editarCuentaModal .modal-body .mb-3:nth-of-type(1),
            #editarCuentaModal .modal-body .mb-3:nth-of-type(2),
            #editarCuentaModal .modal-body .mb-3:nth-of-type(3),
            #editarCuentaModal .modal-body .mb-3:nth-of-type(4) {
                grid-column: 1 / -1;
            }

            #editarCuentaModal .form-label {
                color: #64748b;
                font-size: 0.74rem;
                font-weight: 700;
                margin-bottom: 0.22rem;
            }

            #editarCuentaModal .form-control,
            #editarCuentaModal .form-select {
                border-radius: 11px;
                font-size: 0.92rem;
                min-height: 40px;
            }

            #editarCuentaModal .modal-footer {
                border-top: 1px solid #e9edf3;
                bottom: 0;
                flex-direction: row;
                padding: 0.75rem 1rem;
                position: sticky;
            }

            #editarCuentaModal .modal-footer .btn {
                min-height: 42px;
            }
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
    <div class="container-fluid p-0">
        <!-- Navbar móvil -->
        <div class="mobile-navbar mobile-only">
            <button class="btn btn-link text-dark p-0" id="btnSidebarMobile" type="button">
                <i class="bi bi-list"></i>
            </button>
            <h2 class="mb-0"><i class="bi bi-person"></i> Cuentas</h2>
        </div>
        <div class="sidebar-mobile-backdrop" id="sidebarMobileBackdrop"></div>

        <div class="d-flex">
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
            <main class="main-content p-4">
                <div class="page-header d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><i class="bi bi-person me-2"></i>Listado de cuentas</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaCuentaModal">
                        <i class="bi bi-plus-circle"></i> Nueva cuenta
                    </button>
                </div>

                <div class="mobile-tools mobile-only" id="mobileCuentaTools">
                    <div class="mobile-summary mobile-only">
                        <div class="mobile-summary-card">
                            <span>Mostradas</span>
                            <strong id="mobileVisibleCount">0</strong>
                        </div>
                        <div class="mobile-summary-card">
                            <span>Activas</span>
                            <strong id="mobileActiveCount">0</strong>
                        </div>
                        <div class="mobile-summary-card">
                            <span>Vencidas</span>
                            <strong id="mobileExpiredCount">0</strong>
                        </div>
                        <div class="mobile-summary-card">
                            <span>Gasto visible</span>
                            <strong id="mobileVisibleCost">$0.00</strong>
                        </div>
                    </div>

                    <div class="mobile-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="search" class="form-control" id="mobileCuentaSearch" placeholder="Buscar por correo..." autocomplete="off">
                        <button type="button" class="mobile-search-clear" id="mobileCuentaSearchClear" aria-label="Limpiar búsqueda">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div class="mobile-filter-chips" id="mobileCuentaFilters" aria-label="Filtros de cuentas">
                        <button type="button" class="mobile-filter-chip active" data-filter="all">Todas</button>
                        <button type="button" class="mobile-filter-chip" data-filter="activa">Activas</button>
                        <button type="button" class="mobile-filter-chip" data-filter="inactiva">Inactivas</button>
                        <button type="button" class="mobile-filter-chip" data-filter="vencida">Vencidas</button>
                        <button type="button" class="mobile-filter-chip" data-filter="gpt">GPT</button>
                        <button type="button" class="mobile-filter-chip" data-filter="gemini">Gemini</button>
                        <button type="button" class="mobile-filter-chip" data-filter="perplexity">Perplexity</button>
                    </div>

                    <div class="mobile-sort-wrap">
                        <label for="mobileCuentaSort">Ordenar por</label>
                        <select class="form-select" id="mobileCuentaSort">
                            <option value="id:desc">Más recientes</option>
                            <option value="id:asc">Más antiguas</option>
                            <option value="fecha_inicio:asc">Fecha inicio ascendente</option>
                            <option value="fecha_inicio:desc">Fecha inicio descendente</option>
                            <option value="dias:asc">Menos días restantes</option>
                            <option value="dias:desc">Más días restantes</option>
                            <option value="usuarios_activos:desc">Más usuarios activos</option>
                            <option value="usuarios_activos:asc">Menos usuarios activos</option>
                        </select>
                    </div>

                    <div class="mobile-results-meta mobile-only">
                        <span id="mobileFilterLabel">Todas las cuentas</span>
                        <span id="mobileSearchLabel"></span>
                    </div>
                </div>

                <div class="mobile-no-results mobile-only" id="mobileNoCuentaResults">
                    No se encontraron cuentas con esos filtros.
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
                            $order = strtolower($_GET['order'] ?? 'desc');
                            $order = in_array($order, ['asc', 'desc'], true) ? $order : 'desc';
                            
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
                                    $fecha_fin_calc = null;
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
                                    $esta_vencida = $claseVencida ? '1' : '0';
                                    $dias_valor = $dias !== '' ? intval($dias) : '';
                                    $dias_status = $dias_valor === '' ? '' : ($dias_valor < 0 ? 'expired' : ($dias_valor <= 5 ? 'soon' : 'ok'));
                                    $tipo_cuenta_data = htmlspecialchars(strtolower(trim($fila['tipo_cuenta'] ?? '')), ENT_QUOTES);
                                    $correo_data = htmlspecialchars(strtolower(trim($fila['correo'] ?? '')), ENT_QUOTES);
                                    $costo_data = htmlspecialchars((string) floatval($fila['costo']), ENT_QUOTES);
                                    $estado_data = htmlspecialchars($fila['estado'], ENT_QUOTES);
                                    $fecha_inicio_data = htmlspecialchars($fila['fecha_inicio'], ENT_QUOTES);
                                    echo "<tr class='$claseVencida' data-correo='$correo_data' data-estado='$estado_data' data-tipo='$tipo_cuenta_data' data-vencida='$esta_vencida' data-costo='$costo_data'>
                                    <td data-label='ID'>{$fila['id']}</td>
                                    <td data-label='Tipo' class='text-center'>{$fila['tipo_cuenta_abrev']}</td>
                                    <td data-label='Correo'>" . htmlspecialchars($fila['correo']) . "</td>
                                    <td data-label='Fecha inicio'>" . ($fecha_ini ? date('d/m/Y', strtotime($fecha_ini)) : '') . "</td>
                                    <td data-label='Fecha fin'>$fecha_fin_mostrar</td>
                                    <td data-label='Días' data-dias-status='$dias_status'>" . ($dias !== '' ? intval($dias) : '') . "</td>
                                    <td data-label='Usuarios activos'>{$fila['usuarios_activos']}</td>
                                    <td data-label='Usuarios inactivos'>{$fila['usuarios_inactivos']}</td>
                                    <td data-label='Total usuarios'>{$fila['total_ventas']}</td>
                                    <td data-label='Gasto'>$" . number_format($fila['costo'], 2) . "</td>
                                    <td data-label='Ganancia'>$" . number_format($fila['ganancia'] ?? 0, 2) . "</td>
                                    <td data-label='Estado' class='mobile-action-cell'>
                                        <button class='btn btn-sm $estado_btn toggle-estado' 
                                            data-id='{$fila['id']}' data-estado='$estado_data'>
                                            $estado_txt
                                        </button>
                                    </td>
                                    <td data-label='Editar' class='mobile-action-cell'>
                                        <button class='btn btn-sm btn-warning edit-cuenta' 
                                            data-id='{$fila['id']}'
                                            data-correo='" . htmlspecialchars($fila['correo'], ENT_QUOTES) . "'
                                            data-contrasena-correo='" . htmlspecialchars($fila['contrasena_correo'], ENT_QUOTES) . "'
                                            data-contrasena-gpt='" . htmlspecialchars($fila['contrasena_gpt'], ENT_QUOTES) . "'
                                            data-codigo='" . htmlspecialchars($fila['codigo'] ?? '', ENT_QUOTES) . "'
                                            data-fecha_inicio='$fecha_inicio_data'
                data-costo='$costo_data'
                data-estado='$estado_data'
                data-tipo_cuenta='$tipo_cuenta_data'
                                        >
                                            <i class='bi bi-pencil'></i><span class='mobile-action-label'>Editar</span>
                                        </button>
                                    </td>
                                    <td data-label='Eliminar' class='mobile-action-cell'>
                                        <button class='btn btn-sm btn-danger delete-cuenta' data-id='{$fila['id']}'>
                                            <i class='bi bi-trash'></i><span class='mobile-action-label'>Eliminar</span>
                                        </button>
                                    </td>
                                  </tr>";
                                }
                            } else {
                                echo "<tr class='empty-row'><td colspan='14' class='text-center'>No hay cuentas registradas</td></tr>";
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
        const currentColumn = <?php echo json_encode($column ?? 'id'); ?>;
        const currentOrder = <?php echo json_encode($order ?? 'desc'); ?>;
        if (currentColumn) {
            const btn = document.querySelector(`.sort-btn[data-column="${currentColumn}"] i`);
            if (btn) {
                btn.className = currentOrder === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
            }
        }

        // Búsqueda y filtros móviles. Solo afecta la vista celular porque los controles están ocultos en escritorio.
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('mobileCuentaSearch');
            const clearSearchBtn = document.getElementById('mobileCuentaSearchClear');
            const filterWrap = document.getElementById('mobileCuentaFilters');
            const sortSelect = document.getElementById('mobileCuentaSort');
            const noResults = document.getElementById('mobileNoCuentaResults');
            const visibleCount = document.getElementById('mobileVisibleCount');
            const activeCount = document.getElementById('mobileActiveCount');
            const expiredCount = document.getElementById('mobileExpiredCount');
            const visibleCost = document.getElementById('mobileVisibleCost');
            const filterLabel = document.getElementById('mobileFilterLabel');
            const searchLabel = document.getElementById('mobileSearchLabel');
            const filterLabels = {
                all: 'Todas las cuentas',
                activa: 'Cuentas activas',
                inactiva: 'Cuentas inactivas',
                vencida: 'Cuentas vencidas',
                gpt: 'Cuentas GPT',
                gemini: 'Cuentas Gemini',
                perplexity: 'Cuentas Perplexity'
            };
            let activeFilter = 'all';

            function getRows() {
                return Array.from(document.querySelectorAll('.table-container tbody tr:not(.empty-row)'));
            }

            function matchesFilter(row) {
                if (activeFilter === 'all') return true;
                if (activeFilter === 'vencida') return row.dataset.vencida === '1';
                if (activeFilter === 'activa' || activeFilter === 'inactiva') return row.dataset.estado === activeFilter;
                return row.dataset.tipo === activeFilter;
            }

            function formatMoney(amount) {
                return '$' + amount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function applyMobileCuentaFilters() {
                const rows = getRows();
                const term = (searchInput?.value || '').trim().toLowerCase();
                let shown = 0;
                let active = 0;
                let expired = 0;
                let cost = 0;

                rows.forEach(row => {
                    const matchesSearch = !term || (row.dataset.correo || '').includes(term);
                    const show = matchesSearch && matchesFilter(row);
                    row.classList.toggle('filtered-hidden', !show);

                    if (show) {
                        shown++;
                        if (row.dataset.estado === 'activa') active++;
                        if (row.dataset.vencida === '1') expired++;
                        cost += parseFloat(row.dataset.costo || 0) || 0;
                    }
                });

                if (visibleCount) visibleCount.textContent = shown;
                if (activeCount) activeCount.textContent = active;
                if (expiredCount) expiredCount.textContent = expired;
                if (visibleCost) visibleCost.textContent = formatMoney(cost);
                if (filterLabel) filterLabel.textContent = filterLabels[activeFilter] || 'Todas las cuentas';
                if (searchLabel) searchLabel.textContent = term ? `Buscando: ${term}` : '';
                if (clearSearchBtn) clearSearchBtn.style.display = term ? 'inline-flex' : 'none';
                if (noResults) noResults.classList.toggle('show', rows.length > 0 && shown === 0);
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyMobileCuentaFilters);
            }

            if (clearSearchBtn && searchInput) {
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.focus();
                    applyMobileCuentaFilters();
                });
            }

            if (filterWrap) {
                filterWrap.addEventListener('click', function(e) {
                    const chip = e.target.closest('.mobile-filter-chip');
                    if (!chip) return;

                    activeFilter = chip.dataset.filter || 'all';
                    filterWrap.querySelectorAll('.mobile-filter-chip').forEach(btn => {
                        btn.classList.toggle('active', btn === chip);
                    });
                    applyMobileCuentaFilters();
                });
            }

            if (sortSelect) {
                const selectedSort = `${currentColumn || 'id'}:${currentOrder || 'desc'}`;
                if (sortSelect.querySelector(`option[value="${selectedSort}"]`)) {
                    sortSelect.value = selectedSort;
                }

                sortSelect.addEventListener('change', function() {
                    const [column, order] = this.value.split(':');
                    const url = new URL(window.location.href);
                    url.searchParams.set('column', column);
                    url.searchParams.set('order', order);
                    window.location.href = url.toString();
                });
            }

            window.actualizarFiltrosCuentasMovil = applyMobileCuentaFilters;
            applyMobileCuentaFilters();
        });

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

        // Guardar nueva cuenta. El modal está más abajo en el HTML, por eso se inicializa al cargar el DOM.
        document.addEventListener('DOMContentLoaded', function() {
            const formNuevaCuenta = document.getElementById('formNuevaCuenta');

            if (!formNuevaCuenta) return;

            formNuevaCuenta.addEventListener('submit', function(e) {
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
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEliminarEl = document.getElementById('modalEliminarCuenta');
            let cuentaAEliminar = null;

            function showDeleteModal() {
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                modalEliminarEl.classList.add('show', 'delete-modal-open');
                modalEliminarEl.style.display = 'flex';
                modalEliminarEl.removeAttribute('aria-hidden');
                modalEliminarEl.setAttribute('aria-modal', 'true');
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden';
            }

            function hideDeleteModal() {
                modalEliminarEl.classList.remove('show', 'delete-modal-open');
                modalEliminarEl.style.display = 'none';
                modalEliminarEl.setAttribute('aria-hidden', 'true');
                modalEliminarEl.removeAttribute('aria-modal');
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.removeProperty('padding-right');
            }

            modalEliminarEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    hideDeleteModal();
                });
            });

            // Delegación de eventos para botón eliminar
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.delete-cuenta');
                if (btn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = btn.dataset.id || btn.getAttribute('data-id');

                    if (!id) {
                        showAlert('No se encontró el ID de la cuenta', 'danger');
                        return;
                    }

                    cuentaAEliminar = btn.closest('tr');
                    const btnConfirmar = document.getElementById('btnConfirmarEliminarCuenta');
                    btnConfirmar.dataset.id = id;
                    btnConfirmar.disabled = false;
                    showDeleteModal();
                }
            });

            // Confirmar eliminación
            document.getElementById('btnConfirmarEliminarCuenta').addEventListener('click', function() {
                const id = this.dataset.id;

                if (!id) {
                    showAlert('No se encontró el ID de la cuenta', 'danger');
                    return;
                }

                this.disabled = true;

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
                            hideDeleteModal();

                            // Eliminar fila de la tabla
                            if (cuentaAEliminar) {
                                cuentaAEliminar.remove();
                                updateTotalGasto(data.deleted_amount || 0);
                                showEmptyTableMessageIfNeeded();
                                if (typeof window.actualizarFiltrosCuentasMovil === 'function') {
                                    window.actualizarFiltrosCuentasMovil();
                                }
                            }
                        } else {
                            showAlert('Error al eliminar: ' + (data.error || 'Error desconocido'), 'danger');
                        }
                    })
                    .catch(error => {
                        showAlert('Error en la conexión', 'danger');
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        this.disabled = false;
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

            // Función para actualizar total
            function updateTotalGasto(amountToSubtract) {
                const totalElement = document.querySelector('tfoot th:last-child');
                if (totalElement) {
                    const currentTotal = parseFloat(totalElement.textContent.replace('$', '').replace(/,/g, ''));
                    const safeCurrentTotal = Number.isNaN(currentTotal) ? 0 : currentTotal;
                    const amount = parseFloat(amountToSubtract || 0);
                    const safeAmount = Number.isNaN(amount) ? 0 : amount;
                    const newTotal = Math.max(0, safeCurrentTotal - safeAmount);
                    totalElement.textContent = '$' + newTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }

            function showEmptyTableMessageIfNeeded() {
                const tbody = document.querySelector('.table-container tbody');

                if (tbody && !tbody.querySelector('tr')) {
                    tbody.innerHTML = "<tr class='empty-row'><td colspan='14' class='text-center'>No hay cuentas registradas</td></tr>";
                }
            }
        });
    </script>

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
