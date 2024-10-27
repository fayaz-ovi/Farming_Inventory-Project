<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("User is not logged in.");
}

$user_id = $_SESSION['user_id']; 


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farming_inventory";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the cart items, including product prices
$query = "SELECT product_id, quantity, price FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_amount = 0;

// Start transaction
$conn->begin_transaction();

try {
    // Process each cart item to decrease product quantity
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $price = $row['price']; // Collect price from cart table

        // Decrease the quantity in the product table
        $update_query = "UPDATE product SET QUANTITY = QUANTITY - ? WHERE PRODUCT_ID = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('ii', $quantity, $product_id);
        $update_stmt->execute();

       
        $total_amount += $quantity * $price;

        // Step 2: Insert each product's details into the checkout table
        $total_amountWithFee = $total_amount + ($total_amount * 0.05); // Apply 5% increase

        $checkout_query = "INSERT INTO checkout (product_id, quantity, user_id, total_amount) 
                           VALUES (?, ?, ?, ?)";
        $checkout_stmt = $conn->prepare($checkout_query);
        $checkout_stmt->bind_param('iiid', $product_id, $quantity, $user_id, $total_amountWithFee);
        $checkout_stmt->execute();

        // Step 3: Fetch the user_id (farmer) associated with this product from the product table
        $fetch_farmer_query = "SELECT user_id FROM product WHERE PRODUCT_ID = ?";
        $fetch_farmer_stmt = $conn->prepare($fetch_farmer_query);
        $fetch_farmer_stmt->bind_param('i', $product_id);
        $fetch_farmer_stmt->execute();
        $fetch_farmer_stmt->bind_result($farmer_id);
        $fetch_farmer_stmt->fetch();
        $fetch_farmer_stmt->close();

        // Step 4: Update farmer table with total earnings and sold product count
        $total_earning = $quantity * $price; 
        $update_farmer_query = "UPDATE farmer 
                                SET total_earning = total_earning + ?, product_sold_count = product_sold_count + ?
                                WHERE user_id = ?";
        $update_farmer_stmt = $conn->prepare($update_farmer_query);
        $update_farmer_stmt->bind_param('dii', $total_earning, $quantity, $farmer_id);
        $update_farmer_stmt->execute();
    }

    $clear_cart_query = "DELETE FROM cart WHERE user_id = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_query);
    $clear_cart_stmt->bind_param('i', $user_id);
    $clear_cart_stmt->execute();


    $extra_cost = $total_amount * 0.05;
    $admin_query = "UPDATE admin SET BALANCE = BALANCE + ? WHERE ADMIN_ID = 1";
    $admin_stmt = $conn->prepare($admin_query);
    $admin_stmt->bind_param('d', $extra_cost);
    $admin_stmt->execute();

    $conn->commit();
    

    $conn->close();


    echo "Checkout successful! Your order has been placed.";
    echo "<a href='index.php'><button>Go to Products</button></a>";

} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    echo "An error occurred: " . $e->getMessage();
}
?>
