<?php
require_once 'db_config.php';

$search = isset($_GET['query']) ? trim($_GET['query']) : "";

// Base query
$query = "SELECT 
            item_id,
            name,
            brand,
            category,
            price,
            sale_price,
            currency,
            image_url
          FROM food_items
          WHERE is_active = 1";

// Apply search if not empty
if (!empty($search)) {
  $item = $conn->real_escape_string($search);
  $query .= " AND (name LIKE '%$item%' OR brand LIKE '%$item%' OR category LIKE '%$item%')";
}

$query .= " ORDER BY name ASC";

$result = $conn->query($query);
