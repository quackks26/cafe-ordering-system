<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brewing Cafe | Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header class="site-header">
        <a class="logo" href="index.php">☕ Brewing Cafe</a>

	<nav class="navbar">
	    <a href="index.php" class="active">Home</a>
	    <a href="menu.php">Menu</a>
	    <a href="checkout.php">Checkout</a>
	    <a href="login.php">Admin Login</a>
	</nav>
    </header>

    <main>
        <section class="hero">
            <p class="eyebrow">YOUR DAILY COFFEE MOMENT</p>
            <h1>Welcome to our cafe</h1>
            <p class="tagline">
                Feel the beat, savour the moment.
            </p>
            <a href="menu.php" class="btn">View Menu</a>
        </section>

        <section class="popular">
            <h2>Popular Items</h2>
            <p class="section-description">
                Discover your new favourite drink.
            </p>

            <div class="drink-grid">
                <article class="drink-card">
                    <div class="drink-art matcha-art"
                         role="img"
                         aria-label="Illustration of strawberry matcha">
                        🍓🥤
                    </div>
                    <h3>Strawberry Matcha</h3>
                    <p class="price">$5.50</p>
                    <a class="btn btn-small" href="menu.php">
                        View Menu
                    </a>
                </article>

                <article class="drink-card">
                    <div class="drink-art coffee-art"
                         role="img"
                         aria-label="Illustration of caramel coffee">
                        ☕
                    </div>
                    <h3>Caramel Macchiato</h3>
                    <p class="price">$5.00</p>
                    <a class="btn btn-small" href="menu.php">
                        View Menu
                    </a>
                </article>

                <article class="drink-card">
                    <div class="drink-art lemonade-art"
                         role="img"
                         aria-label="Illustration of lemon soda">
                        🍋🥤
                    </div>
                    <h3>Ice Lemonade Soda</h3>
                    <p class="price">$3.50</p>
                    <a class="btn btn-small" href="menu.php">
                        View Menu
                    </a>
                </article>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <p>Brewing Cafe · Feel the beat, savour the moment.</p>
        <p>&copy; 2026 Brewing Cafe. All rights reserved.</p>
    </footer>

</body>
</html>