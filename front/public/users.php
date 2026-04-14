<?php
session_start();
require '../includes/api_client.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = (int) ($_POST['user_id'] ?? 0);
    if (isset($_POST['delete'])) {
        api("users.php?id=$uid", 'DELETE', ['user_id' => $uid]);
    } elseif (isset($_POST['change_role'])) {
        api("users.php?id=$uid", 'PUT', ['user_id' => $uid, 'role' => $_POST['role']]);
    }
    header("Location: users.php"); exit;
}

$search  = trim($_GET['search'] ?? '');
$role    = in_array($_GET['role'] ?? '', ['admin', 'user']) ? $_GET['role'] : '';
$page    = max(1, (int) ($_GET['page'] ?? 1));

$query  = http_build_query(['search' => $search, 'role' => $role, 'page' => $page]);
$result = api("users.php?$query");
$users      = $result['users'] ?? [];
$total      = $result['total'] ?? 0;
$totalPages = $result['pages'] ?? 1;
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apex Arena | Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="admin-wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin.php">Dashboard</a>
        <a href="users.php" class="active">Manage Users</a>
        <a href="games.php">Manage Games</a>
    </div>

    <main>
        <h1>Manage Users</h1>

        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search by username or email..."
                   value="<?= htmlspecialchars($search) ?>">
            <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
            <button type="submit" class="btn btn-search">Search</button>
            <?php if ($search || $role): ?>
                <a href="users.php" class="btn btn-muted">Clear</a>
            <?php endif; ?>
        </form>

        <div class="filter-bar">
            <a href="?search=<?= urlencode($search) ?>" class="filter-btn <?= $role === '' ? 'active' : '' ?>">All</a>
            <a href="?search=<?= urlencode($search) ?>&role=user" class="filter-btn <?= $role === 'user' ? 'active' : '' ?>">Users</a>
            <a href="?search=<?= urlencode($search) ?>&role=admin" class="filter-btn <?= $role === 'admin' ? 'active' : '' ?>">Admins</a>
        </div>

        <p class="result-count"><?= $total ?> user<?= $total !== 1 ? 's' : '' ?> found</p>

        <table class="admin-table">
            <thead>
            <tr><th>#</th><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:40px;">No users found.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role" onchange="this.form.submit()" class="role-select">
                                    <option value="user"  <?= $user['role'] === 'user'  ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <input type="hidden" name="change_role" value="1">
                            </form>
                        </td>
                        <td class="actions">
                            <button class="btn btn-danger" onclick="openModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>" class="page-btn">&#8592; Prev</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>"
                       class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>" class="page-btn">Next &#8594;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3>Delete User</h3>
        <p>Are you sure you want to delete <span id="modalUsername"></span>?</p>
        <div class="modal-actions">
            <button class="btn btn-muted" onclick="closeModal()">Cancel</button>
            <form method="POST" style="display:inline">
                <input type="hidden" name="user_id" id="modalUserId">
                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id, username) {
        document.getElementById('modalUserId').value = id;
        document.getElementById('modalUsername').textContent = username;
        document.getElementById('deleteModal').classList.add('active');
    }
    function closeModal() { document.getElementById('deleteModal').classList.remove('active'); }
    document.getElementById('deleteModal').addEventListener('click', function(e) { if(e.target===this) closeModal(); });
</script>

<?php include '../includes/footer.php'; ?>
