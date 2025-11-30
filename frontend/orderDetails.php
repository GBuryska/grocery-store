<?php
session_start();
require_once "../backend/db_config.php";

$username = $_SESSION['username'] ?? null;
if (!$username) {
    header("Location: ../frontend/LogIn.php");
    exit();
}

// Validate order_id
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order ID.");
}
$order_id = intval($_GET['order_id']);

// Verify the order belongs to the logged-in user
$chk = $conn->prepare("SELECT total_cost, placed_at FROM orders WHERE order_id = ? AND username = ?");
$chk->bind_param("is", $order_id, $username);
$chk->execute();
$order_res = $chk->get_result();

if ($order_res->num_rows === 0) {
    die("Order not found or you do not have permission to view it.");
}

$order = $order_res->fetch_assoc();

// Fetch all the items for this order
$sql = "
    SELECT 
        oi.quantity, 
        f.name, 
        COALESCE(f.sale_price, f.price) AS price, 
        f.currency
    FROM order_items oi
    JOIN food_items f ON oi.item_id = f.item_id
    WHERE oi.order_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>

    <!-- Navbar -->
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
        <p><strong>Date Ordered:</strong> <?php echo $order['placed_at']; ?></p>
        <hr>

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
                while ($row = $items->fetch_assoc()):
                    $item_total = $row['quantity'] * $row['price'];
                    $grand_total += $item_total;
                    $currency = $row['currency'];
                ?>
                    <tr>
                        <td style="padding:8px;"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td style="padding:8px;"><?php echo $row['quantity']; ?></td>
                        <td style="padding:8px;"><?php echo $currency . number_format($row['price'], 2); ?></td>
                        <td style="padding:8px; text-align:right;"><?php echo $currency . number_format($item_total, 2); ?></td>
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

        <a href="orders.php" style="margin-top:20px; display:inline-block;">Back to Orders</a>
    </div>

</body>
</html>
