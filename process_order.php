<?php
session_start();
require_once "cafe.php";

function jsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Please log in to place an order.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method.');
}

$userId = $_SESSION['user_id'];
$cartData = isset($_POST['cart']) ? json_decode($_POST['cart'], true) : [];
$total = floatval($_POST['total'] ?? 0);

if (empty($cartData) || $total <= 0) {
    jsonResponse(false, 'Invalid cart data.');
}

$conn->begin_transaction();

try {
    // 1. Create the order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Pending')");
    $stmt->bind_param("id", $userId, $total);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to create order: " . $stmt->error);
    }
    
    $orderId = $stmt->insert_id;
    $stmt->close();

    // 2. Process each cart item
    foreach ($cartData as $item) {
        $productName = trim($item['name']);
        $quantity = intval($item['qty']);
        $price = floatval($item['price']);
        $subtotal = $price * $quantity;

        // Get product ID and current stock
        $stmt = $conn->prepare("SELECT product_id, stock_qty, product_name FROM products WHERE product_name = ? LIMIT 1");
        $stmt->bind_param("s", $productName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Product not found: $productName");
        }
        
        $product = $result->fetch_assoc();
        $productId = $product['product_id'];
        $currentStock = intval($product['stock_qty']);
        $stmt->close();

        // Check if enough stock available
        if ($currentStock < $quantity) {
            throw new Exception("Insufficient stock for {$product['product_name']}. Available: $currentStock, Requested: $quantity");
        }

        // 3. Insert order item
        $stmt = $conn->prepare("INSERT INTO orderitems (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $orderId, $productId, $quantity, $subtotal);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to create order item: " . $stmt->error);
        }
        $stmt->close();

        // 4. Deduct stock
        $newStock = $currentStock - $quantity;
        $stmt = $conn->prepare("UPDATE products SET stock_qty = ? WHERE product_id = ?");
        $stmt->bind_param("ii", $newStock, $productId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update stock for {$product['product_name']}: " . $stmt->error);
        }
        $stmt->close();

        // 5. Update availability if stock reaches 0
        if ($newStock <= 0) {
            $stmt = $conn->prepare("UPDATE products SET availability = 0, stock_qty = 0 WHERE product_id = ?");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->commit();
    
    jsonResponse(true, 'Order placed successfully!', [
        'order_id' => $orderId,
        'redirect' => 'payment.php'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    jsonResponse(false, $e->getMessage());
}
?>