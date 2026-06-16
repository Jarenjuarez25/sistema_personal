<?php
require_once(__DIR__ . "/../backend/config/conexion.php");
session_start();
date_default_timezone_set('America/Lima');

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION["usuario"];
$rol     = $_SESSION["rol"] ?? "Administrador";

$pageTitle  = "Configuración";
$activePage = "configuracion";

define('BASE_CSS',    'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="' . BASE_CSS . 'configuracion.css">';

$query = "SELECT * FROM usuario WHERE username = $1";

$palabras  = explode('_', $usuario);
$iniciales = strtoupper(substr($palabras[0], 0, 1) . (isset($palabras[1]) ? substr($palabras[1], 0, 1) : substr($palabras[0], 1, 1)));

$result = pg_query_params($conn, $query, array($usuario));
$datosUsuario = pg_fetch_assoc($result);

include 'includes/layout.php';
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-title">
        <h1>Configuración</h1>
        <p>Administración del perfil y ajustes del sistema</p>
    </div>
</div>

<div class="config-layout">

    <div class="config-col-left">

        <!-- PERFIL -->
        <div class="card config-card">
            <div class="config-card-header">
                <div class="cfg-icon" style="background:rgba(74,154,42,0.15);border-color:rgba(74,154,42,0.4)">
                    <i class="fa fa-user" style="color:#4a9a2a"></i>
                </div>
                <div>
                    <h3>Perfil de Usuario</h3>
                    <p>Información de tu cuenta</p>
                </div>
            </div>

            <div class="config-card-body">
                <div class="profile-avatar-wrap">
                    <div class="profile-avatar">
                        <?php if (!empty($datosUsuario['foto'])): ?>
                            <img src="uploads/<?= $datosUsuario['foto'] ?>" alt="Perfil" class="avatar-img">
                        <?php else: ?>
                            <span class="avatar-placeholder"><?= $iniciales ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="profile-avatar-info">
                        <span class="profile-name"><?= htmlspecialchars($usuario) ?></span>
                        <span class="profile-role-badge">
                            <i class="fa fa-shield-halved"></i>
                            <?= htmlspecialchars($rol) ?>
                        </span>
                    </div>
                </div>

                <div class="info-list">
                    <div class="info-row">
                        <span class="info-label"><i class="fa fa-user"></i> Usuario</span>
                        <span class="info-value"><?= htmlspecialchars($usuario) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fa fa-id-badge"></i> Rol</span>
                        <span class="info-value"><?= htmlspecialchars($rol) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fa fa-calendar"></i> Fecha</span>
                        <span class="info-value"><?= date('d/m/Y') ?></span>
                    </div>
                </div>

                <a href="editarPerfil.php" class="btn-cfg-primary">
                    <i class="fa fa-user-pen"></i> Editar Perfil
                </a>
            </div>
        </div>

        <div class="card config-card">
            <div class="config-card-header">
                <div class="cfg-icon" style="background:rgba(74,159,212,0.15);border-color:rgba(74,159,212,0.4)">
                    <i class="fa fa-server" style="color:#4a9fd4"></i>
                </div>
                <div>
                    <h3>Información del Sistema</h3>
                    <p>Estado y detalles técnicos</p>
                </div>
            </div>

            <div class="config-card-body">
                <div class="info-list">
                    <div class="info-row">
                        <span class="info-label"><i class="fa fa-circle-info"></i> Sistema</span>
                        <span class="info-value">S.I. Personal</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fa fa-building"></i> Institución</span>
                        <span class="info-value">1ra Brigada de Servicios</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fa fa-tag"></i> Versión</span>
                        <span class="info-value">
                            <span class="version-badge">v1.0</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fa fa-database"></i> Base de Datos</span>
                        <span class="info-value">
                            <?php if ($conn): ?>
                                <span class="status-badge online">
                                    <i class="fa fa-circle"></i> PostgreSQL Conectado
                                </span>
                            <?php else: ?>
                                <span class="status-badge offline">
                                    <i class="fa fa-circle"></i> Sin conexión
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fa fa-calendar-days"></i> Fecha actual</span>
                        <span class="info-value"><?= date('d/m/Y H:i') ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="config-col-right">
        <div class="card config-card">
            <div class="config-card-header">
                <div class="cfg-icon" style="background:rgba(212,169,74,0.15);border-color:rgba(212,169,74,0.4)">
                    <i class="fa fa-lock" style="color:#d4a94a"></i>
                </div>
                <div>
                    <h3>Seguridad</h3>
                    <p>Cambia tu contraseña de acceso</p>
                </div>
            </div>

            <div class="config-card-body">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert-msg <?= $_GET['msg'] === 'ok' ? 'success' : 'error' ?>">
                        <i class="fa fa-<?= $_GET['msg'] === 'ok' ? 'circle-check' : 'circle-exclamation' ?>"></i>
                        <?= $_GET['msg'] === 'ok' ? 'Contraseña actualizada correctamente' : 'Error al actualizar la contraseña' ?>
                    </div>
                <?php endif; ?>

                <form action="../backend/controllers/cambiarPassword.php" method="POST">

                    <div class="form-group-cfg">
                        <label class="form-label-cfg">
                            <i class="fa fa-key"></i> Contraseña Actual
                        </label>
                        <div class="pwd-input-wrap">
                            <input type="password" name="actual" id="pwd_actual"
                                placeholder="Ingrese su contraseña actual" required>
                            <button type="button" class="pwd-toggle" data-target="pwd_actual">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group-cfg">
                        <label class="form-label-cfg">
                            <i class="fa fa-lock"></i> Nueva Contraseña
                        </label>
                        <div class="pwd-input-wrap">
                            <input type="password" name="nueva_password" id="pwd_nueva"
                                placeholder="Ingrese la nueva contraseña" required>
                            <button type="button" class="pwd-toggle" data-target="pwd_nueva">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </div>
                        <!-- Barra de fuerza -->
                        <div class="pwd-strength-wrap">
                            <div class="pwd-strength-bar" id="strengthBar"></div>
                        </div>
                        <span class="pwd-strength-label" id="strengthLabel"></span>
                    </div>

                    <div class="form-group-cfg">
                        <label class="form-label-cfg">
                            <i class="fa fa-lock"></i> Confirmar Nueva Contraseña
                        </label>
                        <div class="pwd-input-wrap">
                            <input type="password" name="confirmar" id="pwd_confirmar"
                                placeholder="Repita la nueva contraseña" required>
                            <button type="button" class="pwd-toggle" data-target="pwd_confirmar">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </div>
                        <span class="match-label" id="matchLabel"></span>
                    </div>

                    <button type="submit" class="btn-cfg-primary btn-yellow">
                        <i class="fa fa-key"></i> Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>

        <div class="card config-card">
            <div class="config-card-header">
                <div class="cfg-icon">
                    <i class="fa fa-bolt"></i>
                </div>
                <div>
                    <h3>Accesos Rápidos</h3>
                    <p>Navegación directa a módulos</p>
                </div>
            </div>
            <div class="config-card-body">
                <div class="quick-links">
                    <a href="dashboard.php" class="quick-link">
                        <i class="fa fa-chart-pie"></i>
                        <span>Dashboard</span>
                        <i class="fa fa-arrow-right ql-arrow"></i>
                    </a>
                    <a href="personal.php" class="quick-link">
                        <i class="fa fa-users"></i>
                        <span>Lista de Personal</span>
                        <i class="fa fa-arrow-right ql-arrow"></i>
                    </a>
                    <a href="consultas.php" class="quick-link">
                        <i class="fa fa-magnifying-glass"></i>
                        <span>Consultas</span>
                        <i class="fa fa-arrow-right ql-arrow"></i>
                    </a>
                    <a href="../backend/auth/logout.php" class="quick-link logout-link">
                        <i class="fa fa-arrow-right-from-bracket"></i>
                        <span>Cerrar Sesión</span>
                        <i class="fa fa-arrow-right ql-arrow"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/layout_end.php'; ?>

<script>
    document.querySelectorAll('.pwd-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.querySelector('i').className = isHidden ? 'fa fa-eye' : 'fa fa-eye-slash';
        });
    });

    const pwdNueva = document.getElementById('pwd_nueva');
    const bar = document.getElementById('strengthBar');
    const labelStr = document.getElementById('strengthLabel');
    const pwdConfirm = document.getElementById('pwd_confirmar');
    const matchLabel = document.getElementById('matchLabel');

    pwdNueva.addEventListener('input', () => {
        const val = pwdNueva.value;
        let score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [{
                w: '20%',
                color: '#e74c3c',
                label: 'Muy débil'
            },
            {
                w: '40%',
                color: '#e67e22',
                label: 'Débil'
            },
            {
                w: '60%',
                color: '#f1c40f',
                label: 'Regular'
            },
            {
                w: '80%',
                color: '#2ecc71',
                label: 'Fuerte'
            },
            {
                w: '100%',
                color: '#27ae60',
                label: 'Muy fuerte'
            },
        ];
        const lv = levels[Math.min(score, 4)];
        bar.style.width = lv.w;
        bar.style.background = lv.color;
        labelStr.textContent = val ? lv.label : '';
        labelStr.style.color = lv.color;
    });

    pwdConfirm.addEventListener('input', () => {
        if (!pwdConfirm.value) {
            matchLabel.textContent = '';
            return;
        }
        const match = pwdNueva.value === pwdConfirm.value;
        matchLabel.textContent = match ? '✓ Las contraseñas coinciden' : '✗ Las contraseñas no coinciden';
        matchLabel.style.color = match ? '#3a8a1a' : '#e74c3c';
    });
</script>