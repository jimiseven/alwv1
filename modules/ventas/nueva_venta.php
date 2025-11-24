<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
requireLogin();

// Obtener cuentas activas
$sqlCuentas = "SELECT c.id, c.correo, c.tipo_cuenta, COUNT(v.id) as ventas_count 
               FROM cuentas c 
               LEFT JOIN ventas v ON c.id = v.cuenta_id 
               WHERE c.estado='activa' 
               GROUP BY c.id, c.correo, c.tipo_cuenta 
               ORDER BY ventas_count ASC, c.correo ASC";
$resCuentas = mysqli_query($conn, $sqlCuentas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta - Sistema ALW</title>
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

        .main-content {
            padding: 30px;
            min-height: 100vh;
            background: white;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 991.98px) {
            .main-content {
                padding: 20px 12px;
            }
        }

        /* Formulario */
        .form-card {
            background: #ffffff;
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .form-header h1 {
            color: #0f172a;
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-header p {
            color: #64748b;
            font-size: 0.95rem;
            margin: 0;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-control,
        .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.65rem 1rem;
            font-size: 1rem;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .form-select {
            height: auto;
            max-height: 45px;
            overflow: hidden;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background: #3b82f6;
            border: none;
            padding: 0.75rem 2rem;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 0.9rem;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-primary:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-secondary {
            background: #e2e8f0;
            border: none;
            padding: 0.75rem 2rem;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 0.9rem;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
            transform: translateY(-2px);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #f1f5f9;
        }

        .form-actions a,
        .form-actions button {
            flex: 1;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .form-select#cuenta_id {
                display: block !important;
                font-size: 1rem;
                padding: 0.75rem 1rem;
                height: auto;
            }

            .form-card {
                padding: 1.5rem;
            }

            .form-header h1 {
                font-size: 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .form-actions {
                flex-direction: column-reverse;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="form-card">
            <div class="form-header">
                <h1><i class="bi bi-cart-plus"></i> Nueva Venta</h1>
                <p>Registra una nueva venta en el sistema</p>
            </div>

            <form id="formNuevaVenta" autocomplete="off">
                <div class="form-group">
                    <label for="numero_celular" class="form-label">Número celular</label>
                    <input type="text" class="form-control" name="numero_celular" id="numero_celular" required autofocus placeholder="Ej: 555-1234">
                </div>

                    <div class="form-group">
                        <label for="cuenta_id" class="form-label">Cuenta</label>
                        <select class="form-select" name="cuenta_id" id="cuenta_id" required>
                            <option value="">Selecciona una cuenta...</option>
                            <?php
                            if ($resCuentas && mysqli_num_rows($resCuentas) > 0) {
                                while ($cuenta = mysqli_fetch_assoc($resCuentas)) {
                                    $tipoLetra = '';
                                    $tipoRaw = strtolower(trim($cuenta['tipo_cuenta'] ?? ''));
                                    
                                    if (strpos($tipoRaw, 'perplex') !== false) {
                                        $tipoLetra = 'p';
                                    } elseif (strpos($tipoRaw, 'gemini') !== false) {
                                        $tipoLetra = 'g';
                                    } else {
                                        $tipoLetra = 'c';
                                    }
                                    
                                    echo "<option value='{$cuenta['id']}' data-tipo='{$tipoLetra}' data-email='" . htmlspecialchars($cuenta['correo']) . "' data-ventas='{$cuenta['ventas_count']}'>". 
                                         $tipoLetra . " " .
                                         htmlspecialchars($cuenta['correo']) . 
                                         " (" . $cuenta['ventas_count'] . " ventas)</option>";
                                }
                            } else {
                                echo "<option value=''>No hay cuentas disponibles</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div>
                            <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required>
                        </div>
                        <div>
                            <label for="duracion" class="form-label">Duración</label>
                            <select class="form-select" id="duracion" required>
                                <option value="30">30 días</option>
                                <option value="60">60 días</option>
                                <option value="90">90 días</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pago" class="form-label">Pago</label>
                        <input type="number" step="0.01" class="form-control" name="pago" id="pago" required placeholder="0.00">
                    </div>

                    <input type="hidden" name="fecha_fin" id="fecha_fin">
                    <input type="hidden" name="vendedor_id" value="<?php echo $_SESSION['user_id']; ?>">

                    <div class="form-actions">
                        <a href="ventas.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar Venta
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
        <script>
            // Calcular fecha fin automáticamente
            const fechaInicio = document.getElementById('fecha_inicio');
            const duracion = document.getElementById('duracion');
            const fechaFin = document.getElementById('fecha_fin');
            
            function calcularFechaFin() {
                if (fechaInicio.value) {
                    const fecha = new Date(fechaInicio.value);
                    fecha.setDate(fecha.getDate() + parseInt(duracion.value) + 1);
                    fechaFin.value = fecha.toISOString().split('T')[0];
                }
            }
            
            fechaInicio.addEventListener('change', calcularFechaFin);
            duracion.addEventListener('change', calcularFechaFin);

            // Enviar formulario
            document.getElementById('formNuevaVenta').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

                fetch('guardar_venta.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        if (response.clipboardText) {
                            navigator.clipboard.writeText(response.clipboardText).catch(() => {});
                        }
                        window.location.href = 'ventas.php';
                    } else {
                        alert('Error: ' + (response.message || 'Error al guardar la venta'));
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Guardar Venta';
                    }
                })
                .catch(() => {
                    alert('Error de conexión');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Guardar Venta';
                });
            });

        </script>
</body>
</html>
