<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "store");

if(!isset($_GET['id'])) {
    header("Location: product_form.php");
    exit();
}

// Get existing product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Update functionality
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_stmt = $conn->prepare("UPDATE products SET product_name=?, hsncode=?, tax=?, price=? WHERE id=?");
    $update_stmt->bind_param("ssddi", 
        $_POST['product_name'],
        $_POST['hsncode'],
        $_POST['tax'],
        $_POST['price'],
        $_POST['id']
    );
    
    if($update_stmt->execute()) {
        $_SESSION['alert'] = "Product updated successfully!";
    } else {
        $_SESSION['alert'] = "Error updating product!";
    }
    
    header("Location: product_form.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="heading"><h2>Edit Product</h2></div>
    
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        
        <label>Product Name:</label>
        <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>" required>
        
        <label>HSN Code:</label>
        <input type="text" name="hsncode" value="<?php echo $product['hsncode']; ?>" required>
        
        <label>Tax (%):</label>
        <input type="number" name="tax" step="0.01" value="<?php echo $product['tax']; ?>" required>
        
        <label>Price:</label>
        <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
        
        <input type="submit" value="Update Product">
    </form>
</body>
</html>