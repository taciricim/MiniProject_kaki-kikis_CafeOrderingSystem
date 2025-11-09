<?php
session_start();
require_once "cafe.php";

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userId     = $isLoggedIn ? $_SESSION['user_id'] : 'guest';
$userRole   = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$username   = isset($_SESSION['username']) ? $_SESSION['username'] : '';

function clean($v) { 
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); 
}

$LOW_STOCK_THRESHOLD = 5;

// Get products with stock
$availableProducts = [];
$sqlAvailable = "
    SELECT product_id, product_name, price, stock_qty, description, image_path
    FROM products
    WHERE stock_qty > 0
    ORDER BY product_name ASC
";

if ($result = $conn->query($sqlAvailable)) {
    while ($row = $result->fetch_assoc()) {
        $availableProducts[] = $row;
    }
}

// Get sold out products (stock = 0)
$soldOutProducts = [];
$sqlSoldOut = "
    SELECT product_id, product_name, price, stock_qty, description, image_path
    FROM products
    WHERE stock_qty = 0
    ORDER BY product_name ASC
";

if ($result = $conn->query($sqlSoldOut)) {
    while ($row = $result->fetch_assoc()) {
        $soldOutProducts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>menu | ngopi grounds.</title>
      <link rel="stylesheet" href="style.css" />
      <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  </head>
  <body>

  <?php include 'nav.php'; ?>

  <section class="menu-main" id="menu-main">
      <h2 class="menu-title">our menu</h2>

      <?php if (empty($availableProducts) && empty($soldOutProducts)): ?>
          <!-- Empty State -->
          <div class="empty-menu">
              <i class='bx bx-coffee'></i>
              <p>No items available at the moment.</p>
              <p style="font-size: 1.3rem; color: #999;">Check back soon for our delicious offerings!</p>
          </div>
      <?php else: ?>
          
          <!-- Available Products -->
          <?php if (!empty($availableProducts)): ?>
              <div class="menu-container">
                  <?php foreach ($availableProducts as $product): ?>
                      <?php
                          $img = $product['image_path'] ? clean($product['image_path']) : 'images/f1.jpeg';
                          $name = clean($product['product_name']);
                          $price = (float)$product['price'];
                          $desc = clean($product['description'] ?: 'Delicious and freshly made.');
                          $stock = (int)$product['stock_qty'];
                          $isLowStock = $stock <= $LOW_STOCK_THRESHOLD;
                      ?>
                      <div class="menu-card">
                          <img src="<?= $img ?>" alt="<?= $name ?>">
                          <div class="menu-info">
                              <h3>
                                  <?= $name ?>
                                  <?php if ($isLowStock): ?>
                                      <span class="low-stock">Low stock</span>
                                  <?php endif; ?>
                              </h3>
                              <p><?= $desc ?></p>
                              <div class="price-cart">
                                  <span class="price">RM<?= number_format($price, 2) ?></span>
                                  <button
                                      class="cart-btn add-to-cart-btn"
                                      data-name="<?= $name ?>"
                                      data-price="<?= $price ?>"
                                      title="Add to cart"
                                  >
                                      <i class='bx bx-cart'></i>
                                  </button>
                              </div>
                          </div>
                      </div>
                  <?php endforeach; ?>
              </div>
          <?php endif; ?>

          <!-- Sold Out / Pre-order Section -->
          <?php if (!empty($soldOutProducts)): ?>
              <h2 class="menu-title" style="margin-top: 4rem;">sold out</h2>
              <div class="menu-container">
                  <?php foreach ($soldOutProducts as $product): ?>
                      <?php
                          $img = $product['image_path'] ? clean($product['image_path']) : 'images/f1.jpeg';
                          $name = clean($product['product_name']);
                          $price = (float)$product['price'];
                          $desc = clean($product['description'] ?: 'Delicious and freshly made.');
                          $productId = (int)$product['product_id'];
                      ?>
                      <div class="menu-card">
                          <img src="<?= $img ?>" alt="<?= $name ?>">
                          <div class="menu-info">
                              <h3>
                                  <?= $name ?>
                                  <span class="sold-out-badge">Sold out</span>
                              </h3>
                              <p><?= $desc ?></p>
                              <div class="price-cart">
                                  <span class="price">RM<?= number_format($price, 2) ?></span>
                                  <button
                                      class="preorder-btn"
                                      data-id="<?= $productId ?>"
                                      data-name="<?= $name ?>"
                                      data-price="<?= $price ?>"
                                      title="Pre-order this item"
                                  >
                                      <i class='bx bx-time-five'></i> Pre-order
                                  </button>
                              </div>
                          </div>
                      </div>
                  <?php endforeach; ?>
              </div>
          <?php endif; ?>
          
      <?php endif; ?>
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

      <p class="copyright">© ngopi grounds. | All Rights Reserved.</p>
  </footer>

  <!-- Quantity Modal -->
  <div class="qty-modal-overlay" id="qtyModal">
      <div class="qty-modal-card">
          <button class="qty-close-btn" id="qtyCloseBtn">&times;</button>

          <div class="qty-title" id="qtyItemName">Item Name</div>
          <div class="qty-price">RM<span id="qtyItemPrice">0</span></div>

          <div class="qty-row">
              <button class="qty-btn" id="qtyMinus">-</button>
              <input class="qty-input" id="qtyInput" type="number" min="1" value="1">
              <button class="qty-btn" id="qtyPlus">+</button>
          </div>

          <div class="qty-actions">
              <button class="qty-add-btn" id="qtyAddBtn">Add to Cart</button>
              <button class="qty-cancel-btn" id="qtyCancelBtn">Cancel</button>
          </div>
      </div>
  </div>

  <script>
  const IS_LOGGED_IN     = <?= $isLoggedIn ? 'true' : 'false' ?>;
  const USER_ROLE        = "<?= $userRole ?>";
  const CURRENT_USER_ID  = "<?= $userId ?>";
  const CURRENT_USERNAME = "<?= $username ?>";
  </script>

  <script src="script.js"></script>

  <script>
  // Quantity Modal Logic
  const qtyModal       = document.getElementById("qtyModal");
  const qtyCloseBtn    = document.getElementById("qtyCloseBtn");
  const qtyCancelBtn   = document.getElementById("qtyCancelBtn");
  const qtyAddBtn      = document.getElementById("qtyAddBtn");
  const qtyMinus       = document.getElementById("qtyMinus");
  const qtyPlus        = document.getElementById("qtyPlus");
  const qtyInput       = document.getElementById("qtyInput");
  const qtyItemNameEl  = document.getElementById("qtyItemName");
  const qtyItemPriceEl = document.getElementById("qtyItemPrice");

  let currentItemName   = "";
  let currentItemPrice  = 0;
  let currentIsPreorder = false;
  let currentProductId  = null;

  function openQtyModal(name, price, isPreorder = false, productId = null) {
      currentItemName   = name;
      currentItemPrice  = parseFloat(price);
      currentIsPreorder = !!isPreorder;
      currentProductId  = productId;

      qtyItemNameEl.textContent  = name;
      qtyItemPriceEl.textContent = price;
      qtyInput.value = 1;

      qtyAddBtn.textContent = isPreorder ? "Pre-order" : "Add to Cart";
      qtyModal.style.display = "flex";
  }

  function closeQtyModal() {
      qtyModal.style.display = "none";
  }

  // Modal Controls
  qtyMinus.addEventListener("click", () => {
      let val = parseInt(qtyInput.value) || 1;
      qtyInput.value = Math.max(1, val - 1);
  });

  qtyPlus.addEventListener("click", () => {
      let val = parseInt(qtyInput.value) || 1;
      qtyInput.value = val + 1;
  });

  qtyCloseBtn.addEventListener("click", closeQtyModal);
  qtyCancelBtn.addEventListener("click", closeQtyModal);

  // Add to Cart / Pre-order
  qtyAddBtn.addEventListener("click", async () => {
      const qty = parseInt(qtyInput.value) || 1;

      if (!IS_LOGGED_IN) {
          alert("Please log in first before proceeding.");
          window.location.href = "login.php";
          return;
      }

      if (currentIsPreorder) {
          if (!currentProductId) {
              alert("Missing product ID.");
              return;
          }
          
          const formData = new FormData();
          formData.append("product_id", currentProductId);
          formData.append("qty", qty);

          try {
              const response = await fetch("preorder.php", { method: "POST", body: formData });
              const data = await response.json();
              
              if (data.ok) {
                  alert(`Pre-order placed for "${currentItemName}" (x${qty}).\nReference: #${data.preorder_id}`);
              } else {
                  alert(data.message || "Failed to place pre-order.");
              }
          } catch (error) {
              alert("Network error. Please try again.");
              console.error(error);
          }
      } else {
          // Add to cart
          if (typeof addToCart === "function") {
              addToCart(currentItemName, currentItemPrice, qty);
          } else {
              alert(`${currentItemName} x${qty} added to cart!`);
          }
      }

      closeQtyModal();
  });

  // Event Listeners for Add to Cart buttons
  document.querySelectorAll(".add-to-cart-btn").forEach(btn => {
      btn.addEventListener("click", function () {
          if (!IS_LOGGED_IN) {
              alert("Please log in first before adding to cart.");
              window.location.href = "login.php";
              return;
          }
          
          const name  = this.getAttribute("data-name");
          const price = parseFloat(this.getAttribute("data-price"));
          openQtyModal(name, price, false, null);
      });
  });

  // Event Listeners for Pre-order buttons
  document.addEventListener("click", (e) => {
      const btn = e.target.closest(".preorder-btn");
      if (!btn) return;

      if (!IS_LOGGED_IN) {
          alert("Please log in first before placing a pre-order.");
          window.location.href = "login.php";
          return;
      }
      
      const id    = parseInt(btn.getAttribute("data-id"), 10);
      const name  = btn.getAttribute("data-name");
      const price = parseFloat(btn.getAttribute("data-price"));
      openQtyModal(name, price, true, id);
  });

  // Close modal on overlay click
  qtyModal.addEventListener("click", (e) => {
      if (e.target === qtyModal) closeQtyModal();
  });
  </script>

  </body>
</html>