<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $_SESSION['alert'] = "Database connection failed: " . $conn->connect_error;
    header("Location: product_form.php");
    exit();
}

// Sanitize and validate input data
$product_name = trim($_POST['product_name']);
$hsncode = trim($_POST['hsncode']);
$tax = filter_input(INPUT_POST, 'tax', FILTER_VALIDATE_FLOAT);
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

// Validate inputs
if (empty($product_name) || empty($hsncode) || $tax === false || $price === false) {
    $_SESSION['alert'] = "Invalid input data! Please check all fields.";
    header("Location: product_form.php");
    exit();
}

// Prepare SQL statement
$sql = "INSERT INTO products (product_name, hsncode, tax, price) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['alert'] = "Database error: " . $conn->error;
    header("Location: product_form.php");
    exit();
}

// Bind parameters and execute
$stmt->bind_param("ssdd", $product_name, $hsncode, $tax, $price);

if ($stmt->execute()) {
    $_SESSION['alert'] = "New product added successfully!";
} else {
    $_SESSION['alert'] = "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();

// Redirect back to form
header("Location: product_form.php");
exit();
?>