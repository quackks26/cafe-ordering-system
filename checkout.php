<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brewing Cafe | Checkout</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header class="site-header">
    <a class="logo" href="index.php">☕ Brewing Cafe</a>

	<nav class="navbar">
	    <a href="index.php">Home</a>
	    <a href="menu.php">Menu</a>
	    <a href="checkout.php" class="active">Checkout</a>
	    <a href="login.php">Admin Login</a>
	</nav>
</header>

<main class="checkout-page">

    <section class="menu-heading">
        <p class="eyebrow">ALMOST THERE</p>
        <h1>Checkout</h1>
        <p>Review your order before placing it.</p>
    </section>

    <div class="checkout-layout">

        <!-- ORDER SUMMARY -->
        <section class="order-summary">
            <h2>Your Order</h2>

            <div id="cartItems"></div>

            <p id="emptyMessage" hidden>
                Your cart is empty.
                <a href="menu.php">Visit our menu</a> to add drinks.
            </p>

            <div class="total-row">
                <strong>Total</strong>
                <strong id="totalAmount">$0.00</strong>
            </div>
        </section>

        <!-- CHECKOUT FORM -->
        <section class="checkout-form-section">
            <h2>Customer Details</h2>

            <form id="checkoutForm" method="POST" action="process_order.php">

                <label for="fullName">Full Name</label>
                <input
                    type="text"
                    id="fullName"
                    name="fullName"
                    placeholder="Enter your full name"
                    minlength="2"
                    maxlength="80"
                    pattern="[A-Za-z][A-Za-z .'-]{1,79}"
                    title="Enter a name using letters, spaces, apostrophes, periods or hyphens."
                    autocomplete="name"
                    required
                >

                <label for="phone">Phone Number</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    placeholder="Enter your phone number"
                    minlength="7"
                    maxlength="20"
                    pattern="[0-9+() -]{7,20}"
                    title="Enter a valid phone number."
                    autocomplete="tel"
                    required
                >

                <label for="orderType">Order Type</label>
                <select id="orderType" name="orderType" required>
                    <option value="">Choose order type</option>
                    <option value="Dine in">Dine In</option>
                    <option value="Take away">Take Away</option>
                </select>

                <p id="orderMessage" role="status" aria-live="polite"></p>

		<input type="hidden" id="cartData" name="cartData">

                <button
                    class="btn place-order-btn"
                    id="placeOrderBtn"
                    type="submit"
                >
                    Place Order
                </button>

            </form>
        </section>

    </div>

</main>

<footer class="site-footer">
    <p>Brewing Cafe · Feel the beat, savour the moment.</p>
    <p>&copy; 2026 Brewing Cafe. All rights reserved.</p>
</footer>

<script>
"use strict";

// Load the shopping cart
function loadCart() {
    try {
        const savedCart = JSON.parse(
            localStorage.getItem("brewingCart") || "[]"
        );

        return Array.isArray(savedCart) ? savedCart : [];
    } catch {
        return [];
    }
}

let cart = loadCart();

const cartItems = document.getElementById("cartItems");
const totalAmount = document.getElementById("totalAmount");
const emptyMessage = document.getElementById("emptyMessage");
const checkoutForm = document.getElementById("checkoutForm");
const orderMessage = document.getElementById("orderMessage");
const placeOrderBtn = document.getElementById("placeOrderBtn");

// Check that cart entries contain valid values
function isValidCart() {
    return cart.every(item =>
        item &&
        Number.isInteger(Number(item.item_id)) &&
        Number(item.item_id) > 0 &&
        typeof item.name === "string" &&
        Number.isFinite(item.price) &&
        item.price > 0 &&
        Number.isInteger(item.quantity) &&
        item.quantity > 0
    );
}

// Display the shopping cart
function renderCart() {
    cartItems.replaceChildren();

    let totalCents = 0;

    emptyMessage.hidden = cart.length > 0;
    placeOrderBtn.disabled = cart.length === 0;

    if (!isValidCart()) {
        totalAmount.textContent = "Unavailable";
        emptyMessage.hidden = true;
        placeOrderBtn.disabled = true;
        orderMessage.textContent =
            "Your cart contains invalid items. Please clear your cart and try again.";
        return;
    }

    cart.forEach((item, index) => {
        const subtotalCents =
            Math.round(item.price * 100) * item.quantity;

        totalCents += subtotalCents;

        const row = document.createElement("div");
        row.className = "cart-item";

        const name = document.createElement("h3");
        name.textContent = item.name;

        const price = document.createElement("p");
        price.textContent =
            "$" + item.price.toFixed(2) + " each";

        const controls = document.createElement("div");
        controls.className = "quantity-controls";

        const minusBtn = document.createElement("button");
        minusBtn.type = "button";
        minusBtn.textContent = "−";
        minusBtn.setAttribute(
            "aria-label", "Decrease quantity of " + item.name
        );
        minusBtn.disabled = item.quantity <= 1;
        minusBtn.addEventListener("click", () => {
            changeQuantity(index, -1);
        });

        const quantity = document.createElement("span");
        quantity.textContent = item.quantity;

        const plusBtn = document.createElement("button");
        plusBtn.type = "button";
        plusBtn.textContent = "+";
        plusBtn.setAttribute(
            "aria-label", "Increase quantity of " + item.name
        );
        plusBtn.disabled = item.quantity >= 99;
        plusBtn.addEventListener("click", () => {
            changeQuantity(index, 1);
        });

        controls.append(minusBtn, quantity, plusBtn);

        const subtotal = document.createElement("p");
        subtotal.textContent =
            "Subtotal: $" + (subtotalCents / 100).toFixed(2);

        row.append(name, price, controls, subtotal);
        cartItems.appendChild(row);
    });

    totalAmount.textContent =
        "$" + (totalCents / 100).toFixed(2);
}

// Change the quantity of a drink
function changeQuantity(index, amount) {
    if (!cart[index]) return;

    const newQuantity = cart[index].quantity + amount;

    if (newQuantity < 1 || newQuantity > 99) return;

    cart[index].quantity = newQuantity;

    localStorage.setItem("brewingCart", JSON.stringify(cart));

    orderMessage.textContent = "";
    renderCart();
}

// Validate customer details and demonstrate order confirmation
checkoutForm.addEventListener("submit", function(event) {

    // Clear any previous error message
    orderMessage.textContent = "";

    // Make sure the cart contains something
    if (cart.length === 0) {
        event.preventDefault();

        orderMessage.textContent =
            "Your cart is empty. Please add a drink first.";

        return;
    }

    // Make sure all cart entries have valid values
    if (!isValidCart()) {
        event.preventDefault();

        orderMessage.textContent =
            "Your cart is invalid. Please review your items.";

        return;
    }


    const customerName =
        document.getElementById("fullName").value.trim();

    const phone =
        document.getElementById("phone").value.trim();

    const orderType =
        document.getElementById("orderType").value;


    // Validate customer name
    if (!/^[A-Za-z][A-Za-z .'-]{1,79}$/.test(customerName)) {

        event.preventDefault();

        orderMessage.textContent =
            "Please enter a valid name using at least 2 characters.";

        document.getElementById("fullName").focus();

        return;
    }


    // Validate phone number
    const phoneDigits = phone.replace(/\D/g, "");

    if (
        !/^[0-9+() -]{7,20}$/.test(phone) ||
        phoneDigits.length < 7 ||
        phoneDigits.length > 15
    ) {

        event.preventDefault();

        orderMessage.textContent =
            "Please enter a valid phone number.";

        document.getElementById("phone").focus();

        return;
    }


    // Validate order type
    if (!["Dine in", "Take away"].includes(orderType)) {

        event.preventDefault();

        orderMessage.textContent =
            "Please select Dine In or Take Away.";

        document.getElementById("orderType").focus();

        return;
    }


    // Convert the JavaScript cart into JSON
    // and place it inside the hidden form field.
    document.getElementById("cartData").value =
        JSON.stringify(cart);

});

// Initial display
renderCart();

</script>

<script src="js/validation.js" defer></script>

</body>
</html>