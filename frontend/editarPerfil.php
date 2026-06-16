<?php
session_start();
require_once(__DIR__ . "/../backend/config/conexion.php");

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$pageTitle  = "Editar Perfil";
$activePage = "configuracion";

define('BASE_CSS',    'css/');
define('BASE_ASSETS', '../../assets/');

$extraCSS = '<link rel="stylesheet" href="' . BASE_CSS . 'editarPerfil.css">';


$usuarioSesion = $_SESSION["usuario"];

$query = "SELECT * FROM usuario WHERE username = $1";
$result = pg_query_params($conn, $query, array($usuarioSesion));


$datosUsuario = pg_fetch_assoc($result);

include 'includes/layout.php';
?>

<div class="perfil-container">

    <div class="perfil-card">

        <div class="perfil-header">
            <div class="perfil-avatar">

                <?php if (!empty($datosUsuario['foto'])) { ?>

                    <img
                        src="uploads/<?= $datosUsuario['foto'] ?>"
                        alt="Foto Perfil"
                        class="avatar-img">

                <?php } else { ?>

                    <span>
                        <?= strtoupper(substr($datosUsuario['username'], 0, 1)) ?>
                    </span>

                <?php } ?>

            </div>

            <div>
                <h2>Editar Perfil</h2>
                <p>Actualice su información personal</p>
            </div>
        </div>

        <form action="../backend/controllers/actualizarPerfil.php" method="POST" enctype="multipart/form-data">

            <div class="form-grid">

                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text"
                        name="username"
                        value="<?= htmlspecialchars($datosUsuario['username']) ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email"
                        name="email"
                        value="<?= htmlspecialchars($datosUsuario['email']) ?>"
                        required>
                </div>

                <div class="form-group full">
                    <label>Foto de Perfil</label>
                    <input type="file" name="foto">
                </div>

            </div>

            <div class="perfil-actions">
                <button type="submit" class="btn-save">
                    <i class="fa fa-floppy-disk"></i>
                    Guardar Cambios
                </button>

                <a href="configuracion.php" class="btn-cancel">
                    Cancelar
                </a>
            </div>

        </form>

    </div>

</div>

<?php include 'includes/layout_end.php'; ?>