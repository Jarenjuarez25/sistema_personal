<?php
session_start();
require_once(__DIR__ . "/../config/conexion.php");
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;


$username = $_POST["username"];
$password = $_POST["password"];

$query = "SELECT u.*, r.nombre AS rol FROM usuario u INNER JOIN rol r ON u.id_rol = r.id_rol WHERE u.username = $1";
$result = pg_query_params($conn, $query, array($username));

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
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'juarezjarengamer@gmail.com';
            $mail->Password = 'qjtxwvifrxwjzuyc';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('juarezjarengamer@gmail.com', 'Sistema Brigada');
            $mail->addAddress($row["email"]);

            $mail->Subject = 'Codigo de verificacion';
            $mail->Body = "Tu codigo es: $codigo";

            $mail->send();

            header("Location: ../../frontend/verificar.php");
            exit();
        } catch (Exception $e) {
            echo "Error al enviar correo";
        }
    } else {
        echo "Contraseña incorrecta";
    }
} else {
    echo "Usuario no encontrado";
}
