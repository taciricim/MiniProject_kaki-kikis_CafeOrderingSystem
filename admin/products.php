<?php
session_start();
require_once "../cafe.php";

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: ../login.php");
    exit;
}

$products = $conn->query("SELECT * FROM products ORDER BY product_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>manage products | ngopi admin.</title>
      <link rel="stylesheet" href="admin.css">
      <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  </head>
  <body>

  <header class="header">
    <a href="dashboard.php" class="logo">ngopi admin.</a>
    
    <div class="header-center">
      <span class="greeting">hi, <?= htmlspecialchars($username) ?></span>
    </div>

    <nav class="navbar">
      <a href="dashboard.php">dashboard</a>
      <a href="products.php" class="active">menu</a>
      <a href="orders.php">orders</a>
      <a href="users.php">users</a>
      <a href="stock_management.php">stock</a>
      <a href="../logout.php" title="Log out"><i class='bx bx-log-out'></i></a>
    </nav>
  </header>

  <div class="page-wrap">
      <div class="top-bar">
          <div class="top-left">
              <h2><i class='bx bx-store-alt'></i> Products</h2>
              <div class="sub">Manage your cafe menu items</div>
          </div>
      </div>

      <div class="action-buttons">
          <a href="add_product.php" class="btn-primary">
              <i class='bx bx-plus-circle'></i> Add New Product
          </a>
          <a href="stock_management.php" class="btn-secondary">
              <i class='bx bx-package'></i> Manage Stock Levels
          </a>
      </div>

      <div class="table-card">
          <table>
              <thead>
                  <tr>
                      <th>Image</th>
                      <th>Product Name</th>
                      <th>Price</th>
                      <th>Stock</th>
                      <th>Status</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody>
                  <?php while ($p = $products->fetch_assoc()): ?>
                      <?php
                      $stock = intval($p['stock_qty']);
                      $stockClass = 'stock-ok';
                      $stockText = 'In Stock';
                      
                      if ($stock === 0) {
                          $stockClass = 'stock-out';
                          $stockText = 'Out of Stock';
                      } elseif ($stock <= 5) {
                          $stockClass = 'stock-low';
                          $stockText = 'Low Stock';
                      }
                      
                      $imgPath = $p['image_path'] ? '../' . htmlspecialchars($p['image_path']) : '../images/f1.jpeg';
                      ?>
                      <tr>
                          <td>
                              <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($p['product_name']) ?>" 
                                  style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                          </td>
                          <td><strong><?= htmlspecialchars($p['product_name']) ?></strong></td>
                          <td>RM<?= number_format($p['price'], 2) ?></td>
                          <td><?= $stock ?></td>
                          <td><span class="stock-indicator <?= $stockClass ?>"><?= $stockText ?></span></td>
                          <td class="actions-cell">
                              <a href="edit_product.php?id=<?= $p['product_id'] ?>">
                                  <i class='bx bx-edit'></i> Edit
                              </a>
                              <a href="delete_product.php?id=<?= $p['product_id'] ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this product?');">
                                Delete
                              </a>

                          </td>
                      </tr>
                  <?php endwhile; ?>
              </tbody>
          </table>
      </div>

      <a href="dashboard.php" class="back-link" style="margin-top: 2rem; display: inline-flex;">
          <i class='bx bx-arrow-back'></i> Back to Dashboard
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