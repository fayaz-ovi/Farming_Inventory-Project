<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = ""; // Update with your MySQL password
$dbname = "farming_inventory";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from the request
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$sql = "SELECT * FROM product WHERE PRODUCT_ID = $productId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo json_encode([
        'id' => $product['PRODUCT_ID'],
        'name' => $product['NAME'],
        'price' => $product['PRICE'],
        'photo' => $product['photo'] ?? 'placeholder.jpg',
        'category' => $product['CATEGORY'],  // Include category here
    ]);
} else {
    echo json_encode(['error' => 'Product not found']);
}

$conn->close();
?>
