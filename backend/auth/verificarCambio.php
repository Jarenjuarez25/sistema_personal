<?php

session_start();

require_once(__DIR__ . "/../config/conexion.php");

$codigo_ingresado = $_POST["codigo"];

if ($codigo_ingresado == $_SESSION["codigo"]) {

    $usuario = $_SESSION["usuario_reset"];
    $nuevaPassword = $_SESSION["nueva_password"];

    $query = "UPDATE usuario
              SET password = $1
              WHERE username = $2";

    $result = pg_query_params(
        $conn,
        $query,
        array(
            $nuevaPassword,
            $usuario
        )
    );

    if ($result) {

        session_destroy();

        header("Location: ../../frontend/login.php");
        exit();

    } else {

        echo "Error al actualizar contraseña";

    }

} else {

    echo "Código incorrecto";

}