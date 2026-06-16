<?php

require_once(__DIR__ . "/../config/conexion.php");

function limpiar($valor)
{
    $valor = trim($valor ?? '');
    return $valor === '' ? null : $valor;
}

$id_civil = limpiar($_POST['id_civil'] ?? null);

$nombres = limpiar($_POST['nombres'] ?? null);
$dni = limpiar($_POST['dni'] ?? null);
$na = limpiar($_POST['na'] ?? null);

$grupo = limpiar($_POST['grupo'] ?? null);
$gguu = limpiar($_POST['gguu'] ?? null);
$nucleo = limpiar($_POST['nucleo'] ?? null);

$sexo = limpiar($_POST['sexo'] ?? null);
$grado_laboral = limpiar($_POST['grado_laboral'] ?? null);
$id_unidad = limpiar($_POST['id_unidad'] ?? null);

$fecha_nacimiento = limpiar($_POST['fecha_nacimiento'] ?? null);
$fecha_ingreso = limpiar($_POST['fecha_ingreso'] ?? null);
$fecha_salida = limpiar($_POST['fecha_salida'] ?? null);

$celular = limpiar($_POST['celular'] ?? null);
$condicion = limpiar($_POST['condicion'] ?? null);

if ($id_civil === null) {
    die("ID no recibido");
}

if ($nombres === null) {
    die("El nombre es obligatorio");
}

$query = "
UPDATE personal_civil SET
    nombres = $1,
    dni = $2,
    na = $3,
    grupo = $4,
    gguu = $5,
    nucleo = $6,
    sexo = $7,
    grado_laboral = $8,
    id_unidad = $9,
    fecha_nacimiento = $10,
    fecha_ingreso = $11,
    fecha_salida = $12,
    celular = $13,
    condicion = $14
WHERE id_civil = $15
";

$result = pg_query_params(
    $conn,
    $query,
    [
        $nombres,
        $dni,
        $na,
        $grupo,
        $gguu,
        $nucleo,
        $sexo,
        $grado_laboral,
        $id_unidad,
        $fecha_nacimiento,
        $fecha_ingreso,
        $fecha_salida,
        $celular,
        $condicion,
        $id_civil
    ]
);

if ($result) {
    header("Location: ../../frontend/personal.php");
    exit;
} else {
    echo "Error al actualizar personal civil<br><br>";
    echo pg_last_error($conn);
}

?>