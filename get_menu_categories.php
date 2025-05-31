<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$restaurantId = $_GET['restaurant_id'] ?? null;

if (!$restaurantId) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    // Get categories with their items
    $stmt = $pdo->prepare("
        SELECT c.id, c.name, c.description,
               JSON_ARRAYAGG(
                   JSON_OBJECT(
                       'id', i.id,
                       'name', i.name,
                       'description', i.description,
                       'price', i.price,
                       'stock', i.stock,
                       'image_path', i.image_path
                   )
               ) as items
        FROM menu_categories c
        LEFT JOIN menu_items i ON i.category_id = c.id
        WHERE c.restaurant_id = ?
        GROUP BY c.id
        ORDER BY c.name
    ");
    $stmt->execute([$restaurantId]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process the JSON items
    foreach ($categories as &$category) {
        $category['items'] = json_decode($category['items'], true) ?: [];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $categories
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>