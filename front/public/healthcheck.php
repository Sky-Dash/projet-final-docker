<?php

header('Content-Type: application/json');

$apiUrl = getenv("API_URL") . "/healthcheck.php";
$response = @file_get_contents($apiUrl);

if ($response && json_decode($response, true)['status'] === 'ok') {
    echo json_encode([
        "api" => "online",
        "status" => "ok"
    ]);
} else {
    echo json_encode([
        "api" => "offline",
        "status" => "error"
    ]);
}