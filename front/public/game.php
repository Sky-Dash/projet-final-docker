<?php
session_start();
require '../includes/api_client.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        api('user_games.php', 'POST', ['game_id' => $id]);
    } elseif (isset($_POST['remove'])) {
        api('user_games.php', 'DELETE', ['game_id' => $id]);
    } elseif (isset($_POST['add_success'])) {
        api('user_achievements.php', 'POST', ['success_id' => (int) $_POST['success_id']]);
    } elseif (isset($_POST['remove_success'])) {
        api('user_achievements.php', 'DELETE', ['success_id' => (int) $_POST['success_id']]);
    }
    header("Location: game.php?id=$id");
    exit;
}

$game = api("games.php?id=$id");
if (isset($game['error'])) { header("Location: index.php"); exit; }

$achievements = $game['achievements'] ?? [];

$owned = false;
$ownedSuccesses = [];

if (isset($_SESSION['user_id'])) {

    $profileData = api('user_games.php');
    $games = $profileData['games'] ?? [];

    $ownedGameIds = array_column($games, 'id');

    $owned = in_array($id, $ownedGameIds);

    $uaResult = api("user_achievements.php?game_id=$id");
    $ownedSuccesses = $uaResult['success_ids'] ?? [];
}

$owned_ach  = array_values(array_filter(
        $achievements,
        fn($a) => in_array($a['id'], $ownedSuccesses)
));

$locked_ach = array_values(array_filter(
        $achievements,
        fn($a) => !in_array($a['id'], $ownedSuccesses)
));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apex Arena | <?= htmlspecialchars($game['name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/game.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="game-banner" <?= $game['image_path'] ? 'style="background-image: url(' . htmlspecialchars($game['image_path']) . ')"' : '' ?>>
    <div class="game-banner-overlay"></div>
    <div class="game-banner-content">
        <p class="game-label">Game</p>
        <h1><?= htmlspecialchars($game['name']) ?></h1>
        <p class="game-date">Added <?= date('d M Y', strtotime($game['created_at'])) ?></p>
    </div>
</div>

<div class="game-body">
    <div class="game-info">
        <h2>About</h2>
        <p><?= nl2br(htmlspecialchars($game['description'])) ?></p>
    </div>

    <div class="game-action">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="btn-game">Login to Add</a>
        <?php elseif ($owned): ?>
            <p class="owned-label">✓ Already in your profile</p>
            <form method="POST">
                <button type="submit" name="remove" class="btn-game btn-game-remove">Remove from Profile</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <button type="submit" name="add" class="btn-game">Add to Profile</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (!empty($achievements)): ?>
        <div class="game-achievements">
            <h2>Achievements <span><?= count($achievements) ?></span></h2>
            <div class="achievements-columns">

                <div class="achievements-col">
                    <h3 class="col-label">Locked <span><?= count($locked_ach) ?></span></h3>
                    <?php if (empty($locked_ach)): ?>
                        <p class="col-empty">All achievements unlocked!</p>
                    <?php else: ?>
                        <?php foreach ($locked_ach as $a): ?>
                            <div class="achievement-card achievement-locked">
                                <div class="achievement-icon">🔒</div>
                                <div class="achievement-info">
                                    <h4><?= htmlspecialchars($a['name']) ?></h4>
                                    <p><?= htmlspecialchars($a['description']) ?></p>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <form method="POST">
                                            <input type="hidden" name="success_id" value="<?= $a['id'] ?>">
                                            <button type="submit" name="add_success" class="btn-achievement">Unlock</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="achievements-col">
                    <h3 class="col-label col-label-owned">Unlocked <span><?= count($owned_ach) ?></span></h3>
                    <?php if (empty($owned_ach)): ?>
                        <p class="col-empty">No achievements unlocked yet.</p>
                    <?php else: ?>
                        <?php foreach ($owned_ach as $a): ?>
                            <div class="achievement-card achievement-owned">
                                <div class="achievement-icon">🏆</div>
                                <div class="achievement-info">
                                    <h4><?= htmlspecialchars($a['name']) ?></h4>
                                    <p><?= htmlspecialchars($a['description']) ?></p>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <form method="POST">
                                            <input type="hidden" name="success_id" value="<?= $a['id'] ?>">
                                            <button type="submit" name="remove_success" class="btn-achievement btn-achievement-remove">Remove</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
