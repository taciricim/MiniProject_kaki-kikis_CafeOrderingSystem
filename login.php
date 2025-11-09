<?php
session_start();
require_once "cafe.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {
        $sql  = "SELECT user_id, username, email, password, role FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($uid, $uname, $uemail, $hash, $role);
            $stmt->fetch();

            if (password_verify($password, $hash)) {
                $_SESSION['user_id']  = $uid;
                $_SESSION['username'] = $uname;
                $_SESSION['role']     = $role;

                if ($role === 'admin' || $role === 'staff') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "Wrong password.";
            }
        } else {
            $error = "No account found with that email.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | ngopi grounds.</title>
<link rel="stylesheet" href="style.css"> 
</head>
<body>

<div class="login-wrapper">
  <form class="login-card" method="POST" action="login.php">
    <h2>Log in</h2>

    <?php if ($error): ?>
      <div class="msg-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <label>Email</label>
    <input type="email" name="email" required placeholder="you@example.com">

    <label>Password</label>
    <input type="password" name="password" required placeholder="••••••••">

    <button class="login-btn" type="submit">Sign in</button>

    <a class="small-link" href="signup.php">Create an account →</a>
    <a class="small-link" href="index.php">Back to home</a>
  </form>
</div>

</body>
</html>
