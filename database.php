<?php

// Database settings
$host = "localhost";
$dbname = "cafe_ordering_system";
$username = "root";
$password = "";

try {
    // Connect to the MySQL database using PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Make PDO show database errors as exceptions
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>