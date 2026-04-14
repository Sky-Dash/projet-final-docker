<?php
session_start();
require '../includes/api_client.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit;
}

$id = (int) ($_GET['id'] ?? 0);
if (!$id) { header("Location: games.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_game'])) {
        $files = !empty($_FILES['image']['tmp_name']) ? ['image' => $_FILES['image']] : [];
        api("games.php?id=$id", 'PUT', [
            'name'        => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
        ], $files);
    } elseif (isset($_POST['add_success'])) {
        api('achievements.php', 'POST', [
            'game_id'     => $id,
            'name'        => $_POST['success_name'] ?? '',
            'description' => $_POST['success_description'] ?? '',
        ]);
    } elseif (isset($_POST['delete_success'])) {
        api('achievements.php?_method=DELETE', 'POST', ['success_id' => (int) $_POST['success_id']]);
    }
    header("Location: admin_game.php?id=$id"); exit;
}

$game = api("games.php?id=$id");
if (isset($game['error'])) { header("Location: games.php"); exit; }

$achievements = $game['achievements'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | <?= htmlspecialchars($game['name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/admin_game.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="game-banner" <?= $game['image_path'] ? 'style="background-image: url(' . htmlspecialchars($game['image_path']) . ')"' : '' ?>>
    <div class="game-banner-overlay"></div>
    <div class="game-banner-content">
        <p class="game-label">Admin Panel</p>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" class="admin-title" value="<?= htmlspecialchars($game['name']) ?>" required>
            <p class="game-date">Added <?= date('d M Y', strtotime($game['created_at'])) ?></p>
    </div>
</div>

<div class="game-body">
    <div class="game-info">
        <h2>About</h2>
        <textarea name="description" class="admin-textarea" required><?= htmlspecialchars($game['description']) ?></textarea>
        <label class="file-upload">
            Choose image
            <input type="file" name="image" accept="image/*">
        </label>
        <button type="submit" name="update_game" class="btn-game">Save</button>
        </form>
    </div>

    <div class="admin-success-box">
        <h2>Add Achievement</h2>
        <form method="POST" class="admin-success-form">
            <input type="text" name="success_name" placeholder="Achievement name" required>
            <input type="text" name="success_description" placeholder="Description" required>
            <button type="submit" name="add_success" class="btn-game">Add</button>
        </form>
    </div>

    <?php if (!empty($achievements)): ?>
        <div class="game-achievements">
            <h2>Achievements <span><?= count($achievements) ?></span></h2>
            <div class="achievements-columns">
                <div class="achievements-col">
                    <?php foreach ($achievements as $a): ?>
                        <div class="achievement-card">
                            <div class="achievement-icon">🏆</div>
                            <div class="achievement-info">
                                <h4><?= htmlspecialchars($a['name']) ?></h4>
                                <p><?= htmlspecialchars($a['description']) ?></p>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="success_id" value="<?= $a['id'] ?>">
                                <button type="submit" name="delete_success" class="btn-achievement btn-achievement-remove">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
