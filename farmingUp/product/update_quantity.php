<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farming_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

// Check the available quantity
$sql = "SELECT QUANTITY FROM product WHERE PRODUCT_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($product) {
    $available_quantity = $product['QUANTITY'];

    if ($quantity > $available_quantity) {
        echo json_encode(['success' => false, 'message' => 'Quantity exceeds available stock']);
        exit;
    }

    // Update the cart quantity
    $sql = "UPDATE cart SET QUANTITY = ? WHERE PRODUCT_ID = ? AND USER_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $product_id, $user_id);
    $stmt->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
}

$stmt->close();
$conn->close();
?>
