<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product Entry Form</title>
    <link rel="stylesheet" href="style.css">
    <?php if(isset($_SESSION['alert'])): ?>
    <script>
        alert("<?php echo $_SESSION['alert']; ?>");
    </script>
    <?php unset($_SESSION['alert']); endif; ?>
</head>
<body>
   
    <div class="heading"><h2>Add New Product</h2></div>
    
    <!-- Product Entry Form -->
    <form method="POST" action="submit_product.php">
        <label>Product Name:</label>
        <input type="text" name="product_name" required>
        
        <label>HSN Code:</label>
        <input type="text" name="hsncode" required>
        
        <label>Tax (%):</label>
        <input type="number" name="tax" step="0.01" required>
        
        <label>Price:</label>
        <input type="number" name="price" step="0.01" required>
        
        <button type="submit" class="submit_btn">
            Submit
        </button>
    </form>

    <!-- View Products and Search Form -->
    <form method="GET" style="margin-top: 20px;">
        <button type="submit" name="view_products" class="view-btn">View Products</button>
        <div class="search-container">
            <input type="text" name="search" class="searchbox" placeholder="Search products..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit" name="search_btn" class="search_btn">
                Search
            </button>
        </div>
    </form>

    <?php
    // Display products table when View Products or Search is clicked
    if(isset($_GET['view_products']) || isset($_GET['search_btn'])) {
        $conn = new mysqli("localhost", "root", "", "store");
        
        if($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
         // Search functionality
        $search_term = "%";
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $search_term = "%" . $_GET['search'] . "%";
        }


        // Delete functionality
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
            $delete_stmt = $conn->prepare("DELETE FROM products WHERE id=?");
            $delete_stmt->bind_param("i", $_POST['delete_id']);
            $delete_stmt->execute();
            $delete_stmt->close();
        }

       
        $sql = "SELECT * FROM products WHERE product_name LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            echo '<div class="products-table">
                    <h3>Product List</h3>
                    <table>
                        <tr>
                            <th>Product Name</th>
                            <th>HSN Code</th>
                            <th>Tax (%)</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>';
            
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['product_name']}</td>
                        <td>{$row['hsncode']}</td>
                        <td>{$row['tax']}</td>
                        <td>{$row['price']}</td>
                        <td class='actions'>
                            <form method='GET' action='edit_product.php' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <button type='submit' class='edit-btn'>Edit</button>
                            </form>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='delete_id' value='{$row['id']}'>
                                <button type='submit' class='delete-btn'>Delete</button>
                            </form>
                        </td>
                    </tr>";
            }
            
            echo '</table></div>';
        } else {
            $message = isset($_GET['search']) ? 
                "No products found matching '".htmlspecialchars($_GET['search'])."'" : 
                "No products found in the database.";
            echo '<p style="text-align:center">'.$message.'</p>';
        }
        
        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>