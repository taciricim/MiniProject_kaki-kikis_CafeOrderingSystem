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
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>home | ngopi grounds.</title>
      <link rel="stylesheet" href="style.css" />
      <link
        href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
        rel="stylesheet">
  </head>

  <body>

  <?php include 'nav.php'; ?>

  <section class="home" id="home">
    <div class="home-image">
      <img src="images/banner.jpg" alt="Banner">
    </div>
    <div class="home-content">
      <h2>Handcrafted with love. Freshly baked, everyday.</h2>
      <a href="menu.php" class="btn">shop now</a>
    </div>
  </section>

  <!-- VIDEO SHOWCASE SECTION -->
  <section class="video-showcase" id="video-showcase">
    <div class="video-container">
      <video autoplay muted loop playsinline>
        <source src="images/cafe_video.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
      <div class="video-overlay">
        <h2>Experience the warmth of ngopi grounds.</h2>
        <p>Step inside our cozy space — where every cup tells a story.</p>
      </div>
    </div>
  </section>

  <section class="menu" id="menu">
    <h2 class="menu-title">our bestsellers</h2>

    <div class="menu-container">
      <div class="menu-card">
        <img src="images/f1.jpeg" alt="Tiramisu Petit Tarte">
        <div class="menu-info">
          <h3>Tiramisu Petit Tarte</h3>
          <p>Decadent chocolate with hints of espresso</p>
        </div>
      </div>

      <div class="menu-card">
        <img src="images/f2.jpg" alt="Canele">
        <div class="menu-info">
          <h3>Canele</h3>
          <p>The custardy classic. Our specialty.</p>
        </div>
      </div>

      <div class="menu-card">
        <img src="images/f3.jpeg" alt="Raspberry Tart">
        <div class="menu-info">
          <h3>Raspberry Tart</h3>
          <p>The tartness of raspberry</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FIXED REVIEWS SECTION -->
  <section class="reviews" id="reviews">
    <div class="reviews-video-bg">
      <video autoplay loop muted playsinline>
        <source src="images/vibe.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
      <div class="video-overlay"></div>
    </div>

    <h2 class="reviews-title">our customer reviews</h2>

    <div class="reviews-container">
      <div class="review-card">
        <img src="images/profile-user.png" class="review-img" alt="user profile">
        <h3 class="review-name">Sabrina C.</h3>
        <p class="review-text">Now that's THAT me espresso!</p>
        <div class="review-stars">
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
        </div>
      </div>

      <div class="review-card">
        <img src="images/profile-user.png" class="review-img" alt="user profile">
        <h3 class="review-name">Jeremiah F.</h3>
        <p class="review-text">
          The whole flavor profile of the bitterness of the dark chocolate and the sweet tartness
          of the raspberry compote in the mirror-glaze cake is to die for!
        </p>
        <div class="review-stars">
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
          <i class='bx bx-star'></i>
        </div>
      </div>

      <div class="review-card">
        <img src="images/profile-user.png" class="review-img" alt="user profile">
        <h3 class="review-name">Sofia Rahman</h3>
        <p class="review-text">
          Lovely atmosphere and fresh baked goods every day. Highly recommended.
        </p>
        <div class="review-stars">
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
          <i class='bx bxs-star'></i>
        </div>
      </div>
    </div>

    <div class="reviews-btn-container">
      <a href="reviews.php" class="btn">More Reviews</a>
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
  </script>

  <script src="script.js"></script>

  </body>
</html>