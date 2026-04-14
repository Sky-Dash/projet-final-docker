<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';

require_admin();
$pdo = create_pdo();

json_response([
    'users' => (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'games' => (int) $pdo->query("SELECT COUNT(*) FROM games")->fetchColumn(),
]);
