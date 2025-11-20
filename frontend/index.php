<?php include "./backend/items.php"; ?>

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
            <a href="login.php">Login</a>
            <a href="register.php" class="btn">Create Account</a>
        </div>
    </nav>

    <div class="container">
        <h1>Welcome to My Store</h1>
        <form method="GET" action="index.php" class="search-form">
            <input type="text" name="q" placeholder="Search for a product..."
                value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" />
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="item-grid">
        <?php
        $query = isset($_GET['q']) ? strtolower($_GET['q']) : "";

        foreach ($items as $item) {
            if ($query && strpos(strtolower($item["name"]), $query) === false) {
                continue; // Skip items that don’t match search
            }

            echo "
                <div class='item-card'>
                    <img src='{$item['image']}' alt='{$item['name']}'>
                    <h3>{$item['name']}</h3>
                    <p>$" . number_format($item['price'], 2) . "</p>
                    <button>Add to Cart</button>
                </div>
            ";
        }
        ?>
    </div>
</body>