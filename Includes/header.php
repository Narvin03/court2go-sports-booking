<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userId = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Guest';
?>
<header class="header-container">
    <link rel="stylesheet" href="./Styles/includes.css"/>
    <script src="./JavaScripts/header.js" defer></script>
    <script src="https://kit.fontawesome.com/1af9345e5c.js" crossorigin="anonymous"></script>

    <div class="header-left">
        <button class="burger-menu" id="burger-menu" aria-label="Open navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="sidebar-nav" id="sidebar-nav">
            <div class="sidebar-profile">
                <?php if ($userId): ?>
                    <img src="php/show_avatar.php?id=<?= $userId ?>&t=<?= time() ?>" 
                         alt="Sidebar Avatar" class="sidebar-avatar">
                <?php else: ?>
                    <img src="Assets/other/default_avatar.png" 
                         alt="Default Avatar" class="sidebar-avatar">
                <?php endif; ?>

                <span class="sidebar-username">
                    <a href="profile.php"><?= htmlspecialchars($username) ?></a>
                </span>
            </div>
            <ul class="sidebar-links">
                <li><a href="main.php" class="sidebar-link"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="about.php" class="sidebar-link"><i class="fa fa-info-circle"></i> About Us</a></li>
                <li><a href="support.php" class="sidebar-link"><i class="fa fa-question-circle"></i> Help</a></li>
                <li><a href="booking_main.php" class="sidebar-link"><i class="fa fa-calendar"></i> BookNow</a></li>
                <li><a href="deals.php" class="sidebar-link"><i class="fa fa-tags"></i> Deals</a></li>
            </ul>
            <div class="sidebar-bottom">
                <?php if ($userId): ?>
                    <a href="loginandregister.php" class="sidebar-icon"><i class="fa fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="loginandregister.php" class="sidebar-icon"><i class="fa fa-user"></i> Login</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="logo-header">
            <img src="./Assets/other/logo.png" alt="logo" id="logo-btn" onclick="mainBtn()"> 
            <h1 id="main-btn" onclick="mainBtn()">Court2Go</h1>
        </div>
    </div>
    
    <div class="nav-header">
        <a href="support.php" id="help-btn">Help</a>
        <?php if ($userId): ?>
            <a href="loginandregister.php" id="logout-btn">
                <i class="fa-solid fa-sign-out-alt" style="padding-right:5px;"></i>Logout
            </a>
        <?php else: ?>
            <a href="loginandregister.php" id="logIn-btn">
                <i class="fa-solid fa-user" style="padding-right:5px;"></i>Login / Register
            </a>
        <?php endif; ?>
    </div>
</header>
