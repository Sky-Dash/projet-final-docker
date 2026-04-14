<?php
function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function get_session_user(): ?array {
    session_start();
    if (!isset($_SESSION['user_id'])) return null;
    return [
        'id'   => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'],
    ];
}

function require_auth(): array {
    $user = get_session_user();
    if (!$user) json_response(['error' => 'Unauthorized'], 401);
    return $user;
}

function require_admin(): array {
    $user = require_auth();
    if ($user['role'] !== 'admin') json_response(['error' => 'Forbidden'], 403);
    return $user;
}
