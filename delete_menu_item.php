<?php

session_start();

require_once "config/database.php";


// Only logged-in administrators may delete menu items
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Invalid request.");
}


// Get and validate the item ID

$itemId = filter_input(
    INPUT_POST,
    "item_id",
    FILTER_VALIDATE_INT
);


if ($itemId === false || $itemId < 1) {
    header("Location: manage_menu.php?delete_error=invalid");
    exit;
}


// Check whether this item appears in any order

$checkStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM order_items
    WHERE item_id = :item_id
");

$checkStmt->execute([
    "item_id" => $itemId
]);

$orderCount = (int) $checkStmt->fetchColumn();


// do not allow it to be deleted if the item has been ordered before
if ($orderCount > 0) {

    header(
        "Location: manage_menu.php?delete_error=in_use"
    );

    exit;
}


// Delete the menu item

$deleteStmt = $pdo->prepare("
    DELETE FROM menu_items
    WHERE item_id = :item_id
");

$deleteStmt->execute([
    "item_id" => $itemId
]);


// Check whether anything was actually deleted
if ($deleteStmt->rowCount() === 0) {

    header(
        "Location: manage_menu.php?delete_error=not_found"
    );

    exit;
}


// if uccess
header("Location: manage_menu.php?deleted=1");
exit;

?>