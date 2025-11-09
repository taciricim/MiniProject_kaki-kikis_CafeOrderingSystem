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
    <title>about | ngopi grounds.</title>
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
</head>

<body>

<?php include 'nav.php'; ?>

 <section class="about" id="about">
        <div class="about-content">
            <h2>about us</h2>


            <h4>how it all started...</h4>


            <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum, consequatur
            temporibus deleniti incidunt enim provident laboriosam numquam, quae iusto neque
            dolor tempore odio ratione rem architecto est modi hic ab.
            </p>
            <a href="#team" class="btn">Read More</a>
        </div>


        <div class="about-image">
            <img src="images/about.jpeg">


        </div>
    </section>


    <section class="team" id="team">


        <div class="team-image">
            <img src="images/team.jpeg">
        </div>


        <div class="team-content">
            <h2>our team</h2>


            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum, consequatur temporibus deleniti incidunt enim provident laboriosam numquam, quae iusto neque dolor tempore odio ratione rem architecto est modi hic ab.</p>
            <a href="#" class="btn">Read More</a>
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
</script>

<script src="script.js"></script>

</body>
</html>