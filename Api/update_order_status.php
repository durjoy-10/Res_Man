<?php
require 'db_connection.php';

// Set proper headers first
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Get JSON input
$json = file_get_contents('php://input');
$input = json_decode($json, true);

// Check for JSON decode errors
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid JSON input']));
}

// Validate input
if (!isset($input['order_id']) || !isset($input['status'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Missing required fields']));
}

$orderId = (int)$input['order_id'];
$status = $input['status'];

// Validate status
$allowedStatuses = ['pending', 'processing', 'delivered', 'cancelled'];
if (!in_array($status, $allowedStatuses)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid status value']));
}

try {
    // Verify the order exists first
    $checkStmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
    $checkStmt->execute([$orderId]);
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        die(json_encode(['success' => false, 'message' => 'Order not found']));
    }

    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $success = $stmt->execute([$status, $orderId]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update order status'
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    // Log the full error for debugging
    error_log('Order status update error: ' . $e->getMessage());
}
?>