<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$itemId = $_GET['item_id'] ?? null;

if (!$itemId) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->execute([$itemId]);
    
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>