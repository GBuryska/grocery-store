<?php
session_start();
require_once "../backend/db_config.php";

$username = $_SESSION['username'] ?? null;
if (!$username) {
    header("Location: ../frontend/LogIn.php");
    exit();
}

// Enable mysqli exceptions for proper error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Cancel checkout
    if (isset($_POST['cancel_checkout'])) {
        header("Location: myCart.php");
        exit();
    }

    // Confirm checkout
    if (isset($_POST['confirm_checkout'])) {

        // Fetch cart items
        $cart_sql = "
            SELECT c.item_id, c.quantity, COALESCE(f.sale_price, f.price) AS price
            FROM cart_items c
            JOIN food_items f ON c.item_id = f.item_id
            WHERE c.username = ?
        ";
        $stmt = $conn->prepare($cart_sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            header("Location: myCart.php");
            exit();
        }

        $total_cost = 0;
        $items = [];
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
            $total_cost += $row['quantity'] * $row['price'];
        }

        $conn->begin_transaction();
        try {
            // Insert into orders table
            $order_stmt = $conn->prepare("
                INSERT INTO orders (username, total_cost, order_status, payment_method, placed_at)
                VALUES (?, ?, 'Pending', 'Cash', NOW())
            ");
            $order_stmt->bind_param("sd", $username, $total_cost);
            $order_stmt->execute();
            $order_id = $conn->insert_id;

            // Insert into order_items table
            $item_stmt = $conn->prepare("
                INSERT INTO order_items (order_id, item_id, quantity)
                VALUES (?, ?, ?)
            ");
            foreach ($items as $i) {
                $item_stmt->bind_param("iii", $order_id, $i['item_id'], $i['quantity']);
                $item_stmt->execute();
            }

            // Clear cart
            $clear_stmt = $conn->prepare("DELETE FROM cart_items WHERE username=?");
            $clear_stmt->bind_param("s", $username);
            $clear_stmt->execute();

            $conn->commit();
            header("Location: orders.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            die("Checkout failed: " . $e->getMessage());
        }
    }
}
?>
