<?php
session_start();
require '../includes/api_client.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit;
}

$stats = api('stats.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Arena | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="admin-wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin.php" class="active">Dashboard</a>
        <a href="users.php">Manage Users</a>
        <a href="games.php">Manage Games</a>
    </div>

    <main>
        <h1>Dashboard</h1>
        <div class="stats">
            <div class="stat-card">
                <h3><?= htmlspecialchars($stats['users'] ?? 0) ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3><?= htmlspecialchars($stats['games'] ?? 0) ?></h3>
                <p>Total Games</p>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>