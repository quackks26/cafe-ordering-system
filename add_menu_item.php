<?php

session_start();

require_once "config/database.php";


// Only administrators can add menu items
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


// Only accept POST submissions
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Invalid request.");
}


// Get submitted values
$name = trim($_POST["name"] ?? "");
$category = $_POST["category"] ?? "";
$price = filter_input(
    INPUT_POST,
    "price",
    FILTER_VALIDATE_FLOAT
);

$validCategories = [
    "Coffee",
    "Non-Coffee",
    "Tea"
];


// Server side validation

if (
    $name === "" ||
    strlen($name) > 100
) {
    header("Location: manage_menu.php?error=1");
    exit;
}


if (!in_array($category, $validCategories, true)) {
    header("Location: manage_menu.php?error=1");
    exit;
}


if (
    $price === false ||
    $price <= 0 ||
    $price > 999.99
) {
    header("Location: manage_menu.php?error=1");
    exit;
}


// to insert new item

$stmt = $pdo->prepare("
    INSERT INTO menu_items
        (
            name,
            category,
            price,
            availability
        )
    VALUES
        (
            :name,
            :category,
            :price,
            1
        )
");


$stmt->execute([
    "name" => $name,
    "category" => $category,
    "price" => $price
]);


header("Location: manage_menu.php?added=1");
exit;

?>