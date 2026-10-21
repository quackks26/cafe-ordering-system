<?php

session_start();

require_once "config/database.php";


// Protect this page

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


// Retrieve all orders

$orderStmt = $pdo->query("
    SELECT
        order_id,
        customer_name,
        phone,
        order_type,
        order_date,
        status,
        total
    FROM orders
    ORDER BY order_date DESC
");

$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);


// Prepare a query for the items inside an order

$itemStmt = $pdo->prepare("
    SELECT
        menu_items.name,
        order_items.quantity,
        order_items.unit_price
    FROM order_items

    INNER JOIN menu_items
        ON order_items.item_id = menu_items.item_id

    WHERE order_items.order_id = :order_id

    ORDER BY order_items.order_item_id
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>View Orders | Brewing Cafe</title>

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
	    <a href="manage_menu.php">Manage Menu</a>
	    <a href="view_orders.php" class="active">View Orders</a>
	    <a href="reports.php">Performance Reports</a>
	    <a href="logout.php">Logout</a>
	</nav>

</header>


<main class="orders-page">

    <section class="menu-heading">

        <p class="eyebrow">
            ADMIN AREA
        </p>

        <h1>Customer Orders</h1>

        <p>
            View all orders placed through the cafe website.
        </p>

    </section>


    <section class="admin-orders">

        <?php if (empty($orders)): ?>

            <p class="no-orders">
                No orders have been placed yet.
            </p>

        <?php else: ?>


            <?php foreach ($orders as $order): ?>

                <article class="admin-order-card">


                    <div class="order-heading">

		    <h2>
		        Order #
		        <?= htmlspecialchars($order["order_id"]) ?>
		    </h2>

		    <form
		        class="status-form"
		        method="POST"
		        action="update_order_status.php"
		    >

		        <input
		            type="hidden"
		            name="order_id"
		            value="<?= htmlspecialchars($order["order_id"]) ?>"
		        >

		        <select name="status">

		            <option
		                value="Pending"
		                <?= $order["status"] === "Pending"
		                    ? "selected"
		                    : "" ?>
		            >
		                Pending
		            </option>

		            <option
		                value="Preparing"
		                <?= $order["status"] === "Preparing"
		                    ? "selected"
		                    : "" ?>
		            >
		                Preparing
		            </option>

		            <option
		                value="Completed"
		                <?= $order["status"] === "Completed"
		                    ? "selected"
		                    : "" ?>
		            >
		                Completed
		            </option>

		        </select>
	
		        <button
		            type="submit"
		            class="btn btn-small"
		        >
		            Update
		        </button>

		    </form>

		</div>


                    <div class="order-details">

                        <p>
                            <strong>Customer:</strong>
                            <?= htmlspecialchars($order["customer_name"]) ?>
                        </p>

                        <p>
                            <strong>Phone:</strong>
                            <?= htmlspecialchars($order["phone"]) ?>
                        </p>

                        <p>
                            <strong>Order Type:</strong>
                            <?= htmlspecialchars($order["order_type"]) ?>
                        </p>

                        <p>
                            <strong>Date:</strong>
                            <?= htmlspecialchars($order["order_date"]) ?>
                        </p>

                    </div>


                    <h3>Items</h3>


                    <?php

                    // Retrieve the drinks belonging to this order
                    $itemStmt->execute([
                        "order_id" => $order["order_id"]
                    ]);

                    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

                    ?>


                    <div class="order-items">

                        <?php foreach ($items as $item): ?>

                            <div class="admin-order-item">

                                <span>
                                    <?= htmlspecialchars($item["name"]) ?>
                                    ×
                                    <?= htmlspecialchars($item["quantity"]) ?>
                                </span>

                                <span>
                                    $<?= number_format(
                                        $item["unit_price"] *
                                        $item["quantity"],
                                        2
                                    ) ?>
                                </span>

                            </div>

                        <?php endforeach; ?>

                    </div>


                    <div class="admin-order-total">

                        <strong>Total</strong>

                        <strong>
                            $<?= number_format(
                                $order["total"],
                                2
                            ) ?>
                        </strong>

                    </div>


                </article>

            <?php endforeach; ?>


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