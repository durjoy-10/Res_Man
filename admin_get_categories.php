<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$restaurant_id = $_GET['restaurant_id'] ?? '';

if (empty($restaurant_id)) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, description FROM menu_categories WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($categories as &$category) {
        $stmt = $pdo->prepare("SELECT id, name, description, price, stock, image_path FROM menu_items WHERE category_id = ?");
        $stmt->execute([$category['id']]);
        $category['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result[] = $category;
    }

    echo json_encode(['success' => true, 'data' => $result]);
} catch (Exception $e) {
    error_log("Error in admin_get_categories.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>