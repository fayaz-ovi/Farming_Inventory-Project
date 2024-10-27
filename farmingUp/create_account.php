<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Account</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Farmers Market</h1>
        </header>
        <div class="divBody">
        <?php
// Database connection parameters
$servername = "localhost";
$username_db = "root"; // Renamed to avoid conflict with form input
$password_db = "";
$dbname = "farming_inventory";

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a unique 8-character ID
function generateUniqueID($conn) {
    do {
        $unique_id = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
        $stmt = $conn->prepare("SELECT customer_id FROM users WHERE customer_id = ?");
        $stmt->bind_param("s", $unique_id);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);
    $stmt->close();
    return $unique_id;
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']);
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $userType = $_POST['userType'];
    $bankAccount = trim($_POST['bankAccount']);
    $bankName = $_POST['bankName'];
    $branchOrRouting = $_POST['branchOrRouting'];
    $routingNumber = trim($_POST['routingNumber']);
    $branchName = $_POST['branchName'];

    // Validate form data
    if (empty($username) || empty($password) || empty($phone) || empty($name) || empty($address) || empty($userType)) {
        echo "<div class='error'>All required fields must be filled.</div>";
        exit();
    }

    // Additional validation for farmers
    if ($userType === 'farmer' && (empty($bankName) || empty($bankAccount) || 
        ($branchOrRouting === 'branch' && empty($branchName)) || 
        ($branchOrRouting === 'routing' && empty($routingNumber)))) {
        echo "<div class='error'>All bank details are required for farmers.</div>";
        exit();
    }

    // Check if email or phone number already exists
    $stmt = $conn->prepare("SELECT customer_id FROM users WHERE username = ? OR phone = ?");
    $stmt->bind_param("ss", $username, $phone);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<div class='error'>Email or phone number is already in use.</div>";
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Generate a unique customer ID and hash the password
    $customer_id = generateUniqueID($conn);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into users table
    $sql = "INSERT INTO users (customer_id, username, password, phone, name, address, userType) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $customer_id, $username, $hashed_password, $phone, $name, $address, $userType);

    if ($stmt->execute()) {
        // Insert into farmer table if the user is a farmer
        if ($userType === 'farmer') {
            $final_branch_or_routing = ($branchOrRouting === 'branch') ? $branchName : $routingNumber;
            $stmt_farmer = $conn->prepare("INSERT INTO farmer (user_id, total_earning, product_sold_count, bank_name, branch, account_no) VALUES (?, 0.00, 0, ?, ?, ?)");
            $stmt_farmer->bind_param("ssss", $customer_id, $bankName, $final_branch_or_routing, $bankAccount);

            if (!$stmt_farmer->execute()) {
                echo "<div class='error'>Error inserting farmer details: " . $stmt_farmer->error . "</div>";
                $stmt->close();
                $stmt_farmer->close();
                $conn->close();
                exit();
            }
            $stmt_farmer->close();
        }

        // Redirect to the login page after successful account creation
        header("Location: log_in.php");
        exit();
    } else {
        echo "<div class='error'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<div class="create-account-container">
                <form class="create-account-form" action="create_account.php" method="post" id="createAccountForm">
                    <h2>Create Account</h2>
                    <div class="input-group">
                        <label for="username">Email</label>
                        <input type="email" id="username" name="username" required />
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required />
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required pattern="[0-9]{11}" title="Enter an 11-digit phone number" />
                    </div>
                    <div class="input-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required />
                    </div>
                    <div class="input-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" required />
                    </div>
                    <div class="input-group">
                        <label for="userType">User Type</label>
                        <select id="userType" name="userType" onchange="toggleFarmerFields()" required>
                            <option value="">Select...</option>
                            <option value="customer">Customer</option>
                            <option value="farmer">Farmer</option>
                        </select>
                    </div>

                    <!-- Farmer-specific fields -->
                    <div id="farmerFields" style="display: none;">
                        <div class="input-group">
                            <label for="bankName">Bank Name</label>
                            <select id="bankName" name="bankName">
                                <option value="">Select...</option>
                                <option value="BRAC Bank">BRAC Bank</option>
                                <option value="Sonali Bank">Sonali Bank</option>
                                <option value="City Bank">City Bank</option>
                                <option value="Pubali Bank">Pubali Bank</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="bankAccount">Bank Account Number</label>
                            <input type="text" id="bankAccount" name="bankAccount" />
                        </div>
                        <div class="input-group">
                            <label for="branchOrRouting">Branch or Routing</label>
                            <select id="branchOrRouting" name="branchOrRouting" onchange="toggleBranchRoutingFields()">
                                <option value="">Select...</option>
                                <option value="branch">Branch</option>
                                <option value="routing">Routing Number</option>
                            </select>
                        </div>
                        <div class="input-group" id="branchNameField" style="display: none;">
                            <label for="branchName">Branch Name</label>
                            <input type="text" id="branchName" name="branchName" />
                        </div>
                        <div class="input-group" id="routingNumberField" style="display: none;">
                            <label for="routingNumber">Routing Number</label>
                            <input type="text" id="routingNumber" name="routingNumber" />
                        </div>
                    </div>

                    <!-- End of Farmer-specific fields -->

                    <div class="input-group">
                        <button type="submit">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
        <footer class="footer">
            <p>&copy; 2023 Farmers Market. All rights reserved.</p>
        </footer>
    </div>

    <script>
        // Toggle Farmer Fields based on User Type
        function toggleFarmerFields() {
            var userType = document.getElementById("userType").value;
            var farmerFields = document.getElementById("farmerFields");
            var bankName = document.getElementById("bankName");
            var bankAccount = document.getElementById("bankAccount");
            var branchOrRouting = document.getElementById("branchOrRouting");
            var branchName = document.getElementById("branchName");
            var routingNumber = document.getElementById("routingNumber");

            if (userType === "farmer") {
                farmerFields.style.display = "block";
                // Make farmer fields required
                bankName.setAttribute("required", "required");
                bankAccount.setAttribute("required", "required");
                branchOrRouting.setAttribute("required", "required");
            } else {
                farmerFields.style.display = "none";
                // Remove required attribute from farmer fields
                bankName.removeAttribute("required");
                bankAccount.removeAttribute("required");
                branchOrRouting.removeAttribute("required");
                branchName.removeAttribute("required");
                routingNumber.removeAttribute("required");
            }
        }

        // Toggle Branch/Routing Fields based on selection
        function toggleBranchRoutingFields() {
            var branchOrRouting = document.getElementById("branchOrRouting").value;
            var branchNameField = document.getElementById("branchNameField");
            var routingNumberField = document.getElementById("routingNumberField");

            if (branchOrRouting === "branch") {
                branchNameField.style.display = "block";
                routingNumberField.style.display = "none";
                document.getElementById("branchName").setAttribute("required", "required");
                document.getElementById("routingNumber").removeAttribute("required");
            } else if (branchOrRouting === "routing") {
                branchNameField.style.display = "none";
                routingNumberField.style.display = "block";
                document.getElementById("routingNumber").setAttribute("required", "required");
                document.getElementById("branchName").removeAttribute("required");
            } else {
                branchNameField.style.display = "none";
                routingNumberField.style.display = "none";
                document.getElementById("branchName").removeAttribute("required");
                document.getElementById("routingNumber").removeAttribute("required");
            }
        }
    </script>
</body>
</html>