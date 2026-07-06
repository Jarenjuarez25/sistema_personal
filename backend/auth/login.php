<?php
session_start();

require_once(__DIR__ . "/../config/conexion.php");

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

        $apiKey = getenv("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");
        $fromEmail = getenv("MAIL_FROM") ?: "juarezjarengamer@gmail.com";

        $data = [
            "sender" => [
                "name" => "Sistema Brigada",
                "email" => $fromEmail
            ],
            "to" => [
                [
                    "email" => $row["email"],
                    "name" => $row["username"]
                ]
            ],
            "subject" => "Código de verificación",
            "htmlContent" => "
                <h3>Sistema Brigada</h3>
                <p>Tu código de verificación es:</p>
                <h2>$codigo</h2>
                <p>No compartas este código con nadie.</p>
            "
        ];

        $ch = curl_init("https://api.brevo.com/v3/smtp/email");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "accept: application/json",
            "api-key: " . $apiKey,
            "content-type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            die("Error CURL: " . $curlError);
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            header("Location: ../../frontend/verificar.php");
            exit();
        } else {
            die("Error Brevo API: " . $response);
        }

    } else {
        echo "Contraseña incorrecta";
    }

} else {
    echo "Usuario no encontrado";
}