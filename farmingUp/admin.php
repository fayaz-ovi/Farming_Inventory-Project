<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'farming_inventory');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Log out
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Login Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'admin_id' and 'password' are set in the POST array
    $admin_id = isset($_POST['admin_id']) ? trim($_POST['admin_id']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if (!empty($admin_id) && !empty($password)) {
        $admin_id = $mysqli->real_escape_string($admin_id);
        $password = $mysqli->real_escape_string($password);

        $result = $mysqli->query("SELECT * FROM admin WHERE ADMIN_ID='$admin_id' AND PASSWORD='$password'");
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION['admin'] = $row['ADMIN_ID']; // Store the admin ID in the session
            $admin_balance = $row['BALANCE'];
        } else {
            echo "<p style='color:red; text-align:center;'>Invalid login details</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>Please enter both Admin ID and Password</p>";
    }
}

// Retrieve admin balance if logged in
$admin_balance = '';
if (isset($_SESSION['admin'])) {
    $admin_id = $_SESSION['admin'];
    $admin_id = $mysqli->real_escape_string($admin_id);
    $balance_query = $mysqli->query("SELECT BALANCE FROM admin WHERE ADMIN_ID='$admin_id'");
    if ($balance_query->num_rows > 0) {
        $admin_balance = $balance_query->fetch_assoc()['BALANCE'];
    }
}

// If not logged in, show login form
if (!isset($_SESSION['admin'])) {
    echo "
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f2e6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #4CAF50;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            color: white;
            margin-bottom: 20px;
        }
        .login-container label {
            color: white;
            font-weight: bold;
        }
        .login-container input[type='text'], .login-container input[type='password'] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }
        .login-container button {
            background-color: white;
            color: #4CAF50;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .login-container button:hover {
            background-color: #3e8e41;
        }
    </style>
    <div class='login-container'>
        <h2>Admin Login</h2>
        <form method='POST'>
            <label>Admin ID:</label><br>
            <input type='text' name='admin_id' required><br>
            <label>Password:</label><br>
            <input type='password' name='password' required><br>
            <button type='submit'>Login</button>
        </form>
    </div>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e6f2e6; }
        .navbar { background-color: #4CAF50; overflow: hidden; }
        .navbar a { float: left; display: block; color: white; text-align: center; padding: 14px 16px; text-decoration: none; }
        .navbar a.logout { float: right; }
        .navbar .balance { float: right; color: white; padding: 14px 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 10px; }
        th { background-color: #f2f2f2; }
        .btn { margin: 10px 0; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .btn:hover { background-color: #45a049; }
        .search-bar { margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="?table=farmer">Farmers</a>
    <a href="?table=product">Products</a>
    <a href="?table=checkout">Checkout</a>
    <span class="balance">Balance: $<?php echo htmlspecialchars($admin_balance); ?></span>
    <a class="logout" href="?logout=true">Logout</a>
</div>

<h2 style="text-align:center;">Welcome Admin</h2>

<?php
// Display table content
if (isset($_GET['table'])) {
    $table = $_GET['table'];

    echo "
    <form method='GET' class='search-bar'>
        <input type='hidden' name='table' value='$table'>
        <input type='text' name='search' placeholder='Search by PRODUCT_ID or user_id'>
        <button type='submit'>Search</button>
    </form>";

    // Handle search
    $search_query = "";
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $mysqli->real_escape_string($_GET['search']);
        if ($table === 'farmer') {
            $search_query = "WHERE user_id  LIKE '%$search%'";
        } elseif ($table === 'product') {
            $search_query = "WHERE PRODUCT_ID LIKE '%$search%'";
        }
    }

    if ($table === 'farmer' || $table === 'product' || $table === 'checkout') {
        $result = $mysqli->query("SELECT * FROM $table $search_query");

        echo "<table><tr>";
        $fields = $result->fetch_fields();
        foreach ($fields as $field) {
            if ($field->name !== 'PASSWORD') {
                echo "<th>{$field->name}</th>";
            }
        }
        echo "<th>Action</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                if ($key !== 'PASSWORD') {
                    echo "<td>$value</td>";
                }
            }
            if ($table === 'product') {
                echo "<td><a href='?delete_product={$row['PRODUCT_ID']}'>Delete</a></td>";
            } elseif ($table === 'farmer') {
                echo "<td><a href='?delete_farmer={$row['user_id']}'>Delete</a></td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Delete product
if (isset($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];
    $mysqli->query("DELETE FROM product WHERE PRODUCT_ID='$product_id'");
 
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Delete farmer
if (isset($_GET['delete_farmer'])) {
    $farmer_id = $_GET['delete_farmer'];
    $mysqli->query("DELETE FROM farmer WHERE user_id='$farmer_id'");
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>

</body>
</html>
