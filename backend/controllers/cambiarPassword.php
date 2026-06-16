<?php
session_start();

require_once(__DIR__ . "/../config/conexion.php");
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION["usuario"])) {
    header("Location: ../../frontend/login.php");
    exit();
}

$usuario = $_SESSION["usuario"];
$nuevaPassword = $_POST["nueva_password"];

/* ── Buscar usuario ───────────────────────── */
$query = "SELECT * FROM usuario WHERE username = $1";

$result = pg_query_params($conn, $query, array($usuario));

if ($row = pg_fetch_assoc($result)) {

    $codigo = rand(100000, 999999);

    $_SESSION["codigo"] = $codigo;
    $_SESSION["nueva_password"] = $nuevaPassword;
    $_SESSION["usuario_reset"] = $usuario;

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'juarezjarengamer@gmail.com';
        $mail->Password = 'qjtxwvifrxwjzuyc';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('juarezjarengamer@gmail.com', 'Sistema Brigada');

        // usa el email del usuario encontrado
        $mail->addAddress($row["email"]);

        $mail->Subject = 'Codigo de verificacion';
        $mail->Body = "Tu codigo para cambiar contraseña es: $codigo";

        $mail->send();

        header("Location: ../../frontend/verificarCambio.php");
        exit();

    } catch (Exception $e) {

        echo "Error al enviar correo";

    }

} else {

    echo "Usuario no encontrado";

}