// ====== NAV MENU TOGGLE (burger menu support / mobile) ======
let menuIcon = document.querySelector('#menu-icon');
let navbar = document.querySelector('.navbar');

if (menuIcon) {
  menuIcon.onclick = () => {
    menuIcon.classList.toggle('bx-x');
    navbar.classList.toggle('active');
  };
}

// ===================================================================
// 1. USER + CART KEY
// ===================================================================

let EFFECTIVE_USER_ID = "guest";
if (typeof CURRENT_USER_ID !== "undefined" && CURRENT_USER_ID) {
  EFFECTIVE_USER_ID = CURRENT_USER_ID;
}
if (
  EFFECTIVE_USER_ID === "undefined" ||
  EFFECTIVE_USER_ID === "null" ||
  EFFECTIVE_USER_ID === null
) {
  EFFECTIVE_USER_ID = "guest";
}

if (typeof CURRENT_USERNAME !== "undefined" && CURRENT_USERNAME) {
  const greetEl = document.getElementById("nav-greeting");
  if (greetEl) {
    greetEl.textContent = "hi, " + CURRENT_USERNAME;
    greetEl.style.display = "inline-block";
  }
}

const CART_KEY = "cart_user_" + EFFECTIVE_USER_ID;

// ===================================================================
// 2. CART STATE
// ===================================================================
let cart = [];
let discount = 0;

try {
  const raw = localStorage.getItem(CART_KEY);
  cart = raw ? JSON.parse(raw) : [];
  if (!Array.isArray(cart)) {
    cart = [];
  }
} catch (e) {
  cart = [];
}

function normalizeCart() {
  cart.forEach(item => {
    if (item.qty === undefined || item.qty === null) {
      item.qty = 1;
    } else {
      let q = parseInt(item.qty);
      if (isNaN(q) || q < 1) q = 1;
      item.qty = q;
    }
  });
}

function saveCart() {
  normalizeCart();
  console.log("[saveCart] CART_KEY =", CART_KEY, "cart =", cart);
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

// ===================================================================
// 3. BADGE UPDATE
// ===================================================================
function updateCartCount() {
  const countEl = document.getElementById("cart-count");
  if (!countEl) {
    console.log("[updateCartCount] no #cart-count element on this page");
    return;
  }

  normalizeCart();

  const totalQty = cart.reduce((sum, item) => {
    const q = parseInt(item.qty);
    return sum + (isNaN(q) ? 0 : q);
  }, 0);

  console.log(
    "[updateCartCount] totalQty =", totalQty,
    "for CART_KEY =", CART_KEY
  );

  countEl.textContent = totalQty;

  if (totalQty > 0) {
    countEl.style.display = "inline-block";

    countEl.classList.remove("pop");
    void countEl.offsetWidth;
    countEl.classList.add("pop");
  } else {
    countEl.style.display = "none";
  }
}

// ===================================================================
// 4. RENDER CART PAGE (cart.php)
// ===================================================================
function renderCart() {
  const tbody         = document.getElementById("cart-items");
  const totalEl       = document.getElementById("cart-total");
  const emptyStateBox = document.getElementById("cart-empty-state");

  normalizeCart();

  if (!tbody || !totalEl) {
    console.log("[renderCart] not on cart.php, just updating count");
    updateCartCount();
    return;
  }

  if (cart.length === 0) {
    tbody.innerHTML = "";
    totalEl.textContent = "0.00";

    if (emptyStateBox) {
      emptyStateBox.style.display = "block";
    }
  } else {
    if (emptyStateBox) {
      emptyStateBox.style.display = "none";
    }

    tbody.innerHTML = "";
    let total = 0;

    cart.forEach((item, index) => {
      const qty = parseInt(item.qty) || 1;
      const subtotal = item.price * qty;
      total += subtotal;

      tbody.innerHTML += `
        <tr>
          <td>${item.name}</td>
          <td>RM${item.price}</td>
          <td>
            <input type="number" min="1" value="${qty}"
              onchange="updateQty(${index}, this.value)">
          </td>
          <td>RM${subtotal}</td>
          <td><button onclick="removeItem(${index})">❌</button></td>
        </tr>
      `;
    });

    total = total - discount;
    if (total < 0) total = 0;
    totalEl.textContent = total.toFixed(2);
  }

  updateCartCount();
}

// ===================================================================
// 5. CART MANIPULATION
// ===================================================================
function addToCart(name, price, qty = 1) {
  console.log("[addToCart] called with", name, price, qty);

  if (typeof IS_LOGGED_IN !== "undefined" && !IS_LOGGED_IN) {
    alert("Please log in first before adding to cart.");
    window.location.href = "login.php";
    return;
  }

  let addQty = parseInt(qty);
  if (isNaN(addQty) || addQty < 1) addQty = 1;

  let existing = cart.find(item => item.name === name);

  if (existing) {
    let currentQty = parseInt(existing.qty);
    if (isNaN(currentQty) || currentQty < 1) currentQty = 1;
    existing.qty = currentQty + addQty;
  } else {
    cart.push({
      name: name,
      price: parseFloat(price),
      qty: addQty
    });
    alert(name + " x" + addQty + " added to cart!");
  }

  saveCart();
  renderCart();
}

function updateQty(index, newQty) {
  let q = parseInt(newQty);
  if (isNaN(q) || q < 1) q = 1;
  cart[index].qty = q;

  saveCart();
  renderCart();
}

function removeItem(index) {
  cart.splice(index, 1);

  saveCart();
  renderCart();
}

// ===================================================================
// 6. CHECKOUT
// ===================================================================

const checkoutBtn = document.getElementById("checkout-btn");
if (checkoutBtn) {
  checkoutBtn.addEventListener("click", () => {
    if (typeof IS_LOGGED_IN !== "undefined" && !IS_LOGGED_IN) {
      alert("Please log in first to proceed to checkout.");
      window.location.href = "login.php";
      return;
    }

    if (!cart || cart.length === 0) {
      alert("Your cart is empty.");
      return;
    }

    window.location.href = "checkout.php";
  });
}

// ===================================================================
// 7. INIT
// ===================================================================
console.log(
  "[INIT] EFFECTIVE_USER_ID =", EFFECTIVE_USER_ID,
  "CART_KEY =", CART_KEY,
  "loaded cart =", cart
);

renderCart();
updateCartCount();

