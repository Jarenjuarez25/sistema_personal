<?php

session_start();

require_once(__DIR__ . "/../config/conexion.php");

if (!isset($_SESSION["usuario"])) {

    header("Location: ../../frontend/login.php");
    exit();
}

$usuarioActual = $_SESSION["usuario"];

$username = $_POST["username"];
$email = $_POST["email"];

if (!empty($_FILES["foto"]["name"])) {

    $foto = time() . "_" . $_FILES["foto"]["name"];

    $tmp = $_FILES["foto"]["tmp_name"];

    $ruta = __DIR__ . "/../../frontend/uploads/" . $foto;

    move_uploaded_file($tmp, $ruta);

    $query = "UPDATE usuario
              SET username = $1,
                  email = $2,
                  foto = $3
              WHERE username = $4";

    $params = array(
        $username,
        $email,
        $foto,
        $usuarioActual
    );
} else {

    $query = "UPDATE usuario
              SET username = $1,
                  email = $2
              WHERE username = $3";

    $params = array(
        $username,
        $email,
        $usuarioActual
    );
}

$result = pg_query_params(
    $conn,
    $query,
    $params
);

if ($result) {

    $_SESSION["usuario"] = $username;

    header("Location: ../../frontend/editarPerfil.php");
    exit();
} else {

    echo "Error al actualizar perfil";
}
