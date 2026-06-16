<?php

require_once(__DIR__ . "/../config/conexion.php");

function limpiar($valor) {
    $valor = trim($valor ?? '');
    return $valor === '' ? null : $valor;
}

$tipo_personal = limpiar($_POST['tipo_personal'] ?? null);

$nombres = limpiar($_POST['nombres'] ?? null);
$dni = limpiar($_POST['dni'] ?? null);
$na = limpiar($_POST['na'] ?? null);

$cip = limpiar($_POST['cip'] ?? null);
$arma_texto = limpiar($_POST['arma'] ?? null);
$grado_texto = limpiar($_POST['grado_texto'] ?? null);

$grupo = limpiar($_POST['grupo'] ?? null);
$gguu = limpiar($_POST['gguu'] ?? null);
$nucleo = limpiar($_POST['nucleo'] ?? null);
$sexo = limpiar($_POST['sexo'] ?? null);

$fecha_nacimiento = limpiar($_POST['fecha_nacimiento'] ?? null);
$fecha_ingreso = limpiar($_POST['fecha_ingreso'] ?? null);
$fecha_salida = limpiar($_POST['fecha_salida'] ?? null);
$fecha_ascenso = limpiar($_POST['fecha_ascenso'] ?? null);

$celular = limpiar($_POST['celular'] ?? null);
$tiempo_servicio = limpiar($_POST['tiempo_servicio'] ?? null);
$anio_ingreso = limpiar($_POST['anio_ingreso'] ?? null);

$observacion = limpiar($_POST['observacion'] ?? null);
$obs = limpiar($_POST['obs'] ?? null);

$nro_cuenta = limpiar($_POST['nro_cuenta'] ?? null);
$departamento = limpiar($_POST['departamento'] ?? null);
$provincia = limpiar($_POST['provincia'] ?? null);
$distrito = limpiar($_POST['distrito'] ?? null);
$direccion = limpiar($_POST['direccion'] ?? null);

$id_unidad = limpiar($_POST['id_unidad'] ?? null);
$id_estado = limpiar($_POST['id_estado'] ?? null);

if ($nombres === null) {
    die("El nombre es obligatorio");
}

if ($tipo_personal === null) {
    die("El tipo de personal es obligatorio");
}

/* =========================================================
   SI ES CIVIL, GUARDAR EN personal_civil
========================================================= */

if ($tipo_personal === 'CIVIL') {

    $queryCivil = "
        INSERT INTO personal_civil
        (
            dni,
            nombres,
            na,
            grupo,
            grado_laboral,
            nucleo,
            gguu,
            celular,
            sexo,
            condicion,
            fecha_ingreso,
            id_unidad,
            creado_en
        )
        VALUES
        (
            $1, $2, $3, $4, $5,
            $6, $7, $8, $9, $10,
            $11, $12, NOW()
        )
    ";

    $resultCivil = pg_query_params(
        $conn,
        $queryCivil,
        [
            $dni,
            $nombres,
            $na,
            $grupo,
            $grado_texto,
            $nucleo,
            $gguu,
            $celular,
            $sexo,
            $obs,
            $fecha_ingreso,
            $id_unidad
        ]
    );

    if ($resultCivil) {
        header("Location: ../../frontend/personal.php");
        exit;
    } else {
        echo "Error al guardar personal civil.<br><br>";
        echo pg_last_error($conn);
        exit;
    }
}

/* =========================================================
   SI NO ES CIVIL, GUARDAR EN personal_militar
========================================================= */

/* GRADO */
$id_grado = null;

if ($grado_texto !== null) {

    $buscarGrado = pg_query_params(
        $conn,
        "SELECT id_grado FROM grado WHERE UPPER(nombre) = UPPER($1)",
        [$grado_texto]
    );

    if (pg_num_rows($buscarGrado) > 0) {

        $gradoData = pg_fetch_assoc($buscarGrado);
        $id_grado = $gradoData['id_grado'];

    } else {

        pg_query_params(
            $conn,
            "INSERT INTO grado(nombre, categoria) VALUES($1, $2)",
            [$grado_texto, $tipo_personal]
        );

        $nuevoGrado = pg_query_params(
            $conn,
            "SELECT id_grado FROM grado WHERE UPPER(nombre) = UPPER($1)",
            [$grado_texto]
        );

        $gradoData = pg_fetch_assoc($nuevoGrado);
        $id_grado = $gradoData['id_grado'];
    }
}

/* ARMA */
$id_arma = null;

if ($arma_texto !== null) {

    $buscarArma = pg_query_params(
        $conn,
        "SELECT id_arma FROM arma WHERE UPPER(nombre) = UPPER($1)",
        [$arma_texto]
    );

    if (pg_num_rows($buscarArma) > 0) {

        $armaData = pg_fetch_assoc($buscarArma);
        $id_arma = $armaData['id_arma'];

    } else {

        pg_query_params(
            $conn,
            "INSERT INTO arma(nombre) VALUES($1)",
            [$arma_texto]
        );

        $buscarNueva = pg_query_params(
            $conn,
            "SELECT id_arma FROM arma WHERE UPPER(nombre) = UPPER($1)",
            [$arma_texto]
        );

        $armaData = pg_fetch_assoc($buscarNueva);
        $id_arma = $armaData['id_arma'];
    }
}

$queryMilitar = "
    INSERT INTO personal_militar
    (
        dni,
        cip,
        nombres,
        fecha_nacimiento,
        celular,
        tipo_personal,
        tiempo_servicio,
        anio_ingreso,
        fecha_ingreso,
        fecha_salida,
        fecha_ascenso,
        observacion,
        id_grado,
        id_arma,
        id_unidad,
        id_estado,
        obs,
        nro_cuenta,
        departamento,
        provincia,
        distrito,
        direccion,
        grupo,
        sexo,
        na,
        gguu,
        nucleo
    )
    VALUES
    (
        $1, $2, $3, $4, $5,
        $6, $7, $8, $9, $10,
        $11, $12, $13, $14, $15,
        $16, $17, $18, $19, $20,
        $21, $22, $23, $24, $25,
        $26, $27
    )
";

$resultMilitar = pg_query_params(
    $conn,
    $queryMilitar,
    [
        $dni,
        $cip,
        $nombres,
        $fecha_nacimiento,
        $celular,
        $tipo_personal,
        $tiempo_servicio,
        $anio_ingreso,
        $fecha_ingreso,
        $fecha_salida,
        $fecha_ascenso,
        $observacion,
        $id_grado,
        $id_arma,
        $id_unidad,
        $id_estado,
        $obs,
        $nro_cuenta,
        $departamento,
        $provincia,
        $distrito,
        $direccion,
        $grupo,
        $sexo,
        $na,
        $gguu,
        $nucleo
    ]
);

if ($resultMilitar) {
    header("Location: ../../frontend/personal.php");
    exit;
} else {
    echo "Error al guardar personal militar.<br><br>";
    echo pg_last_error($conn);
    exit;
}

?>