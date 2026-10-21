<?php

session_start();

require_once "config/database.php";


if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


$stmt = $pdo->query("
    SELECT
        item_id,
        name,
        category,
        price,
        availability
    FROM menu_items
    ORDER BY item_id
");

$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Menu | Brewing Cafe</title>

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

        <h1>Manage Menu</h1>

        <p>
            Add and manage drinks available on the cafe menu.
        </p>

    </section>


    <!-- ADD NEW MENU ITEM -->

    <section class="admin-menu-section">

        <h2>Add New Item</h2>


        <?php if (isset($_GET["added"])): ?>

            <p class="admin-message">
                Menu item added successfully.
            </p>

        <?php endif; ?>


	<?php if (isset($_GET["updated"])): ?>

	    <p class="admin-message">
	        Menu item updated successfully.
	    </p>

	<?php endif; ?>

	<?php if (isset($_GET["deleted"])): ?>

	    <p class="admin-message">
	        Menu item deleted successfully.
	    </p>

	<?php endif; ?>

        <?php if (isset($_GET["error"])): ?>

            <p class="admin-error">
                Unable to add menu item. Please check the entered details.
            </p>

        <?php endif; ?>

	<?php if (
	    isset($_GET["delete_error"]) &&
	    $_GET["delete_error"] === "in_use"
	): ?>

	    <p class="admin-error">
	        This menu item cannot be deleted because it appears in an existing 		order. *Mark it as Unavailable instead.
	    </p>

	<?php endif; ?>

	<?php if (
	    isset($_GET["delete_error"]) &&
	    $_GET["delete_error"] === "invalid"
	): ?>

	    <p class="admin-error">
	        Invalid menu item.
	    </p>

	<?php endif; ?>


	<?php if (
	    isset($_GET["delete_error"]) &&
	    $_GET["delete_error"] === "not_found"
	): ?>

	    <p class="admin-error">
	        The menu item could not be found.
	    </p>

	<?php endif; ?>


        <form
            class="admin-menu-form"
            method="POST"
            action="add_menu_item.php"
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

                    <option value="">
                        Select Category
                    </option>

                    <option value="Coffee">
                        Coffee
                    </option>

                    <option value="Non-Coffee">
                        Non-Coffee
                    </option>

                    <option value="Tea">
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
                    required
                >

            </div>


            <button
                type="submit"
                class="btn"
            >
                Add Item
            </button>

        </form>

    </section>


    <!-- EXISTING MENU ITEMS -->

    <section class="admin-menu-section">

        <h2>Current Menu</h2>


        <?php if (empty($menuItems)): ?>

            <p>
                No menu items found.
            </p>


        <?php else: ?>

            <div class="admin-menu-table-wrapper">

                <table class="admin-menu-table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Availability</th>
		   	    <th>Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($menuItems as $item): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($item["item_id"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item["name"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item["category"]) ?>
                                </td>

                                <td>
                                    $<?= number_format(
                                        $item["price"],
                                        2
                                    ) ?>
                                </td>

                                <td>

                                    <?php if ($item["availability"] == 1): ?>

                                        <span class="available">
                                            Available
                                        </span>

                                    <?php else: ?>

                                        <span class="unavailable">
                                            Unavailable
                                        </span>

                                    <?php endif; ?>

                                </td>

				<td>

				    <div class="menu-actions">

				        <a
				            href="edit_menu_item.php?id=<?= htmlspecialchars($item["item_id"]) ?>"
				            class="btn btn-small"
				        >
				            Edit
				        </a>


				        <form
				            method="POST"
				            action="delete_menu_item.php"
				            onsubmit="return confirm('Are you sure you want to delete this menu item?');"
				        >

				            <input
				                type="hidden"
				                name="item_id"
				                value="<?= htmlspecialchars($item["item_id"]) ?>"
				            >

				            <button
				                type="submit"
				                class="btn btn-small btn-danger"
				            >
				                Delete
				            </button>
	
				        </form>

				    </div>

				</td>
				
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

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