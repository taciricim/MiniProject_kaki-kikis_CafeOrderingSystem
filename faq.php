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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FAQ | ngopi grounds.</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>

  <!-- Optional safety net: ensures answers show even if style.css misses the active rule -->
  <style>
    .faq-item .faq-answer { max-height:0; opacity:0; overflow:hidden; transition:max-height .5s ease, opacity .4s ease, padding .4s ease; }
    .faq-item.active .faq-answer { max-height:1000px; opacity:1; padding:1.5rem 2rem; }
  </style>
</head>
<body>

<?php include 'nav.php'; ?>

<section class="faq" id="faq">
  <h2 class="faq-title">frequently asked questions.</h2>

  <div class="faq-container">

    <div class="faq-item">
      <div class="faq-question">What are your opening hours?</div>
      <div class="faq-answer">
        We’re open daily from <strong>8:00 AM to 10:00 PM</strong>, including weekends and public holidays.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">Is there Wi-Fi in your café?</div>
      <div class="faq-answer">
        Yes! We have Wi-Fi available for customers. Just place an order first and you’re welcome to relax, study, or work here.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">Can I preorder items that are out of stock?</div>
      <div class="faq-answer">
        Absolutely. You can place a preorder for out-of-stock items by clicking the “Preorder” button through our <a href="menu.php">menu page</a>.
        You’ll be notified once your order is ready for pickup.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">Where is your café located?</div>
      <div class="faq-answer">
        We’re located at <strong>Lot 24, Jalan Tun Razak, Kuala Lumpur</strong>.
        You can easily find us on Google Maps by searching for <em>ngopi grounds café</em>.
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-question">Is there parking available nearby?</div>
      <div class="faq-answer">
        Yes, parking is available near our café. We also recommend arriving a little early during peak hours as spaces may fill up quickly.
      </div>
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
    <li><a href="faq.php">FAQ</a></li>
    <li><a href="menu.php">menu</a></li>
    <li><a href="about.php">about us</a></li>
    <li><a href="contact.php">contact</a></li>
  </ul>
  <p class="copyright">© ngopi grounds. | All Rights Reserved.</p>
</footer>

<!-- Expose login/cart info to JS (cart badge animation uses this) -->
<script>
  const IS_LOGGED_IN     = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
  const USER_ROLE        = "<?php echo $userRole; ?>";
  const CURRENT_USER_ID  = "<?php echo $userId; ?>";
  const CURRENT_USERNAME = "<?php echo $username; ?>";
</script>

<!-- Your main site JS (cart badge etc.) -->
<script src="script.js"></script>

<script>
(function () {
  const container = document.querySelector('.faq-container');
  if (!container) return;

  container.addEventListener('click', function (e) {
    const q = e.target.closest('.faq-question');
    if (!q) return;

    const item = q.closest('.faq-item');
    if (!item) return;

    const isOpen = item.classList.contains('active');

    // Close all
    document.querySelectorAll('.faq-item.active').forEach(i => i.classList.remove('active'));

    // Toggle clicked one (only open if it was closed)
    if (!isOpen) {
      item.classList.add('active');
    }
  });
})();
</script>

</body>
</html>
