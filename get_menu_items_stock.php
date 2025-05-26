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
    // Fetch all menu items for the restaurant
    $stmt = $pdo->prepare("
        SELECT mi.id, mi.stock
        FROM menu_items mi
        JOIN menu_categories mc ON mi.category_id = mc.id
        WHERE mc.restaurant_id = ?
    ");
    $stmt->execute([$restaurantId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $items]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>