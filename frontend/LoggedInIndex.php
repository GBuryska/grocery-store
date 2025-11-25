<?php include "../backend/items.php"; ?>

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
            <a href="cart.php">My Cart</a>
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

                        <!-- Button -->
                        <button>Add to Cart</button>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
                <p>No items found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>