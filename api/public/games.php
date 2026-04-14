<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$pdo    = create_pdo();
$method = $_SERVER['REQUEST_METHOD'];

$override = $_GET['_method'] ?? $_POST['_method'] ?? null;
if ($method === 'POST' && $override) $method = strtoupper($override);

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id   = (int) $_GET['id'];
        $stmt = $pdo->prepare("SELECT id, name, description, image_path, created_at FROM games WHERE id = ?");
        $stmt->execute([$id]);
        $game = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$game) json_response(['error' => 'Not found'], 404);

        $achStmt = $pdo->prepare("SELECT id, name, description FROM success WHERE game_id = ?");
        $achStmt->execute([$id]);
        $game['achievements'] = $achStmt->fetchAll(PDO::FETCH_ASSOC);
        json_response($game);
    }

    $search  = trim($_GET['search'] ?? '');
    $page    = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = (int) ($_GET['per_page'] ?? 0);
    $where   = [];
    $params  = [];

    if ($search !== '') {
        $where[]  = "name LIKE ?";
        $params[] = '%' . $search . '%';
    }

    $whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    $total = (int) $pdo->prepare("SELECT COUNT(*) FROM games $whereSQL")->execute($params) && true;
    $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM games $whereSQL");
    $cntStmt->execute($params);
    $total = (int) $cntStmt->fetchColumn();

    if ($perPage > 0) {
        $offset = ($page - 1) * $perPage;
        $stmt   = $pdo->prepare("SELECT id, name, image_path, created_at FROM games $whereSQL ORDER BY id ASC LIMIT $perPage OFFSET $offset");
    } else {
        $stmt = $pdo->prepare("SELECT id, name, image_path, created_at FROM games $whereSQL ORDER BY created_at DESC");
    }
    $stmt->execute($params);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response(['total' => $total, 'games' => $games, 'pages' => $perPage > 0 ? (int) ceil($total / $perPage) : 1]);
}

if ($method === 'POST') {
    require_admin();
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $imagePath   = null;

    if (!$name) json_response(['error' => 'Name is required'], 400);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename  = basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            $imagePath = 'uploads/' . $filename;
        }
    }

    $pdo->prepare("INSERT INTO games (name, description, image_path, created_at) VALUES (?, ?, ?, NOW())")
        ->execute([$name, $description, $imagePath]);

    json_response(['success' => true, 'id' => $pdo->lastInsertId()], 201);
}

if ($method === 'PUT') {
    require_admin();
    $id = (int) ($_GET['id'] ?? $_POST['game_id'] ?? 0);
    if (!$id) json_response(['error' => 'ID required'], 400);

    $stmt = $pdo->prepare("SELECT id, image_path FROM games WHERE id = ?");
    $stmt->execute([$id]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$game) json_response(['error' => 'Not found'], 404);

    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $imagePath   = $game['image_path'];

    if (!$name) json_response(['error' => 'Name is required'], 400);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename  = basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            $imagePath = 'uploads/' . $filename;
        }
    }

    $pdo->prepare("UPDATE games SET name = ?, description = ?, image_path = ? WHERE id = ?")
        ->execute([$name, $description, $imagePath, $id]);

    json_response(['success' => true]);
}

if ($method === 'DELETE') {
    require_admin();
    $id = (int) ($_GET['id'] ?? $_POST['game_id'] ?? 0);
    if (!$id) json_response(['error' => 'ID required'], 400);

    $pdo->prepare("DELETE FROM user_success WHERE success_id IN (SELECT id FROM success WHERE game_id = ?)")->execute([$id]);
    $pdo->prepare("DELETE FROM success WHERE game_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM user_games WHERE game_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM games WHERE id = ?")->execute([$id]);

    json_response(['success' => true]);
}

json_response(['error' => 'Method not allowed'], 405);
