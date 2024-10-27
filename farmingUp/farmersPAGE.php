<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

$message = ""; // Variable to store messages

// Handle form submission for adding a product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "farming_inventory";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $category = $_POST['category'];
    $productName = $_POST['productName'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = $_FILES['image']['name'];
    $target_dir = "product/uploads/";
    $target_file = $target_dir . basename($image);

    // Handle file upload
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO product (NAME, CATEGORY, PRICE, QUANTITY, photo, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiss", $productName, $category, $price, $quantity, $image, $user_id);

        if ($stmt->execute()) {
            $message = "Product added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Error uploading file.";
    }

    $conn->close();
}

// Database connection
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "farming_inventory";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for total earning, sold, and unsold values
$total_earning = 0;
$sold_products = 0;
$unsold_products = 0;

// Fetch total earning and sold products count from the farmer table
$farmer_query = "SELECT total_earning, product_sold_count FROM farmer WHERE user_id = ?";
$stmt = $conn->prepare($farmer_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_earning, $sold_products);
$stmt->fetch();
$stmt->close();

// Fetch unsold product quantity from the product table
$unsold_query = "SELECT SUM(QUANTITY) AS unsold_quantity FROM product WHERE user_id = ?";
$stmt = $conn->prepare($unsold_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($unsold_products);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        body {
            background-color: #f5f5f5;
            color: #333;
            padding: 20px;
        }
        .navbar {
            height: 10vh;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            background-image: linear-gradient(to right, rgb(38, 166, 154), rgb(33, 150, 243));
            border-radius: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .pinfo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .userName, .userID {
            font-size: 20px;
            color: #fff;
        }
        .edit {
            padding: 10px 20px;
            background-color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        .edit:hover {
            cursor: pointer;
            background-color: #000;
            color: #fff;
        }
        .hero {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            height: 40vh;
        }
        .centerBox {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            background-color: #444;
            padding: 30px;
            border-radius: 20px;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .totalearning, .totalsales {
            width: 45%;
            padding: 20px;
            background-color: #333;
            border-radius: 15px;
            text-align: center;
            color: white;
        }
        .tearning {
            font-size: 48px;
            margin-top: 10px;
            color: #ffca28;
        }
        .sales-info {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .sales-box {
            text-align: center;
            font-size: 24px;
            padding: 10px;
        }
        .sales-box h2 {
            margin: 0;
            font-size: 36px;
        }
        .sales-box p {
            margin: 0;
            font-size: 16px;
            color: lightgray;
        }
        .sold-box h2 {
            color: #4caf50;
        }
        .unsold-box h2 {
            color: #f44336;
        }
        .addProduct {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }
        .addProductInfo {
            width: 60%;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .addProductInfo label {
            display: block;
            margin: 15px 0 5px;
            font-size: 16px;
        }
        .addProductInfo input, .addProductInfo select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .submitButton {
            width: 100%;
            padding: 12px;
            background-color: #2196f3;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submitButton:hover {
            background-color: #1e88e5;
        }
        @media (max-width: 768px) {
            .centerBox {
                flex-direction: column;
                text-align: center;
            }
            .totalearning, .totalsales {
                width: 100%;
                margin-bottom: 20px;
            }
            .addProductInfo {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <section class="navbar">
        <div class="pinfo">
            <h1 class="userName"><?php echo htmlspecialchars($user_name); ?></h1>
            <h1 class="userID"><?php echo htmlspecialchars($user_id); ?></h1>
        </div>
        <button id="edit" class="edit">Edit</button>
    </section>

    <!-- Hero Section -->
    <section class="hero">
        <div class="centerBox">

            <div class="totalearning">
                <h1>Total Earning</h1>
                <div class="tearning"><?php echo number_format($total_earning, 2); ?> taka</div> <!-- Display total earning -->
            </div>

            <div class="totalsales">
                <h1>Total Sales</h1>
                <div class="sales-info">
                    <div class="sales-box sold-box">
                        <h2><?php echo $sold_products; ?></h2> <!-- Display sold products count -->
                        <p>Sold</p>
                    </div>
                    <div class="sales-box unsold-box">
                        <h2><?php echo $unsold_products; ?></h2> <!-- Display unsold products count -->
                        <p>Unsold</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Add Product Section -->
    <section class="addProduct">
        <form action="" method="post" enctype="multipart/form-data" class="addProductInfo">
            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <option value="">Select Category</option>
                <option value="Fish">Fish</option>
                <option value="Vegetable">Vegetable</option>
                <option value="Fruits">Fruits</option>
                <option value="Meat">Meat</option>
                <option value="Rice">Rice</option>
            </select>

            <label for="productName">Product Name:</label>
            <input type="text" id="productName" name="productName" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>

            <label for="image">Product Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <input type="submit" value="Add Product" class="submitButton">
        </form>
    </section>

    <script>
        document.getElementById("edit").addEventListener("click", function() {
            window.location.href = "edit_farmer.php";
        });
    </script>

    <?php if ($message): ?>
        <script>alert('<?php echo $message; ?>');</script>
    <?php endif; ?>

</body>
</html>
