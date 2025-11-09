<?php
require_once "auth_check.php";
require_once "../cafe.php";

/* Filter by status (optional) */
$filter = $_GET['status'] ?? '';
$where  = '';
if ($filter !== '' && in_array($filter, ['new','confirmed','fulfilled','cancelled'], true)) {
  $where = "WHERE p.status = '" . $conn->real_escape_string($filter) . "'";
}

/* Update status quick action */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['preorder_id'], $_POST['new_status'])) {
  $pid = (int)$_POST['preorder_id'];
  $ns  = $_POST['new_status'];
  if (in_array($ns, ['new','confirmed','fulfilled','cancelled'], true)) {
    $stmt = $conn->prepare("UPDATE preorders SET status=? WHERE preorder_id=?");
    $stmt->bind_param("si", $ns, $pid);
    $stmt->execute();
    header("Location: preorders.php?status=" . urlencode($filter));
    exit;
  }
}

/* Fetch preorders (with product & user info) */
$sql = "
  SELECT
    p.preorder_id, p.product_id, p.user_id, p.qty, p.note, p.status,
    p.created_at, p.updated_at,
    pr.product_name,
    u.username, u.email, u.phone_number
  FROM preorders p
  LEFT JOIN products pr ON pr.product_id = p.product_id
  LEFT JOIN users u     ON u.user_id    = p.user_id
  $where
  ORDER BY p.preorder_id DESC
";
$rows = [];
if ($res = $conn->query($sql)) {
  while ($r = $res->fetch_assoc()) $rows[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>pre-orders | ngopi admin.</title>
<link rel="stylesheet" href="admin.css" />
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
</head>
<body>

<header class="admin-header-bar">
  <div class="admin-header-left">ngopi admin.</div>
  <div class="admin-header-right">
    <div>hi, <?= htmlspecialchars($username) ?></div>
    <div class="role-badge"><?= htmlspecialchars($role) ?></div>
    <a href="../logout.php" title="Log out" style="color:#7c1c1c;"><i class='bx bx-log-out'></i></a>
  </div>
</header>

<div class="page-wrap">

  <div class="top-bar">
    <div class="top-left">
      <h2><i class='bx bx-time-five'></i> pre-orders</h2>
      <div class="sub">Reservations for sold-out items.</div>
    </div>
    <div class="top-right" style="display:flex;gap:.6rem;flex-wrap:wrap;">
      <a class="admin-link-btn" href="preorders.php">all</a>
      <a class="admin-link-btn" href="preorders.php?status=new">new</a>
      <a class="admin-link-btn" href="preorders.php?status=confirmed">confirmed</a>
      <a class="admin-link-btn" href="preorders.php?status=fulfilled">fulfilled</a>
      <a class="admin-link-btn" href="preorders.php?status=cancelled">cancelled</a>
    </div>
  </div>

  <div class="table-card">
    <table>
      <thead>
        <tr>
          <th style="width:70px;">ID</th>
          <th>Product</th>
          <th>Customer</th>
          <th style="width:80px;">Qty</th>
          <th style="width:140px;">Status</th>
          <th>Note</th>
          <th style="width:180px;">Created</th>
          <th style="width:220px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="8" style="text-align:center; padding:2rem 0; color:#999;">no pre-orders yet.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td>#<?= (int)$r['preorder_id'] ?></td>
            <td>
              <?= htmlspecialchars($r['product_name'] ?? '—') ?>
              <div class="muted" style="font-size:.8rem;">PID: <?= (int)$r['product_id'] ?></div>
            </td>
            <td>
              <?= htmlspecialchars($r['username'] ?? ('User #'.(int)$r['user_id'])) ?><br>
              <span class="muted" style="font-size:.8rem;">
                <?= htmlspecialchars($r['email'] ?? '') ?>
                <?php if (!empty($r['phone_number'])): ?> · <?= htmlspecialchars($r['phone_number']) ?><?php endif; ?>
              </span>
            </td>
            <td><?= (int)$r['qty'] ?></td>
            <td>
              <?php
                $map = [
                  'new'       => 'status-Pending',
                  'confirmed' => 'status-Preparing',
                  'fulfilled' => 'status-Completed',
                  'cancelled' => 'status-Cancelled'
                ];
                $cls = $map[$r['status']] ?? 'status-Pending';
              ?>
              <span class="status-badge <?= $cls ?>"><?= htmlspecialchars($r['status']) ?></span>
            </td>
            <td><?= htmlspecialchars($r['note'] ?? '') ?></td>
            <td>
              <div><?= htmlspecialchars($r['created_at']) ?></div>
              <div class="muted" style="font-size:.8rem;">upd: <?= htmlspecialchars($r['updated_at']) ?></div>
            </td>
            <td class="actions-cell">
              <form method="post" style="display:inline-flex; gap:.5rem; align-items:center;">
                <input type="hidden" name="preorder_id" value="<?= (int)$r['preorder_id'] ?>">
                <select name="new_status" style="padding:.4rem;border:1px solid #ddd;border-radius:.5rem;">
                  <option value="new"       <?= $r['status']==='new'?'selected':''; ?>>new</option>
                  <option value="confirmed" <?= $r['status']==='confirmed'?'selected':''; ?>>confirmed</option>
                  <option value="fulfilled" <?= $r['status']==='fulfilled'?'selected':''; ?>>fulfilled</option>
                  <option value="cancelled" <?= $r['status']==='cancelled'?'selected':''; ?>>cancelled</option>
                </select>
                <button class="admin-link-btn" style="padding:.5rem .8rem; border-radius:.5rem; box-shadow:none;">update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <a class="back-link" href="dashboard.php"><i class='bx bx-left-arrow-alt'></i> back to dashboard</a>
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
