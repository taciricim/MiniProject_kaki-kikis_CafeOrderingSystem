<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userId     = $_SESSION['user_id'];
$userRole   = isset($_SESSION['role']) ? $_SESSION['role'] : 'customer';
$username   = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>checkout | ngopi grounds.</title>
        <link rel="stylesheet" href="style.css" />
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    </head>
    <body>

    <?php include 'nav.php'; ?>

    <section class="checkout-page">
        <div class="checkout-grid">
            <div class="checkout-card">
                <h1>your details</h1>

                <form id="checkoutForm">
                    <label class="checkout-label">
                        name
                        <input type="text" name="name" class="checkout-input" placeholder="Your name" value="<?php echo htmlspecialchars($username); ?>" required>
                    </label>

                    <label class="checkout-label">
                        phone number
                        <input type="text" name="phone" class="checkout-input" placeholder="01X-XXXXXXX" required>
                    </label>

                    <label class="checkout-label">
                        pickup time
                        <input type="time" name="pickup_time" class="checkout-input" required>
                    </label>

                    <label class="checkout-label">
                        notes for barista / kitchen
                        <textarea name="notes" class="checkout-input" placeholder="Less ice, extra shot, nut allergy..."></textarea>
                    </label>

                    <button type="submit" class="btn" style="width:100%;margin-top:1rem;">
                        place order
                    </button>
                </form>
            </div>

            <div class="checkout-card">
                <h1>your order</h1>
                <div id="summary-items"></div>

                <div class="summary-total-row">
                    <span>Total:</span>
                    <span>RM<span id="summary-total">0.00</span></span>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="social-icons">
            <a href="#"><i class="bx bxl-facebook"></i></a>
            <a href="#"><i class="bx bxl-instagram-alt"></i></a>
            <a href="#"><i class="bx bxl-twitter"></i></a>
        </div>

        <ul class="list">
            <li><a href="#">FAQ</a></li>
            <li><a href="menu.php">menu</a></li>
            <li><a href="about.php">about us</a></li>
            <li><a href="contact.php">contact</a></li>
        </ul>

        <p class="copyright">
            © ngopi grounds. | All Rights Reserved.
        </p>
    </footer>

    <script>
    const IS_LOGGED_IN     = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    const USER_ROLE        = "<?php echo $userRole; ?>";
    const CURRENT_USER_ID  = "<?php echo $userId; ?>";
    const CURRENT_USERNAME = "<?php echo $username; ?>";
    </script>

    <script src="script.js"></script>

    <script>
    // Build checkout summary
    (function buildCheckoutSummary(){
        if (typeof cart === "undefined") return;

        const listBox = document.getElementById("summary-items");
        const totalBox = document.getElementById("summary-total");
        if (!listBox || !totalBox) return;

        if (cart.length === 0) {
            listBox.innerHTML = "<p>Your cart is empty.</p>";
            totalBox.textContent = "0.00";
            return;
        }

        let sum = 0;
        let html = "";

        cart.forEach(item => {
            const qty = parseInt(item.qty) || 1;
            const sub = item.price * qty;
            sum += sub;
            html += `
                <div class="summary-row">
                    <span>${item.name} x ${qty}</span>
                    <span>RM${sub.toFixed(2)}</span>
                </div>
            `;
        });

        listBox.innerHTML = html;
        totalBox.textContent = sum.toFixed(2);
    })();

    // Handle form submission
    document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (typeof cart === "undefined" || cart.length === 0) {
            alert('Your cart is empty!');
            return;
        }

        const totalEl = document.getElementById("summary-total");
        const total = parseFloat(totalEl.textContent);

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Processing...';
        submitBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('cart', JSON.stringify(cart));
            formData.append('total', total);

            const response = await fetch('process_order.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Clear cart from localStorage
                const CART_KEY = "cart_user_" + CURRENT_USER_ID;
                localStorage.removeItem(CART_KEY);
                
                // Redirect to payment page with order ID
                window.location.href = `payment.php?order_id=${data.order_id}&total=${total}`;
            } else {
                alert('Error: ' + data.message);
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        } catch (error) {
            alert('Network error. Please try again.');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });
    </script>

    </body>
</html>