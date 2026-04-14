<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$pdo    = create_pdo();
$method = $_SERVER['REQUEST_METHOD'];
$override = $_GET['_method'] ?? $_POST['_method'] ?? null;
if ($method === 'POST' && $override) $method = strtoupper($override);


if ($method === 'POST') {
    require_admin();
    $gameId      = (int) ($_POST['game_id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if (!$gameId || !$name) json_response(['error' => 'game_id and name are required'], 400);

    $pdo->prepare("INSERT INTO success (game_id, name, description) VALUES (?, ?, ?)")
        ->execute([$gameId, $name, $description]);
    json_response(['success' => true, 'id' => $pdo->lastInsertId()], 201);
}

if ($method === 'DELETE') {
    require_admin();
    $id = (int) ($_GET['id'] ?? $_POST['success_id'] ?? 0);
    if (!$id) json_response(['error' => 'ID required'], 400);

    $pdo->prepare("DELETE FROM user_success WHERE success_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM success WHERE id = ?")->execute([$id]);
    json_response(['success' => true]);
}

json_response(['error' => 'Method not allowed'], 405);
