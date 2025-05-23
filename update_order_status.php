<?php
require 'db_connection.php';

header('Content-Type: application/json');

try {
    // Get and validate input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }
    
    if (!isset($input['order_id']) || !is_numeric($input['order_id'])) {
        throw new Exception('Invalid order ID');
    }
    
    if (!isset($input['status'])) {
        throw new Exception('Status parameter is required');
    }
    
    $order_id = (int)$input['order_id'];
    $status = trim($input['status']);
    
    // Validate status
    $allowed_statuses = ['pending', 'processing', 'delivered', 'cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        throw new Exception('Invalid status value');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Check if order exists
    $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Order not found');
    }
    
    // Update status
    $updateStmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $updateStmt->execute([$status, $order_id]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order status updated successfully',
        'new_status' => $status
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // if (isset($pdo) $pdo->rollBack();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>