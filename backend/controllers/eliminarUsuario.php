<?php
session_start();

require_once(__DIR__ . "/../config/conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: ../../frontend/login.php");
    exit();
}

$id_usuario = $_POST['id_usuario'] ?? null;

if (!$id_usuario) {
    die("ID no recibido");
}

if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == $id_usuario) {
    $_SESSION['error_usuario'] = "No puede eliminar su propio usuario.";
    header("Location: ../../frontend/usuarios.php");
    exit();
}

$queryRol = pg_query_params($conn, "
SELECT id_rol
FROM usuario
WHERE id_usuario = $1
", [$id_usuario]);

$usuario = pg_fetch_assoc($queryRol);

if ($usuario && $usuario['id_rol'] == 1) {
    $querySuper = pg_query($conn, "
    SELECT COUNT(*) AS total
    FROM usuario
    WHERE id_rol = 1
    ");

    $dataSuper = pg_fetch_assoc($querySuper);

    if (($dataSuper['total'] ?? 0) <= 1) {
        $_SESSION['error_usuario'] = "Debe existir al menos un superusuario.";
        header("Location: ../../frontend/usuarios.php");
        exit();
    }
}

$result = pg_query_params($conn, "
DELETE FROM usuario
WHERE id_usuario = $1
", [$id_usuario]);

if ($result) {
    header("Location: ../../frontend/usuarios.php");
    exit();
}

echo "Error al eliminar usuario<br>";
echo pg_last_error($conn);
