<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$restaurantId = isset($_GET['restaurant_id']) ? (int)$_GET['restaurant_id'] : null;

if (!$restaurantId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    // Check if restaurant exists
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurantId]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Restaurant not found']);
        exit;
    }

    // Fetch categories
    $stmt = $pdo->prepare("SELECT id, name, description FROM menu_categories WHERE restaurant_id = ?");
    $stmt->execute([$restaurantId]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch items for each category
    foreach ($categories as &$category) {
        $stmt = $pdo->prepare("SELECT id, name, description, price, stock, image_path FROM menu_items WHERE category_id = ?");
        $stmt->execute([$category['id']]);
        $category['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['success' => true, 'data' => $categories]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>