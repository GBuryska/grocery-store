<?php
require_once 'env.php';

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("ERROR: Could not connect to the database. {$conn->connect_error}");
}
