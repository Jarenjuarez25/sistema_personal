<?php

require_once(__DIR__ . "/../backend/config/conexion.php");

session_start();

$pageTitle = "Editar Personal Civil";
$activePage = "personal";

define('BASE_CSS', 'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="css/editarPersonal.css">';

$id = $_GET['id'] ?? null;

$query = "
SELECT *
FROM personal_civil
WHERE id_civil = $1
";

$result = pg_query_params($conn, $query, [$id]);
$personal = pg_fetch_assoc($result);

if (!$personal) {
    die("Personal civil no encontrado");
}

$unidades = pg_query($conn, "
SELECT *
FROM unidad
ORDER BY nombre ASC
");

include 'includes/layout.php';
?>

<div class="edit-container">

    <div class="reg-card">

        <div class="edit-header">
            <h1>Editar Personal Civil</h1>

            <p>
                Modificando:
                <strong>
                    <?= htmlspecialchars($personal['nombres']) ?>
                </strong>
            </p>
        </div>

        <form
            action="../backend/controllers/editarPersonalCivil.php"
            method="POST"
            class="reg-form">

            <input
                type="hidden"
                name="id_civil"
                value="<?= $personal['id_civil'] ?>">

            <div class="form-group span-2">
                <label>Apellidos y Nombres</label>

                <input
                    type="text"
                    name="nombres"
                    required
                    value="<?= htmlspecialchars($personal['nombres']) ?>">
            </div>

            <div class="form-group">
                <label>DNI</label>

                <input
                    type="text"
                    name="dni"
                    value="<?= htmlspecialchars($personal['dni']) ?>">
            </div>

            <div class="form-group">
                <label>N/A</label>

                <input
                    type="text"
                    name="na"
                    value="<?= htmlspecialchars($personal['na'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Grupo</label>

                <input
                    type="text"
                    name="grupo"
                    value="<?= htmlspecialchars($personal['grupo'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>GGUU</label>

                <input
                    type="text"
                    name="gguu"
                    value="<?= htmlspecialchars($personal['gguu'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Núcleo</label>

                <input
                    type="text"
                    name="nucleo"
                    value="<?= htmlspecialchars($personal['nucleo'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Sexo</label>

                <select name="sexo">
                    <option value="">Seleccione</option>

                    <option value="M"
                        <?= (($personal['sexo'] ?? '') == 'M') ? 'selected' : '' ?>>
                        Masculino
                    </option>

                    <option value="F"
                        <?= (($personal['sexo'] ?? '') == 'F') ? 'selected' : '' ?>>
                        Femenino
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Grado Laboral</label>

                <input
                    type="text"
                    name="grado_laboral"
                    value="<?= htmlspecialchars($personal['grado_laboral'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Unidad</label>

                <select name="id_unidad">

                    <?php while($u = pg_fetch_assoc($unidades)): ?>

                        <option
                            value="<?= $u['id_unidad'] ?>"
                            <?= (($u['id_unidad'] == ($personal['id_unidad'] ?? 0)) ? 'selected' : '') ?>>

                            <?= htmlspecialchars($u['nombre']) ?>

                        </option>

                    <?php endwhile; ?>

                </select>
            </div>

            <div class="form-group">
                <label>Fecha Nacimiento</label>

                <input
                    type="date"
                    name="fecha_nacimiento"
                    value="<?= $personal['fecha_nacimiento'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label>Fecha Ingreso</label>

                <input
                    type="date"
                    name="fecha_ingreso"
                    value="<?= $personal['fecha_ingreso'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label>Fecha Salida</label>

                <input
                    type="date"
                    name="fecha_salida"
                    value="<?= $personal['fecha_salida'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label>Celular</label>

                <input
                    type="text"
                    name="celular"
                    value="<?= htmlspecialchars($personal['celular'] ?? '') ?>">
            </div>

            <div class="form-group span-2">
                <label>Condición</label>

                <textarea name="condicion"><?= htmlspecialchars($personal['condicion'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">

                <a href="personal.php" class="btn-cancel">
                    Cancelar
                </a>

                <button type="submit" class="btn-save">
                    Actualizar Personal Civil
                </button>

            </div>

        </form>

    </div>

</div>

<?php include 'includes/layout_end.php'; ?>