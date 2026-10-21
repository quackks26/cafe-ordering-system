<?php

session_start();

require_once "config/database.php";


// Only accept data submitted using POST

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Invalid request.");
}


// Get the submitted customer information

$customerName = trim($_POST["fullName"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$orderType = $_POST["orderType"] ?? "";


// get cart
$cartJson = $_POST["cartData"] ?? "";

// json cart -> php array
$cart = json_decode($cartJson, true);


// Server-side validation

$errors = [];


// Validate customer name
if (
    !preg_match(
        "/^[A-Za-z][A-Za-z .'-]{1,79}$/",
        $customerName
    )
) {
    $errors[] = "Please enter a valid customer name.";
}


// Validate phone number
$phoneDigits = preg_replace("/\D/", "", $phone);

if (
    !preg_match("/^[0-9+() -]{7,20}$/", $phone) ||
    strlen($phoneDigits) < 7 ||
    strlen($phoneDigits) > 15
) {
    $errors[] = "Please enter a valid phone number.";
}


// Validate order type
$validOrderTypes = ["Dine in", "Take away"];

if (!in_array($orderType, $validOrderTypes, true)) {
    $errors[] = "Please select a valid order type.";
}


// Validate cart
if (!is_array($cart) || count($cart) === 0) {
    $errors[] = "Your cart is empty or invalid.";
}


// Stop here if basic validation failed
if (!empty($errors)) {

    echo "<h1>Unable to place order</h1>";

    foreach ($errors as $error) {
        echo "<p>" . htmlspecialchars($error) . "</p>";
    }

    echo '<p><a href="checkout.php">Return to Checkout</a></p>';

    exit;
}


// Check cart items against the database

try {

    $menuStmt = $pdo->prepare("
        SELECT item_id, name, price
        FROM menu_items
        WHERE item_id = :item_id
          AND availability = 1
    ");


    $validatedItems = [];

    // Calculate money using cents
    $totalCents = 0;


    foreach ($cart as $item) {

        // Make sure each cart entry is an array
        if (!is_array($item)) {
            throw new Exception("Invalid cart item.");
        }


        // Validate item ID
        $itemId = filter_var(
            $item["item_id"] ?? null,
            FILTER_VALIDATE_INT
        );


        // Validate quantity
        $quantity = filter_var(
            $item["quantity"] ?? null,
            FILTER_VALIDATE_INT
        );


        if (
            $itemId === false ||
            $itemId < 1 ||
            $quantity === false ||
            $quantity < 1 ||
            $quantity > 99
        ) {
            throw new Exception(
                "One or more cart items are invalid."
            );
        }


        // Look up the item in mysql
        $menuStmt->execute([
            "item_id" => $itemId
        ]);

        $menuItem = $menuStmt->fetch(PDO::FETCH_ASSOC);


         //no item found -> unavailable or does not exist
        if (!$menuItem) {
            throw new Exception(
                "One or more menu items are unavailable."
            );
        }


        $unitPrice = (float) $menuItem["price"];

        // Convert price to cents
        $unitPriceCents =
            (int) round($unitPrice * 100);

        // Calculate item's subtotal
        $subtotalCents =
            $unitPriceCents * $quantity;

        // Add it to the full order total
        $totalCents += $subtotalCents;


        // Store item information
        $validatedItems[] = [
            "item_id" => (int) $menuItem["item_id"],
            "name" => $menuItem["name"],
            "quantity" => $quantity,
            "unit_price" => $unitPrice
        ];
    }


    // Convert total back into dollar
    $total = number_format(
        $totalCents / 100,
        2,
        ".",
        ""
    );


    // start database transaction

    $pdo->beginTransaction();


    // Insert the main order

    $orderStmt = $pdo->prepare("
        INSERT INTO orders
            (
                customer_name,
                phone,
                order_type,
                status,
                total
            )
        VALUES
            (
                :customer_name,
                :phone,
                :order_type,
                'Pending',
                :total
            )
    ");


    $orderStmt->execute([
        "customer_name" => $customerName,
        "phone" => $phone,
        "order_type" => $orderType,
        "total" => $total
    ]);


    $orderId = $pdo->lastInsertId();


    // Insert the individual order items

    $itemStmt = $pdo->prepare("
        INSERT INTO order_items
            (
                order_id,
                item_id,
                quantity,
                unit_price
            )
        VALUES
            (
                :order_id,
                :item_id,
                :quantity,
                :unit_price
            )
    ");


    foreach ($validatedItems as $item) {

        $itemStmt->execute([
            "order_id" => $orderId,
            "item_id" => $item["item_id"],
            "quantity" => $item["quantity"],
            "unit_price" => $item["unit_price"]
        ]);
    }


    // Everything worked -> save all changes

    $pdo->commit();

	// Store the confirmation details temporarily so is displays on success page
	$_SESSION["order_success"] = [
	    "order_id" => $orderId,
	    "customer_name" => $customerName,
	    "order_type" => $orderType,
	    "total" => $total
	];


	// Redirect to a separate confirmation page
	header("Location: order_success.php");
	exit;


} catch (Throwable $e) {

	// undo database changes if something failed

	if ($pdo->inTransaction()) {
    		$pdo->rollBack();
	}

	die("Unable to process the order. Please try again.");
}


?>

