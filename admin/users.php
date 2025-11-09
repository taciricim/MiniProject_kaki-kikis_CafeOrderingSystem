<?php
require_once "auth_check.php";
require_once "../cafe.php";

// Fetch all users
$users = [];
$sql = "SELECT user_id, username, email, role, phone_number
        FROM users
        ORDER BY user_id DESC";
if ($res = $conn->query($sql)) {
    while ($row = $res->fetch_assoc()) $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>users | ngopi admin.</title>
<link rel="stylesheet" href="admin.css" />
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
</head>
<body>

 <header class="header">
    <a href="dashboard.php" class="logo">ngopi admin.</a>
    
    <div class="header-center">
      <span class="greeting">hi, <?= htmlspecialchars($username) ?></span>
    </div>

    <nav class="navbar">
      <a href="dashboard.php">dashboard</a>
      <a href="products.php">menu</a>
      <a href="orders.php">orders</a>
      <a href="users.php" class="active">users</a>
      <a href="stock_management.php">stock</a>
      <a href="../logout.php" title="Log out"><i class='bx bx-log-out'></i></a>
    </nav>
  </header>

<div class="page-wrap">
    <div class="top-bar">
        <div class="top-left">
            <h2><i class='bx bx-user'></i> users</h2>
            <div class="sub">All registered accounts (customers, staff, admin).</div>
        </div>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th style="width:90px;">Role</th>
                    <th style="width:120px;">Phone</th>
                    <th style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$users): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:2rem 0; color:#999;">no users yet.</td>
                </tr>
            <?php else: foreach ($users as $u): ?>
                <tr>
                    <td>#<?= htmlspecialchars($u['user_id']) ?></td>
                    <td><?= htmlspecialchars($u['username'] ?? '') ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php if ($u['role'] === 'admin'): ?>
                            <span class="badge-role role-admin">admin</span>
                        <?php elseif ($u['role'] === 'staff'): ?>
                            <span class="badge-role role-staff">staff</span>
                        <?php else: ?>
                            <span class="badge-role role-customer">customer</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['phone_number'] ?? '') ?></td>
                    <td class="actions-cell">
                        <a href="#">
                            <i class='bx bx-up-arrow-alt'></i> promote
                        </a>
                        <a href="#" style="color:#c00;">
                            <i class='bx bx-block'></i> disable
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <a class="back-link" href="dashboard.php">
        <i class='bx bx-left-arrow-alt'></i> back to dashboard
    </a>
</div>

<!-- FOOTER -->
  <footer class="footer">
    <div class="social-icons">
      <a href="https://youtu.be/BbeeuzU5Qc8?si=pZgIxUo1ourlyert"><i class="bx bxl-facebook"></i></a>
      <a href="https://www.instagram.com/uronlynish/?utm_source=ig_web_button_share_sheet"><i class="bx bxl-instagram-alt"></i></a>
      <a href="https://x.com/hdamirrr"><i class="bx bxl-twitter"></i></a>
    </div>

    <ul class="list">
      <li><a href="dashboard.php">dashboard</a></li>
      <li><a href="products.php">menu</a></li>
      <li><a href="orders.php">orders</a></li>
      <li><a href="users.php">users</a></li>
    </ul>

    <p class="copyright">
      © ngopi admin. | All Rights Reserved.
    </p>
  </footer>

</body>
</html>
