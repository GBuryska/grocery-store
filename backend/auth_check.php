<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function auth($redirect_to)
{
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        header("Location: $redirect_to");
        exit();
    }
}
