<?php
session_start();
require_once "cafe.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        $error = "Username, email, and password are required.";
    } else {
        $checkSql = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();

        if ($checkRes->fetch_assoc()) {
            $error = "That email is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertSql = "INSERT INTO users (username, email, password, role, phone_number)
                          VALUES (?, ?, ?, 'customer', ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ssss", $username, $email, $hashedPassword, $phone_number);

            if ($insertStmt->execute()) {
                $_SESSION['user_id']   = $conn->insert_id;
                $_SESSION['username']  = $username;
                $_SESSION['role']      = 'customer';

                header("Location: index.php");
                exit;
            } else {
                $error = "Error creating account. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
  <title>Create Account | ngopi grounds.</title>
  <link rel="stylesheet" href="style.css">

  </head>
  <body>

  <div class="signup-wrapper">
    <form class="signup-card" method="POST" action="signup.php">
      <h2>Create Account</h2>
      <p class="sub">Sign up to order and checkout faster.</p>

      <?php if ($error): ?>
        <div class="msg-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="msg-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <label>Username</label>
      <input type="text" name="username" required placeholder="your name">

      <label>Email</label>
      <input type="email" name="email" required placeholder="you@example.com">

      <label>Password</label>
      <input type="password" name="password" required placeholder="••••••••">

      <label>Phone Number (optional)</label>
      <input type="text" name="phone_number" placeholder="0123456789">

      <button class="signup-btn" type="submit">Create Account</button>

      <a class="login-link" href="login.php">Already have an account? Log in →</a>
    </form>
  </div>

  </body>
</html>
