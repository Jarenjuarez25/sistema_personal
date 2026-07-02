<?php
session_start();

require_once(__DIR__ . "/../backend/config/conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: usuarios.php");
    exit();
}

$pageTitle  = "Editar Usuario";
$activePage = "usuarios";

define('BASE_CSS', 'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="' . BASE_CSS . 'editarUsuario.css">';

$query = pg_query_params($conn, "
SELECT id_usuario, username, email, id_rol
FROM usuario
WHERE id_usuario = $1
", [$id]);

if (!$query) {
    die("Error en la query: " . pg_last_error($conn));
}

if (pg_num_rows($query) === 0) {
    die("Usuario no encontrado con ID: " . htmlspecialchars($id));
}

$usuarioData = pg_fetch_assoc($query);

// DEBUG: Verifica que $usuarioData tiene datos
if (!$usuarioData) {
    die("Error al obtener datos del usuario");
}
echo "<!-- DEBUG: Usuario = " . htmlspecialchars(json_encode($usuarioData)) . " -->";

$roles = pg_query($conn, "
SELECT id_rol, nombre
FROM rol
ORDER BY id_rol ASC
");

include 'includes/layout.php';
?>

<div class="page-header">
    <div class="page-header-title">
        <h1>Editar Usuario</h1>
        <p>Modifique los datos y rol del usuario seleccionado</p>
    </div>
</div>

<div class="edit-user-card">

    <div class="edit-user-header">
        <div class="edit-user-icon">
            <i class="fa fa-user-pen"></i>
        </div>
        <div>
            <h3>Datos del Usuario</h3>
            <p>Actualización de credenciales básicas</p>
        </div>
    </div>

    <form action="../backend/controllers/actualizarUsuario.php" method="POST" class="edit-user-form" style="display: block !important; height: auto !important;">

        <input type="hidden" name="id_usuario" value="<?= htmlspecialchars((string)$usuarioData['id_usuario']) ?>">

        <div class="edit-form-group">
            <label>Nombre de usuario</label>
            <input type="text" name="username" value="<?= htmlspecialchars((string)$usuarioData['username']) ?>" required>
        </div>

        <div class="edit-form-group">
            <label>Correo electrónico</label>
            <input type="email" name="email" value="<?= htmlspecialchars((string)$usuarioData['email']) ?>" required>
        </div>

        <div class="edit-form-group">
            <label>Rol</label>
            <select name="id_rol" required>
                <?php while ($r = pg_fetch_assoc($roles)): ?>
                    <option value="<?= $r['id_rol'] ?>" <?= ($usuarioData['id_rol'] == $r['id_rol']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="edit-actions">
            <a href="usuarios.php" class="btn-edit-cancel">
                <i class="fa fa-arrow-left"></i>
                Cancelar
            </a>

            <button type="submit" class="btn-edit-save">
                <i class="fa fa-floppy-disk"></i>
                Guardar Cambios
            </button>
        </div>

    </form>

</div>

<?php include 'includes/layout_end.php'; ?>