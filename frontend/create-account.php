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
            <a class="brand" href="index.php">My Store</a>
        </div>

        <div class="nav-right">
            <a href="index.php">Search</a>
            <a href="login.php">Login</a>
            <a href="create-account.php" class="btn">Create Account</a>
        </div>
    </nav>

    <div class="container login-signup">

        <h1>Sign Up</h1>
        <p>Join the Grocery Store for exclusive offers.</p>

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

            // Reset classes
            messageBox.className = 'p-3 mb-6 rounded-lg text-sm transition-all duration-300 ease-in-out';

            // Set classes based on message type
            if (type === 'success') {
                messageBox.classList.add('bg-green-100', 'text-green-700');
            } else if (type === 'error') {
                messageBox.classList.add('bg-red-100', 'text-red-700');
            } else {
                // Default style for general info if needed
                messageBox.classList.add('bg-blue-100', 'text-blue-700');
            }

            messageText.textContent = message;
            // Show the box
            setTimeout(() => {
                messageBox.classList.add('message-show');
            }, 10);

            // Hide the message after 5 seconds
            setTimeout(() => {
                messageBox.classList.remove('message-show');
            }, 5000);
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