<?php
require_once(__DIR__ . "/../backend/config/conexion.php");
session_start();

$pageTitle  = "Registrar Personal";
$activePage = "personal";

define('BASE_CSS', 'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="' . BASE_CSS . 'registrar.css">';

$grados = pg_query($conn, "SELECT * FROM grado ORDER BY nombre");
$armas = pg_query($conn, "SELECT * FROM arma ORDER BY nombre");
$unidades = pg_query($conn, "SELECT * FROM unidad ORDER BY nombre");
$estados = pg_query($conn, "SELECT * FROM estado_personal ORDER BY nombre");

include 'includes/layout.php';
?>

<div class="page-header">
    <div class="page-header-title">
        <h1>Registrar Personal Militar</h1>
        <p>Registro del personal militar de la brigada</p>
    </div>
    <div class="ph-actions">
        <a href="personal.php" class="btn-outline">
            <i class="fa fa-arrow-left"></i>
            Volver
        </a>
    </div>
</div>


<div class="card reg-card">
    <div class="reg-card-header">
        <div class="reg-header-icon">
            <i class="fa fa-user-plus"></i>
        </div>
        <div>
            <h3>Nuevo Registro</h3>
            <p>Complete la información requerida</p>
        </div>
    </div>
    <form action="../backend/controllers/guardarPersonal.php" method="POST" class="reg-form">

        <div class="form-section">
            <div class="section-title">
                <i class="fa fa-users"></i>
                <span>Tipo de Personal</span>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Tipo *</label>
                    <div class="input-wrap select-wrap">
                        <i class="fa fa-users"></i>
                        <select name="tipo_personal" id="tipo_personal" required>
                            <option value="">Seleccione</option>
                            <option value="CIVIL">Civil</option>
                            <option value="TROPA">Tropa</option>
                            <option value="REE">REE</option>
                            <option value="SUB OFICIAL">Sub Oficial</option>
                            <option value="TEC">TEC</option>
                            <option value="OF">OF</option>
                        </select>
                        <i class="fa fa-chevron-down sel-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title">
                <i class="fa fa-id-card"></i>
                <span>Datos Generales</span>
            </div>
            <div class="form-grid">
                <div class="form-group span-2">
                    <label class="form-label">Apellidos y Nombres *</label>
                    <div class="input-wrap">
                        <i class="fa fa-user"></i>
                        <input type="text" id="nombres" name="nombres" required readonly autocomplete="off" oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group" id="na_box">
                    <label class="form-label">N/A *</label>
                    <div class="input-wrap">
                        <i class="fa fa-id-badge"></i>
                        <input type="text" name="na" id="na" maxlength="9" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">DNI *</label>
                    <div class="input-wrap">
                        <i class="fa fa-id-badge"></i>
                        <input type="text" name="dni" id="dni" maxlength="8" required autocomplete="off">
                        <button type="button" id="btnBuscarDni" class="btn-dni">Buscar</button>
                    </div>
                </div>
                <div class="form-group" id="grupo_box">
                    <label class="form-label">Grupo</label>
                    <div class="input-wrap">
                        <i class="fa fa-medal"></i>
                        <input type="text" name="grupo" oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Grado</label>
                    <div class="input-wrap">
                        <i class="fa fa-medal"></i>
                        <input type="text" name="grado_texto" placeholder="Ej: CRL / SGTO1 REE / SLDO SAA" required oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Unidad *</label>
                    <div class="input-wrap select-wrap">
                        <i class="fa fa-building"></i>
                        <select name="id_unidad" required>
                            <option value="">Seleccione</option>
                            <?php while ($u = pg_fetch_assoc($unidades)): ?>
                                <option value="<?= $u['id_unidad'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <i class="fa fa-chevron-down sel-arrow"></i>
                    </div>
                </div>
                <div class="form-group" id="gguu_box">
                    <label class="form-label">GGUU</label>
                    <div class="input-wrap">
                        <i class="fa fa-medal"></i>
                        <input type="text" name="gguu" oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group" id="nucleo_box">
                    <label class="form-label">Nucleo</label>
                    <div class="input-wrap">
                        <i class="fa fa-medal"></i>
                        <input type="text" name="nucleo" oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group" id="celular">
                    <label class="form-label">Celular</label>
                    <div class="input-wrap">
                        <i class="fa fa-phone"></i>
                        <input type="text" name="celular" maxlength="9" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="form-group half">
                <label class="form-label">Sexo *</label>
                <div class="input-wrap select-wrap">
                    <i class="fa fa-user"></i>
                    <select name="sexo">
                        <option value="">Seleccione</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                    <i class="fa fa-chevron-down sel-arrow"></i>
                </div>
            </div>

            <div class="form-group half">
                <label class="form-label">Estado *</label>
                <div class="input-wrap select-wrap">
                    <i class="fa fa-circle-dot"></i>
                    <select name="id_estado" required>
                        <option value="">Seleccione</option>
                        <?php while ($e = pg_fetch_assoc($estados)): ?>
                            <option value="<?= $e['id_estado'] ?>">
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <i class="fa fa-chevron-down sel-arrow"></i>
                </div>
            </div>
        </div>

        <!--ESTADO MAYOR INPUTS-->

        <div class="form-section" id="campos_estado_mayor">
            <div class="section-title">
                <i class="fa fa-shield-halved"></i>
                <span>Estado Mayor</span>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Arma</label>
                    <div class="input-wrap">
                        <i class="fa fa-medal"></i>
                        <input type="text" name="arma" oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section" id="campos_tropa">
            <div class="section-title">
                <i class="fa fa-person-rifle"></i>
                <span>Tropa / Reenganchado</span>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Fecha de Ingreso *</label>
                    <div class="input-wrap">
                        <i class="fa fa-calendar"></i>
                        <input type="date" name="fecha_ingreso" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha de Salida / Retiro</label>
                    <div class="input-wrap">
                        <i class="fa fa-calendar-xmark"></i>
                        <input type="date" name="fecha_salida" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha de Nacimiento *</label>
                    <div class="input-wrap">
                        <i class="fa fa-calendar"></i>
                        <input type="date" name="fecha_nacimiento" autocomplete="off">
                    </div>
                </div>
                <div class="form-group"  id="cip_box">
                    <label class="form-label">Cip</label>
                    <div class="input-wrap">
                        <i class="fa fa-money-check"></i>
                        <input type="text" name="cip" autocomplete="off">
                    </div>
                </div>
                <div class="form-group"  id="cuenta_box">
                    <label class="form-label">N° Cuenta</label>
                    <div class="input-wrap">
                        <i class="fa fa-money-check"></i>
                        <input type="text" name="nro_cuenta" autocomplete="off">
                    </div>
                </div>
                <div class="form-group"  id="departamento_box">
                    <label class="form-label">Departamento</label>
                    <div class="input-wrap">
                        <i class="fa fa-location-dot"></i>
                        <input type="text" name="departamento" autocomplete="off" id="departamento">
                    </div>
                </div>
                <div class="form-group" id="provincia_box">
                    <label class="form-label">Provincia</label>
                    <div class="input-wrap">
                        <i class="fa fa-map"></i>
                        <input type="text" name="provincia" autocomplete="off" id="provincia">
                    </div>
                </div>
                <div class="form-group"  id="distrito_box">
                    <label class="form-label">Distrito</label>
                    <div class="input-wrap">
                        <i class="fa fa-map-pin"></i>
                        <input type="text" name="distrito" autocomplete="off" id="distrito">
                    </div>
                </div>
                <div class="form-group"  id="direccion_box">
                    <label class="form-label">Dirección</label>
                    <div class="input-wrap">
                        <i class="fa fa-house"></i>
                        <input type="text" name="direccion" autocomplete="off" id="direccion">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title">
                <i class="fa fa-note-sticky"></i>
                <span>Observación</span>
            </div>
            <div class="form-grid">
                <div class="form-group span-2">
                    <textarea name="obs" rows="4" placeholder="Observaciones..." autocomplete="off"></textarea>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <a href="personal.php" class="btn-cancel">
                <i class="fa fa-xmark"></i>
                Cancelar
            </a>
            <button type="reset" class="btn-reset">
                <i class="fa fa-rotate-left"></i>
                Limpiar
            </button>
            <button type="submit" class="btn-save">
                <i class="fa fa-floppy-disk"></i>
                Guardar
            </button>
        </div>
    </form>
</div>

<script>
const tipoPersonal = document.getElementById('tipo_personal');

const estadoMayor = document.getElementById('campos_estado_mayor');
const tropa = document.getElementById('campos_tropa');

const gruponaBox = document.getElementById('na_box');
const grupoBox = document.getElementById('grupo_box');
const gguuBox = document.getElementById('gguu_box');
const nucleoBox = document.getElementById('nucleo_box');

const cipBox = document.getElementById('cip_box');
const cuentaBox = document.getElementById('cuenta_box');
const departamentoBox = document.getElementById('departamento_box');
const provinciaBox = document.getElementById('provincia_box');
const distritoBox = document.getElementById('distrito_box');
const direccionBox = document.getElementById('direccion_box');

estadoMayor.style.display = 'none';
tropa.style.display = 'none';

tipoPersonal.addEventListener('change', () => {

    const tipo = tipoPersonal.value;

    if (tipo === 'CIVIL') {

        estadoMayor.style.display = 'none';
        tropa.style.display = 'block';

        document.querySelector('#campos_tropa .section-title span').textContent = 'Datos Civil';

        gruponaBox.style.display = 'block';
        grupoBox.style.display = 'block';
        gguuBox.style.display = 'block';
        nucleoBox.style.display = 'block';

        cipBox.style.display = 'none';
        cuentaBox.style.display = 'none';
        departamentoBox.style.display = 'none';
        provinciaBox.style.display = 'none';
        distritoBox.style.display = 'none';
        direccionBox.style.display = 'none';

    } else if (
        tipo === 'TROPA' ||
        tipo === 'REE' ||
        tipo === 'SUB OFICIAL' ||
        tipo === 'TEC' ||
        tipo === 'OF'
    ) {

        estadoMayor.style.display = 'none';
        tropa.style.display = 'block';

        document.querySelector('#campos_tropa .section-title span').textContent = 'Tropa / Reenganchado';

        gruponaBox.style.display = 'none';
        grupoBox.style.display = 'none';
        gguuBox.style.display = 'none';
        nucleoBox.style.display = 'none';

        cipBox.style.display = 'block';
        cuentaBox.style.display = 'block';
        departamentoBox.style.display = 'block';
        provinciaBox.style.display = 'block';
        distritoBox.style.display = 'block';
        direccionBox.style.display = 'block';

    } else {

        estadoMayor.style.display = 'none';
        tropa.style.display = 'none';

        gruponaBox.style.display = 'block';
        grupoBox.style.display = 'block';
        gguuBox.style.display = 'block';
        nucleoBox.style.display = 'block';
    }

});
</script>

<?php include 'includes/layout_end.php'; ?>

<script>
    document.getElementById('btnBuscarDni').addEventListener('click', function() {
        const dni = document.getElementById('dni').value.trim();
        if (dni.length !== 8) {
            alert('Ingrese un DNI válido de 8 dígitos');
            return;
        }
        fetch('../backend/controllers/consultarDni.php?dni=' + dni)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('No se encontró información del DNI');
                    return;
                }
                if (data.nombre_completo) {
                    document.getElementById('nombres').value = data.nombre_completo.toUpperCase();
                }
            })
            .catch(error => {
                console.error(error);
                alert('Error al consultar el DNI');
            });
    });
</script>