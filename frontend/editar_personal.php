<?php

require_once(__DIR__ . "/../backend/config/conexion.php");

session_start();

$pageTitle  = "Editar Personal Militar";
$activePage = "personal";

define('BASE_CSS', 'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="css/editarPersonal.css">';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID no válido");
}

$query = "SELECT * FROM personal_militar WHERE id_personal = $1";
$result = pg_query_params($conn, $query, [$id]);
$personal = pg_fetch_assoc($result);

if (!$personal) {
    die("Personal no encontrado");
}

$grados   = pg_query($conn, "SELECT * FROM grado ORDER BY nombre ASC");
$armas    = pg_query($conn, "SELECT * FROM arma ORDER BY nombre ASC");
$unidades = pg_query($conn, "SELECT * FROM unidad ORDER BY nombre ASC");
$estados  = pg_query($conn, "SELECT * FROM estado_personal ORDER BY nombre ASC");

include 'includes/layout.php';

?>

<div class="edit-container">

    <div class="reg-card">

        <div class="edit-header">
            <h1>Editar Personal Militar</h1>
            <p>
                Modificando registro de:
                <strong><?= htmlspecialchars($personal['nombres'] ?? '') ?></strong>
            </p>
        </div>

        <form action="../backend/controllers/editarPersonal.php" method="POST" class="reg-form">

            <input type="hidden" name="id_personal" value="<?= htmlspecialchars($personal['id_personal'] ?? '') ?>">

            <div class="form-group span-2">
                <label>Apellidos y Nombres</label>
                <input
                    type="text"
                    name="nombres"
                    value="<?= htmlspecialchars($personal['nombres'] ?? '') ?>"
                    required
                    oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="form-group">
                <label>DNI</label>
                <input
                    type="text"
                    name="dni"
                    maxlength="8"
                    value="<?= htmlspecialchars($personal['dni'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>CIP</label>
                <input
                    type="text"
                    name="cip"
                    maxlength="9"
                    value="<?= htmlspecialchars($personal['cip'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Sexo</label>
                <select name="sexo">
                    <option value="">Seleccione</option>
                    <option value="M" <?= (($personal['sexo'] ?? '') == 'M') ? 'selected' : '' ?>>
                        Masculino
                    </option>
                    <option value="F" <?= (($personal['sexo'] ?? '') == 'F') ? 'selected' : '' ?>>
                        Femenino
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Tipo Personal</label>
                <select name="tipo_personal" required>
                    <?php
                    $tipos = ['TROPA', 'REE', 'SUB OFICIAL', 'TEC', 'OF'];
                    foreach ($tipos as $tipo):
                    ?>
                        <option
                            value="<?= $tipo ?>"
                            <?= (($personal['tipo_personal'] ?? '') == $tipo) ? 'selected' : '' ?>>
                            <?= $tipo ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Grado</label>
                <select name="id_grado">
                    <option value="">Seleccione</option>
                    <?php while ($g = pg_fetch_assoc($grados)): ?>
                        <option
                            value="<?= $g['id_grado'] ?>"
                            <?= (($g['id_grado'] == ($personal['id_grado'] ?? 0)) ? 'selected' : '') ?>>
                            <?= htmlspecialchars($g['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Arma</label>
                <select name="id_arma">
                    <option value="">Seleccione</option>
                    <?php while ($a = pg_fetch_assoc($armas)): ?>
                        <option
                            value="<?= $a['id_arma'] ?>"
                            <?= (($a['id_arma'] == ($personal['id_arma'] ?? 0)) ? 'selected' : '') ?>>
                            <?= htmlspecialchars($a['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Unidad</label>
                <select name="id_unidad">
                    <option value="">Seleccione</option>
                    <?php while ($u = pg_fetch_assoc($unidades)): ?>
                        <option
                            value="<?= $u['id_unidad'] ?>"
                            <?= (($u['id_unidad'] == ($personal['id_unidad'] ?? 0)) ? 'selected' : '') ?>>
                            <?= htmlspecialchars($u['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select name="id_estado">
                    <option value="">Seleccione</option>
                    <?php while ($e = pg_fetch_assoc($estados)): ?>
                        <option
                            value="<?= $e['id_estado'] ?>"
                            <?= (($e['id_estado'] == ($personal['id_estado'] ?? 0)) ? 'selected' : '') ?>>
                            <?= htmlspecialchars($e['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha Nacimiento</label>
                <input
                    type="date"
                    name="fecha_nacimiento"
                    value="<?= htmlspecialchars($personal['fecha_nacimiento'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Fecha Ingreso</label>
                <input
                    type="date"
                    name="fecha_ingreso"
                    value="<?= htmlspecialchars($personal['fecha_ingreso'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Fecha Salida / Retiro</label>
                <input
                    type="date"
                    name="fecha_salida"
                    value="<?= htmlspecialchars($personal['fecha_salida'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Fecha Ascenso</label>
                <input
                    type="date"
                    name="fecha_ascenso"
                    value="<?= htmlspecialchars($personal['fecha_ascenso'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Celular</label>
                <input
                    type="text"
                    name="celular"
                    maxlength="9"
                    value="<?= htmlspecialchars($personal['celular'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>N° Cuenta</label>
                <input
                    type="text"
                    name="nro_cuenta"
                    value="<?= htmlspecialchars($personal['nro_cuenta'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Departamento</label>
                <input
                    type="text"
                    name="departamento"
                    value="<?= htmlspecialchars($personal['departamento'] ?? '') ?>"
                    oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="form-group">
                <label>Provincia</label>
                <input
                    type="text"
                    name="provincia"
                    value="<?= htmlspecialchars($personal['provincia'] ?? '') ?>"
                    oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="form-group">
                <label>Distrito</label>
                <input
                    type="text"
                    name="distrito"
                    value="<?= htmlspecialchars($personal['distrito'] ?? '') ?>"
                    oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="form-group span-2">
                <label>Dirección</label>
                <textarea name="direccion"><?= htmlspecialchars($personal['direccion'] ?? '') ?></textarea>
            </div>

            <div class="form-group span-2">
                <label>Observación</label>
                <textarea name="obs"><?= htmlspecialchars($personal['obs'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <a href="personal.php" class="btn-cancel">Cancelar</a>

                <button type="submit" class="btn-save">
                    Actualizar Personal
                </button>
            </div>

        </form>

    </div>

</div>

<?php include 'includes/layout_end.php'; ?>