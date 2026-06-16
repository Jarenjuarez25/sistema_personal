<?php

header('Content-Type: application/json');

$dni = $_GET['dni'] ?? '';

if (!preg_match('/^[0-9]{8}$/', $dni)) {
    echo json_encode([
        'success' => false,
        'message' => 'DNI inválido'
    ]);
    exit;
}

$token = '286a52f76a0d0070b9f4f93b405c685e04b46e418432899edb28e935c637fb4c';

$params = json_encode([
    'dni' => $dni
]);

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => 'https://apiperu.dev/api/dni',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $params,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]
]);

$response = curl_exec($curl);
$error = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($error) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión: ' . $error
    ]);
    exit;
}

$data = json_decode($response, true);

if ($httpCode !== 200 || !isset($data['success']) || $data['success'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => $data['message'] ?? $data['mensaje'] ?? 'No se encontró información del DNI',
        'debug' => $data
    ]);
    exit;
}

$persona = $data['data'] ?? [];

$nombreCompleto = trim(
    ($persona['apellido_paterno'] ?? '') . ' ' .
    ($persona['apellido_materno'] ?? '') . ' ' .
    ($persona['nombres'] ?? '')
);

echo json_encode([
    'success' => true,
    'nombre_completo' => $nombreCompleto,
]);