<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

use GuzzleHttp\Client;

function preguntarGroq($mensaje)
{
    $apiKey = getenv("GROQ_API_KEY");

    if (!$apiKey) {
        return "Error Groq: API Key no configurada.";
    }

    $client = new Client([
        'timeout' => 30
    ]);

    try {
        $response = $client->post(
            "https://api.groq.com/openai/v1/chat/completions",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . trim($apiKey),
                    'Content-Type'  => 'application/json'
                ],
                'json' => [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres un asistente virtual militar del sistema de personal de la 1ra Brigada de Servicios del Ejército del Perú. Responde breve, claro y profesional. Además responde cualquier consulta que te hagan aparte de tu rol.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $mensaje
                        ]
                    ],
                    'temperature' => 0.4
                ]
            ]
        );

        $body = json_decode($response->getBody(), true);

        return $body['choices'][0]['message']['content'] ?? 'No pude responder.';

    } catch (Exception $e) {
        return 'Error Groq: ' . $e->getMessage();
    }
}