<?php

session_start();

require_once "config/database.php";


// Only logged in administrators may use this page
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
$orderId = filter_input(
    INPUT_POST,
    "order_id",
    FILTER_VALIDATE_INT
);

$status = $_POST["status"] ?? "";


// List of statuses that are actually allowed
$validStatuses = [
    "Pending",
    "Preparing",
    "Completed"
];


// Validate the order ID
if ($orderId === false || $orderId < 1) {
    die("Invalid order ID.");
}


// Validate the status
if (!in_array($status, $validStatuses, true)) {
    die("Invalid order status.");
}


// Update the order
$stmt = $pdo->prepare("
    UPDATE orders
    SET status = :status
    WHERE order_id = :order_id
");

$stmt->execute([
    "status" => $status,
    "order_id" => $orderId
]);


// Return to the order page
header("Location: view_orders.php");
exit;

?>