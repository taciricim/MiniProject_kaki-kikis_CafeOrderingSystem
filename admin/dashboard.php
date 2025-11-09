<?php
require_once "auth_check.php";
require_once "../cafe.php";

$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$totalCustomers = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='customer'")->fetch_assoc()['c'];
$totalStaff = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role IN ('admin','staff')")->fetch_assoc()['c'];
$totalProducts = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$totalOrders = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$lowStockItems = $conn->query("SELECT COUNT(*) AS c FROM products WHERE stock_qty <= 5 AND stock_qty > 0")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashboard | ngopi admin.</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  </head>
  <body>

  <!-- HEADER -->
  <header class="header">
    <a href="dashboard.php" class="logo">ngopi admin.</a>
    
    <div class="header-center">
      <span class="greeting">hi, <?= htmlspecialchars($username) ?></span>
    </div>

    <nav class="navbar">
      <a href="dashboard.php" class="active">dashboard</a>
      <a href="products.php">menu</a>
      <a href="orders.php">orders</a>
      <a href="users.php">users</a>
      <a href="stock_management.php">stock</a>
      <a href="../logout.php" title="Log out"><i class='bx bx-log-out'></i></a>
    </nav>
  </header>

  <div class="dashboard-container">
    <!-- WELCOME BANNER -->
    <div class="welcome-banner">
      <h1>Welcome back, <?= htmlspecialchars($username) ?>!</h1>
      <p>Monitor your café's performance and manage everything from one place.</p>
    </div>

    <!-- QUICK STATS -->
    <h2 class="section-title"><i class='bx bxs-dashboard'></i> Quick Stats</h2>
    <div class="stats-grid">
      <div class="stat-box">
        <i class='bx bxs-user-detail icon'></i>
        <div class="label">Total Users</div>
        <div class="value"><?= $totalUsers ?></div>
      </div>
      
      <div class="stat-box">
        <i class='bx bxs-group icon'></i>
        <div class="label">Customers</div>
        <div class="value"><?= $totalCustomers ?></div>
      </div>
      
      <div class="stat-box">
        <i class='bx bxs-user-voice icon'></i>
        <div class="label">Staff / Admin</div>
        <div class="value"><?= $totalStaff ?></div>
      </div>
      
      <div class="stat-box">
        <i class='bx bxs-shopping-bag icon'></i>
        <div class="label">Total Products</div>
        <div class="value"><?= $totalProducts ?></div>
      </div>
      
      <div class="stat-box">
        <i class='bx bxs-receipt icon'></i>
        <div class="label">Total Orders</div>
        <div class="value"><?= $totalOrders ?></div>
      </div>
      
      <div class="stat-box">
        <i class='bx bxs-error icon'></i>
        <div class="label">Low Stock Items</div>
        <div class="value"><?= $lowStockItems ?></div>
      </div>
    </div>

    <!-- MANAGEMENT SECTIONS -->
    <h2 class="section-title"><i class='bx bxs-cog'></i> Management</h2>
    <div class="management-grid">
      <a href="products.php" class="management-card">
        <div class="card-icon">
          <i class='bx bxs-coffee'></i>
        </div>
        <div class="card-content">
          <h3>Menu Management</h3>
          <p>Add, edit, or remove café products. Manage your menu items and pricing.</p>
        </div>
      </a>

      <a href="orders.php" class="management-card">
        <div class="card-icon">
          <i class='bx bxs-cart'></i>
        </div>
        <div class="card-content">
          <h3>Orders</h3>
          <p>Track current and past orders. Update order status and manage payments.</p>
        </div>
      </a>

      <a href="users.php" class="management-card">
        <div class="card-icon">
          <i class='bx bxs-user-account'></i>
        </div>
        <div class="card-content">
          <h3>User Management</h3>
          <p>View, promote, or remove registered users. Manage roles and permissions.</p>
        </div>
      </a>

      <a href="stock_management.php" class="management-card">
        <div class="card-icon">
          <i class='bx bxs-package'></i>
        </div>
        <div class="card-content">
          <h3>Stock Management</h3>
          <p>Monitor inventory levels. Update stock quantities and view low stock alerts.</p>
        </div>
      </a>
    </div>

    <!-- QUICK ACTIONS -->
    <h2 class="section-title"><i class='bx bxs-zap'></i> Quick Actions</h2>
    <div class="quick-actions">
      <a href="add_product.php" class="action-button">
        <i class='bx bx-plus-circle'></i>
        Add New Product
      </a>
      
      <a href="orders.php" class="action-button">
        <i class='bx bx-list-ul'></i>
        View All Orders
      </a>
      
      <a href="stock_management.php" class="action-button">
        <i class='bx bx-package'></i>
        Manage Stock
      </a>
      
      <a href="users.php" class="action-button">
        <i class='bx bx-user-plus'></i>
        View Users
      </a>
    </div>
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