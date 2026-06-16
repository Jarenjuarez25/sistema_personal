<?php

require_once(__DIR__ . "/../backend/config/conexion.php");

session_start();

$pageTitle  = "Lista de Personal Militar";
$activePage = "personal";

define('BASE_CSS', 'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="' . BASE_CSS . 'personal.css">';

$queryMilitar = "
SELECT
    p.id_personal,
    p.nombres,
    p.dni,
    p.tipo_personal,
    p.sexo,
    g.nombre AS grado,
    u.nombre AS unidad,
    ep.nombre AS estado
FROM personal_militar p
LEFT JOIN grado g ON p.id_grado = g.id_grado
LEFT JOIN unidad u ON p.id_unidad = u.id_unidad
LEFT JOIN estado_personal ep ON p.id_estado = ep.id_estado
ORDER BY p.id_personal DESC
";

$resultMilitar = pg_query($conn, $queryMilitar);

if (!$resultMilitar) {
    die('Error SQL Militar: ' . pg_last_error($conn));
}

$queryCivil = "
SELECT
    pc.id_civil,
    pc.nombres,
    pc.dni,
    pc.grado_laboral,
    pc.grupo,
    pc.nucleo,
    pc.gguu,
    pc.condicion,
    pc.sexo,
    u.nombre AS unidad
FROM personal_civil pc
LEFT JOIN unidad u ON pc.id_unidad = u.id_unidad
ORDER BY pc.id_civil DESC
";

$resultCivil = pg_query($conn, $queryCivil);

if (!$resultCivil) {
    die('Error SQL Civil: ' . pg_last_error($conn));
}

$totalRegistros = pg_num_rows($resultMilitar) + pg_num_rows($resultCivil);

include 'includes/layout.php';
?>

<div class="page-header">

    <div class="page-header-title">

        <h1>Lista de Personal</h1>

        <p>
            Registro completo del personal —
            <?= date('d/m/Y') ?>
        </p>

    </div>

    <div class="ph-actions">

        <a href="registrar_personal.php" class="btn-primary">

            <i class="fa fa-user-plus"></i>
            Nuevo Personal

        </a>

    </div>

</div>

<div class="card personal-card">

    <div class="personal-toolbar">

        <div class="search-wrap">

            <i class="fa fa-magnifying-glass"></i>

            <input
                type="text"
                id="searchInput"
                placeholder="Buscar personal...">

        </div>

        <div class="toolbar-right">

            <span class="total-badge" id="totalBadge">
                <?= $totalRegistros ?> registros
            </span>

        </div>

    </div>

    <h3 class="table-title">
        <i class="fa fa-person-rifle"></i>
        Personal Militar
    </h3>

    <div class="table-wrap">

        <table class="personal-table" id="personalTable">

            <thead>

                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Grado</th>
                    <th>DNI</th>
                    <th>Unidad</th>
                    <th>Sexo</th>
                    <th>Estado</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

                <?php
                $i = 1;

                while ($row = pg_fetch_assoc($resultMilitar)):

                    $palabras = explode(' ', trim($row['nombres'] ?? ''));

                    $iniciales = strtoupper(
                        substr($palabras[0] ?? '', 0, 1) .
                            (isset($palabras[1]) ? substr($palabras[1], 0, 1) : '')
                    );

                    $estadoLower = strtolower($row['estado'] ?? '');

                    $badgeClass = match (true) {
                        str_contains($estadoLower, 'actividad') => 'active',
                        str_contains($estadoLower, 'licencia')  => 'license',
                        str_contains($estadoLower, 'vacacion')  => 'vacations',
                        str_contains($estadoLower, 'retiro')    => 'retired',
                        str_contains($estadoLower, 'permiso')   => 'permit',
                        str_contains($estadoLower, 'cambiado')  => 'changed',
                        default => 'active'
                    };
                ?>

                    <tr>

                        <td><?= $i++ ?></td>

                        <td>
                            <div class="td-user">
                                <div class="td-av">
                                    <?= $iniciales ?>
                                </div>

                                <span>
                                    <?= htmlspecialchars($row['nombres'] ?? '') ?>
                                </span>
                            </div>
                        </td>

                        <td><?= htmlspecialchars($row['grado'] ?? '-') ?></td>

                        <td><?= htmlspecialchars($row['dni'] ?? '-') ?></td>

                        <td><?= htmlspecialchars($row['unidad'] ?? '-') ?></td>

                        <td><?= htmlspecialchars($row['sexo'] ?? '-') ?></td>

                        <td>
                            <span class="badge <?= $badgeClass ?>">
                                <?= htmlspecialchars($row['estado'] ?? '-') ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($row['tipo_personal'] ?? '-') ?></td>

                        <td class="td-acciones">

                            <a
                                href="editar_personal.php?id=<?= $row['id_personal'] ?>"
                                class="btn-action edit">

                                <i class="fa fa-pen-to-square"></i>

                            </a>

                            <a
                                href="../backend/controllers/eliminarPersonal.php?id=<?= $row['id_personal'] ?>"
                                class="btn-action delete"
                                onclick="return confirm('¿Eliminar registro militar?')">

                                <i class="fa fa-trash"></i>

                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

    <h3 class="table-title civil-title">
        <i class="fa fa-users"></i>
        Personal Civil
    </h3>

    <div class="table-wrap">

        <table class="personal-table" id="civilTable">

            <thead>

                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Grado Laboral</th>
                    <th>DNI</th>
                    <th>Unidad</th>
                    <th>Grupo</th>
                    <th>Condición</th>
                    <th>Sexo</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

                <?php
                $j = 1;

                while ($row = pg_fetch_assoc($resultCivil)):

                    $palabras = explode(' ', trim($row['nombres'] ?? ''));

                    $iniciales = strtoupper(
                        substr($palabras[0] ?? '', 0, 1) .
                            (isset($palabras[1]) ? substr($palabras[1], 0, 1) : '')
                    );
                ?>

                    <tr>

                        <td><?= $j++ ?></td>

                        <td>
                            <div class="td-user">
                                <div class="td-av">
                                    <?= $iniciales ?>
                                </div>

                                <span>
                                    <?= htmlspecialchars($row['nombres'] ?? '') ?>
                                </span>
                            </div>
                        </td>

                        <td><?= htmlspecialchars($row['grado_laboral'] ?? '-') ?></td>

                        <td><?= htmlspecialchars($row['dni'] ?? '-') ?></td>

                        <td><?= htmlspecialchars($row['unidad'] ?? '-') ?></td>

                        <td><?= htmlspecialchars($row['grupo'] ?? '-') ?></td>

                        <td>
                            <span class="badge active">
                                <?= htmlspecialchars($row['condicion'] ?? '-') ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($row['sexo'] ?? '-') ?></td>

                        <td class="td-acciones">

                            <a
                                href="editarPersonalCivil.php?id=<?= $row['id_civil'] ?>"
                                class="btn-action edit">

                                <i class="fa fa-pen-to-square"></i>

                            </a>

                            <a
                                href="../backend/controllers/eliminarCivil.php?id=<?= $row['id_civil'] ?>"
                                class="btn-action delete"
                                onclick="return confirm('¿Eliminar registro civil?')">

                                <i class="fa fa-trash"></i>

                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {

        const value = this.value.toLowerCase();

        const rowsMilitar = document.querySelectorAll('#personalTable tbody tr');
        const rowsCivil = document.querySelectorAll('#civilTable tbody tr');

        rowsMilitar.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });

        rowsCivil.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });

    });
</script>

<?php include 'includes/layout_end.php'; ?>