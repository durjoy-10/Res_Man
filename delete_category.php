<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$categoryId = $_GET['category_id'] ?? null;

if (!$categoryId) {
    echo json_encode(['success' => false, 'message' => 'Category ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // First delete all items in this category
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE category_id = ?");
    $stmt->execute([$categoryId]);
    
    // Then delete the category
    $stmt = $pdo->prepare("DELETE FROM menu_categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    
    $pdo->commit();
    
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>