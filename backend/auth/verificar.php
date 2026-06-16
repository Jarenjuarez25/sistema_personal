<?php

session_start();

require_once(__DIR__ . "/../config/conexion.php");

$codigo_ingresado = $_POST["codigo"];

/* Verificar código */
if ($codigo_ingresado == $_SESSION["codigo"]) {

    /* SI ES CAMBIO DE PASSWORD */
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

        /* Limpiar sesiones */
        session_destroy();

        /* Volver al login */
        header("Location: ../../frontend/login.php");

        exit();
    }

    /* LOGIN NORMAL */
    $_SESSION["usuario"] = $_SESSION["usuario_temp"];

    unset($_SESSION["codigo"]);
    unset($_SESSION["usuario_temp"]);

    header("Location: ../../frontend/dashboard.php");

} else {

    echo "Código incorrecto";
}