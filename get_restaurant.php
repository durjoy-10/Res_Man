<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

try {
    // Get all restaurants with their first image
    $stmt = $pdo->query("
        SELECT r.*, 
               (SELECT i.image_path FROM menu_items i 
                JOIN menu_categories c ON i.category_id = c.id 
                WHERE c.restaurant_id = r.id LIMIT 1) as sample_item_image
        FROM restaurants r
        ORDER BY r.created_at DESC
    ");
    
    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($restaurants);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>