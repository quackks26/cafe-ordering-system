<?php

require_once "config/database.php";

// Retrieve all available menu items from  database
$sql = "
    SELECT item_id, name, category, price
    FROM menu_items
    WHERE availability = 1
    ORDER BY item_id
";

$stmt = $pdo->query($sql);

// store all retrieved menu items in an array
$drinks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brewing Cafe | Menu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <a class="logo" href="index.php">☕ Brewing Cafe</a>
	<nav class="navbar">
	    <a href="index.php">Home</a>
	    <a href="menu.php" class="active">Menu</a>
	    <a href="checkout.php">Checkout</a>
	    <a href="login.php">Admin Login</a>
	</nav>
</header>

<main class="menu-page">
    <section class="menu-heading">
        <p class="eyebrow">MADE FOR YOUR MOMENT</p>
        <h1>Our Menu</h1>
        <p>Find your favourite drink at Brewing Cafe.</p>
    </section>

    <section class="menu-section">
        <div class="category-buttons">
            <button class="category-btn active" data-category="All">All</button>
            <button class="category-btn" data-category="Coffee">Coffee</button>
            <button class="category-btn" data-category="Non-Coffee">Non-Coffee</button>
            <button class="category-btn" data-category="Tea">Tea</button>
        </div>

        <div class="drink-grid" id="drinkGrid"></div>
    </section>
</main>

<footer class="site-footer">
    <p>Brewing Cafe · Feel the beat, savour the moment.</p>
    <p>&copy; 2026 Brewing Cafe. All rights reserved.</p>
</footer>

<script>

// php database record -> javascript
const drinks = <?= json_encode($drinks) ?>;

const grid = document.getElementById("drinkGrid");
const buttons = document.querySelectorAll(".category-btn");


// Decorative icons for menu
function getDrinkArt(name) {

    const art = {
        "Espresso": "☕",
        "Caramel Macchiato": "🥤",
        "Vanilla Latte": "☕",
        "Americano": "☕",
        "Strawberry Matcha": "🍓🥤",
        "Strawberry Milk": "🍓🥛",
        "Ice Chocolate Cream": "🍫🥤",
        "Ice Lemonade Soda": "🍋🥤",
        "Green Tea": "🍵",
        "Strawberry Tea": "🍓🫖",
        "Milk Tea": "🧋",
        "Peach Tea": "🍑🫖"
    };

    return art[name] || "🥤";
}


// Display drinks from the database
function showDrinks(category = "All") {

    const filtered = category === "All"
        ? drinks
        : drinks.filter(drink => drink.category === category);

    grid.innerHTML = filtered.map(drink => {

        const price = Number(drink.price);

        return `
            <article class="drink-card">

                <div
                    class="drink-art"
                    role="img"
                    aria-label="${drink.name}"
                >
                    ${getDrinkArt(drink.name)}
                </div>

                <h3>${drink.name}</h3>

                <p class="price">
                    $${price.toFixed(2)}
                </p>

                <button
                    class="btn btn-small"
                    onclick="addToCart(
                        ${drink.item_id},
                        '${drink.name.replace(/'/g, "\\'")}',
                        ${price}
                    )"
                >
                    Add to Cart
                </button>

            </article>
        `;
    }).join("");
}


// Add a selected drink to localStorage
function addToCart(itemId, name, price) {

    let cart = JSON.parse(
        localStorage.getItem("brewingCart") || "[]"
    );

    const existing = cart.find(
        item => Number(item.item_id) === Number(itemId)
    );

    if (existing) {

        existing.quantity += 1;

    } else {

        cart.push({
            item_id: Number(itemId),
            name: name,
            price: Number(price),
            quantity: 1
        });
    }

    localStorage.setItem(
        "brewingCart",
        JSON.stringify(cart)
    );

    alert(name + " added to your cart!");
}


// Filter menu items by category
buttons.forEach(button => {

    button.addEventListener("click", () => {

        buttons.forEach(item =>
            item.classList.remove("active")
        );

        button.classList.add("active");

        showDrinks(button.dataset.category);
    });
});


showDrinks();

</script>
</body>
</html>
