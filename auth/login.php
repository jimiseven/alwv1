<?php
require_once '../config/db.php';
require_once '../config/config.php';

// Verificar si ya está logueado
if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['contrasena']);

    // Validar campos
    if (empty($usuario) || empty($password)) {
        $_SESSION['error'] = "Usuario y contraseña son requeridos";
    } else {
        // Consultar usuario
        $sql = "SELECT id, usuario, contrasena, activo, rol FROM vendedores WHERE usuario = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $usuario, $hashed_password, $activo, $rol);
                mysqli_stmt_fetch($stmt);
                
                if ($activo && password_verify($password, $hashed_password)) {
                    // Autenticación exitosa
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $usuario;
                    $_SESSION['user_rol'] = $rol;
                    
                    // Regenerar ID de sesión
                    session_regenerate_id(true);
                    
                    header("Location: " . BASE_URL . "dashboard.php");
                    exit;
                } else {
                    $_SESSION['error'] = "Credenciales inválidas o cuenta inactiva";
                }
            } else {
                $_SESSION['error'] = "Usuario no encontrado";
            }
        } else {
            $_SESSION['error'] = "Error en la base de datos";
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Iniciar Sesión</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="usuario">Usuario</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" required>
                            </div>
                            <div class="form-group">
                                <label for="contrasena">Contraseña</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>