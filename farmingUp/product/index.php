<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    die("User is not logged in.");
}

// Get user details from session
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farming_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';
$sortBy = isset($_POST['sort_by']) ? $_POST['sort_by'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';

//SQL query with search filter, category filter, and sorting option
$sql = "SELECT * FROM product WHERE NAME LIKE ?";

// Add category filter to SQL query if a category is selected
if (!empty($category)) {
    $sql .= " AND CATEGORY = ?";
}

// Add sorting to SQL query
if ($sortBy === 'price') {
    $sql .= " ORDER BY PRICE";
} elseif ($sortBy === 'rating') {
    $sql .= " ORDER BY RATING";
} else {
    $sql .= " ORDER BY NAME";
}

$stmt = $conn->prepare($sql);
$searchTermWildcard = '%' . $searchTerm . '%';

if (!empty($category)) {
    $stmt->bind_param("ss", $searchTermWildcard, $category);
} else {
    $stmt->bind_param("s", $searchTermWildcard);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Store</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="user-info">
            <span>User Name: <?php echo htmlspecialchars($name); ?></span> | <span>ID: <?php echo htmlspecialchars($user_id); ?></span>
        </div>
        <form method="post" action="">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search for products..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                
                <select name="category">
                    <option value="">All Categories</option>
                    <option value="vegetables" <?php echo $category == 'vegetables' ? 'selected' : ''; ?>>Vegetables</option>
                    <option value="fruits" <?php echo $category == 'fruits' ? 'selected' : ''; ?>>Fruits</option>
                    <option value="fish" <?php echo $category == 'fish' ? 'selected' : ''; ?>>Fish</option>
                </select>

                <select name="sort_by">
                    <option value="">Sort by</option>
                    <option value="name" <?php echo $sortBy == 'name' ? 'selected' : ''; ?>>Name</option>
                    <option value="price" <?php echo $sortBy == 'price' ? 'selected' : ''; ?>>Price</option>
                    <option value="rating" <?php echo $sortBy == 'rating' ? 'selected' : ''; ?>>Rating</option>
                </select>
                
                <button type="submit">Search</button>
            </div>
        </form>
        <div class="cart">
            <span id="cart-count">0</span>
            <button id="cart-button" class="cart-button">Cart</button>
        </div>
    </nav>

    <section class="product-list" id="product-list">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product">';
            
            // Get photo filename or URL from the database
            $photo = htmlspecialchars($row["photo"]);
            $photoPath = 'uploads/' . $photo;

            // Check if photo is a URL or local path
            if (filter_var($photo, FILTER_VALIDATE_URL)) {
                // If it's a URL, use it directly
                $displayPhotoPath = $photo;
            } else {
                // If it's a local path, check if the file exists
                $displayPhotoPath = file_exists($photoPath) ? $photoPath : 'images/default.jpg'; 
            }
            
            echo '<img src="' . $displayPhotoPath . '" alt="Product Image">';
            echo '<div class="product-info">';
            echo '<h3>' . htmlspecialchars($row["NAME"]) . '</h3>';
            echo '<p>Category: ' . htmlspecialchars($row["CATEGORY"]) . '</p>';
            echo '<p>Quantity: ' . htmlspecialchars($row["QUANTITY"]) . '</p>';
            echo '<p>Rating: ' . htmlspecialchars($row["RATING"]) . '</p>';
            echo '<p>Review: ' . htmlspecialchars($row["REVIEW"]) . '</p>';
            echo '<p>Price: ' . htmlspecialchars($row["PRICE"]) . ' taka</p>';
            echo '<button class="add-to-cart" data-id="' . htmlspecialchars($row["PRODUCT_ID"]) . '" data-quantity="' . htmlspecialchars($row["QUANTITY"]) . '">Add to Cart</button>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo 'No products found.';
    }
    ?>
    </section>

    <aside class="cart-sidebar" id="cart-sidebar">
        <button class="close-cart" id="close-cart">X</button>
        <h2>Your Cart</h2>
        <div id="cart-items" class="cart-items">
            <p>Your cart is empty.</p>
        </div>
        <div class="cart-summary">
            <p>Total Price: <span id="total-price">0.00 taka</span></p>
            <button id="checkout-button">Checkout</button>
        </div>
    </aside>

<script>
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function () {
        const productId = this.getAttribute('data-id');
        const availableQuantity = parseInt(this.getAttribute('data-quantity'), 10);
        addToCart(productId, availableQuantity);
    });
});

function addToCart(productId, availableQuantity) {
    const quantity = 1; 

    if (quantity > availableQuantity) {
        alert("Cannot add more of this product. Available quantity: " + availableQuantity);
        return;
    }

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'product_id': productId,
            'quantity': quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartUI();
        } else {
            alert("Failed to add product to cart. bacause  Product unavailable or quantity exceeds available stock.");
        }
    });
}

function updateCartUI() {
    fetch('get_cart.php')
    .then(response => response.json())
    .then(data => {
        const cartItemsContainer = document.getElementById('cart-items');
        cartItemsContainer.innerHTML = '';

        let totalPrice = 0;
        data.cartItems.forEach(item => {
            totalPrice += item.total_price;
            cartItemsContainer.innerHTML += 
                `<div class="cart-item" data-id="${item.product_id}">
                    <img src="${item.photo}" alt="${item.name}">
                    <div>
                        <p>${item.name}</p>
                        <p>Price: ${item.price} taka x 
                            <input type="number" class="quantity-input" data-id="${item.product_id}" value="${item.quantity}" min="1">
                        </p>
                        <button class="remove-button" data-id="${item.product_id}">Remove</button>
                    </div>
                </div>`;
        });

        document.getElementById('total-price').innerText = `${totalPrice.toFixed(2)} taka`;
        document.getElementById('cart-count').innerText = data.cartItems.length;

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function () {
                const productId = this.getAttribute('data-id');
                const newQuantity = parseInt(this.value, 10);
                updateQuantity(productId, newQuantity);
            });
        });

        document.querySelectorAll('.remove-button').forEach(button => {
            button.addEventListener('click', function () {
                removeFromCart(this.getAttribute('data-id'));
            });
        });
    });
}

function updateQuantity(productId, quantity) {
    const cartItem = document.querySelector(`.cart-item[data-id="${productId}"]`);
    const availableQuantity = parseInt(cartItem.querySelector('.quantity-input').getAttribute('data-available-quantity'), 10);

    if (quantity > availableQuantity) {
        alert("Cannot set quantity more than available. Available quantity: " + availableQuantity);
        return;
    }

    fetch('update_quantity.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'product_id': productId,
            'quantity': quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartUI();
        } else {
            alert("Failed to update quantity.");
        }
    });
}

function removeFromCart(productId) {
    fetch('remove_from_cart.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'product_id': productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartUI();
        } else {
            alert("Failed to remove product from cart.");
        }
    });
}

document.getElementById('checkout-button').addEventListener('click', function () {
    const cartItems = Array.from(document.querySelectorAll('.cart-item'));
    if (cartItems.length === 0) {
        alert("Your cart is empty.");
        return;
    }

    const queryParams = new URLSearchParams();
    cartItems.forEach(item => {
        const productId = item.getAttribute('data-id');
        const quantity = item.querySelector('.quantity-input').value;
        queryParams.append('id[]', productId);
        queryParams.append('quantity[]', quantity);
    });

    queryParams.append('total_price', document.getElementById('total-price').innerText);

    window.location.href = `chart_want.php?${queryParams.toString()}`;
});

//to handle cart button click
document.getElementById('cart-button').addEventListener('click', function () {
    document.getElementById('cart-sidebar').classList.add('show');
});

//to handle close button click
document.getElementById('close-cart').addEventListener('click', function () {
    document.getElementById('cart-sidebar').classList.remove('show');
});
</script>


</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
