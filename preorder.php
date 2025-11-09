<?php
// preorder.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once "cafe.php";

function jexit($ok, $msg = '', $extra = []) {
    echo json_encode(array_merge(['ok' => $ok, 'message' => $msg], $extra));
    exit;
}

// Require login (you can allow guests later if you want)
if (empty($_SESSION['user_id'])) {
    jexit(false, 'Please log in to place a pre-order.');
}

$user_id    = (int)$_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty        = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
$note       = isset($_POST['note']) ? trim($_POST['note']) : '';

if ($product_id <= 0 || $qty <= 0) {
    jexit(false, 'Invalid product or quantity.');
}

// Check product exists
$sql = "SELECT product_id, product_name, stock_qty, status FROM products WHERE product_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id); // OK with BIGINT on 64-bit PHP
$stmt->execute();
$res = $stmt->get_result();
if (!$res || !$res->num_rows) {
    jexit(false, 'Product not found.');
}
$p = $res->fetch_assoc();
$stmt->close();

/*
 * Your enums:
 *   status: 'In Stock' | 'Sold Out'
 * We allow pre-order only when status = 'Sold Out' OR stock_qty = 0
 */
if (!($p['status'] === 'Sold Out' || (int)$p['stock_qty'] === 0)) {
    jexit(false, 'This item is currently available. Please add to cart instead.');
}

// Insert preorder
$ins = $conn->prepare("INSERT INTO preorders (product_id, user_id, qty, note) VALUES (?, ?, ?, ?)");
$ins->bind_param("iiis", $product_id, $user_id, $qty, $note);
if (!$ins->execute()) {
    jexit(false, 'Failed to create pre-order. Please try again.');
}
$preorder_id = $ins->insert_id;
$ins->close();

jexit(true, 'Pre-order placed!', [
    'preorder_id' => $preorder_id,
    'product_name' => $p['product_name']
]);
