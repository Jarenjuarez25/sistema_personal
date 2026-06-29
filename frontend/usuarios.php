<?php
session_start();

require_once(__DIR__ . "/../backend/config/conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$pageTitle  = "Gestión de Usuarios";
$activePage = "usuarios";
define('BASE_CSS',    'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="' . BASE_CSS . 'usuarios.css">';

$roles = pg_query($conn, "
    SELECT *
    FROM rol
    ORDER BY id_rol ASC
");

$usuarios = pg_query($conn, "
    SELECT 
        u.id_usuario,
        u.username,
        u.email,
        u.foto,
        r.nombre AS rol
    FROM usuario u
    INNER JOIN rol r
    ON u.id_rol = r.id_rol
    ORDER BY u.id_usuario DESC
");

include 'includes/layout.php';
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-title">
        <h1>Gestión de Usuarios</h1>
        <p>Solo el superusuario puede registrar nuevos usuarios del sistema</p>
    </div>
</div>

<?php if (isset($_SESSION['password_generada'])): ?>
    <div class="alert-msg success">
        <i class="fa fa-circle-check"></i>
        <div>
            <strong>Usuario creado correctamente.</strong><br>
            Usuario: <strong><?= htmlspecialchars($_SESSION['usuario_creado']) ?></strong> —
            Contraseña generada: <code><?= htmlspecialchars($_SESSION['password_generada']) ?></code>
        </div>
    </div>
    <?php
    unset($_SESSION['password_generada']);
    unset($_SESSION['usuario_creado']);
    ?>
<?php endif; ?>

<div class="usuarios-layout">

    <div class="usuarios-col-left">
        <div class="card config-card">
            <div class="config-card-header">
                <div class="cfg-icon" style="background:rgba(74,154,42,0.15);border-color:rgba(74,154,42,0.4)">
                    <i class="fa fa-user-plus" style="color:#4a9a2a"></i>
                </div>
                <div>
                    <h3>Registrar Nuevo Usuario</h3>
                    <p>La contraseña se genera automáticamente</p>
                </div>
            </div>

            <div class="config-card-body">
                <form action="../backend/controllers/guardarUsuario.php" method="POST" class="pwd-form">

                    <div class="form-group-cfg">
                        <label class="form-label-cfg">
                            <i class="fa fa-user"></i> Nombre de usuario
                        </label>
                        <div class="input-wrap">
                            <i class="fa fa-at"></i>
                            <input type="text" name="username" placeholder="Ej: usuario_admin" required autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group-cfg">
                        <label class="form-label-cfg">
                            <i class="fa fa-envelope"></i> Correo electrónico
                        </label>
                        <div class="input-wrap">
                            <i class="fa fa-envelope"></i>
                            <input type="email" name="email" placeholder="correo@ejemplo.com" required autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group-cfg">
                        <label class="form-label-cfg">
                            <i class="fa fa-id-badge"></i> Rol
                        </label>
                        <div class="input-wrap select-wrap">
                            <i class="fa fa-shield-halved"></i>
                            <select name="id_rol" required>
                                <option value="">Seleccione rol</option>
                                <?php while ($r = pg_fetch_assoc($roles)): ?>
                                    <option value="<?= $r['id_rol'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                                <?php endwhile; ?>
                            </select>
                            <i class="fa fa-chevron-down sel-arrow"></i>
                        </div>
                    </div>

                    <div class="info-tip">
                        <i class="fa fa-circle-info"></i>
                        La contraseña será generada automáticamente con 6 caracteres.
                    </div>

                    <button type="submit" class="btn-cfg-primary">
                        <i class="fa fa-user-plus"></i> Registrar Usuario
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="usuarios-col-right">
        <div class="card usuarios-table-card">
            <div class="config-card-header">
                <div class="cfg-icon" style="background:rgba(74,159,212,0.15);border-color:rgba(74,159,212,0.4)">
                    <i class="fa fa-users" style="color:#4a9fd4"></i>
                </div>
                <div>
                    <h3>Usuarios Registrados</h3>
                    <p><?= pg_num_rows($usuarios) ?> usuario<?= pg_num_rows($usuarios) != 1 ? 's' : '' ?> en el sistema</p>
                </div>
            </div>

            <div class="table-wrap">
                <?php if ($usuarios && pg_num_rows($usuarios) > 0): ?>
                    <table class="usuarios-table">
                        <thead>
                            <tr>
                                <th style="width:60px">#</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th style="width:100px; text-align:center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($u = pg_fetch_assoc($usuarios)): ?>
                                <?php
                                $iniciales = strtoupper(substr($u['username'], 0, 2));
                                $rolLower  = strtolower($u['rol']);
                                $rolClass  = match (true) {
                                    str_contains($rolLower, 'super')  => 'role-super',
                                    str_contains($rolLower, 'admin')  => 'role-admin',
                                    default                            => 'role-user'
                                };
                                ?>
                                <tr>
                                    <td class="td-id"><?= $u['id_usuario'] ?></td>
                                    <td>
                                        <div class="td-user">
                                            <div class="td-av"><?= $iniciales ?></div>
                                            <span><?= htmlspecialchars($u['username'] ?? '') ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                                    <td>
                                        <span class="role-badge <?= $rolClass ?>">
                                            <i class="fa fa-shield-halved"></i>
                                            <?= htmlspecialchars($u['rol'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td class="td-acciones">
                                        <a href="editar_usuario.php?id=<?= $u['id_usuario'] ?>" class="btn-action edit" title="Editar">
                                            <i class="fa fa-pen-to-square"></i>
                                        </a>
                                        <form action="../backend/controllers/eliminarUsuario.php" method="POST" style="display:inline;"
                                            onsubmit="return confirm('¿Seguro que desea eliminar a <?= htmlspecialchars($u['username']) ?>?')">

                                            <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">

                                            <button type="submit" class="btn-action delete" title="Eliminar">
                                                <i class="fa fa-trash"></i>
                                            </button>

                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fa fa-users-slash"></i>
                        <p>No hay usuarios registrados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php include 'includes/layout_end.php'; ?>