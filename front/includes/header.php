<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header>
    <div class="header-inner">
        <a href="index.php" class="logo">
            <span class="logo-text">APEX<span>ARENA</span></span>
        </a>

        <nav>
            <a href="index.php" class="active">Home</a>
            <div class="nav-divider"></div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php" class="nav-cta nav-admin">ADMIN PANEL</a>
                <?php endif; ?>
                <a href="profile.php" class="nav-cta">Profile</a>
                <a href="logout.php" class="nav-cta">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-cta">Login</a>
                <a href="register.php" class="nav-cta">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
