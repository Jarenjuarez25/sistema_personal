<?php
session_start();

require_once(__DIR__ . "/../backend/config/conexion.php");

if (!isset($_SESSION["usuario"])) {

    header("Location: login.php");
    exit();
}

$pageTitle  = "Dashboard";
$activePage = "dashboard";

define('BASE_CSS', 'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="' . BASE_CSS . 'dashboard.css">';

$queryTotal = "
SELECT COUNT(*) AS total
FROM personal_militar
";

$resultTotal = pg_query($conn, $queryTotal);
$totalPersonal = $resultTotal ? pg_fetch_assoc($resultTotal)['total'] : 0;

$queryTipoPersonal = "
SELECT 
    tipo_personal,
    COUNT(*) AS total
FROM personal_militar
GROUP BY tipo_personal
ORDER BY total DESC
";

$resultTipoPersonal = pg_query($conn, $queryTipoPersonal);

$labelsTipo = [];
$dataTipo = [];

if ($resultTipoPersonal) {
    while ($row = pg_fetch_assoc($resultTipoPersonal)) {
        $labelsTipo[] = $row['tipo_personal'] ?: 'Sin tipo';
        $dataTipo[] = (int)$row['total'];
    }
}

$queryActivos = "
SELECT COUNT(*) AS activos
FROM personal_militar p
JOIN estado_personal ep ON p.id_estado = ep.id_estado
WHERE LOWER(TRIM(ep.nombre)) = 'actividad'
";

$resultActivos = pg_query($conn, $queryActivos);
$totalActivos = $resultActivos ? pg_fetch_assoc($resultActivos)['activos'] : 0;

$queryPermiso = "
SELECT COUNT(*) AS permiso
FROM personal_militar p
JOIN estado_personal ep ON p.id_estado = ep.id_estado
WHERE LOWER(TRIM(ep.nombre)) = 'permiso'
";

$resultPermiso = pg_query($conn, $queryPermiso);
$totalPermiso = $resultPermiso ? pg_fetch_assoc($resultPermiso)['permiso'] : 0;

$queryVacaciones = "
SELECT COUNT(*) AS vacaciones
FROM personal_militar p
JOIN estado_personal ep ON p.id_estado = ep.id_estado
WHERE LOWER(TRIM(ep.nombre)) = 'vacaciones'
";

$resultVacaciones = pg_query($conn, $queryVacaciones);
$totalVacaciones = $resultVacaciones ? pg_fetch_assoc($resultVacaciones)['vacaciones'] : 0;

$queryRetirados = "
SELECT COUNT(*) AS retirados
FROM personal_militar p
JOIN estado_personal ep ON p.id_estado = ep.id_estado
WHERE LOWER(TRIM(ep.nombre)) = 'retiro'
";

$resultRetirados = pg_query($conn, $queryRetirados);
$totalRetirados = $resultRetirados ? pg_fetch_assoc($resultRetirados)['retirados'] : 0;

$queryLicencia = "
SELECT COUNT(*) AS licencia
FROM personal_militar p
JOIN estado_personal ep ON p.id_estado = ep.id_estado
WHERE LOWER(TRIM(ep.nombre)) = 'licencia'
";

$resultLicencia = pg_query($conn, $queryLicencia);
$totalLicencia = $resultLicencia ? pg_fetch_assoc($resultLicencia)['licencia'] : 0;

$queryCambiado = "
SELECT COUNT(*) AS cambiado
FROM personal_militar p
JOIN estado_personal ep ON p.id_estado = ep.id_estado
WHERE LOWER(TRIM(ep.nombre)) = 'cambiado'
";

$resultCambiado = pg_query($conn, $queryCambiado);
$totalCambiado = $resultCambiado ? pg_fetch_assoc($resultCambiado)['cambiado'] : 0;

$queryUltimos = "
SELECT 
    p.nombres,
    g.nombre AS grado,
    u.nombre AS unidad,
    ep.nombre AS estado

FROM personal_militar p

LEFT JOIN grado g
ON p.id_grado = g.id_grado

LEFT JOIN unidad u
ON p.id_unidad = u.id_unidad

LEFT JOIN estado_personal ep
ON p.id_estado = ep.id_estado

ORDER BY p.id_personal DESC
LIMIT 5
";

$resultUltimos = pg_query($conn, $queryUltimos);

include 'includes/layout.php';

?>

<div class="page-header">

    <div class="page-header-title">

        <h1>Dashboard Principal</h1>

        <p>

            Resumen general del personal —
            <?= date('d \d\e F, Y') ?>

        </p>

    </div>

</div>

<div class="stats-grid">

    <div class="stat-card" style="--accent:#5db85d">
        <div class="stat-icon"><i class="fa fa-users"></i></div>
        <div class="stat-body">
            <span class="stat-label">Personal Total</span>
            <span class="stat-value"><?= $totalPersonal ?></span>
        </div>
    </div>

    <div class="stat-card" style="--accent:#4a9fd4">
        <div class="stat-icon"><i class="fa fa-user-check"></i></div>
        <div class="stat-body">
            <span class="stat-label">Actividad</span>
            <span class="stat-value"><?= $totalActivos ?></span>
        </div>
    </div>

    <div class="stat-card" style="--accent:#d4a94a">
        <div class="stat-icon"><i class="fa fa-user-clock"></i></div>
        <div class="stat-body">
            <span class="stat-label">Permiso</span>
            <span class="stat-value"><?= $totalPermiso ?></span>
        </div>
    </div>

    <div class="stat-card" style="--accent:#c0392b">
        <div class="stat-icon"><i class="fa fa-user-minus"></i></div>
        <div class="stat-body">
            <span class="stat-label">Retiro</span>
            <span class="stat-value"><?= $totalRetirados ?></span>
        </div>
    </div>
</div>

<div class="dash-grid">

    <div class="card dash-chart-card">

        <div class="card-header">

            <h3>Personal por Estado</h3>

        </div>

        <div class="chart-wrap">

            <canvas id="barChart"></canvas>

        </div>

    </div>

<div class="card dash-donut-card">

    <div class="card-header">
        <h3>Personal por Tipo</h3>
    </div>

    <div class="donut-wrap">
        <canvas id="donutChart"></canvas>
    </div>

</div>

    <div class="card dash-table-card">

        <div class="card-header">

            <h3>Últimos Registros</h3>

            <a href="personal.php" class="card-link">

                Ver todo

            </a>

        </div>

        <div class="table-wrap">

            <table class="dash-table">

                <thead>

                    <tr>

                        <th>Nombre</th>
                        <th>Grado</th>
                        <th>Unidad</th>
                        <th>Estado</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if ($resultUltimos): ?>

                        <?php while ($row = pg_fetch_assoc($resultUltimos)) { ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($row['nombres'] ?? '-') ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($row['grado'] ?? '-') ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($row['unidad'] ?? '-') ?>

                                </td>

                                <td>

                                    <?php

                                    $estado = strtolower($row['estado'] ?? '');

                                    $class = match (true) {
                                        str_contains($estado, 'actividad') => 'active',
                                        str_contains($estado, 'permiso')   => 'permit',
                                        str_contains($estado, 'vacacion')  => 'vacations',
                                        str_contains($estado, 'licencia')  => 'license',
                                        str_contains($estado, 'retiro')    => 'retired',
                                        str_contains($estado, 'cambiado')  => 'changed',
                                        default                            => 'retired'
                                    };

                                    ?>

                                    <span class="badge <?= $class ?>">

                                        <?= htmlspecialchars($row['estado'] ?? '-') ?>

                                    </span>

                                </td>

                            </tr>

                        <?php } ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <div class="card dash-quick-card">

        <div class="card-header">

            <h3>Accesos Rápidos</h3>

        </div>

        <div class="quick-grid">

            <a href="personal.php" class="quick-btn">

                <i class="fa fa-users"></i>

                <span>Lista Personal</span>

            </a>

            <a href="consultas.php" class="quick-btn">

                <i class="fa fa-magnifying-glass"></i>

                <span>Búsqueda</span>

            </a>

            <a href="reportes.php" class="quick-btn">

                <i class="fa fa-file-lines"></i>

                <span>Reportes</span>

            </a>

            <a href="configuracion.php" class="quick-btn">

                <i class="fa fa-gear"></i>

                <span>Config.</span>

            </a>

        </div>

    </div>

</div>

<?php include 'includes/layout_end.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const GREEN = '#3a8a1a';
    const GOLD = '#9a7010';
    const GRAY = '#555570';
    const BLUE = '#5096a4';
    const RED = '#c0392b';
    const PURPLE = '#8e44ad';

new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: ['Actividad','Permiso','Vacaciones','Retiro','Licencia','Cambiado'],
        datasets: [{
            label: 'Personas',
            data: [
                <?= $totalActivos ?>,
                <?= $totalPermiso ?>,
                <?= $totalVacaciones ?>,
                <?= $totalRetirados ?>,
                <?= $totalLicencia ?>,
                <?= $totalCambiado ?>
            ],
            backgroundColor: [GREEN, BLUE, GOLD, GRAY, RED, PURPLE]
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($labelsTipo) ?>,
        datasets: [{
            data: <?= json_encode($dataTipo) ?>,
            backgroundColor: [
                GREEN,
                BLUE,
                GOLD,
                GRAY,
                RED,
                PURPLE
            ],
            borderWidth: 2,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 14,
                    boxHeight: 8,
                    padding: 12,
                    font: {
                        size: 11,
                        family: 'Barlow'
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const value = context.raw;
                        const porcentaje = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return `${context.label}: ${value} (${porcentaje}%)`;
                    }
                }
            }
        }
    }
});
</script>