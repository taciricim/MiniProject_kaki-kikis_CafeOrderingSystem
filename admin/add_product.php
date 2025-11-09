<?php
session_start();
require_once "../cafe.php";

// Check admin/staff access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: ../login.php");
    exit;
}

$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = trim($_POST['product_name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stockQty = intval($_POST['stock_qty'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $availability = $stockQty > 0 ? 1 : 0;
    
    // Validate inputs
    if (empty($productName)) {
        $error = "Product name is required.";
    } elseif ($price <= 0) {
        $error = "Price must be greater than 0.";
    } elseif ($stockQty < 0) {
        $error = "Stock quantity cannot be negative.";
    } else {
        // Handle image upload
        $imagePath = null;
        
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../images/products/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                $error = "Invalid file type. Allowed: JPG, PNG, WEBP, GIF";
            } else {
                // Generate unique filename
                $uniqueName = 'p_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExtension;
                $targetFile = $uploadDir . $uniqueName;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
                    $imagePath = 'images/products/' . $uniqueName;
                } else {
                    $error = "Failed to upload image.";
                }
            }
        }
        
        // Insert product if no errors
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO products (product_name, price, stock_qty, availability, description, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdiiss", $productName, $price, $stockQty, $availability, $description, $imagePath);
            
            if ($stmt->execute()) {
                $success = "Product added successfully!";
                // Clear form
                $productName = $price = $stockQty = $description = "";
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>add product | ngopi admin.</title>
        <link rel="stylesheet" href="admin.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    </head>
    <body>

    <div class="admin-header-bar">
        <div class="admin-header-left">Add New Product</div>
        <div class="admin-header-right">
            <span><?= htmlspecialchars($_SESSION['username']) ?></span>
            <span class="role-badge"><?= htmlspecialchars($_SESSION['role']) ?></span>
            <a href="../logout.php" style="color: inherit; text-decoration: none;"><i class='bx bx-log-out'></i></a>
        </div>
    </div>

    <div class="page-wrap">
        <div class="top-bar">
            <div class="top-left">
                <h2><i class='bx bx-plus-circle'></i> Add New Product</h2>
                <div class="sub">Fill in the details to add a new menu item</div>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert-success">
                <i class='bx bx-check-circle'></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error">
                <i class='bx bx-error-circle'></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name *</label>
                    <input type="text" id="product_name" name="product_name" 
                        placeholder="e.g., Caramel Macchiato" required
                        value="<?= isset($productName) ? htmlspecialchars($productName) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="price">Price (RM) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0.01" 
                        placeholder="e.g., 12.50" required
                        value="<?= isset($price) ? $price : '' ?>">
                    <div class="hint">Enter price in Ringgit Malaysia (RM)</div>
                </div>

                <div class="form-group">
                    <label for="stock_qty">Stock Quantity *</label>
                    <input type="number" id="stock_qty" name="stock_qty" min="0" 
                        placeholder="e.g., 50" required
                        value="<?= isset($stockQty) ? $stockQty : '' ?>">
                    <div class="hint">Set to 0 if out of stock</div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" 
                            placeholder="Brief description of the product..."><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                    <div class="hint">Optional: Add a short description (max 500 characters)</div>
                </div>

                <div class="form-group">
                    <label>Product Image</label>
                    <div class="file-input-wrapper">
                        <label for="product_image" class="file-input-label">
                            <i class='bx bx-cloud-upload'></i>
                            <span id="file-name">Click to upload image</span>
                        </label>
                        <input type="file" id="product_image" name="product_image" 
                            accept="image/jpeg,image/png,image/webp,image/gif">
                    </div>
                    <div class="hint">Supported: JPG, PNG, WEBP, GIF (Max 5MB)</div>
                    <img id="imagePreview" class="image-preview" alt="Preview">
                </div>

                <button type="submit" class="btn-submit">
                    <i class='bx bx-plus-circle'></i> Add Product
                </button>
            </form>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="products.php" class="back-link"><i class='bx bx-arrow-back'></i> Back to Products</a>
            <a href="dashboard.php" class="back-link"><i class='bx bx-home'></i> Dashboard</a>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="social-icons">
        <a href="#"><i class="bx bxl-facebook"></i></a>
        <a href="#"><i class="bx bxl-instagram-alt"></i></a>
        <a href="#"><i class="bx bxl-twitter"></i></a>
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

    <script>
    // Image preview functionality
    const fileInput = document.getElementById('product_image');
    const fileNameSpan = document.getElementById('file-name');
    const imagePreview = document.getElementById('imagePreview');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const fileName = this.files[0].name;
            fileNameSpan.textContent = fileName;
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            fileNameSpan.textContent = 'Click to upload image';
            imagePreview.style.display = 'none';
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const price = parseFloat(document.getElementById('price').value);
        const stockQty = parseInt(document.getElementById('stock_qty').value);
        
        if (price <= 0) {
            alert('Price must be greater than 0');
            e.preventDefault();
            return false;
        }
        
        if (stockQty < 0) {
            alert('Stock quantity cannot be negative');
            e.preventDefault();
            return false;
        }
    });
    </script>

    </body>
</html>