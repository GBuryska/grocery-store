<?php
include "../backend/db_config.php";

require_once "../backend/auth_check.php";
auth("./login.php");
$username = $_SESSION['username'];

// Handle Confirm Checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_checkout'])) {
    // Calculate total
    $cart_items = $conn->prepare("
        SELECT c.quantity, COALESCE(f.sale_price, f.price) AS item_price
        FROM cart_items c
        JOIN food_items f ON c.item_id = f.item_id
        WHERE c.username = ?
    ");
    $cart_items->bind_param("s", $username);
    $cart_items->execute();
    $res = $cart_items->get_result();

    $total = 0;
    while ($row = $res->fetch_assoc()) {
        $total += $row['quantity'] * $row['item_price'];
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (username, total_amount, date_ordered) VALUES (?, ?, NOW())");
    $stmt->bind_param("sd", $username, $total);
    $stmt->execute();

    // Clear cart
    $clear = $conn->prepare("DELETE FROM cart_items WHERE username=?");
    $clear->bind_param("s", $username);
    $clear->execute();

    // Redirect to success page
    header("Location: order_success.php");
    exit();
}

// Handle Cancel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_checkout'])) {
    header("Location: myCart.php");
    exit();
}

// Fetch cart items for display
$sql = "
    SELECT c.cart_item_id, c.quantity, f.name, COALESCE(f.sale_price, f.price) AS price, f.currency
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
    <title>Checkout</title>
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
        <h1>Checkout</h1>
        <hr>

        <?php if ($result && $result->num_rows > 0): ?>
            <table style="width:100%; border-collapse: collapse; text-align:left;">
                <thead>
                    <tr>
                        <th style="border-bottom:1px solid #ddd; padding:8px;">Item</th>
                        <th style="border-bottom:1px solid #ddd; padding:8px;">Quantity</th>
                        <th style="border-bottom:1px solid #ddd; padding:8px;">Price</th>
                        <th style="border-bottom:1px solid #ddd; padding:8px; text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand_total = 0;
                    $currency = '$';
                    while ($row = $result->fetch_assoc()):
                        $item_total = $row['quantity'] * $row['price'];
                        $grand_total += $item_total;
                        $currency = $row['currency'];
                        ?>
                        <tr>
                            <td style="padding:8px;"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td style="padding:8px;"><?php echo $row['quantity']; ?></td>
                            <td style="padding:8px;"><?php echo $row['currency'] . number_format($row['price'], 2); ?></td>
                            <td style="padding:8px; text-align:right;">
                                <?php echo $row['currency'] . number_format($item_total, 2); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="3" style="text-align:right; padding:8px; font-weight:bold;">Grand Total:</td>
                        <td style="padding:8px; font-weight:bold; text-align:right;">
                            <?php echo $currency . number_format($grand_total, 2); ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <form method="POST" action="ConfirmCheckout.php" style="margin-top:20px;">
                <button type="submit" name="confirm_checkout"
                    style="padding:10px 20px; background-color:green; color:white;">Confirm Checkout</button>
                <button type="submit" name="cancel_checkout"
                    style="padding:10px 20px; background-color:red; color:white;">Cancel</button>
            </form>


        <?php else: ?>
            <p>Your cart is empty.</p>
            <a href="items.php">Go back to items</a>
        <?php endif; ?>
    </div>

</body>

</html>