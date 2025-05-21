<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$product_name = $_POST['product_name'];
$hsncode = $_POST['hsncode'];
$tax = $_POST['tax'];
$price = $_POST['price'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO products (product_name, hsncode, tax, price) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssdd", $product_name, $hsncode, $tax, $price);

// Execute the query
if ($stmt->execute()) {
    echo "New product added successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>