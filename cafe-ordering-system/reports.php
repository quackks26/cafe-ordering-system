<?php

session_start();

require_once "config/database.php";


// Only logged-in administrators may access reports
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


// Overall order summary

$summaryStmt = $pdo->query("
    SELECT
        COUNT(*) AS total_orders,
        COALESCE(SUM(total), 0) AS total_revenue,

        SUM(
            CASE
                WHEN status = 'Pending'
                THEN 1
                ELSE 0
            END
        ) AS pending_orders,

        SUM(
            CASE
                WHEN status = 'Preparing'
                THEN 1
                ELSE 0
            END
        ) AS preparing_orders,

        SUM(
            CASE
                WHEN status = 'Completed'
                THEN 1
                ELSE 0
            END
        ) AS completed_orders

    FROM orders
");

$summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);


// Sales by menu item

$salesStmt = $pdo->query("
    SELECT
        menu_items.name,
        SUM(order_items.quantity) AS quantity_sold,
        SUM(
            order_items.quantity *
            order_items.unit_price
        ) AS sales_total

    FROM order_items

    INNER JOIN menu_items
        ON order_items.item_id = menu_items.item_id

    GROUP BY
        menu_items.item_id,
        menu_items.name

    ORDER BY quantity_sold DESC
");

$sales = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Performance Report | Brewing Cafe</title>

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
	    <a href="admin_dashboard.php">Dashboard</a>
	    <a href="manage_menu.php">Manage Menu</a>
	    <a href="view_orders.php">View Orders</a>
	    <a href="reports.php" class="active">Performance Reports</a>
	    <a href="logout.php">Logout</a>
	</nav>

</header>


<main class="reports-page">


    <section class="menu-heading">

        <p class="eyebrow">
            ADMIN AREA
        </p>

        <h1>Performance Report</h1>

        <p>
            View a summary of customer orders and menu sales.
        </p>

    </section>


    <!-- OVERALL SUMMARY -->

    <section class="report-summary">

        <div class="report-card">

            <h2>
                <?= htmlspecialchars($summary["total_orders"]) ?>
            </h2>

            <p>Total Orders</p>

        </div>


        <div class="report-card">

            <h2>
                $<?= number_format(
                    $summary["total_revenue"],
                    2
                ) ?>
            </h2>

            <p>Total Revenue</p>

        </div>


        <div class="report-card">

            <h2>
                <?= htmlspecialchars($summary["pending_orders"] ?? 0) ?>
            </h2>

            <p>Pending</p>

        </div>


        <div class="report-card">

            <h2>
                <?= htmlspecialchars($summary["preparing_orders"] ?? 0) ?>
            </h2>

            <p>Preparing</p>

        </div>


        <div class="report-card">

            <h2>
                <?= htmlspecialchars($summary["completed_orders"] ?? 0) ?>
            </h2>

            <p>Completed</p>

        </div>

    </section>


    <!-- SALES BY MENU ITEM -->

    <section class="admin-menu-section">

        <h2>Sales by Menu Item</h2>


        <?php if (empty($sales)): ?>

            <p>
                No sales data is available yet.
            </p>


        <?php else: ?>

            <div class="admin-menu-table-wrapper">

                <table class="admin-menu-table">

                    <thead>

                        <tr>
                            <th>Menu Item</th>
                            <th>Quantity Sold</th>
                            <th>Sales</th>
                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($sales as $item): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($item["name"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item["quantity_sold"]) ?>
                                </td>

                                <td>
                                    $<?= number_format(
                                        $item["sales_total"],
                                        2
                                    ) ?>
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