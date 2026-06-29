<?php

ob_start();

require '../../vendor/autoload.php';
require_once(__DIR__ . '/../config/conexion.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

session_start();

date_default_timezone_set('America/Lima');

$fechaInicio = $_GET['fecha_inicio'] ?? null;
$fechaFin    = $_GET['fecha_fin'] ?? null;
$tipo        = $_GET['tipo'] ?? 'general';
$sexo        = $_GET['sexo'] ?? '';

$idUsuario = $_SESSION['id_usuario'] ?? null;

$sql = "
SELECT *
FROM (
    SELECT
        p.id_personal AS id,
        p.nombres,
        p.dni,
        p.cip,
        p.tipo_personal,
        p.sexo,
        g.nombre AS grado,
        a.nombre AS arma,
        u.nombre AS unidad,
        ep.nombre AS estado,
        p.fecha_nacimiento,
        p.fecha_ingreso,
        p.fecha_ascenso,
        p.fecha_salida,
        p.tiempo_servicio,
        p.nro_cuenta,
        p.departamento,
        p.provincia,
        p.distrito,
        p.direccion,
        p.obs
    FROM personal_militar p
    LEFT JOIN grado g ON g.id_grado = p.id_grado
    LEFT JOIN arma a ON a.id_arma = p.id_arma
    LEFT JOIN unidad u ON u.id_unidad = p.id_unidad
    LEFT JOIN estado_personal ep ON ep.id_estado = p.id_estado

    UNION ALL

    SELECT
        pc.id_civil AS id,
        pc.nombres,
        pc.dni,
        NULL AS cip,
        'CIVIL' AS tipo_personal,
        pc.sexo,
        pc.grado_laboral AS grado,
        NULL AS arma,
        u.nombre AS unidad,
        pc.condicion AS estado,
        pc.fecha_nacimiento,
        pc.fecha_ingreso,
        NULL AS fecha_ascenso,
        pc.fecha_salida,
        NULL AS tiempo_servicio,
        NULL AS nro_cuenta,
        NULL AS departamento,
        NULL AS provincia,
        NULL AS distrito,
        NULL AS direccion,
        pc.obs
    FROM personal_civil pc
    LEFT JOIN unidad u ON u.id_unidad = pc.id_unidad
) datos
WHERE 1=1
";

$params = [];
$contador = 1;

if ($tipo === 'MILITAR') {
    $sql .= " AND tipo_personal <> $" . $contador;
    $params[] = 'CIVIL';
    $contador++;
}

if ($tipo === 'CIVIL') {
    $sql .= " AND tipo_personal = $" . $contador;
    $params[] = 'CIVIL';
    $contador++;
}

if (!empty($sexo)) {
    $sql .= " AND sexo = $" . $contador;
    $params[] = $sexo;
    $contador++;
}

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $sql .= " AND fecha_ingreso BETWEEN $" . $contador . " AND $" . ($contador + 1);
    $params[] = $fechaInicio;
    $params[] = $fechaFin;
    $contador += 2;
}

$sql .= "
ORDER BY
CASE
    WHEN tipo_personal = 'OF' THEN 1
    WHEN tipo_personal = 'TEC' THEN 2
    WHEN tipo_personal = 'SUB OFICIAL' THEN 3
    WHEN tipo_personal = 'REE' THEN 4
    WHEN tipo_personal = 'TROPA' THEN 5
    WHEN tipo_personal = 'CIVIL' THEN 6
    ELSE 9
END,
grado ASC,
nombres ASC
";

$query = pg_query_params($conn, $sql, $params);

if (!$query) {
    die("Error SQL: " . pg_last_error($conn));
}

$datos = pg_fetch_all($query);

if (!$datos) {
    $datos = [];
}

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Reporte Personal');

$sheet->mergeCells('A1:O1');

$sheet->setCellValue('A1', 'REPORTE GENERAL DEL PERSONAL');

$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '204615']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
]);

$sheet->mergeCells('A2:O2');

$sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i:s'));

$headers = [
    'ID',
    'NOMBRES',
    'DNI',
    'CIP',
    'TIPO',
    'SEXO',
    'GRADO',
    'ARMA',
    'UNIDAD',
    'ESTADO',
    'F. NACIMIENTO',
    'F. INGRESO',
    'F. ASCENSO',
    'SERVICIO',
    'DIRECCIÓN'
];

$sheet->fromArray($headers, null, 'A4');

$sheet->getStyle('A4:O4')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '204615']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ]
]);

$fila = 5;

foreach ($datos as $row) {
    $sheet->setCellValue('A' . $fila, $row['id']);
    $sheet->setCellValue('B' . $fila, $row['nombres']);
    $sheet->setCellValue('C' . $fila, $row['dni']);
    $sheet->setCellValue('D' . $fila, $row['cip']);
    $sheet->setCellValue('E' . $fila, $row['tipo_personal']);
    $sheet->setCellValue('F' . $fila, $row['sexo']);
    $sheet->setCellValue('G' . $fila, $row['grado']);
    $sheet->setCellValue('H' . $fila, $row['arma']);
    $sheet->setCellValue('I' . $fila, $row['unidad']);
    $sheet->setCellValue('J' . $fila, $row['estado']);
    $sheet->setCellValue('K' . $fila, $row['fecha_nacimiento']);
    $sheet->setCellValue('L' . $fila, $row['fecha_ingreso']);
    $sheet->setCellValue('M' . $fila, $row['fecha_ascenso']);
    $sheet->setCellValue('N' . $fila, $row['tiempo_servicio']);
    $sheet->setCellValue('O' . $fila, $row['direccion']);

    $fila++;
}

if ($fila > 5) {
    $sheet->getStyle('A5:O' . ($fila - 1))->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'CCCCCC']
            ]
        ]
    ]);
}

foreach (range('A', 'O') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$resumen = $spreadsheet->createSheet();
$resumen->setTitle('Resumen');

$conteoTipo = [];
$conteoSexo = [];
$conteoUnidad = [];
$conteoEstado = [];
$conteoGrado = [];
$conteoArma = [];

foreach ($datos as $row) {
    $tipoKey   = $row['tipo_personal'] ?: 'SIN DATO';
    $sexoKey   = $row['sexo'] ?: 'SIN DATO';
    $unidadKey = $row['unidad'] ?: 'SIN DATO';
    $estadoKey = $row['estado'] ?: 'SIN DATO';
    $gradoKey  = $row['grado'] ?: 'SIN DATO';
    $armaKey   = $row['arma'] ?: 'SIN DATO';

    $conteoTipo[$tipoKey] = ($conteoTipo[$tipoKey] ?? 0) + 1;
    $conteoSexo[$sexoKey] = ($conteoSexo[$sexoKey] ?? 0) + 1;
    $conteoUnidad[$unidadKey] = ($conteoUnidad[$unidadKey] ?? 0) + 1;
    $conteoEstado[$estadoKey] = ($conteoEstado[$estadoKey] ?? 0) + 1;
    $conteoGrado[$gradoKey] = ($conteoGrado[$gradoKey] ?? 0) + 1;

    if ($tipo !== 'CIVIL') {
        $conteoArma[$armaKey] = ($conteoArma[$armaKey] ?? 0) + 1;
    }
}

$resumen->mergeCells('A1:H1');
$resumen->setCellValue('A1', 'RESUMEN ESTADÍSTICO DEL PERSONAL');

$resumen->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '204615']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

function pintarTablaResumen($sheet, $titulo1, $titulo2, $datos, $col1, $col2, $filaInicio)
{
    $sheet->setCellValue($col1 . $filaInicio, $titulo1);
    $sheet->setCellValue($col2 . $filaInicio, $titulo2);

    $sheet->getStyle($col1 . $filaInicio . ':' . $col2 . $filaInicio)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '204615']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);

    $fila = $filaInicio + 1;

    foreach ($datos as $key => $value) {
        $sheet->setCellValue($col1 . $fila, $key);
        $sheet->setCellValue($col2 . $fila, $value);
        $fila++;
    }

    return $fila;
}

function crearGrafico($sheet, $nombre, $titulo, $tipoGrafico, $labelRange, $valueRange, $topLeft, $bottomRight)
{
    $labels = [
        new DataSeriesValues('String', $labelRange, null, 10)
    ];

    $values = [
        new DataSeriesValues('Number', $valueRange, null, 10)
    ];

    $series = new DataSeries(
        $tipoGrafico,
        $tipoGrafico === DataSeries::TYPE_BARCHART ? DataSeries::GROUPING_CLUSTERED : null,
        range(0, count($values) - 1),
        [],
        $labels,
        $values
    );

    if ($tipoGrafico === DataSeries::TYPE_BARCHART) {
        $series->setPlotDirection(DataSeries::DIRECTION_COL);
    }

    $chart = new Chart(
        $nombre,
        new Title($titulo),
        new Legend(Legend::POSITION_RIGHT, null, false),
        new PlotArea(null, [$series])
    );

    $chart->setTopLeftPosition($topLeft);
    $chart->setBottomRightPosition($bottomRight);

    $sheet->addChart($chart);
}

if ($tipo === 'general') {
    $filaTipo = pintarTablaResumen($resumen, 'Tipo de personal', 'Cantidad', $conteoTipo, 'A', 'B', 3);
    $filaSexo = pintarTablaResumen($resumen, 'Sexo', 'Cantidad', $conteoSexo, 'D', 'E', 3);
    $filaUnidad = pintarTablaResumen($resumen, 'Unidad', 'Cantidad', $conteoUnidad, 'A', 'B', 18);
    $filaEstado = pintarTablaResumen($resumen, 'Estado / Condición', 'Cantidad', $conteoEstado, 'D', 'E', 18);

    if ($filaTipo > 4) {
        crearGrafico($resumen, 'chart_tipo', 'Personal por tipo', DataSeries::TYPE_BARCHART, 'Resumen!$A$4:$A$' . ($filaTipo - 1), 'Resumen!$B$4:$B$' . ($filaTipo - 1), 'G3', 'N16');
    }

    if ($filaSexo > 4) {
        crearGrafico($resumen, 'chart_sexo', 'Personal por sexo', DataSeries::TYPE_PIECHART, 'Resumen!$D$4:$D$' . ($filaSexo - 1), 'Resumen!$E$4:$E$' . ($filaSexo - 1), 'G18', 'N31');
    }

    if ($filaUnidad > 19) {
        crearGrafico($resumen, 'chart_unidad', 'Personal por unidad', DataSeries::TYPE_BARCHART, 'Resumen!$A$19:$A$' . ($filaUnidad - 1), 'Resumen!$B$19:$B$' . ($filaUnidad - 1), 'A35', 'N55');
    }

    if ($filaEstado > 19) {
        crearGrafico($resumen, 'chart_estado', 'Personal por estado / condición', DataSeries::TYPE_BARCHART, 'Resumen!$D$19:$D$' . ($filaEstado - 1), 'Resumen!$E$19:$E$' . ($filaEstado - 1), 'A58', 'N78');
    }
}

if ($tipo === 'MILITAR') {
    $filaGrado = pintarTablaResumen($resumen, 'Grado', 'Cantidad', $conteoGrado, 'A', 'B', 3);
    $filaArma = pintarTablaResumen($resumen, 'Arma', 'Cantidad', $conteoArma, 'D', 'E', 3);
    $filaUnidad = pintarTablaResumen($resumen, 'Unidad', 'Cantidad', $conteoUnidad, 'A', 'B', 18);
    $filaEstado = pintarTablaResumen($resumen, 'Estado', 'Cantidad', $conteoEstado, 'D', 'E', 18);
    $filaSexo = pintarTablaResumen($resumen, 'Sexo', 'Cantidad', $conteoSexo, 'A', 'B', 35);

    if ($filaGrado > 4) {
        crearGrafico($resumen, 'chart_grado', 'Personal por grado', DataSeries::TYPE_BARCHART, 'Resumen!$A$4:$A$' . ($filaGrado - 1), 'Resumen!$B$4:$B$' . ($filaGrado - 1), 'G3', 'N16');
    }

    if ($filaArma > 4) {
        crearGrafico($resumen, 'chart_arma', 'Personal por arma', DataSeries::TYPE_BARCHART, 'Resumen!$D$4:$D$' . ($filaArma - 1), 'Resumen!$E$4:$E$' . ($filaArma - 1), 'G18', 'N31');
    }

    if ($filaUnidad > 19) {
        crearGrafico($resumen, 'chart_unidad', 'Personal por unidad', DataSeries::TYPE_BARCHART, 'Resumen!$A$19:$A$' . ($filaUnidad - 1), 'Resumen!$B$19:$B$' . ($filaUnidad - 1), 'A40', 'N58');
    }
}

if ($tipo === 'CIVIL') {
    $filaGrado = pintarTablaResumen($resumen, 'Grado laboral', 'Cantidad', $conteoGrado, 'A', 'B', 3);
    $filaUnidad = pintarTablaResumen($resumen, 'Unidad', 'Cantidad', $conteoUnidad, 'D', 'E', 3);
    $filaEstado = pintarTablaResumen($resumen, 'Condición', 'Cantidad', $conteoEstado, 'A', 'B', 18);
    $filaSexo = pintarTablaResumen($resumen, 'Sexo', 'Cantidad', $conteoSexo, 'D', 'E', 18);

    if ($filaGrado > 4) {
        crearGrafico($resumen, 'chart_grado_laboral', 'Personal civil por grado laboral', DataSeries::TYPE_BARCHART, 'Resumen!$A$4:$A$' . ($filaGrado - 1), 'Resumen!$B$4:$B$' . ($filaGrado - 1), 'G3', 'N16');
    }

    if ($filaUnidad > 4) {
        crearGrafico($resumen, 'chart_unidad_civil', 'Personal civil por unidad', DataSeries::TYPE_BARCHART, 'Resumen!$D$4:$D$' . ($filaUnidad - 1), 'Resumen!$E$4:$E$' . ($filaUnidad - 1), 'G18', 'N31');
    }

    if ($filaEstado > 19) {
        crearGrafico($resumen, 'chart_condicion', 'Personal civil por condición', DataSeries::TYPE_PIECHART, 'Resumen!$A$19:$A$' . ($filaEstado - 1), 'Resumen!$B$19:$B$' . ($filaEstado - 1), 'A35', 'N52');
    }
}

foreach (range('A', 'N') as $col) {
    $resumen->getColumnDimension($col)->setAutoSize(true);
}

$spreadsheet->setActiveSheetIndex(0);

$carpeta = '../../reportes_generados/';

if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}

$nombreArchivo = 'reporte_personal_' . time() . '.xlsx';

$rutaArchivo = $carpeta . $nombreArchivo;

$writer = new Xlsx($spreadsheet);
$writer->setIncludeCharts(true);
$writer->save($rutaArchivo);

$sqlHistorial = "
INSERT INTO historial_reportes
(
    nombre_reporte,
    tipo,
    formato,
    archivo,
    generado_por
)
VALUES
(
    $1,
    $2,
    $3,
    $4,
    $5
)
";

pg_query_params($conn, $sqlHistorial, [
    'Reporte Personal',
    $tipo,
    'Excel',
    $nombreArchivo,
    $idUsuario
]);

ob_end_clean();

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($rutaArchivo));

readfile($rutaArchivo);

exit;
