<?php

session_start();

require_once(__DIR__ . "/../config/conexion.php");

$codigo_ingresado = $_POST["codigo"];

if ($codigo_ingresado == $_SESSION["codigo"]) {
    if (isset($_SESSION["cambio_password"])) {

        $usuario = $_SESSION["usuario_temp"];

        $nuevaPassword = $_SESSION["nueva_password"];

        $query = "
        UPDATE usuarios
        SET password = $1
        WHERE usuario = $2
        ";

        pg_query_params($conn, $query, array(
            $nuevaPassword,
            $usuario
        ));

        session_destroy();
        header("Location: ../../frontend/login.php");

        exit();
    }

    $_SESSION["usuario"] = $_SESSION["usuario_temp"];

    unset($_SESSION["codigo"]);
    unset($_SESSION["usuario_temp"]);

    header("Location: ../../frontend/dashboard.php");

} else {

    echo "Código incorrecto";
}