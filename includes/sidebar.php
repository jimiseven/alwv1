<div class="col-md-3 col-lg-2 d-md-block sidebar collapse" style="background: linear-gradient(180deg, #2c3e50 0%, #1a2530 100%);">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <h3 class="text-white">ALW</h3>
            <hr class="border-light my-3">
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link text-white rounded-pill <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active bg-primary' : ''; ?>"
                    href="<?php echo BASE_URL; ?>dashboard.php"
                    style="transition: all 0.3s;">
                    <i class="bi bi-house-door me-2"></i> Centralizador
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-white rounded-pill <?php echo (basename($_SERVER['PHP_SELF']) == 'cuentas.php' || strpos($_SERVER['PHP_SELF'], '/cuentas/')) ? 'active bg-primary' : ''; ?>"
                    href="<?php echo BASE_URL; ?>modules/cuentas/cuentas.php"
                    style="transition: all 0.3s;">
                    <i class="bi bi-person me-2"></i> Cuentas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-white rounded-pill <?php echo (basename($_SERVER['PHP_SELF']) == 'ventas.php' || strpos($_SERVER['PHP_SELF'], '/ventas/')) ? 'active bg-primary' : ''; ?>"
                    href="<?php echo BASE_URL; ?>modules/ventas/ventas.php"
                    style="transition: all 0.3s;">
                    <i class="bi bi-cart me-2"></i> Ventas
                </a>
            </li>

            <div class="mt-5 pt-3 border-top border-secondary">
                <li class="nav-item">
                    <a class="nav-link text-white rounded-pill bg-danger-hover"
                        href="<?php echo BASE_URL; ?>auth/logout.php"
                        style="transition: all 0.3s;">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                    </a>
                </li>
            </div>
        </ul>
    </div>
</div>


<style>
    .nav-link:hover:not(.active) {
        background-color: rgba(255, 255, 255, 0.1) !important;
        transform: translateX(5px);
    }

    .bg-danger-hover:hover {
        background-color: #dc3545 !important;
    }

    .sidebar {
        min-height: 100vh;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }
</style>