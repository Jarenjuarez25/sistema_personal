<?php

require_once(__DIR__ . "/../config/conexion.php");

function limpiar($valor)
{
    $valor = trim($valor ?? '');
    return $valor === '' ? null : $valor;
}

$id_personal = limpiar($_POST['id_personal'] ?? null);

$nombres = limpiar($_POST['nombres'] ?? null);
$dni = limpiar($_POST['dni'] ?? null);
$cip = limpiar($_POST['cip'] ?? null);
$sexo = limpiar($_POST['sexo'] ?? null);
$tipo_personal = limpiar($_POST['tipo_personal'] ?? null);

$id_grado = limpiar($_POST['id_grado'] ?? null);
$id_arma = limpiar($_POST['id_arma'] ?? null);
$id_unidad = limpiar($_POST['id_unidad'] ?? null);
$id_estado = limpiar($_POST['id_estado'] ?? null);

$fecha_nacimiento = limpiar($_POST['fecha_nacimiento'] ?? null);
$fecha_ingreso = limpiar($_POST['fecha_ingreso'] ?? null);
$fecha_salida = limpiar($_POST['fecha_salida'] ?? null);
$fecha_ascenso = limpiar($_POST['fecha_ascenso'] ?? null);

$celular = limpiar($_POST['celular'] ?? null);
$nro_cuenta = limpiar($_POST['nro_cuenta'] ?? null);

$departamento = limpiar($_POST['departamento'] ?? null);
$provincia = limpiar($_POST['provincia'] ?? null);
$distrito = limpiar($_POST['distrito'] ?? null);
$direccion = limpiar($_POST['direccion'] ?? null);
$obs = limpiar($_POST['obs'] ?? null);

if ($id_personal === null) {
    die("ID no recibido");
}

if ($nombres === null) {
    die("El nombre es obligatorio");
}

$query = "
UPDATE personal_militar SET
    nombres = $1,
    dni = $2,
    cip = $3,
    sexo = $4,
    tipo_personal = $5,
    id_grado = $6,
    id_arma = $7,
    id_unidad = $8,
    id_estado = $9,
    fecha_nacimiento = $10,
    fecha_ingreso = $11,
    fecha_salida = $12,
    fecha_ascenso = $13,
    celular = $14,
    nro_cuenta = $15,
    departamento = $16,
    provincia = $17,
    distrito = $18,
    direccion = $19,
    obs = $20
WHERE id_personal = $21
";

$result = pg_query_params(
    $conn,
    $query,
    [
        $nombres,
        $dni,
        $cip,
        $sexo,
        $tipo_personal,
        $id_grado,
        $id_arma,
        $id_unidad,
        $id_estado,
        $fecha_nacimiento,
        $fecha_ingreso,
        $fecha_salida,
        $fecha_ascenso,
        $celular,
        $nro_cuenta,
        $departamento,
        $provincia,
        $distrito,
        $direccion,
        $obs,
        $id_personal
    ]
);

if ($result) {
    header("Location: ../../frontend/personal.php");
    exit;
} else {
    echo "Error al actualizar personal militar<br><br>";
    echo pg_last_error($conn);
}

?>