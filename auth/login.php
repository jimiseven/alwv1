// auth/login.php
<?php
require_once '../config/db.php';
require_once '../config/config.php';

$error = '';

// Si el usuario ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['contraseña']);
    
    // Validar que se proporcionaron ambos campos
    if (empty($usuario) || empty($password)) {
        $error = "Por favor ingrese usuario y contraseña.";
    } else {
        // Consultar la base de datos
        $sql = "SELECT id, usuario, contraseña, activo FROM vendedores WHERE usuario = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_usuario);
            $param_usuario = $usuario;
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                // Verificar si el usuario existe
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $usuario, $hashed_password, $activo);
                    
                    if (mysqli_stmt_fetch($stmt)) {
                        // Verificar si el usuario está activo
                        if ($activo) {
                            // Verificar la contraseña
                            if (password_verify($password, $hashed_password)) {
                                // Contraseña correcta, iniciar sesión
                                $_SESSION["user_id"] = $id;
                                $_SESSION["username"] = $usuario;
                                
                                // Redirigir al usuario al dashboard
                                header("Location: " . BASE_URL . "dashboard.php");
                                exit;
                            } else {
                                $error = "La contraseña ingresada no es válida.";
                            }
                        } else {
                            $error = "Esta cuenta no está activa. Contacte al administrador.";
                        }
                    }
                } else {
                    $error = "No existe un usuario con ese nombre.";
                }
            } else {
                $error = "Error en la consulta. Intente más tarde.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Ventas</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>ALW</h4>
                        <p class="mb-0">Sistema de Gestión</p>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario:</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" required>
                            </div>
                            <div class="mb-3">
                                <label for="contraseña" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="contraseña" name="contraseña" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Ingresar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
