<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    die("User is not logged in.");
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farming_inventory";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT p.PRODUCT_ID, p.NAME, p.PRICE, p.photo, c.quantity AS cart_quantity, (c.quantity * p.PRICE) AS total_price 
          FROM cart c 
          JOIN product p ON c.product_id = p.PRODUCT_ID 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Error preparing the SQL statement: " . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
            background-color: #333;
            color: white;
            padding: 10px 0;
        }
        .navbar .user-info {
            font-size: 18px;
        }
        .cart-container {
            width: 80%;
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .cart-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .cart-table th, .cart-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .cart-item-image {
            width: 100px;
            height: auto;
        }
        .cart-summary {
            margin-top: 20px;
        }
        .checkout-btn {
            background-color: #ffcc00;
            color: #000;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .checkout-btn:hover {
            background-color: #e6b800;
        }
    </style>
</head>
<body>
    <nav class='navbar'>
        <div class='user-info'>
            <span>User Name: <?php echo htmlspecialchars($name); ?></span> || 
            <span>ID: <?php echo htmlspecialchars($user_id); ?></span>
        </div>
    </nav>

    <div class="cart-container">
        <h1>My Cart</h1>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)) : ?>
                    <?php foreach ($products as $item) : ?>
                        <tr>
                            <td>
                                <?php
                                // Check if the photo is a valid URL or use a local path
                                $photo = htmlspecialchars($item['photo']);
                                $localPhotoPath = 'uploads/' . $photo;

                                if (filter_var($photo, FILTER_VALIDATE_URL)) {
                                    $displayPhotoPath = $photo;
                                } else {
                                    $displayPhotoPath = $localPhotoPath;
                                }
                                ?>
                                <img src="<?php echo $displayPhotoPath; ?>" alt="Product Image" class="cart-item-image">
                                <div class="cart-item-details">
                                    <p><?php echo htmlspecialchars($item['NAME']); ?></p>
                                </div>
                            </td>
                            <td><?php echo number_format($item['PRICE'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['cart_quantity']); ?></td>
                            <td><?php echo number_format($item['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Order Summary</h3>
            <?php 
                $subtotal = array_sum(array_column($products, 'total_price')); 
                $platformFee = $subtotal * 0.05; 
                $estimatedTotal = $subtotal + $platformFee;
            ?>
            <p>Subtotal: <?php echo number_format($subtotal, 2); ?></p>
            <p>Platform Fee (5%): <?php echo number_format($platformFee, 2); ?></p>
            <p class="estimated-total">Estimated Total: <?php echo number_format($estimatedTotal, 2); ?></p>
            <button class="checkout-btn" onclick="window.location.href='checkout.php'">Checkout</button>
        </div>
    </div>
</body>
</html>