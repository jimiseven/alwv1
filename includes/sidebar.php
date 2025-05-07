// includes/sidebar.php
<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <h3>ALW</h3>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>dashboard.php">
                    <i class="bi bi-house-door"></i> Centralizador
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/cuentas/') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>modules/cuentas/index.php">
                    <i class="bi bi-person"></i> Cuentas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/ventas/') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>modules/ventas/index.php">
                    <i class="bi bi-cart"></i> Ventas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/reportes/') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>modules/reportes/index.php">
                    <i class="bi bi-file-earmark-text"></i> Estado económico
                </a>
            </li>
        </ul>
    </div>
</div>
