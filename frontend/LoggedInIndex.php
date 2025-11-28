<?php 
session_start();
include "../backend/db_config.php"; // DB connection
include "../backend/items.php"; // if this is where $result comes from

// Assume user is logged in
$username = $_SESSION['username'] ?? 'Braden'; // fallback for testing

// Handle Add to Cart submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);
    
    // Check if item is already in cart
    $stmt = $conn->prepare("SELECT quantity FROM cart_items WHERE username=? AND item_id=?");
    $stmt->bind_param("si", $username, $item_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        // Update existing quantity
        $row = $res->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE cart_items SET quantity=? WHERE username=? AND item_id=?");
        $update->bind_param("isi", $new_quantity, $username, $item_id);
        $update->execute();
    } else {
        // Insert new row
        $insert = $conn->prepare("INSERT INTO cart_items (username, item_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("sii", $username, $item_id, $quantity);
        $insert->execute();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grocery Store</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a class="brand" href="index.php">My Store</a>
        </div>
        <div class="nav-right">
            <a href="items.php">Search Items</a>
            <a href="myCart.php">My Cart</a>
            <a href="orders.php">My Orders</a>
            <a href="../backend/logout.php">Log Out</a>
        </div>
    </nav>

    <div class="container">
        <h1>Available Items</h1>

        <!-- Search Bar -->
        <form method="GET" action="items.php" class="search-form">
            <input type="text" name="q" placeholder="Search items..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <hr>

        <div class="item-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="item-card">
                        <!-- Image -->
                        <img src="<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">

                        <!-- Name -->
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>

                        <!-- Brand -->
                        <?php if (!empty($row['brand'])): ?>
                            <p style="color:#777; font-size:14px;"><?php echo htmlspecialchars($row['brand']); ?></p>
                        <?php endif; ?>

                        <!-- Price -->
                        <?php if (!empty($row['sale_price']) && $row['sale_price'] > 0): ?>
                            <p>
                                <span style="text-decoration: line-through; color:#888;">
                                    <?php echo $row['currency'] . $row['price']; ?>
                                </span>
                                <span style="color:green; font-weight:bold;">
                                    <?php echo $row['currency'] . $row['sale_price']; ?>
                                </span>
                            </p>
                        <?php else: ?>
                            <p><?php echo $row['currency'] . $row['price']; ?></p>
                        <?php endif; ?>

                        <!-- Add to Cart Form -->
                        <form method="POST">
                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" style="width:60px;">
                            <button type="submit" name="add_to_cart">Add to Cart</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No items found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
