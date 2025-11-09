<?php
session_start();
require_once "../cafe.php";

// Check admin/staff access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: ../login.php");
    exit;
}

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_stock') {
        $productId = intval($_POST['product_id']);
        $newStock = intval($_POST['stock_qty']);
        
        $availability = $newStock > 0 ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE products SET stock_qty = ?, availability = ? WHERE product_id = ?");
        $stmt->bind_param("iii", $newStock, $availability, $productId);
        
        if ($stmt->execute()) {
            $success = "Stock updated successfully!";
        } else {
            $error = "Failed to update stock.";
        }
        $stmt->close();
    }
}

// Get all products
$products = $conn->query("SELECT * FROM products ORDER BY product_name ASC");

// Get recent orders with stock impact
$recentOrders = $conn->query("
    SELECT o.order_id, o.order_date, o.total_amount, o.status,
           u.username,
           GROUP_CONCAT(CONCAT(p.product_name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    LEFT JOIN orderitems oi ON o.order_id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.product_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>manage stock | ngopi admin.</title>
        <link rel="stylesheet" href="admin.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <style>
            .stock-low { background: #fff5d6; color: #7c4a00; padding: 0.3rem 0.6rem; border-radius: 0.5rem; font-weight: 600; }
            .stock-out { background: #ffe1e1; color: #9c0000; padding: 0.3rem 0.6rem; border-radius: 0.5rem; font-weight: 600; }
            .stock-ok { background: #c8ffd1; color: #137a00; padding: 0.3rem 0.6rem; border-radius: 0.5rem; font-weight: 600; }
            .quick-update { display: flex; gap: 0.5rem; align-items: center; }
            .quick-update input { width: 70px; padding: 0.4rem; border: 1px solid #bbb; border-radius: 0.3rem; }
            .quick-update button { padding: 0.4rem 0.8rem; background: #7c1c1c; color: #fff; border: none; border-radius: 0.3rem; cursor: pointer; font-size: 0.85rem; }
            .alert-success { background: #c8ffd1; color: #137a00; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
            .alert-error { background: #ffe1e1; color: #9c0000; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
        </style>
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
      <a href="users.php">users</a>
      <a href="stock_management.php" class="active">stock</a>
      <a href="../logout.php" title="Log out"><i class='bx bx-log-out'></i></a>
    </nav>
  </header>

    <div class="page-wrap">
        <?php if (isset($success)): ?>
            <div class="alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="section-card">
            <h3><i class='bx bx-package'></i> Product Stock Levels</h3>
            
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Update Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $products->fetch_assoc()): ?>
                            <?php
                            $stock = intval($p['stock_qty']);
                            $statusClass = 'stock-ok';
                            $statusText = 'In Stock';
                            
                            if ($stock === 0) {
                                $statusClass = 'stock-out';
                                $statusText = 'Out of Stock';
                            } elseif ($stock <= 5) {
                                $statusClass = 'stock-low';
                                $statusText = 'Low Stock';
                            }
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['product_name']) ?></strong></td>
                                <td><?= $stock ?></td>
                                <td><span class="<?= $statusClass ?>"><?= $statusText ?></span></td>
                                <td>RM<?= number_format($p['price'], 2) ?></td>
                                <td>
                                    <form method="POST" class="quick-update">
                                        <input type="hidden" name="action" value="update_stock">
                                        <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                                        <input type="number" name="stock_qty" value="<?= $stock ?>" min="0" required>
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-card">
            <h3><i class='bx bx-history'></i> Recent Orders (Stock Impact)</h3>
            
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items Sold</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recentOrders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $order['order_id'] ?></td>
                                <td><?= date('M d, Y H:i', strtotime($order['order_date'])) ?></td>
                                <td><?= htmlspecialchars($order['username'] ?? 'Guest') ?></td>
                                <td><?= htmlspecialchars($order['items'] ?? 'N/A') ?></td>
                                <td>RM<?= number_format($order['total_amount'], 2) ?></td>
                                <td><span class="status-badge status-<?= $order['status'] ?>"><?= $order['status'] ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="dashboard.php" class="back-link"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>
            <a href="products.php" class="back-link"><i class='bx bx-store-alt'></i> View Products</a>
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