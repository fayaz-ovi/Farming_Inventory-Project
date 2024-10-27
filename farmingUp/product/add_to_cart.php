<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farming_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

// Fetch available stock for the product
$sql = "SELECT QUANTITY FROM product WHERE PRODUCT_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($availableQuantity);
$stmt->fetch();
$stmt->close();

if ($quantity > $availableQuantity) {
    echo json_encode(['success' => false, 'message' => 'Requested quantity exceeds available stock. Available: ' . $availableQuantity]);
    $conn->close();
    exit;
}


$sql = "SELECT QUANTITY FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->bind_result($cartQuantity);
$stmt->fetch();
$stmt->close();

if ($cartQuantity !== null) {
    // If product is already in cart, update the quantity
    $newQuantity = $cartQuantity + $quantity;
    
    // Check again if the new quantity exceeds available stock
    if ($newQuantity > $availableQuantity) {
        echo json_encode(['success' => false, 'message' => 'Updated quantity exceeds available stock. Available: ' . $availableQuantity]);
        $conn->close();
        exit;
    }

    // Update the cart quantity
    $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $newQuantity, $user_id, $product_id);
} else {

    $sql = "INSERT INTO cart (user_id, product_id, quantity, price) 
            VALUES (?, ?, ?, (SELECT PRICE FROM product WHERE PRODUCT_ID = ?))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $product_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
}

$stmt->close();
$conn->close();
?>
