<?php
session_start();

require_once(__DIR__ . "/../config/conexion.php");
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$username = $_POST["username"] ?? '';
$password = $_POST["password"] ?? '';

$query = "
SELECT u.*, r.nombre AS rol 
FROM usuario u 
INNER JOIN rol r ON u.id_rol = r.id_rol 
WHERE u.username = $1
";

$result = pg_query_params($conn, $query, [$username]);

if (!$result) {
    die("Error SQL: " . pg_last_error($conn));
}

if ($row = pg_fetch_assoc($result)) {

    if ($password === $row["password"]) {

        $codigo = rand(100000, 999999);

        $_SESSION["codigo"] = $codigo;
        $_SESSION["usuario_temp"] = $row["username"];
        $_SESSION["id_usuario"] = $row["id_usuario"];
        $_SESSION["username"] = $row["username"];
        $_SESSION["rol"] = $row["rol"];

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = getenv("MAIL_HOST") ?: "smtp.gmail.com";
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv("MAIL_USER");
            $mail->Password   = getenv("MAIL_PASS");
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = getenv("MAIL_PORT") ?: 587;

            $mail->setFrom(getenv("MAIL_FROM") ?: getenv("MAIL_USER"), "Sistema Brigada");
            $mail->addAddress($row["email"]);

            $mail->CharSet = "UTF-8";
            $mail->Subject = "Código de verificación";
            $mail->Body    = "Tu código de verificación es: $codigo";

            $mail->send();

            header("Location: ../../frontend/verificar.php");
            exit();

        } catch (Exception $e) {
            die("Error al enviar correo: " . $mail->ErrorInfo);
        }

    } else {
        echo "Contraseña incorrecta";
    }

} else {
    echo "Usuario no encontrado";
}