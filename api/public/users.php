<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$pdo    = create_pdo();
$method = $_SERVER['REQUEST_METHOD'];
$override = $_GET['_method'] ?? $_POST['_method'] ?? null;
if ($method === 'POST' && $override) $method = strtoupper($override);


if ($method === 'GET') {
    require_admin();
    $search  = trim($_GET['search'] ?? '');
    $role    = in_array($_GET['role'] ?? '', ['admin', 'user']) ? $_GET['role'] : '';
    $page    = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = 5;
    $offset  = ($page - 1) * $perPage;
    $where   = [];
    $params  = [];

    if ($search !== '') {
        $where[]  = "(username LIKE ? OR email LIKE ?)";
        $like     = '%' . $search . '%';
        $params[] = $like;
        $params[] = $like;
    }
    if ($role !== '') {
        $where[]  = "role = ?";
        $params[] = $role;
    }

    $whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM users $whereSQL");
    $cntStmt->execute($params);
    $total = (int) $cntStmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users $whereSQL ORDER BY id ASC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);

    json_response([
        'total' => $total,
        'pages' => (int) ceil($total / $perPage),
        'users' => $stmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
}

if ($method === 'PUT') {
    require_admin();
    $id   = (int) ($_GET['id'] ?? $_POST['user_id'] ?? 0);
    $role = $_POST['role'] ?? '';
    if (!$id) json_response(['error' => 'ID required'], 400);
    if (!in_array($role, ['user', 'admin'])) json_response(['error' => 'Invalid role'], 400);

    $pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, $id]);
    json_response(['success' => true]);
}

if ($method === 'DELETE') {
    require_admin();
    $id = (int) ($_GET['id'] ?? $_POST['user_id'] ?? 0);
    if (!$id) json_response(['error' => 'ID required'], 400);

    $pdo->prepare("DELETE FROM user_success WHERE user_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM user_games WHERE user_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    json_response(['success' => true]);
}

if ($method === 'GET' && isset($_GET['me'])) {
    $user = require_auth();
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    json_response($stmt->fetch(PDO::FETCH_ASSOC));
}

json_response(['error' => 'Method not allowed'], 405);
