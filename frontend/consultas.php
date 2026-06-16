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
$where   = [];
$params  = [];
$contador = 1;
if (!empty($_GET['fecha_ingreso'])) {
    $where[]  = "p.fecha_ingreso = $" . $contador;
    $params[] = $_GET['fecha_ingreso'];
    $contador++;
}
if (!empty($_GET['fecha_ascenso'])) {
    $where[]  = "p.fecha_ascenso = $" . $contador;
    $params[] = $_GET['fecha_ascenso'];
    $contador++;
}
if (!empty($_GET['sexo'])) {
    $where[]  = "p.sexo = $" . $contador;
    $params[] = $_GET['sexo'];
    $contador++;
}
if (!empty($_GET['grado'])) {
    $where[]  = "p.id_grado = $" . $contador;
    $params[] = $_GET['grado'];
    $contador++;
}
if (!empty($_GET['unidad'])) {
    $where[]  = "p.id_unidad = $" . $contador;
    $params[] = $_GET['unidad'];
    $contador++;
}
if (!empty($_GET['estado'])) {
    $where[]  = "p.id_estado = $" . $contador;
    $params[] = $_GET['estado'];
    $contador++;
}
if (!empty($_GET['buscar'])) {
    $where[]  = "(p.nombres ILIKE $" . $contador . " OR p.dni ILIKE $" . $contador . " OR p.cip ILIKE $" . $contador . ")";
    $params[] = '%' . $_GET['buscar'] . '%';
    $contador++;
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
    p.id_arma AS arma,
    p.sexo,
    g.nombre  AS grado,
    u.nombre  AS unidad,
    ep.nombre AS estado,
    a.nombre AS arma
FROM personal_militar p
LEFT JOIN arma a ON p.id_arma = a.id_arma
LEFT JOIN grado g ON p.id_grado = g.id_grado
LEFT JOIN unidad u ON p.id_unidad = u.id_unidad
LEFT JOIN estado_personal ep ON p.id_estado = ep.id_estado
";
if (count($where) > 0) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " ORDER BY p.nombres ASC";
$result = pg_query_params($conn, $query, $params);
$total  = pg_num_rows($result);
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
        <div class="form-grid">
            <div class="form-group span-3">
                <label>Buscar por Nombre / DNI / CIP</label>
                <input type="text" name="buscar" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" placeholder="Escriba nombre, DNI o CIP..." autocomplete="off" style="height: 48px; width: 100%; border-radius: 12px; border: 1px solid rgba(0,0,0,0.10); padding: 0 14px; background: #fdfdfd; font-size: 14px; color: #1e2c1e; box-sizing: border-box;">
            </div>
            <div class="form-group">
                <label>Grado</label>
                <select name="grado">
                    <option value="">Todos los grados</option>
                    <?php while ($g = pg_fetch_assoc($grados)): ?>
                        <option value="<?= $g['id_grado'] ?>" <?= (($_GET['grado'] ?? '') == $g['id_grado']) ? 'selected' : '' ?>><?= htmlspecialchars($g['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Unidad</label>
                <select name="unidad">
                    <option value="">Todas las unidades</option>
                    <?php while ($u = pg_fetch_assoc($unidades)): ?>
                        <option value="<?= $u['id_unidad'] ?>" <?= (($_GET['unidad'] ?? '') == $u['id_unidad']) ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <option value="">Todos los estados</option>
                    <?php while ($e = pg_fetch_assoc($estados)): ?>
                        <option value="<?= $e['id_estado'] ?>" <?= (($_GET['estado'] ?? '') == $e['id_estado']) ? 'selected' : '' ?>><?= htmlspecialchars($e['nombre']) ?></option>
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
                <label>Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso" value="<?= htmlspecialchars($_GET['fecha_ingreso'] ?? '') ?>" style="height: 48px; width: 100%; border-radius: 12px; border: 1px solid rgba(0,0,0,0.10); padding: 0 14px; background: #fdfdfd; font-size: 14px; color: #1e2c1e; box-sizing: border-box;">
            </div>
            <div class="form-group">
                <label>Fecha de Ascenso</label>
                <input type="date" name="fecha_ascenso" value="<?= htmlspecialchars($_GET['fecha_ascenso'] ?? '') ?>" style="height: 48px; width: 100%; border-radius: 12px; border: 1px solid rgba(0,0,0,0.10); padding: 0 14px; background: #fdfdfd; font-size: 14px; color: #1e2c1e; box-sizing: border-box;">
            </div>
        </div>
        <div class="form-actions">
            <a href="consultas.php" class="btn-clear"><i class="fa fa-rotate-left"></i> Limpiar</a>
            <button type="submit" class="btn-search"><i class="fa fa-magnifying-glass"></i> Buscar</button>
        </div>
    </form>
</div>
<div class="card table-card">
    <div class="table-header">
        <div>
            <h3>Resultados Encontrados</h3>
            <span><?= $total ?> registros encontrados</span>
        </div>
    </div>
    <div class="table-wrap">
        <?php if ($total > 0): ?>
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
                    <?php $i = 1;
                    while ($row = pg_fetch_assoc($result)): ?>
                        <?php $palabras = explode(' ', trim($row['nombres'] ?? ''));
                        $iniciales = strtoupper(substr($palabras[0] ?? '', 0, 1) . substr($palabras[1] ?? '', 0, 1)); ?>
                        <tr>
                            <td class="td-id"><?= $i++ ?></td>
                            <td>
                                <div class="td-user">
                                    <div class="td-avatar"><?= $iniciales ?></div>
                                    <span><?= htmlspecialchars($row['nombres'] ?? '') ?></span>
                                </div>
                            </td>
                            <td><span class="badge-grade"><?= htmlspecialchars($row['grado'] ?? '-') ?></span></td>
                            <td><?= htmlspecialchars($row['arma'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['unidad'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['dni'] ?? '-') ?></td>
                            <td><code><?= htmlspecialchars($row['cip'] ?? '-') ?></code></td>
                            <td>
                                <?= (!empty($row['fecha_ingreso']) && $row['fecha_ingreso'] !== '0000-00-00') ? date('d/m/Y', strtotime($row['fecha_ingreso'])) : '-' ?>
                            </td>
                            <td>
                                <?= (!empty($row['fecha_ascenso']) && $row['fecha_ascenso'] !== '0000-00-00') ? date('d/m/Y', strtotime($row['fecha_ascenso'])) : '-' ?>
                            </td>
                            <td><span class="badge"><?= htmlspecialchars($row['estado'] ?? '-') ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa fa-users-slash"></i>
                <h3>No se encontraron registros</h3>
                <p>No existen resultados con los filtros seleccionados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/layout_end.php'; ?>