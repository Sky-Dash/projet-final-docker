<?php
session_start();
require '../includes/api_client.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $files = !empty($_FILES['image']['tmp_name']) ? ['image' => $_FILES['image']] : [];
        api('games.php', 'POST', [
            'name'        => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
        ], $files);
    } elseif (isset($_POST['delete'])) {
        api('games.php?_method=DELETE', 'POST', ['game_id' => (int) $_POST['game_id']]);
    }
    header("Location: games.php"); exit;
}

$search  = trim($_GET['search'] ?? '');
$page    = max(1, (int) ($_GET['page'] ?? 1));

$query = http_build_query(['search' => $search, 'page' => $page, 'per_page' => 5]);
$result = api("games.php?$query");
$games      = $result['games'] ?? [];
$total      = $result['total'] ?? 0;
$totalPages = $result['pages'] ?? 1;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Apex Arena | Manage Games</title>
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
        <a href="users.php">Manage Users</a>
        <a href="games.php" class="active">Manage Games</a>
    </div>

    <main>
        <h1>Manage Games</h1>

        <div class="actions-bar">
            <button class="btn btn-primary" onclick="openAddModal()">Add New Game</button>
        </div>
        <br>

        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search by game name..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-search">Search</button>
            <?php if ($search): ?>
                <a href="games.php" class="btn btn-muted">Clear</a>
            <?php endif; ?>
        </form>

        <p class="result-count"><?= $total ?> game<?= $total !== 1 ? 's' : '' ?> found</p>

        <table class="admin-table">
            <thead>
            <tr>
                <th>#</th><th>Game Name</th><th>Image</th><th>Created At</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($games)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:40px;">No games found.</td></tr>
            <?php else: ?>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?= $game['id'] ?></td>
                        <td><?= htmlspecialchars($game['name']) ?></td>
                        <td>
                            <?php if ($game['image_path']): ?>
                                <img src="<?= htmlspecialchars($game['image_path']) ?>" alt="<?= htmlspecialchars($game['name']) ?>" width="80">
                            <?php else: ?>N/A<?php endif; ?>
                        </td>
                        <td><?= $game['created_at'] ?></td>
                        <td class="actions">
                            <a href="admin_game.php?id=<?= $game['id'] ?>" class="btn btn-primary">Edit</a>
                            <button class="btn btn-danger" onclick="openDeleteModal(<?= $game['id'] ?>, '<?= htmlspecialchars($game['name'], ENT_QUOTES) ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">&#8592; Prev</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">Next &#8594;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <h3>Add New Game</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Game Name" required maxlength="30">
            <textarea name="description" placeholder="Game description..." rows="4"></textarea>
            <label class="file-upload">
                Choose image
                <input type="file" name="image" accept="image/*">
            </label>
            <div class="modal-actions">
                <button type="button" class="btn btn-muted" onclick="closeAddModal()">Cancel</button>
                <button type="submit" name="add" class="btn btn-primary">Add Game</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3>Delete Game</h3>
        <p>Are you sure you want to delete <span id="modalGameName"></span>?</p>
        <div class="modal-actions">
            <button class="btn btn-muted" onclick="closeDeleteModal()">Cancel</button>
            <form method="POST" style="display:inline">
                <input type="hidden" name="game_id" id="modalGameId">
                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddModal()  { document.getElementById('addModal').classList.add('active'); }
    function closeAddModal() { document.getElementById('addModal').classList.remove('active'); }
    function openDeleteModal(id, name) {
        document.getElementById('modalGameId').value = id;
        document.getElementById('modalGameName').textContent = name;
        document.getElementById('deleteModal').classList.add('active');
    }
    function closeDeleteModal() { document.getElementById('deleteModal').classList.remove('active'); }
    document.getElementById('addModal').addEventListener('click', function(e)   { if(e.target===this) closeAddModal(); });
    document.getElementById('deleteModal').addEventListener('click', function(e) { if(e.target===this) closeDeleteModal(); });
</script>

<?php include '../includes/footer.php'; ?>
