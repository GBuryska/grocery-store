<?php include "../backend/create-account.php"; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Grocery Store</title>
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

    <div class="container login-signup">

        <h1>Sign Up</h1>

        <div id="messageBox">
            <p id="messageText"></p>
        </div>

        <form id="registerForm" onsubmit="handleRegistration(event)">

            <div style="margin-bottom:15px;">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter a unique username"
                    minlength="3" maxlength="50">
            </div>

            <div style="margin-bottom:15px;">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required
                    placeholder="Must be at least 8 characters" minlength="8">
            </div>

            <button type="submit" id="submitButton">
                Create Account
            </button>
        </form>
    </div>
    <script>
        function showMessage(message, type) {
            const messageBox = document.getElementById('messageBox');
            const messageText = document.getElementById('messageText');

            messageText.textContent = message;
        }
        async function handleRegistration(event) {
            event.preventDefault();

            const form = document.getElementById('registerForm');
            const formData = new FormData(form);

            const response = await fetch("../backend/create-account.php", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (data.result == 0) {
                showMessage("Username taken!");
                return;
            }

            window.location.href = "login.php";
        }
    </script>
</body>