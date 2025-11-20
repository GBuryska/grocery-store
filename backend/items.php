<?php
require_once 'db_config.php';

// Search string (optional)
$search = isset($_GET['q']) ? trim($_GET['q']) : "";

// Base query
$query = "SELECT 
            item_id,
            name,
            brand,
            category,
            price,
            sale_price,
            currency,
            image_url,
            thumbnail_url
          FROM food_items
          WHERE is_active = 1";

// Apply search if not empty
if (!empty($search)) {
    $escaped = $conn->real_escape_string($search);
    $query .= " AND (name LIKE '%$escaped%' OR brand LIKE '%$escaped%' OR category LIKE '%$escaped%')";
}

$query .= " ORDER BY name ASC";

$result = $conn->query($query);
