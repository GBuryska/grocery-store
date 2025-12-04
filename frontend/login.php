<?php
session_start();
require_once "../backend/db_config.php";

$error = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Table: username
    // Columns: username (PK), password (PLAINTEXT)
    $sql = "SELECT username, password FROM username WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($db_user, $db_pass);
            $stmt->fetch();

            // Compare plain text to plain text
            if ($password === $db_pass) {

                $_SESSION["username"] = $db_user;

                header("Location: LoggedInIndex.php");
                exit();

            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Username not found.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
    <nav class="navbar">
        <div class="nav-left">
            <a class="brand" href="index.php">Grocery Store</a>
        </div>

        <div class="nav-right">
            <a href="index.php">Search</a>
            <a href="login.php">Login</a>
            <a href="create-account.php" class="btn">Create Account</a>
        </div>
    </nav>

    <div class=" login-signup">

        <h1>Login</h1>

        <?php if (!empty($error)): ?>
            <p style="color:red; font-weight:bold;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">

            <label>Username</label>
            <input type="text" name="username" required>
            <br>
            <br>
            <label>Password</label>
            <input type="password" name="password" required>
            <br>
            <br>
            <br>
            <button type="submit">Login</button>

        </form>

    </div>

</body>

</html>