<?php
session_start();

// cart page STILL requires login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userId     = $isLoggedIn ? $_SESSION['user_id'] : 'guest';
$userRole   = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$username   = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>cart | ngopi grounds.</title>
    <link rel="stylesheet" href="style.css" />
    <link
        href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
        rel="stylesheet"
    />
</head>

<body>

<?php include 'nav.php'; ?>

<section class="cart" id="cart">
    <h2 class="cart-title">Your Shopping Cart</h2>

    <div id="cart-empty-state" class="cart-empty-box" style="display:none;">
        <p>Your cart is empty 😔</p>
        <a class="btn" href="menu.php">shop now</a>
    </div>

    <table class="cart-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody id="cart-items">
        </tbody>
    </table>

    <div class="cart-actions">

        <div class="checkout">
            <p><strong>Total:</strong> RM<span id="cart-total">0</span></p>
            <button class="btn" id="checkout-btn">Proceed to Checkout</button>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="social-icons">
      <a href="https://youtu.be/BbeeuzU5Qc8?si=pZgIxUo1ourlyert"><i class="bx bxl-facebook"></i></a>
      <a href="https://www.instagram.com/uronlynish/?utm_source=ig_web_button_share_sheet"><i class="bx bxl-instagram-alt"></i></a>
      <a href="https://x.com/hdamirrr"><i class="bx bxl-twitter"></i></a>
    </div>

    <ul class="list">
        <li><a href="#">FAQ</a></li>
        <li><a href="menu.php">menu</a></li>
        <li><a href="about.php">about us</a></li>
        <li><a href="contact.php">contact</a></li>
    </ul>

    <p class="copyright">
        © ngopi grounds. | All Rights Reserved.
    </p>
</footer>

<script>
const IS_LOGGED_IN     = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
const USER_ROLE        = "<?php echo $userRole; ?>";
const CURRENT_USER_ID  = "<?php echo $userId; ?>";
const CURRENT_USERNAME = "<?php echo $username; ?>";
</script>

<script src="script.js"></script>

</body>
</html>