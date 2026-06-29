<?php
session_start();

require_once(__DIR__ . "/../config/conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: ../../frontend/login.php");
    exit();
}

$id_usuario = $_POST['id_usuario'] ?? null;
$username   = trim($_POST['username'] ?? '');
$email      = trim($_POST['email'] ?? '');
$id_rol     = $_POST['id_rol'] ?? null;

if (!$id_usuario || $username === '' || $email === '' || !$id_rol) {
    die("Datos incompletos");
}

$query = "
UPDATE usuario
SET
    username = $1,
    email = $2,
    id_rol = $3
WHERE id_usuario = $4
";

$result = pg_query_params($conn, $query, [
    $username,
    $email,
    $id_rol,
    $id_usuario
]);

if ($result) {
    header("Location: ../../frontend/usuarios.php");
    exit();
}

echo "Error al actualizar usuario<br>";
echo pg_last_error($conn);
