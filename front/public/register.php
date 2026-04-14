<?php
session_start();
require '../includes/api_client.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = api('register.php', 'POST', [
        'email'    => $_POST['email'] ?? '',
        'username' => $_POST['username'] ?? '',
        'password' => $_POST['password'] ?? '',
    ]);

    if (!empty($result['success'])) {
        header("Location: login.php");
        exit;
    } else {
        $error = $result['error'] ?? 'Registration failed';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Apex Arena</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<main>
<div class="container">
    <h1>REGISTER</h1>
    <form method="POST" action="">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <button type="submit">ENTER THE ARENA</button>
    </form>
    <div class="footer">
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>
