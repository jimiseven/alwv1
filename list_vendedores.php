<?php
require_once 'config/db.php';

$sql = "SELECT id, usuario, activo, created_at FROM vendedores ORDER BY id";
$result = mysqli_query($conn, $sql);

echo "Listado de Vendedores Registrados\n";
echo "--------------------------------\n";

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "ID: ".$row['id']."\n";
        echo "Usuario: ".$row['usuario']."\n";
        echo "Estado: ".($row['activo'] ? "Activo" : "Inactivo")."\n";
        echo "Registrado: ".$row['created_at']."\n";
        echo "--------------------------------\n";
    }
} else {
    echo "No hay vendedores registrados\n";
}

mysqli_close($conn);
?>