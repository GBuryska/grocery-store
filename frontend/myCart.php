<?php
include "../backend/db_config.php"; // your DB connection

require_once "../backend/auth_check.php";
auth("./login.php");
$username = $_SESSION['username'];

// Handle update quantity or remove item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        $cart_item_id = intval($_POST['cart_item_id']);
        $quantity = intval($_POST['quantity']);
        if ($quantity > 0) {
            $stmt = $conn->prepare("UPDATE cart_items SET quantity=? WHERE cart_item_id=? AND username=?");
            $stmt->bind_param("iis", $quantity, $cart_item_id, $username);
            $stmt->execute();
        }
    } elseif (isset($_POST['remove_cart'])) {
        $cart_item_id = intval($_POST['cart_item_id']);
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_item_id=? AND username=?");
        $stmt->bind_param("is", $cart_item_id, $username);
        $stmt->execute();
    }
}

// Fetch cart items for display
$sql = "
    SELECT c.cart_item_id, c.quantity, f.name, f.brand, f.image_url, f.price, f.sale_price, f.currency
    FROM cart_items c
    JOIN food_items f ON c.item_id = f.item_id
    WHERE c.username = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Cart</title>
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
        <h1>My Cart</h1>
        <hr>

        <div class="item-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="item-card">
                        <img src="<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <?php if (!empty($row['brand'])): ?>
                            <p class="brand"><?php echo htmlspecialchars($row['brand']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($row['sale_price']) && $row['sale_price'] > 0): ?>
                            <p>
                                <span class="original-price"><?php echo $row['currency'] . $row['price']; ?></span>
                                <span class="sale-price"><?php echo $row['currency'] . $row['sale_price']; ?></span>
                            </p>
                        <?php else: ?>
                            <p><?php echo $row['currency'] . $row['price']; ?></p>
                        <?php endif; ?>

                        <form method="POST" style="margin-top:10px;">
                            <input type="hidden" name="cart_item_id" value="<?php echo $row['cart_item_id']; ?>">
                            <label>Quantity: </label>
                            <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1"
                                style="width:60px;">
                            <button type="submit" name="update_cart">Update</button>
                            <button type="submit" name="remove_cart" style="background-color:red;color:white;">Remove</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <form action="checkout.php" method="GET">
                <button type="submit" class="checkout-btn">Checkout</button>
            </form>
        <?php endif; ?>
    </div>

</body>

</html>