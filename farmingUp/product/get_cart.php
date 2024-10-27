<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'User not logged in']));
}

$user_id = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farming_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

// Fetch cart data
$sql = "SELECT c.product_id, c.quantity, c.price, p.NAME AS name, p.photo 
        FROM cart c 
        JOIN product p ON c.product_id = p.PRODUCT_ID 
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$totalPrice = 0;

while ($row = $result->fetch_assoc()) {
    $itemTotalPrice = $row['price'] * $row['quantity'];
    $totalPrice += $itemTotalPrice;
    $cartItems[] = array_merge($row, ['total_price' => $itemTotalPrice]);
}

$stmt->close();
$conn->close();

echo json_encode(["cartItems" => $cartItems, "totalPrice" => $totalPrice]);
?>
