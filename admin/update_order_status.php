<?php
require_once "../cafe.php";

if (isset($_POST['order_id'], $_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $status = trim($_POST['action']);

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo "Order #$order_id updated to $status";
        exit;
    }

    header("Location: orders.php");
    exit;
} else {
    echo "Invalid request.";
}
?>
