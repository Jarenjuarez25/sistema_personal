<?php

header('Content-Type: application/json; charset=utf-8');

require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/groq.php');

$data = json_decode(file_get_contents("php://input"), true);
$mensaje = trim($data['mensaje'] ?? '');

if ($mensaje === '') {
    echo json_encode(["respuesta" => "Escriba una consulta."]);
    exit;
}

function responder($texto)
{
    echo json_encode([
        "respuesta" => nl2br($texto)
    ]);
    exit;
}

$promptSQL = "
Eres un asistente del sistema de personal militar de la 1ra Brigada de Servicios del Ejército del Perú.

Tu tarea es decidir si la pregunta requiere consultar la base de datos o si es una pregunta general.

Si la pregunta es general, responde exactamente:
GENERAL

Si requiere base de datos, genera SOLO una consulta SQL SELECT válida para PostgreSQL.

TABLAS:
personal_militar:
id_personal, dni, cip, nombres, fecha_nacimiento, celular,
tipo_personal, tiempo_servicio, anio_ingreso, fecha_ingreso,
fecha_ascenso, observacion, id_grado, id_arma, id_unidad,
id_estado, obs, nro_cuenta, departamento, provincia, distrito, direccion

grado:
id_grado, nombre, categoria, descripcion

arma:
id_arma, nombre

unidad:
id_unidad, nombre, descripcion

estado_personal:
id_estado, nombre

historial_reportes:
id_historial, nombre_reporte, tipo, formato, archivo, generado_por, fecha_generado

REGLAS SQL:
- Solo SELECT.
- Prohibido INSERT, UPDATE, DELETE, DROP, ALTER, TRUNCATE, CREATE.
- Usa LIMIT 20 en listados.
- Usa ILIKE para buscar texto.
- Si preguntan por DNI de una persona, selecciona p.nombres, p.dni, g.nombre AS grado, u.nombre AS unidad, ep.nombre AS estado.
- Si preguntan por nombre o apellidos, usa: p.nombres ILIKE '%PALABRAS%'
- Si preguntan por soldados, usa g.nombre ILIKE '%SLDO%' OR p.tipo_personal ILIKE '%TROPA%'.
- Si preguntan por cabos, usa g.nombre ILIKE '%CABO%'.
- Si preguntan por sargentos, usa g.nombre ILIKE '%SGTO%'.
- Si preguntan por reenganchados, usa g.nombre ILIKE '%REE%'.
- Si preguntan por actividad, licencia, permiso, vacaciones o retiro, usa ep.nombre ILIKE.
- Para datos personales usa JOIN con grado, unidad y estado.

FORMATO SQL recomendado:
SELECT p.nombres, p.dni, p.cip, g.nombre AS grado, u.nombre AS unidad, ep.nombre AS estado
FROM personal_militar p
LEFT JOIN grado g ON p.id_grado = g.id_grado
LEFT JOIN unidad u ON p.id_unidad = u.id_unidad
LEFT JOIN estado_personal ep ON p.id_estado = ep.id_estado
WHERE ...
LIMIT 20

Pregunta:
$mensaje
";

$sql = trim(preguntarGroq($promptSQL));
$sql = str_replace(["```sql", "```"], "", $sql);
$sql = trim($sql);

if (strtoupper($sql) === 'GENERAL') {

    $promptGeneral = "
    Eres un asistente virtual militar de la 1ra Brigada de Servicios del Ejército del Perú.
    Responde de forma clara, breve y profesional.
    Puedes responder preguntas comunes, administrativas, de ayuda, fechas, explicaciones simples o dudas generales.

    Pregunta:
    $mensaje
    ";

    responder(preguntarGroq($promptGeneral));
}

$sqlLower = strtolower($sql);

$bloqueados = [
    'insert',
    'update',
    'delete',
    'drop',
    'alter',
    'truncate',
    'create',
    'grant',
    'revoke'
];

foreach ($bloqueados as $bad) {
    if (strpos($sqlLower, $bad) !== false) {
        responder("No puedo ejecutar operaciones que modifiquen la base de datos.");
    }
}

if (!str_starts_with($sqlLower, 'select')) {
    responder("No pude interpretar la consulta como una búsqueda segura.");
}

if (strpos($sqlLower, 'limit') === false) {
    $sql .= " LIMIT 20";
}

$result = pg_query($conn, $sql);

if (!$result) {
    responder(
        "No pude consultar la base de datos.\n\n" .
        "SQL generado:\n" . $sql . "\n\n" .
        "Error:\n" . pg_last_error($conn)
    );
}

$datos = pg_fetch_all($result);

if (!$datos) {
    responder("No se encontraron resultados para esa consulta.");
}

$datosTexto = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$promptRespuesta = "
Eres un asistente virtual militar de la 1ra Brigada de Servicios del Ejército del Perú.

Responde usando SOLO estos datos encontrados en la base de datos.

Pregunta:
$mensaje

Datos:
$datosTexto

Reglas:
- No inventes información.
- Si el usuario pidió DNI, responde directamente el DNI.
- Si hay una sola persona, responde en formato breve.
- Si hay lista, usa viñetas.
- Muestra nombre, DNI, CIP, grado, unidad y estado si existen.
- Sé claro y profesional.
";

$respuestaFinal = preguntarGroq($promptRespuesta);

responder($respuestaFinal);

?>