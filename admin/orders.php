<?php
require_once "auth_check.php";
require_once "../cafe.php";

$orders = [];
$sql = "
    SELECT 
        o.order_id,
        o.user_id,
        o.order_date,
        o.total_amount,
        o.status,
        o.screenshot,
        u.username,
        u.email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_id DESC
";
if ($res = $conn->query($sql)) {
    while ($row = $res->fetch_assoc()) $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>manage orders | ngopi admin.</title>
    <link rel="stylesheet" href="admin.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>

  <!-- HEADER -->
  <header class="header">
    <a href="dashboard.php" class="logo">ngopi admin.</a>
    
    <div class="header-center">
      <span class="greeting">hi, <?= htmlspecialchars($username) ?></span>
    </div>

    <nav class="navbar">
      <a href="dashboard.php">dashboard</a>
      <a href="products.php">menu</a>
      <a href="orders.php" class="active">orders</a>
      <a href="users.php">users</a>
      <a href="stock_management.php">stock</a>
      <a href="../logout.php" title="Log out"><i class='bx bx-log-out'></i></a>
    </nav>
  </header>

  <div class="page-wrap">
    <div class="top-bar">
      <div class="top-left">
        <h2><i class='bx bx-receipt'></i> Orders</h2>
        <div class="sub">All recent customer orders.</div>
      </div>
    </div>

    <div class="table-card">
      <table>
        <thead>
          <tr>
            <th style="width:60px;">ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th style="width:100px;">Total (RM)</th>
            <th style="width:160px;">Status / Action</th>
            <th style="width:160px;">Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$orders): ?>
            <tr>
              <td colspan="6" style="text-align:center; padding:2rem 0; color:#999;">No orders yet.</td>
            </tr>
          <?php else: foreach ($orders as $o): ?>
            <tr>
              <td>#<?= htmlspecialchars($o['order_id']) ?></td>
              <td>
                <?php
                if (!empty($o['username'])) {
                  echo htmlspecialchars($o['username']);
                } elseif (!empty($o['user_id'])) {
                  echo "User #" . htmlspecialchars($o['user_id']);
                } else {
                  echo "Guest";
                }
                ?>
              </td>
              <td><?= htmlspecialchars($o['email'] ?? '') ?></td>
              <td>RM<?= number_format($o['total_amount'], 2) ?></td>
              <td>
                <?php if (strtolower($o['status']) === 'pending'): ?>
                  <button class="review-btn btn-secondary"
                          data-id="<?= $o['order_id'] ?>"
                          data-screenshot="<?= htmlspecialchars($o['screenshot']) ?>"
                          style="padding: 0.5rem 1rem; font-size: 1.2rem; cursor: pointer;">
                    Review Screenshot
                  </button>
                <?php else: ?>
                  <?php
                  $statusText = ucfirst(strtolower(trim($o['status'])));
                  $statusClass = 'status-' . $statusText;
                  ?>
                  <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($o['status']) ?></span>
                <?php endif; ?>
              </td>
              <td><?= date('M d, Y H:i', strtotime($o['order_date'])) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <a class="back-link" href="dashboard.php">
      <i class='bx bx-left-arrow-alt'></i> Back to Dashboard
    </a>
  </div>

  <!-- Screenshot Review Modal -->
  <div class="modal fade" id="screenshotModal" tabindex="-1" aria-labelledby="screenshotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="screenshotModalLabel">Payment Screenshot Review</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="screenshotImage" src="" alt="Payment Screenshot" class="img-fluid rounded shadow-sm">
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-success" id="approveBtn">Approve</button>
          <button type="button" class="btn btn-danger" id="rejectBtn">Reject</button>
        </div>
      </div>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const reviewButtons = document.querySelectorAll('.review-btn');
    const screenshotImage = document.getElementById('screenshotImage');
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    let currentOrderId = null;

    reviewButtons.forEach(button => {
      button.addEventListener('click', () => {
        const screenshot = button.getAttribute('data-screenshot');
        currentOrderId = button.getAttribute('data-id');

        // Show screenshot from /cafe/uploads/
        screenshotImage.src = '../uploads/' + screenshot;
        const modal = new bootstrap.Modal(document.getElementById('screenshotModal'));
        modal.show();
      });
    });

    // Handle Approve / Reject
    approveBtn.addEventListener('click', () => updateStatus('Approved'));
    rejectBtn.addEventListener('click', () => updateStatus('Rejected'));

    function updateStatus(status) {
      fetch('update_order_status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
          order_id: currentOrderId,
          action: status
        })
      })
      .then(response => response.text())
      .then(data => {
        alert(data);
        location.reload();
      })
      .catch(err => console.error(err));
    }
  });
  </script>

  </body>
  </html>