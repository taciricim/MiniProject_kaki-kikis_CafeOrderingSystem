<?php
session_start();
require_once "cafe.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$isLoggedIn = true;
$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'customer';

// Fetch user information
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userInfo = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Check which table name exists (orderitems vs order_items)
$orderItemsTable = 'orderitems'; // default
$result = $conn->query("SHOW TABLES LIKE 'order_items'");
if ($result && $result->num_rows > 0) {
    $orderItemsTable = 'order_items';
}

// Check which column name exists for screenshot
$screenshotColumn = 'screenshot'; // default
$result = $conn->query("SHOW COLUMNS FROM orders LIKE 'screenshot_path'");
if ($result && $result->num_rows > 0) {
    $screenshotColumn = 'screenshot_path';
}

// Fetch user's order history with items
$ordersQuery = "
    SELECT 
        o.order_id,
        o.order_date,
        o.total_amount,
        o.status,
        o.$screenshotColumn as screenshot_path
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
";
$stmt = $conn->prepare($ordersQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$ordersResult = $stmt->get_result();

// Store orders with their items
$orders = [];
while ($order = $ordersResult->fetch_assoc()) {
    // Fetch items for this order
    $itemsQuery = "
        SELECT 
            oi.quantity,
            oi.subtotal,
            p.product_name,
            p.price,
            p.image_path
        FROM $orderItemsTable oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ";
    $itemStmt = $conn->prepare($itemsQuery);
    $itemStmt->bind_param("i", $order['order_id']);
    $itemStmt->execute();
    $itemsResult = $itemStmt->get_result();
    
    $order['items'] = [];
    while ($item = $itemsResult->fetch_assoc()) {
        $order['items'][] = $item;
    }
    $itemStmt->close();
    
    $orders[] = $order;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profile | ngopi grounds.</title>
    <link rel="stylesheet" href="style.css"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>

<?php include 'nav.php'; ?>

<section class="profile-section">
    <div class="profile-container">
        
        <!-- Profile Header -->
        <div class="profile-header">
            <h1>Welcome back, <?= htmlspecialchars($username) ?>!</h1>
            
            <div class="profile-info">
                <div class="info-item">
                    <i class='bx bx-user'></i>
                    <span><strong>Username:</strong> <?= htmlspecialchars($userInfo['username']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-envelope'></i>
                    <span><strong>Email:</strong> <?= htmlspecialchars($userInfo['email']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-receipt'></i>
                    <span><strong>Total Orders:</strong> <?= count($orders) ?></span>
                </div>
            </div>
        </div>

        <!-- Order History -->
        <div class="orders-section">
            <h2><i class='bx bx-receipt'></i> Order History</h2>
            
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">
                                Order #<?= $order['order_id'] ?>
                            </div>
                            <span class="order-status status-<?= strtolower($order['status']) ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                        
                        <div class="order-summary">
                            <div class="order-detail">
                                <i class='bx bx-calendar'></i>
                                <strong>Date:</strong> <?= date('M d, Y', strtotime($order['order_date'])) ?>
                            </div>
                            <div class="order-detail">
                                <i class='bx bx-time'></i>
                                <strong>Time:</strong> <?= date('h:i A', strtotime($order['order_date'])) ?>
                            </div>
                            <div class="order-detail">
                                <i class='bx bx-shopping-bag'></i>
                                <strong>Items:</strong> <?= count($order['items']) ?>
                            </div>
                            <div class="order-detail">
                                <i class='bx bx-money'></i>
                                <strong>Total:</strong> RM <?= number_format($order['total_amount'], 2) ?>
                            </div>
                        </div>
                        
                        <div class="order-actions">
                            <button class="btn-small btn-toggle" onclick="toggleOrderItems(<?= $order['order_id'] ?>)">
                                <i class='bx bx-chevron-down' id="icon-<?= $order['order_id'] ?>"></i> 
                                <span id="text-<?= $order['order_id'] ?>">View Items</span>
                            </button>
                            
                            <?php if (strtolower($order['status']) == 'pending' && empty($order['screenshot_path'])): ?>
                                <a href="payment.php?order_id=<?= $order['order_id'] ?>&total=<?= $order['total_amount'] ?>" class="btn-small btn-pay">
                                    <i class='bx bx-credit-card'></i> Complete Payment
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Order Items (Initially Hidden) -->
                        <div class="order-items" id="items-<?= $order['order_id'] ?>">
                            <div class="items-header">
                                <i class='bx bx-package'></i> Order Items
                            </div>
                            
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="item-card">
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="item-image">
                                    <?php else: ?>
                                        <div class="item-image" style="background: #eee; display: flex; align-items: center; justify-content: center;">
                                            <i class='bx bx-image' style="font-size: 3rem; color: #999;"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="item-info">
                                        <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                        <div class="item-details">
                                            <span><i class='bx bx-purchase-tag'></i> RM <?= number_format($item['price'], 2) ?> each</span>
                                            <span><i class='bx bx-shopping-bag'></i> Qty: <?= $item['quantity'] ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="item-subtotal">
                                        RM <?= number_format($item['subtotal'], 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="order-total-display">
                                <span class="total-label">Order Total:</span>
                                <span class="total-amount">RM <?= number_format($order['total_amount'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-orders">
                    <i class='bx bx-cart-alt'></i>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders yet. Start shopping to see your order history here!</p>
                    <a href="menu.php" class="btn">Browse Menu</a>
                </div>
            <?php endif; ?>
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

    <p class="copyright">
        © ngopi grounds. | All Rights Reserved.
    </p>
</footer>

<script>
    const IS_LOGGED_IN     = true;
    const USER_ROLE        = "<?php echo $userRole; ?>";
    const CURRENT_USER_ID  = "<?php echo $userId; ?>";
    const CURRENT_USERNAME = "<?php echo $username; ?>";
    
    function toggleOrderItems(orderId) {
        const itemsDiv = document.getElementById('items-' + orderId);
        const icon = document.getElementById('icon-' + orderId);
        const text = document.getElementById('text-' + orderId);
        
        if (itemsDiv.classList.contains('expanded')) {
            itemsDiv.classList.remove('expanded');
            icon.classList.remove('bx-chevron-up');
            icon.classList.add('bx-chevron-down');
            text.textContent = 'View Items';
        } else {
            itemsDiv.classList.add('expanded');
            icon.classList.remove('bx-chevron-down');
            icon.classList.add('bx-chevron-up');
            text.textContent = 'Hide Items';
        }
    }
</script>

<script src="script.js"></script>

</body>
</html>