<?php
// Database connection
$servername = "localhost";
$username_db = "root";
$password_db = "12345";
$dbname = "farming_inventory";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if (!isset($_SESSION['user_id'])) {
    die("User is not logged in.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $bank_name = isset($_POST['bankName']) ? $_POST['bankName'] : null;
    $branch = isset($_POST['branch']) ? $_POST['branch'] : null;
    $account_no = isset($_POST['bankAccount']) ? $_POST['bankAccount'] : null;
    $routing_number = isset($_POST['routingNumber']) ? trim($_POST['routingNumber']) : null;

    // Email validation
    if ($email) {
        $stmt = $conn->prepare("SELECT customer_id FROM users WHERE username = ? AND customer_id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error_message = "This email is already in use.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE customer_id = ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Phone number validation
    if ($phone) {
        $stmt = $conn->prepare("SELECT customer_id FROM users WHERE phone = ? AND customer_id != ?");
        $stmt->bind_param("si", $phone, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error_message = "This phone number is already in use.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET phone = ? WHERE customer_id = ?");
            $stmt->bind_param("si", $phone, $user_id);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Name update
    if ($name) {
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE customer_id = ?");
        $stmt->bind_param("si", $name, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Bank details update (optional)
    if ($bank_name && $branch && $account_no) {
        $stmt = $conn->prepare("UPDATE farmer SET bank_name = ?, branch = ?, account_no = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $bank_name, $branch, $account_no, $user_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($routing_number && $bank_name && $account_no) {
        $stmt = $conn->prepare("UPDATE farmer SET bank_name = ?, branch = ?, account_no = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $bank_name, $routing_number, $account_no, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    $success_message = "Details updated successfully!";
}

// Fetch current details
$stmt = $conn->prepare("SELECT u.username, u.phone, u.name, f.bank_name, f.branch, f.account_no FROM users u JOIN farmer f ON u.customer_id = f.user_id WHERE u.customer_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Farmer Details</title>
    <style>
        /* Same styles as before */
        body {
            font-family: 'Arial', sans-serif;
            background: #e8f4f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 40px auto;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .input-group { margin: 20px 0; }
        .input-group label { display: block; color: #555; font-weight: bold; }
        .input-group input, .input-group select { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        .input-group .btn { width: 100%; background: #28a745; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 18px; cursor: pointer; transition: background 0.3s ease, transform 0.2s ease; }
        .input-group .btn:hover { background: #218838; transform: scale(1.02); }
        .input-group .btn:active { background: #1e7e34; }
        .success, .error { text-align: center; margin-bottom: 15px; font-size: 18px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Your Details</h2>

    <?php if (!empty($success_message)) : ?>
        <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)) : ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form action="" method="post" class="edit-account-form">
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" />
        </div>
        <div class="input-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" />
        </div>
        <div class="input-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" />
        </div>
        <div class="input-group">
            <label for="bankName">Bank Name</label>
            <select id="bankName" name="bankName">
                <option value="">Select...</option>
                <option value="BRAC Bank" <?php echo ($user['bank_name'] == 'BRAC Bank') ? 'selected' : ''; ?>>BRAC Bank</option>
                <option value="Rupali Bank" <?php echo ($user['bank_name'] == 'Rupali Bank') ? 'selected' : ''; ?>>Rupali Bank</option>
                <option value="National Bank" <?php echo ($user['bank_name'] == 'National Bank') ? 'selected' : ''; ?>>National Bank</option>
                <option value="City Bank" <?php echo ($user['bank_name'] == 'City Bank') ? 'selected' : ''; ?>>City Bank</option>
                <option value="Dutch Bangla Bank" <?php echo ($user['bank_name'] == 'Dutch Bangla Bank') ? 'selected' : ''; ?>>Dutch Bangla Bank</option>
                <option value="Islami Bank" <?php echo ($user['bank_name'] == 'Islami Bank') ? 'selected' : ''; ?>>Islami Bank</option>
                <option value="South East Bank" <?php echo ($user['bank_name'] == 'South East Bank') ? 'selected' : ''; ?>>South East Bank</option>
                <option value="One Bank" <?php echo ($user['bank_name'] == 'One Bank') ? 'selected' : ''; ?>>One Bank</option>
                <option value="Sonali Bank" <?php echo ($user['bank_name'] == 'Sonali Bank') ? 'selected' : ''; ?>>Sonali Bank</option>
            </select>
        </div>
        <div class="input-group">
            <label for="branch">Branch or Routing</label>
            <input type="text" id="branch" name="branch" value="<?php echo htmlspecialchars($user['branch'] ?? ''); ?>" />
        </div>
        <div class="input-group">
            <label for="bankAccount">Bank Account Number</label>
            <input type="text" id="bankAccount" name="bankAccount" value="<?php echo htmlspecialchars($user['account_no'] ?? ''); ?>" />
        </div>
        <div class="input-group">
            <button type="submit" class="btn">Update Details</button>
        </div>
    </form>
</div>

</body>
</html>
