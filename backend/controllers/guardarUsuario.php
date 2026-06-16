<?php

session_start();

require_once(__DIR__ . "/../config/conexion.php");

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "superUsuario") {
    header("Location: ../../frontend/usuarios.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$id_rol   = $_POST['id_rol'] ?? null;

if ($username == '' || $email == '' || empty($id_rol)) {
    die("Faltan datos obligatorios.");
}


function generarPassword($length = 6)
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@$#';
    $pass = '';

    for ($i = 0; $i < $length; $i++) {
        $index = random_int(0, strlen($chars) - 1);
        $pass .= $chars[$index];
    }

    return $pass;
}

$passwordGenerada = generarPassword(6);

$validarUsuario = pg_query_params(
    $conn,
    "SELECT id_usuario FROM usuario WHERE username = $1",
    [$username]
);

if (pg_num_rows($validarUsuario) > 0) {
    die("El nombre de usuario ya existe.");
}

$query = "
INSERT INTO usuario
(
    username,
    password,
    email,
    id_rol
)
VALUES
(
    $1, $2, $3, $4
)
";

$result = pg_query_params(
    $conn,
    $query,
    [
        $username,
        $passwordGenerada,
        $email,
        $id_rol
    ]
);

if ($result) {

    $_SESSION['password_generada'] = $passwordGenerada;
    $_SESSION['usuario_creado'] = $username;

    header("Location: ../../frontend/usuarios.php");
    exit();
} else {

    echo "Error al registrar usuario.<br><br>";
    echo pg_last_error($conn);
}
