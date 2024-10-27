<?php
session_start(); // Start the session

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbname = "farming_inventory";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT customer_id, password, userType, name FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $hashed_password, $userType, $name);
    $stmt->fetch();

    if ($hashed_password && password_verify($password, $hashed_password)) {
        // Store user_id and name in session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['name'] = $name;

        if ($userType == 'customer') {
            $message = "Login successful! Redirecting to shopping...";
            echo "<script>setTimeout(() => { window.location.href = 'product/index.php'; }, 1000);</script>";
        } elseif ($userType == 'farmer') {
            $message = "Login successful! Redirecting to farmer's market...";
            echo "<script>setTimeout(() => { window.location.href = 'farmersPAGE.php'; }, 1000);</script>";
        }
    } else {
        $message = "Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Farmers Market</h1>
        </header>
        <div class="divBody">
            <div class="login-container">
                <form class="login-form" action="" method="post">
                    <h2>Login</h2>
                    <div class="input-group">
                        <label for="username">Email</label>
                        <input type="text" id="username" name="username" required />
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required />
                    </div>
                    <div class="input-group">
                        <button type="submit">Login</button>
                    </div>
                    <div id="message" style="color:red;"><?php echo $message; ?></div>
                    <div class="input-group center-link">
                        <a href="create_account.php"><h3>Create an account</h3></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
