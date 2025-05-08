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
        .table-container {
            max-height: 500px;
            overflow-y: auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 1.5rem;
        }
        .sidebar {
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .nav-link:hover:not(.active) {
            background-color: rgba(255,255,255,0.1) !important;
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
                    <h2 class="mb-0"><i class="bi bi-cart me-2"></i>Listado de Ventas</h2>
                    <!-- Puedes agregar botón para nueva venta si quieres -->
                </div>
                <div class="table-container">
                    <table class="table table-hover align-middle">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Número Celular</th>
                                <th>Vendedor</th>
                                <th>Cuenta</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Días</th>
                                <th>Pago</th>
                                <th>Creado</th>
                                <th>Actualizado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta para obtener todas las ventas con datos relacionados
                            $sql = "SELECT v.id, v.numero_celular, v.fecha_inicio, v.fecha_fin, v.dias, v.pago, 
                                           v.created_at, v.updated_at,
                                           c.correo AS cuenta_correo,
                                           u.usuario AS vendedor_usuario
                                    FROM ventas v
                                    INNER JOIN cuentas c ON v.cuenta_id = c.id
                                    INNER JOIN vendedores u ON v.vendedor_id = u.id
                                    ORDER BY v.fecha_inicio DESC";
                            $resultado = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($resultado) > 0) {
                                while ($fila = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>
                                            <td>{$fila['id']}</td>
                                            <td>" . htmlspecialchars($fila['numero_celular']) . "</td>
                                            <td>" . htmlspecialchars($fila['vendedor_usuario']) . "</td>
                                            <td>" . htmlspecialchars($fila['cuenta_correo']) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "</td>
                                            <td>" . date('d/m/Y', strtotime($fila['fecha_fin'])) . "</td>
                                            <td>{$fila['dias']}</td>
                                            <td>$" . number_format($fila['pago'], 2) . "</td>
                                            <td>" . date('d/m/Y H:i', strtotime($fila['created_at'])) . "</td>
                                            <td>" . date('d/m/Y H:i', strtotime($fila['updated_at'])) . "</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10' class='text-center'>No hay ventas registradas</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
