<?php

session_start();

require_once "config/database.php";


// Only administrators may update menu items
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Invalid request.");
}


// Get submitted values

$itemId = filter_input(
    INPUT_POST,
    "item_id",
    FILTER_VALIDATE_INT
);

$name = trim($_POST["name"] ?? "");

$category = $_POST["category"] ?? "";

$price = filter_input(
    INPUT_POST,
    "price",
    FILTER_VALIDATE_FLOAT
);

$availability = filter_input(
    INPUT_POST,
    "availability",
    FILTER_VALIDATE_INT
);


// Allowed categories
$validCategories = [
    "Coffee",
    "Non-Coffee",
    "Tea"
];


// Server-side validation

if ($itemId === false || $itemId < 1) {
    die("Invalid menu item ID.");
}


if (
    $name === "" ||
    strlen($name) > 100
) {
    die("Invalid item name.");
}


if (!in_array($category, $validCategories, true)) {
    die("Invalid category.");
}


if (
    $price === false ||
    $price <= 0 ||
    $price > 999.99
) {
    die("Invalid price.");
}


if (
    $availability === false ||
    !in_array($availability, [0, 1], true)
) {
    die("Invalid availability value.");
}


// to update the database

$stmt = $pdo->prepare("
    UPDATE menu_items

    SET
        name = :name,
        category = :category,
        price = :price,
        availability = :availability

    WHERE item_id = :item_id
");


$stmt->execute([
    "name" => $name,
    "category" => $category,
    "price" => $price,
    "availability" => $availability,
    "item_id" => $itemId
]);


header("Location: manage_menu.php?updated=1");
exit;

?>