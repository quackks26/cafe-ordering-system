<?php

session_start();


// The visitor must be logged in as an administrator
if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard | Brewing Cafe</title>

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
	    <a href="index.php">Cafe Home</a>
	    <a href="admin_dashboard.php" class="active">Dashboard</a>
	    <a href="manage_menu.php">Manage Menu</a>
	    <a href="view_orders.php">View Orders</a>
	    <a href="reports.php">Performance Reports</a>
	    <a href="logout.php">Logout</a>
	</nav>

</header>


<main>

    <section class="menu-heading">

        <p class="eyebrow">
            ADMIN AREA
        </p>

        <h1>
            Welcome,
            <?= htmlspecialchars(
                $_SESSION["admin_username"]
            ) ?>
        </h1>

        <p>
            You have successfully logged in.
        </p>

	<p>
	    <a href="manage_menu.php" class="btn">
	        Manage Menu
	    </a>
	</p>

	<p>
	    <a href="view_orders.php" class="btn">
	        View Orders
	    </a>
	</p>

	<p>
	    <a href="reports.php" class="btn">
	        Performance Report
	    </a>
	</p>

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


</body>

</html>