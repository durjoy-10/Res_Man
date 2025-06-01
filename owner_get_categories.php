<?php
// owner_get_categories.php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

$restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_SANITIZE_NUMBER_INT);

if (!$restaurant_id) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, description FROM menu_categories WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $categories = $stmt->fetchAll();

    $result = [];
    foreach ($categories as &$category) {
        $stmt = $pdo->prepare("SELECT id, name, description, price, image_path, stock FROM menu_items WHERE category_id = ?");
        $stmt->execute([$category['id']]);
        $category['items'] = $stmt->fetchAll();
        $result[] = $category;
    }

    echo json_encode(['success' => true, 'data' => $result]);
} catch (Exception $e) {
    error_log("Error in owner_get_categories.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>