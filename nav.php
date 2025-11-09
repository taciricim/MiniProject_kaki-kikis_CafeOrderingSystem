<?php
// nav.php - Reusable navigation component
// Make sure session is started before including this file

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userId     = $isLoggedIn ? $_SESSION['user_id'] : 'guest';
$userRole   = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$username   = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Get current page to highlight active nav item
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="style.css" />
<header class="header">
    <a href="index.php" class="logo">ngopi grounds.</a>

    <div class="header-center">
      <?php if ($isLoggedIn): ?>
        <a href="profile.php" style="text-decoration: none;">
            <span class="greeting">hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
        </a>
      <?php endif; ?>
    </div>

    <nav class="navbar">
      <a href="index.php" <?= ($current_page == 'index.php') ? 'class="active"' : '' ?>>home</a>
      <a href="menu.php" <?= ($current_page == 'menu.php') ? 'class="active"' : '' ?>>menu</a>
      <a href="about.php" <?= ($current_page == 'about.php') ? 'class="active"' : '' ?>>about</a>
      <a href="contact.php" <?= ($current_page == 'contact.php') ? 'class="active"' : '' ?>>contact</a>
      <a href="faq.php" <?= ($current_page == 'faq.php') ? 'class="active"' : '' ?>>faq</a>
      

      <a href="cart.php" class="cart-link" <?= ($current_page == 'cart.php') ? 'class="active"' : '' ?>>
        <i class='bx bxs-cart' id="cart-icon"></i>
        <span id="cart-count" class="cart-badge">0</span>
      </a>

      <?php if ($isLoggedIn): ?>
        <a href="profile.php" <?= ($current_page == 'profile.php') ? 'class="active"' : '' ?>><i class='bx  bx-user'></i> </a>
        <a href="logout.php"><i class='bx bx-log-out'></i></a>
      <?php else: ?>
        <a href="login.php"><i class='bx bx-user'></i></a>
      <?php endif; ?>
    </nav>
</header>

<script>
    const IS_LOGGED_IN     = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    const USER_ROLE        = "<?php echo $userRole; ?>";
    const CURRENT_USER_ID  = "<?php echo $userId; ?>";
    const CURRENT_USERNAME = "<?php echo $username; ?>";
</script>