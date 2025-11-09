<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userId     = $isLoggedIn ? $_SESSION['user_id'] : 'guest';
$userRole   = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$username   = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Pending | ngopi grounds.</title>
    <link rel="stylesheet" href="style.css"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>

<?php include 'nav.php'; ?>

<section class="order-pending-section">
    <div class="order-pending-container">
        <div class="success-icon">
            <i class='bx bx-check'></i>
        </div>
        
        <h2>Thank You for Your Payment!</h2>
        
        <p>Your order has been received and is currently <strong>pending verification</strong>.</p>
        
        <div class="info-box">
            <p><i class='bx bx-info-circle'></i> <strong>What happens next?</strong></p>
            <p>• We'll verify your payment within 24 hours</p>
            <p>• You'll receive a confirmation email once approved</p>
            <p>• Your order will then be prepared for pickup/delivery</p>
        </div>
        
        <p>We'll notify you once your payment has been confirmed.</p>
        
        <a href="index.php" class="btn">Return to Home</a>
    </div>
</section>

<footer class="footer">
    <div class="social-icons">
      <a href="https://youtu.be/BbeeuzU5Qc8?si=pZgIxUo1ourlyert"><i class="bx bxl-facebook"></i></a>
      <a href="https://www.instagram.com/uronlynish/?utm_source=ig_web_button_share_sheet"><i class="bx bxl-instagram-alt"></i></a>
      <a href="https://x.com/hdamirrr"><i class="bx bxl-twitter"></i></a>
    </div>

    <ul class="list">
      <li><a href="faq.php">FAQ</a></li>
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

    // Clear the cart after successful order
    (function(){
      var uid = (typeof CURRENT_USER_ID !== 'undefined' && CURRENT_USER_ID) 
                  ? CURRENT_USER_ID 
                  : (localStorage.getItem('CURRENT_USER_ID') || 'guest');
      var key = 'cart_user_' + uid;
      try { 
        localStorage.removeItem(key);
        // Update cart badge to 0
        const cartBadge = document.getElementById('cart-count');
        if (cartBadge) {
          cartBadge.textContent = '0';
          cartBadge.style.display = 'none';
        }
      } catch(e) {}
    })();
</script>

</body>
</html>