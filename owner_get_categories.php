<?php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

$restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);

if (!$restaurant_id || $restaurant_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid restaurant ID']);
    exit;
}

try {
    // Get categories with their items
    $stmt = $pdo->prepare("SELECT id, name, description FROM menu_categories WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $categories = $stmt->fetchAll();

    $result = [];
    foreach ($categories as $category) {
        // Get items for each category
        $itemStmt = $pdo->prepare("SELECT id, name, description, price, stock, image_path FROM menu_items WHERE category_id = ?");
        $itemStmt->execute([$category['id']]);
        $items = $itemStmt->fetchAll();

        $result[] = [
            'id' => $category['id'],
            'name' => $category['name'],
            'description' => $category['description'],
            'items' => $items
        ];
    }

    echo json_encode(['success' => true, 'data' => $result]);
} catch (Exception $e) {
    error_log("Error in owner_get_categories.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load categories']);
}
?>