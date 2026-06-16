<?php

session_start();

require_once(__DIR__ . "/../backend/config/conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$pageTitle  = "Reportes";
$activePage = "reportes";

define('BASE_CSS', 'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="css/reportes.css">';

$queryPersonal = pg_query($conn, "
SELECT
    (
        SELECT COUNT(*) FROM personal_militar
    ) +
    (
        SELECT COUNT(*) FROM personal_civil
    ) AS total
");

$totalPersonal = 0;

if ($queryPersonal) {
    $data = pg_fetch_assoc($queryPersonal);
    $totalPersonal = $data['total'] ?? 0;
}

$queryReportes = pg_query($conn, "
SELECT COUNT(*) AS total
FROM historial_reportes
");

$totalReportes = 0;

if ($queryReportes) {
    $data = pg_fetch_assoc($queryReportes);
    $totalReportes = $data['total'] ?? 0;
}

$queryUltimo = pg_query($conn, "
SELECT fecha_generado
FROM historial_reportes
ORDER BY fecha_generado DESC
LIMIT 1
");

$fechaUltimoReporte = "Sin datos";

if ($queryUltimo && pg_num_rows($queryUltimo) > 0) {
    $ultimo = pg_fetch_assoc($queryUltimo);
    $fechaUltimoReporte = date('d/m/Y', strtotime($ultimo['fecha_generado']));
}

$queryHistorial = pg_query($conn, "
SELECT
    h.*,
    u.username
FROM historial_reportes h
LEFT JOIN usuario u ON u.id_usuario = h.generado_por
ORDER BY h.fecha_generado DESC
LIMIT 10
");

$historial = [];

if ($queryHistorial) {
    $historial = pg_fetch_all($queryHistorial);
    if (!$historial) {
        $historial = [];
    }
}

include 'includes/layout.php';

?>

<div class="reports-page">

    <div class="reports-header">
        <div>
            <h1>Reportes del Sistema</h1>
            <p>Genere reportes institucionales del personal registrado</p>
        </div>

        <div class="reports-badge">
            <i class="fa fa-chart-column"></i>
            Centro de Reportes
        </div>
    </div>

    <div class="report-filter-card">

        <div class="filter-top">
            <h3>
                <i class="fa fa-filter"></i>
                Generar Reporte
            </h3>
        </div>

        <form class="report-form" action="../backend/reportes/reportePersonalExcel.php" method="GET">

            <div class="filter-grid">

                <div class="filter-group">
                    <label>Desde</label>
                    <input type="date" name="fecha_inicio">
                </div>

                <div class="filter-group">
                    <label>Hasta</label>
                    <input type="date" name="fecha_fin">
                </div>

                <div class="filter-group">
                    <label>Tipo de Personal</label>
                    <select name="tipo">
                        <option value="general">General</option>
                        <option value="CIVIL">Civil</option>
                        <option value="TROPA">Tropa</option>
                        <option value="REE">REE</option>
                        <option value="SUB OFICIAL">Sub Oficial</option>
                        <option value="TEC">TEC</option>
                        <option value="OF">OF</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Sexo</label>
                    <select name="sexo">
                        <option value="">Todos</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Formato</label>
                    <select name="formato">
                        <option value="excel">Excel (.xlsx)</option>
                    </select>
                </div>

            </div>

            <div class="report-actions">
                <button type="submit" class="btn-primary">
                    <i class="fa fa-file-export"></i>
                    Generar Reporte
                </button>
            </div>

        </form>

    </div>

    <div class="reports-stats">

        <div class="report-stat-card">
            <div class="stat-icon green">
                <i class="fa fa-file-excel"></i>
            </div>
            <div class="stat-info">
                <h2><?= $totalReportes ?></h2>
                <p>Reportes Generados</p>
            </div>
        </div>

        <div class="report-stat-card">
            <div class="stat-icon blue">
                <i class="fa fa-clock"></i>
            </div>
            <div class="stat-info">
                <h2><?= $fechaUltimoReporte ?></h2>
                <p>Último Reporte</p>
            </div>
        </div>

        <div class="report-stat-card">
            <div class="stat-icon orange">
                <i class="fa fa-users"></i>
            </div>
            <div class="stat-info">
                <h2><?= $totalPersonal ?></h2>
                <p>Personal Registrado</p>
            </div>
        </div>

    </div>

    <div class="report-history-card">

        <div class="history-header">
            <h3>
                <i class="fa fa-history"></i>
                Historial de Reportes
            </h3>
        </div>

        <div class="table-wrap">

            <table class="reports-table">

                <thead>
                    <tr>
                        <th>Reporte</th>
                        <th>Formato</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Descargar</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($historial)): ?>

                        <?php foreach ($historial as $r): ?>

                            <tr>
                                <td><?= htmlspecialchars($r['nombre_reporte'] ?? '-') ?></td>

                                <td>
                                    <span class="badge excel">
                                        <?= htmlspecialchars($r['formato'] ?? '-') ?>
                                    </span>
                                </td>

                                <td>
                                    <?= date('d/m/Y H:i', strtotime($r['fecha_generado'])) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($r['username'] ?? 'Desconocido') ?>
                                </td>

                                <td>
                                    <a href="../reportes_generados/<?= htmlspecialchars($r['archivo'] ?? '') ?>" class="btn-download" download>
                                        <i class="fa fa-download"></i>
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5">No hay reportes generados</td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include 'includes/layout_end.php'; ?>