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
    <title>contact | ngopi grounds.</title>
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
</head>

<body>

<?php include 'nav.php'; ?>

 <section class="contact" id="contact">
       
        <h2 class="heading">get in touch!</h2>
        <p>got an inquiry? your macaron just too good? contact us :D</p>


        <form action="#">
            <div class="input-group">
                <div class="input-box">
                    <input type="text" placeholder="Full Name">
                    <input type="email" placeholder="Email">
                </div>


                <div class="input-box">
                    <input type="text" placeholder="Phone Number">
                    <input type="text" placeholder="Subject">


                </div>
            </div>


            <div class="input-group-2">
                <textarea name="" id="" placeholder="Your Message" cols="30" rows="10"></textarea>
                <input type="submit" value="Send Message" class="btn">
            </div>


        </form>
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

<!-- expose login/cart info to JS -->
<script>
const IS_LOGGED_IN     = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
const USER_ROLE        = "<?php echo $userRole; ?>";
const CURRENT_USER_ID  = "<?php echo $userId; ?>";
</script>

<script src="script.js"></script>

</body>
</html>