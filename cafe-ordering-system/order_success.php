<?php

session_start();


// Make sure the visitor actually just placed an order
if (!isset($_SESSION["order_success"])) {

    header("Location: menu.php");
    exit;
}


// Copy the information before removing it
$order = $_SESSION["order_success"];


// Remove it from the session (confirmation is a one time message)
unset($_SESSION["order_success"]);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Order Confirmed | Brewing Cafe</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<header class="site-header">

    <a class="logo" href="index.php">
        ☕ Brewing Cafe
    </a>

	<nav class="navbar">
	    <a href="index.php">Home</a>
	    <a href="menu.php">Menu</a>
	    <a href="checkout.php">Checkout</a>
	    <a href="login.php">Admin Login</a>
	</nav>

</header>


<main>

    <section class="menu-heading">

        <p class="eyebrow">
            ORDER CONFIRMED
        </p>

        <h1>
            Thank you,
            <?= htmlspecialchars($order["customer_name"]) ?>!
        </h1>

        <p>
            Your order has been successfully placed.
        </p>

        <p>
            <strong>Order Number:</strong>

            #<?= htmlspecialchars($order["order_id"]) ?>
        </p>

        <p>
            <strong>Order Type:</strong>

            <?= htmlspecialchars($order["order_type"]) ?>
        </p>

        <p>
            <strong>Total:</strong>

            $<?= htmlspecialchars($order["total"]) ?>
        </p>

        <br>

        <a
            href="menu.php"
            class="btn"
        >
            Back to Menu
        </a>

    </section>

</main>


<footer class="site-footer">

    <p>
        Brewing Cafe · Feel the beat, savour the moment.
    </p>

    <p>
        &copy; 2026 Brewing Cafe. All rights reserved.
    </p>

</footer>


<script>

    // remove old cart since order is already saved successfully
    localStorage.removeItem("brewingCart");

</script>


</body>

</html>