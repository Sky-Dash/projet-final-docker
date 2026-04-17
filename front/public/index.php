<?php
session_start();
require '../includes/api_client.php';

$result = api('games.php');
$games  = $result['games'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Arena 2.0 | Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<main>
    <h1 class="page-title">All Games</h1>
    <div class="games-grid">
        <?php if (!empty($games)): ?>
            <?php foreach ($games as $game): ?>
                <a href="game.php?id=<?= $game['id'] ?>" class="game-card">
                    <?php if (!empty($game['image_path'])): ?>
                        <img src="<?= htmlspecialchars($game['image_path']) ?>"
                             alt="<?= htmlspecialchars($game['name']) ?>"
                             onerror="this.src='assets/images/default.png';">
                    <?php else: ?>
                        <img src="assets/images/default.png" alt="No image">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($game['name']) ?></h3>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-games">No games available yet.</p>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>
