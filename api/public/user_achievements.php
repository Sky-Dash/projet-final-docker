<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$pdo    = create_pdo();
$method = $_SERVER['REQUEST_METHOD'];
$override = $_GET['_method'] ?? $_POST['_method'] ?? null;
if ($method === 'POST' && $override) $method = strtoupper($override);

if ($method === 'GET') {
    $user   = require_auth();
    $gameId = (int) ($_GET['game_id'] ?? 0);
    if (!$gameId) json_response(['error' => 'game_id required'], 400);

    $stmt = $pdo->prepare("
        SELECT us.success_id FROM user_success us
        JOIN success s ON s.id = us.success_id
        WHERE us.user_id = ? AND s.game_id = ?
    ");
    $stmt->execute([$user['id'], $gameId]);
    $ids = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'success_id');
    json_response(['success_ids' => $ids]);
}

if ($method === 'POST') {
    $user      = require_auth();
    $userId    = $user['id'];
    $successId = (int) ($_POST['success_id'] ?? $_GET['success_id'] ?? 0);
    if (!$successId) json_response(['error' => 'success_id required'], 400);

    $check = $pdo->prepare("SELECT id FROM user_success WHERE user_id = ? AND success_id = ?");
    $check->execute([$userId, $successId]);
    if (!$check->fetch()) {
        $pdo->prepare("INSERT INTO user_success (user_id, success_id, obtained_at) VALUES (?, ?, NOW())")
            ->execute([$userId, $successId]);
    }
    json_response(['success' => true]);
}

if ($method === 'DELETE') {
    $user      = require_auth();
    $userId    = $user['id'];
    $successId = (int) ($_GET['success_id'] ?? $_POST['success_id'] ?? 0);
    if (!$successId) json_response(['error' => 'success_id required'], 400);

    $pdo->prepare("DELETE FROM user_success WHERE user_id = ? AND success_id = ?")
        ->execute([$userId, $successId]);
    json_response(['success' => true]);
}

json_response(['error' => 'Method not allowed'], 405);
