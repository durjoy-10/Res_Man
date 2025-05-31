<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$restaurantId = $_POST['restaurant_id'] ?? null;
$categoryId = $_POST['category_id'] ?? null;
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$isNew = isset($_POST['is_new']);

if (!$restaurantId || !$name) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
    exit;
}

try {
    if ($isNew) {
        // Insert new category
        $stmt = $pdo->prepare("
            INSERT INTO menu_categories (restaurant_id, name, description)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$restaurantId, $name, $description]);
    } else {
        // Update existing category
        $stmt = $pdo->prepare("
            UPDATE menu_categories 
            SET name = ?, description = ?
            WHERE id = ? AND restaurant_id = ?
        ");
        $stmt->execute([$name, $description, $categoryId, $restaurantId]);
    }
    
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>