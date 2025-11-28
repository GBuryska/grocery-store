<?php
session_start();

// Force browser not to cache this page
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// If user is logged in, redirect to logged-in page
if (isset($_SESSION["username"])) {
    header("Location: LoggedInIndex.php");
    exit();
}
?>


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
            <a href="index.php">Search</a>
            <a href="login.php">Login</a>
            <a href="create-account.php" class="btn">Create Account</a>
        </div>
    </nav>

    <div class="container">

        <h1>Available Items</h1>

        <form method="GET" action="items.php" class="search-form">
            <input type="text" name="query" placeholder="Search items..."
                value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <hr>

        <div class="item-grid">
            <?php if ($result && $result->num_rows > 0): ?>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="item-card">

                        <img src="<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">

                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>

                        <?php if (!empty($row['brand'])): ?>
                            <p style="color:#777; font-size:14px;"><?php echo htmlspecialchars($row['brand']); ?></p>
                        <?php endif; ?>

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
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
                <p>No items found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>