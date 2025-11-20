<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function auth($required_role, $redirect_to)
{
    if (!isset($_SESSION['username']) || empty($_SESSION['username']) || !isset($_SESSION['role'])) {
        // User is not logged in or session is invalid
        header("Location: $redirect_to"); // Redirect to the central login page
        exit();
    }

    $current_role = $_SESSION['role'] ?? 'guest';

    if ($current_role !== $required_role) {
        header("Location: $redirect_to");
        exit();
    }
}
