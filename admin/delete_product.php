<?php
require_once "auth_check.php";
require_once "../cafe.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php?msg=Invalid+product+ID");
    exit;
}

$product_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT image_path FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: products.php?msg=Product+not+found");
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

if (!empty($product['image_path'])) {
    $imageFile = "../" . $product['image_path'];
    if (file_exists($imageFile)) {
        @unlink($imageFile);
    }
}

$stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
if ($stmt->execute()) {
    $msg = "Product deleted successfully.";
} else {
    $msg = "Failed to delete product. Error: " . $stmt->error;
}
$stmt->close();

header("Location: products.php?msg=" . urlencode($msg));
exit;

?>
