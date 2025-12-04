<?php
require_once "../backend/db_config.php";

require_once "../backend/auth_check.php";
auth("./login.php");
$username = $_SESSION['username'];

// Fetch all orders for this user
$sql = "
    SELECT order_id, total_cost, placed_at
    FROM orders
    WHERE username = ?
    ORDER BY placed_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="styles.css" />
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-left">
            <a class="brand" href="index.php">Grocery Store</a>
        </div>
        <div class="nav-right">
            <a href="items.php">Search Items</a>
            <a href="myCart.php">My Cart</a>
            <a href="orders.php">My Orders</a>
            <a href="../backend/logout.php">Log Out</a>
        </div>
    </nav>

    <div class="container">
        <h1>My Orders</h1>
        <hr>

        <?php if ($result && $result->num_rows > 0): ?>
            <table style="width:100%; border-collapse: collapse; text-align:left;">
                <thead>
                    <tr>
                        <th style="border-bottom:1px solid #ddd; padding:8px;">Order ID</th>
                        <th style="border-bottom:1px solid #ddd; padding:8px;">Date Ordered</th>
                        <th style="border-bottom:1px solid #ddd; padding:8px;">Total Amount</th>
                        <th style="border-bottom:1px solid #ddd; padding:8px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td style="padding:8px;"><?php echo $row['order_id']; ?></td>
                            <td style="padding:8px;"><?php echo $row['placed_at']; ?></td>
                            <td style="padding:8px;"><?php echo '$' . number_format($row['total_cost'], 2); ?></td>
                            <td style="padding:8px;">
                                <a href="orderDetails.php?order_id=<?php echo $row['order_id']; ?>">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You haven’t placed any orders yet.</p>
            <a href="items.php">Start Shopping</a>
        <?php endif; ?>

    </div>

</body>

</html>