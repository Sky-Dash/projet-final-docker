<?php

require_once '../includes/db.php';

header('Content-Type: application/json');

try {
    $pdo = create_pdo();

    $pdo->query("SELECT 1");

    echo json_encode([
        "status" => "ok",
        "database" => "connected",
        "time" => date("c")
    ]);

} catch (Exception $e) {
    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "database" => "failed"
    ]);
}