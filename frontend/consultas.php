<?php
require_once(__DIR__ . "/../backend/config/conexion.php");
session_start();
$pageTitle  = "Consultas";
$activePage = "consultas";
define('BASE_CSS', 'css/');
define('BASE_ASSETS', '../../assets/');
$extraCSS = '<link rel="stylesheet" href="css/consultas.css">';
$grados   = pg_query($conn, "SELECT * FROM grado ORDER BY nombre ASC");
$unidades = pg_query($conn, "SELECT * FROM unidad ORDER BY nombre ASC");
$estados  = pg_query($conn, "SELECT * FROM estado_personal ORDER BY nombre ASC");

$buscar = trim($_GET['buscar'] ?? '');
$tipoPersonalFiltro = $_GET['tipo_personal'] ?? '';

$whereMilitar = [];
$paramsMilitar = [];
$cm = 1;

$whereCivil = [];
$paramsCivil = [];
$cc = 1;

if ($tipoPersonalFiltro === 'MILITAR') {
    $whereCivil[] = "1=0";
}

if ($tipoPersonalFiltro === 'CIVIL') {
    $whereMilitar[] = "1=0";
}

$buscarLower = mb_strtolower($buscar);

if ($buscar !== '') {
    if (str_contains($buscarLower, 'soldado') || str_contains($buscarLower, 'sldo')) {
        $whereMilitar[] = "(g.nombre ILIKE $" . $cm . " OR p.tipo_personal ILIKE $" . ($cm + 1) . ")";
        $paramsMilitar[] = '%SLDO%';
        $paramsMilitar[] = '%TROPA%';
        $cm += 2;

        $whereCivil[] = "1=0";
    } elseif (str_contains($buscarLower, 'civil')) {
        $whereMilitar[] = "1=0";
    } else {
        $whereMilitar[] = "(p.nombres ILIKE $" . $cm . " OR p.dni ILIKE $" . $cm . " OR p.cip ILIKE $" . $cm . " OR g.nombre ILIKE $" . $cm . " OR p.tipo_personal ILIKE $" . $cm . ")";
        $paramsMilitar[] = '%' . $buscar . '%';
        $cm++;

        $whereCivil[] = "(c.nombres ILIKE $" . $cc . " OR c.dni ILIKE $" . $cc . " OR c.na ILIKE $" . $cc . " OR c.grado_laboral ILIKE $" . $cc . ")";
        $paramsCivil[] = '%' . $buscar . '%';
        $cc++;
    }
}

if (!empty($_GET['fecha_ingreso'])) {
    $whereMilitar[] = "p.fecha_ingreso = $" . $cm;
    $paramsMilitar[] = $_GET['fecha_ingreso'];
    $cm++;

    $whereCivil[] = "c.fecha_ingreso = $" . $cc;
    $paramsCivil[] = $_GET['fecha_ingreso'];
    $cc++;
}

if (!empty($_GET['fecha_ascenso'])) {
    $whereMilitar[] = "p.fecha_ascenso = $" . $cm;
    $paramsMilitar[] = $_GET['fecha_ascenso'];
    $cm++;

    $whereCivil[] = "1=0";
}

if (!empty($_GET['sexo'])) {
    $whereMilitar[] = "p.sexo = $" . $cm;
    $paramsMilitar[] = $_GET['sexo'];
    $cm++;

    $whereCivil[] = "c.sexo = $" . $cc;
    $paramsCivil[] = $_GET['sexo'];
    $cc++;
}

if (!empty($_GET['grado'])) {
    $whereMilitar[] = "p.id_grado = $" . $cm;
    $paramsMilitar[] = $_GET['grado'];
    $cm++;

    $whereCivil[] = "1=0";
}

if (!empty($_GET['unidad'])) {
    $whereMilitar[] = "p.id_unidad = $" . $cm;
    $paramsMilitar[] = $_GET['unidad'];
    $cm++;

    $whereCivil[] = "c.id_unidad = $" . $cc;
    $paramsCivil[] = $_GET['unidad'];
    $cc++;
}

if (!empty($_GET['estado'])) {
    $whereMilitar[] = "p.id_estado = $" . $cm;
    $paramsMilitar[] = $_GET['estado'];
    $cm++;

    $whereCivil[] = "1=0";
}

$query = "
SELECT
    p.id_personal,
    p.nombres,
    p.dni,
    p.cip,
    p.tipo_personal,
    p.fecha_ingreso,
    p.fecha_ascenso,
    p.sexo,
    g.nombre AS grado,
    a.nombre AS arma,
    u.nombre AS unidad,
    ep.nombre AS estado
FROM personal_militar p
LEFT JOIN arma a ON p.id_arma = a.id_arma
LEFT JOIN grado g ON p.id_grado = g.id_grado
LEFT JOIN unidad u ON p.id_unidad = u.id_unidad
LEFT JOIN estado_personal ep ON p.id_estado = ep.id_estado
";

if (count($whereMilitar) > 0) {
    $query .= " WHERE " . implode(" AND ", $whereMilitar);
}

$query .= " ORDER BY p.nombres ASC";

$result = pg_query_params($conn, $query, $paramsMilitar);

if (!$result) {
    die("Error SQL militar: " . pg_last_error($conn));
}

$total = pg_num_rows($result);

$queryCivil = "
SELECT
    c.id_civil,
    c.nombres,
    c.dni,
    c.na,
    c.grupo,
    c.gguu,
    c.nucleo,
    c.grado_laboral,
    c.fecha_ingreso,
    c.celular,
    c.condicion,
    u.nombre AS unidad
FROM personal_civil c
LEFT JOIN unidad u ON c.id_unidad = u.id_unidad
";

if (count($whereCivil) > 0) {
    $queryCivil .= " WHERE " . implode(" AND ", $whereCivil);
}

$queryCivil .= " ORDER BY c.nombres ASC";

$resultCivil = pg_query_params($conn, $queryCivil, $paramsCivil);

if (!$resultCivil) {
    die("Error SQL civil: " . pg_last_error($conn));
}

$totalCivil = pg_num_rows($resultCivil);


include 'includes/layout.php';
?>
<div class="page-header">
    <div class="page-header-title">
        <h1>Consultas de Personal</h1>
        <p>Filtrado y búsqueda avanzada del personal militar</p>
    </div>
</div>
<div class="card consulta-card">
    <div class="card-header-custom">
        <div>
            <h3>Filtros de Búsqueda</h3>
            <span>Seleccione criterios para consultar personal</span>
        </div>
    </div>

    <form action="" method="GET" class="consulta-form">
        <div class="main-filter-row">

            <div class="form-group search-main">
                <label>Buscar por Nombre / DNI / CIP</label>
                <input
                    type="text"
                    name="buscar"
                    value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>"
                    placeholder="Escriba nombre, DNI o CIP..."
                    autocomplete="off">
            </div>

            <div class="form-group type-main">
                <label>Tipo de personal</label>
                <select name="tipo_personal">
                    <option value="">Todos</option>
                    <option value="MILITAR" <?= (($_GET['tipo_personal'] ?? '') == 'MILITAR') ? 'selected' : '' ?>>Militar</option>
                    <option value="CIVIL" <?= (($_GET['tipo_personal'] ?? '') == 'CIVIL') ? 'selected' : '' ?>>Civil</option>
                </select>
            </div>

            <div class="form-group button-main">
                <label>&nbsp;</label>
                <button type="submit" class="btn-search">
                    <i class="fa fa-magnifying-glass"></i>
                    Buscar
                </button>
            </div>

        </div>

        <details class="advanced-filters" <?= (
                                                !empty($_GET['grado']) ||
                                                !empty($_GET['unidad']) ||
                                                !empty($_GET['estado']) ||
                                                !empty($_GET['sexo']) ||
                                                !empty($_GET['fecha_ingreso']) ||
                                                !empty($_GET['fecha_ascenso'])
                                            ) ? 'open' : '' ?>>

            <summary>
                <i class="fa fa-sliders"></i>
                Más filtros
            </summary>

            <div class="form-grid filtros-grid">

                <div class="form-group">
                    <label>Grado</label>
                    <select name="grado">
                        <option value="">Todos los grados</option>
                        <?php while ($g = pg_fetch_assoc($grados)): ?>
                            <option value="<?= $g['id_grado'] ?>" <?= (($_GET['grado'] ?? '') == $g['id_grado']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Unidad</label>
                    <select name="unidad">
                        <option value="">Todas las unidades</option>
                        <?php while ($u = pg_fetch_assoc($unidades)): ?>
                            <option value="<?= $u['id_unidad'] ?>" <?= (($_GET['unidad'] ?? '') == $u['id_unidad']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="">Todos los estados</option>
                        <?php while ($e = pg_fetch_assoc($estados)): ?>
                            <option value="<?= $e['id_estado'] ?>" <?= (($_GET['estado'] ?? '') == $e['id_estado']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Sexo</label>
                    <select name="sexo">
                        <option value="">Todos</option>
                        <option value="M" <?= (($_GET['sexo'] ?? '') == 'M') ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= (($_GET['sexo'] ?? '') == 'F') ? 'selected' : '' ?>>Femenino</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha de ingreso</label>
                    <input type="date" name="fecha_ingreso" value="<?= htmlspecialchars($_GET['fecha_ingreso'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Fecha de ascenso</label>
                    <input type="date" name="fecha_ascenso" value="<?= htmlspecialchars($_GET['fecha_ascenso'] ?? '') ?>">
                </div>

                <div class="form-group clear-filter">
                    <label>&nbsp;</label>
                    <a href="consultas.php" class="btn-clear">
                        <i class="fa fa-rotate-left"></i>
                        Limpiar filtros
                    </a>
                </div>

            </div>

        </details>

    </form>


</div>

<?php if ($total == 0 && $totalCivil == 0): ?>

    <div class="card table-card">

        <div class="empty-state">

            <i class="fa fa-users-slash"></i>

            <h3>No se encontraron registros</h3>

            <p>No existen resultados con los filtros seleccionados.</p>

        </div>

    </div>

<?php endif; ?>

<?php if ($total > 0): ?>

    <div class="card table-card">

        <div class="table-header">

            <div>

                <h3>Personal Militar</h3>

                <span><?= $total ?> registros encontrados</span>

            </div>

        </div>

        <div class="table-wrap">

            <table class="consulta-table">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Personal</th>
                        <th>Grado</th>
                        <th>Arma</th>
                        <th>Unidad</th>
                        <th>DNI</th>
                        <th>CIP</th>
                        <th>Fecha Ingreso</th>
                        <th>Fecha Ascenso</th>
                        <th>Estado</th>
                    </tr>

                </thead>

                <tbody>

                    <?php $i = 1; ?>

                    <?php while ($row = pg_fetch_assoc($result)): ?>

                        <?php
                        $palabras = explode(' ', trim($row['nombres'] ?? ''));

                        $iniciales = strtoupper(
                            substr($palabras[0] ?? '', 0, 1) .
                                substr($palabras[1] ?? '', 0, 1)
                        );
                        ?>

                        <tr>

                            <td class="td-id"><?= $i++ ?></td>

                            <td>
                                <div class="td-user">
                                    <div class="td-avatar"><?= $iniciales ?></div>
                                    <span><?= htmlspecialchars($row['nombres'] ?? '-') ?></span>
                                </div>
                            </td>

                            <td>
                                <span class="badge-grade">
                                    <?= htmlspecialchars($row['grado'] ?? '-') ?>
                                </span>
                            </td>

                            <td><?= htmlspecialchars($row['arma'] ?? '-') ?></td>

                            <td><?= htmlspecialchars($row['unidad'] ?? '-') ?></td>

                            <td><?= htmlspecialchars($row['dni'] ?? '-') ?></td>

                            <td>
                                <code><?= htmlspecialchars($row['cip'] ?? '-') ?></code>
                            </td>

                            <td>
                                <?= !empty($row['fecha_ingreso']) ? date('d/m/Y', strtotime($row['fecha_ingreso'])) : '-' ?>
                            </td>

                            <td>
                                <?= !empty($row['fecha_ascenso']) ? date('d/m/Y', strtotime($row['fecha_ascenso'])) : '-' ?>
                            </td>

                            <td>
                                <span class="badge">
                                    <?= htmlspecialchars($row['estado'] ?? '-') ?>
                                </span>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

<?php endif; ?>

<?php if ($totalCivil > 0): ?>

    <div class="card table-card" style="margin-top:20px;">

        <div class="table-header">

            <div>

                <h3>Personal Civil</h3>

                <span><?= $totalCivil ?> registros encontrados</span>

            </div>

        </div>

        <div class="table-wrap">

            <table class="consulta-table">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Personal</th>
                        <th>DNI</th>
                        <th>N/A</th>
                        <th>Grupo</th>
                        <th>GGUU</th>
                        <th>Núcleo</th>
                        <th>Grado Laboral</th>
                        <th>Unidad</th>
                        <th>Celular</th>
                        <th>Condición</th>
                    </tr>

                </thead>

                <tbody>

                    <?php $j = 1; ?>

                    <?php while ($row = pg_fetch_assoc($resultCivil)): ?>

                        <?php
                        $palabras = explode(' ', trim($row['nombres'] ?? ''));

                        $iniciales = strtoupper(
                            substr($palabras[0] ?? '', 0, 1) .
                                substr($palabras[1] ?? '', 0, 1)
                        );
                        ?>

                        <tr>

                            <td class="td-id"><?= $j++ ?></td>

                            <td>
                                <div class="td-user">
                                    <div class="td-avatar"><?= $iniciales ?></div>
                                    <span><?= htmlspecialchars($row['nombres'] ?? '-') ?></span>
                                </div>
                            </td>

                            <td><?= htmlspecialchars($row['dni'] ?? '-') ?></td>

                            <td>
                                <code><?= htmlspecialchars($row['na'] ?? '-') ?></code>
                            </td>

                            <td><?= htmlspecialchars($row['grupo'] ?? '-') ?></td>

                            <td><?= htmlspecialchars($row['gguu'] ?? '-') ?></td>

                            <td><?= htmlspecialchars($row['nucleo'] ?? '-') ?></td>

                            <td>
                                <span class="badge-grade">
                                    <?= htmlspecialchars($row['grado_laboral'] ?? '-') ?>
                                </span>
                            </td>

                            <td><?= htmlspecialchars($row['unidad'] ?? '-') ?></td>

                            <td><?= htmlspecialchars($row['celular'] ?? '-') ?></td>

                            <td>
                                <span class="badge">
                                    <?= htmlspecialchars($row['condicion'] ?? '-') ?>
                                </span>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

<?php endif; ?>

<?php include 'includes/layout_end.php'; ?>