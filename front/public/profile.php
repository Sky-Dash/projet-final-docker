<?php
session_start();
require '../includes/api_client.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$result = api('user_games.php');
$games  = $result['games'] ?? [];

$totalGames        = count($games);
$totalAchievements = array_sum(array_column($games, 'owned_achievements'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apex Arena | Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="profile-wrapper">
    <aside class="profile-sidebar">
        <p class="profile-username"><?= htmlspecialchars($_SESSION['username']) ?></p>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <span class="profile-role">Admin</span>
        <?php endif; ?>

        <div class="profile-divider"></div>

        <div class="profile-stat">
            <span class="profile-stat-label">Games</span>
            <span class="profile-stat-value"><?= $totalGames ?></span>
        </div>
        <div class="profile-stat">
            <span class="profile-stat-label">Achievements</span>
            <span class="profile-stat-value"><?= $totalAchievements ?></span>
        </div>
    </aside>

    <div class="profile-main">
        <div class="profile-section">
            <div class="profile-section-header">
                <h2>My Games</h2>
                <span><?= $totalGames ?> game<?= $totalGames !== 1 ? 's' : '' ?></span>
            </div>
            <div class="profile-section-body">
                <?php if (empty($games)): ?>
                    <p class="empty-state">No games added yet. <a href="index.php">Browse games</a></p>
                <?php else: ?>
                    <div class="profile-games">
                        <?php foreach ($games as $game): ?>
                            <a href="game.php?id=<?= $game['id'] ?>" class="profile-game-card">
                                <div class="profile-game-img"
                                     <?= $game['image_path'] ? 'style="background-image:url(' . htmlspecialchars($game['image_path']) . ')"' : '' ?>></div>
                                <div class="profile-game-info">
                                    <h4><?= htmlspecialchars($game['name']) ?></h4>
                                    <p><?= $game['owned_achievements'] ?> / <?= $game['total_achievements'] ?> achievements</p>
                                    <?php if ($game['total_achievements'] > 0): ?>
                                        <div class="progress-bar">
                                            <div class="progress-fill"
                                                 style="width:<?= round(($game['owned_achievements'] / $game['total_achievements']) * 100) ?>%"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
