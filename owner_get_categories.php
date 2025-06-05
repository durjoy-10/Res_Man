<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "food_delivery");
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// Get restaurant ID from query parameter
$restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_SANITIZE_NUMBER_INT);
if ($restaurant_id <= 0) {
    error_log("Invalid restaurant ID: $restaurant_id");
    echo json_encode(["success" => false, "message" => "Invalid restaurant ID"]);
    $conn->close();
    exit();
}

// Fetch categories
$stmt = $conn->prepare("SELECT id, name, description FROM categories WHERE restaurant_id = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(["success" => false, "message" => "Database query preparation failed"]);
    $conn->close();
    exit();
}
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();

$categories = [];
while ($category = $result->fetch_assoc()) {
    $category_id = $category['id'];
    
    // Fetch items for this category
    $item_stmt = $conn->prepare("SELECT id, name, description, price, stock, image_path FROM items WHERE category_id = ?");
    if (!$item_stmt) {
        error_log("Prepare failed for items: " . $conn->error);
        continue;
    }
    $item_stmt->bind_param("i", $category_id);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
    
    $items = [];
    while ($item = $item_result->fetch_assoc()) {
        $items[] = [
            "id" => $item['id'],
            "name" => $item['name'] ?? '',
            "description" => $item['description'] ?? '',
            "price" => $item['price'] ?? 0.0,
            "stock" => $item['stock'] ?? 0,
            "image_path" => $item['image_path'] ?? ''
        ];
    }
    $item_stmt->close();
    
    $categories[] = [
        "id" => $category['id'],
        "name" => $category['name'] ?? '',
        "description" => $category['description'] ?? '',
        "items" => $items
    ];
}

echo json_encode(["success" => true, "data" => $categories]);

$stmt->close();
$conn->close();
?>