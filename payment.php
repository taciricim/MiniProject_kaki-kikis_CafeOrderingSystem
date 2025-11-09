<?php
session_start();
require_once "cafe.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$total = isset($_GET['total']) ? floatval($_GET['total']) : 0;

// Verify order belongs to user
if ($orderId > 0) {
    $stmt = $conn->prepare("SELECT total_amount FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $orderId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("Invalid order.");
    }
    
    $order = $result->fetch_assoc();
    $total = $order['total_amount'];
    $stmt->close();
} else {
    die("Invalid order ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> | ngopi grounds.</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  </head>
  <body>

  <header class="header">
    <a href="index.php" class="logo">ngopi grounds.</a>
  </header>

  <section class="payment-section">
    <div class="payment-card">
      <div class="order-info">
        <p><strong>Order #<?php echo $orderId; ?></strong></p>
        <p><strong>Total to Pay:</strong> RM <?php echo number_format($total, 2); ?></p>
      </div>

      <h2>Complete Your Payment</h2>
      <p>Please scan the QR code below to make your payment.</p>

      <img src="images/bankqr.jpg" alt="Bank QR Code">

      <div class="bank-details">
        <p><strong>Bank Name:</strong> CIMB Bank</p>
        <p><strong>Account Holder:</strong> MUHAMMAD DANISH</p>
        <p><strong>Account Number:</strong> 2991 8806 3401</p>
        <p><strong>Reference:</strong> Order #<?php echo $orderId; ?></p>
      </div>

      <div class="upload-section">
        <h3>Upload Your Payment Screenshot</h3>
        <form action="order_pending.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="order_id" value="<?= $orderId ?>">
          <label>Upload Payment Screenshot:</label><br>
          <input type="file" name="screenshot" accept="image/*" required><br><br>
          <button type="submit" class="btn-submit">Submit Screenshot</button>
        </form>
      </div>
    </div>
  </section>

  <footer class="footer">
    <p>© ngopi grounds. | All Rights Reserved.</p>
  </footer>

  </body>
</html>