<?php

function create_account($username, $password)
{
    require_once 'db_config.php';

    $result = $conn->query("SELECT 1 FROM username WHERE username = '$username';");

    if ($result->num_rows > 0) {
        return 0;
    }

    $conn->query("INSERT INTO username (username, password) VALUES ('$username', '$password');");

    return 1;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $result = create_account($username, $password);

    echo json_encode(["result" => $result]);
    exit;
}