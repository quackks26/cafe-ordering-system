<?php

session_start();

require_once "config/database.php";


// Only logged-in administrators may access this page
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


// Get the item ID from the URL
$itemId = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);


// Make sure ID is valid
if ($itemId === false || $itemId < 1) {
    die("Invalid menu item ID.");
}


// Retrieve the selected menu item
$stmt = $pdo->prepare("
    SELECT
        item_id,
        name,
        category,
        price,
        availability
    FROM menu_items
    WHERE item_id = :item_id
");

$stmt->execute([
    "item_id" => $itemId
]);

$item = $stmt->fetch(PDO::FETCH_ASSOC);


// If no matching item exists
if (!$item) {
    die("Menu item not found.");
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

    <title>Edit Menu Item | Brewing Cafe</title>

    <link rel="stylesheet" href="css/style.css">

</head>


<body>

<header class="site-header">

    <a class="logo" href="index.php">
        ☕ Brewing Cafe
    </a>

	<nav class="navbar">
	    <a href="index.php">Cafe Home</a>
	    <a href="admin_dashboard.php">Dashboard</a>
	    <a href="manage_menu.php" class="active">Manage Menu</a>
	    <a href="view_orders.php">View Orders</a>
	    <a href="reports.php">Performance Reports</a>
	    <a href="logout.php">Logout</a>
	</nav>

</header>


<main class="manage-menu-page">

    <section class="menu-heading">

        <p class="eyebrow">
            ADMIN AREA
        </p>

        <h1>Edit Menu Item</h1>

        <p>
            Update this item's menu information.
        </p>

    </section>


    <section class="admin-menu-section">

        <form
            class="admin-menu-form"
            method="POST"
            action="update_menu_item.php"
        >


            <!-- Hidden because the user does not need to edit the ID -->
            <input
                type="hidden"
                name="item_id"
                value="<?= htmlspecialchars($item["item_id"]) ?>"
            >


            <div class="form-group">

                <label for="name">
                    Item Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="100"
                    value="<?= htmlspecialchars($item["name"]) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="category">
                    Category
                </label>

                <select
                    id="category"
                    name="category"
                    required
                >

                    <option
                        value="Coffee"
                        <?= $item["category"] === "Coffee"
                            ? "selected"
                            : "" ?>
                    >
                        Coffee
                    </option>

                    <option
                        value="Non-Coffee"
                        <?= $item["category"] === "Non-Coffee"
                            ? "selected"
                            : "" ?>
                    >
                        Non-Coffee
                    </option>

                    <option
                        value="Tea"
                        <?= $item["category"] === "Tea"
                            ? "selected"
                            : "" ?>
                    >
                        Tea
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="price">
                    Price ($)
                </label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    min="0.01"
                    max="999.99"
                    step="0.01"
                    value="<?= htmlspecialchars($item["price"]) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="availability">
                    Availability
                </label>

                <select
                    id="availability"
                    name="availability"
                    required
                >

                    <option
                        value="1"
                        <?= $item["availability"] == 1
                            ? "selected"
                            : "" ?>
                    >
                        Available
                    </option>

                    <option
                        value="0"
                        <?= $item["availability"] == 0
                            ? "selected"
                            : "" ?>
                    >
                        Unavailable
                    </option>

                </select>

            </div>


            <div class="edit-actions">

                <button
                    type="submit"
                    class="btn"
                >
                    Save Changes
                </button>

                <a
                    href="manage_menu.php"
                    class="btn"
                >
                    Cancel
                </a>

            </div>

        </form>

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