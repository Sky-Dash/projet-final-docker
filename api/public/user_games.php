<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$pdo    = create_pdo();
$method = $_SERVER['REQUEST_METHOD'];
$override = $_GET['_method'] ?? $_POST['_method'] ?? null;
if ($method === 'POST' && $override) $method = strtoupper($override);

if ($method === 'GET') {
    $user   = require_auth();
    $userId = $user['id'];

    $stmt = $pdo->prepare("
        SELECT g.id, g.name, g.image_path,
               COUNT(DISTINCT s.id)  AS total_achievements,
               COUNT(DISTINCT us.id) AS owned_achievements
        FROM user_games ug
        JOIN games g ON g.id = ug.game_id
        LEFT JOIN success s ON s.game_id = g.id
        LEFT JOIN user_success us ON us.success_id = s.id AND us.user_id = ?
        WHERE ug.user_id = ?
        GROUP BY g.id
        ORDER BY g.name ASC
    ");
    $stmt->execute([$userId, $userId]);
    json_response(['games' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

if ($method === 'POST') {
    $user   = require_auth();
    $userId = $user['id'];
    $gameId = (int) ($_POST['game_id'] ?? $_GET['game_id'] ?? 0);
    if (!$gameId) json_response(['error' => 'game_id required'], 400);

    $check = $pdo->prepare("SELECT id FROM user_games WHERE user_id = ? AND game_id = ?");
    $check->execute([$userId, $gameId]);
    if (!$check->fetch()) {
        $pdo->prepare("INSERT INTO user_games (user_id, game_id, obtained_at) VALUES (?, ?, NOW())")
            ->execute([$userId, $gameId]);
    }
    json_response(['success' => true]);
}

if ($method === 'DELETE') {
    $user   = require_auth();
    $userId = $user['id'];
    $gameId = (int) ($_GET['game_id'] ?? $_POST['game_id'] ?? 0);
    if (!$gameId) json_response(['error' => 'game_id required'], 400);

    $pdo->prepare("DELETE FROM user_games WHERE user_id = ? AND game_id = ?")
        ->execute([$userId, $gameId]);
    json_response(['success' => true]);
}

json_response(['error' => 'Method not allowed'], 405);
