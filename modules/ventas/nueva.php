<?php
require_once '../../config/db.php';
require_once '../../config/config.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar datos
    $required = ['cuenta_id', 'numero_celular', 'fecha_inicio', 'dias'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("El campo $field es requerido");
        }
    }

    // Calcular fecha_fin
    $fecha_inicio = new DateTime($_POST['fecha_inicio']);
    $fecha_fin = $fecha_inicio->add(new DateInterval('P' . $_POST['dias'] . 'D'));

    // Insertar en BD
    $sql = "INSERT INTO ventas (cuenta_id, numero_celular, fecha_inicio, fecha_fin, dias)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'isssi', 
        $_POST['cuenta_id'],
        $_POST['numero_celular'],
        $_POST['fecha_inicio'],
        $fecha_fin->format('Y-m-d'),
        $_POST['dias']
    );

    if (mysqli_stmt_execute($stmt)) {
        header('Location: ../../dashboard.php?success=1');
        exit;
    } else {
        die("Error al registrar venta: " . mysqli_error($conn));
    }
}

// Obtener lista de cuentas activas con contador de usuarios activos
$sql_cuentas = "SELECT
                  c.id,
                  c.correo,
                  (SELECT COUNT(*)
                   FROM ventas v
                   WHERE v.cuenta_id = c.id
                   AND v.fecha_fin >= CURDATE()) as usuarios
                FROM cuentas c
                WHERE c.estado = 'activa'
                ORDER BY c.correo";
$result_cuentas = mysqli_query($conn, $sql_cuentas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Nueva Venta</h1>
        
        <form method="POST">
            <div class="mb-3">
                <label for="cuenta_id" class="form-label">Cuenta</label>
                <select class="form-select" id="cuenta_id" name="cuenta_id" required>
                    <option value="">Seleccionar cuenta</option>
                    <?php while ($cuenta = mysqli_fetch_assoc($result_cuentas)): ?>
                        <option value="<?= $cuenta['id'] ?>" data-usuarios="<?= $cuenta['usuarios'] ?>">
                            <?= htmlspecialchars($cuenta['correo']) ?> - <?= $cuenta['usuarios'] ?> usuario(s) activo(s)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="numero_celular" class="form-label">Número de celular</label>
                <input type="text" class="form-control" id="numero_celular" name="numero_celular" required>
            </div>
            
            <div class="mb-3">
                <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            
            <div class="mb-3">
                <label for="dias" class="form-label">Días</label>
                <input type="number" class="form-control" id="dias" name="dias" min="1" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="../../dashboard.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar contador de usuarios al seleccionar cuenta
        document.getElementById('cuenta_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const usuarios = selectedOption.dataset.usuarios || '0';
            
            // Crear o actualizar el contador
            let counter = document.getElementById('usuarios-counter');
            if (!counter) {
                counter = document.createElement('div');
                counter.id = 'usuarios-counter';
                counter.className = 'alert alert-info mt-3';
                this.parentNode.appendChild(counter);
            }
            
            counter.innerHTML = `Esta cuenta tiene actualmente <strong>${usuarios}</strong> usuarios activos`;
        });
    </script>
</body>
</html>