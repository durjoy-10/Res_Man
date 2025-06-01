<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? '';
$new_status = $input['status'] ?? '';

if (empty($order_id) || empty($new_status)) {
    echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
    exit;
}

// Validate status
$valid_statuses = ['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    // Check current status and validate transition
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $current_status = $stmt->fetchColumn();

    if ($current_status === false) {
        throw new Exception("Order not found");
    }

    // Define valid status transitions
    $valid_transitions = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['preparing', 'cancelled'],
        'preparing' => ['out_for_delivery', 'cancelled'],
        'out_for_delivery' => ['delivered', 'cancelled'],
        'delivered' => [],
        'cancelled' => []
    ];

    if (!in_array($new_status, $valid_transitions[$current_status] ?? [])) {
        throw new Exception("Invalid status transition from $current_status to $new_status");
    }

    // Update status
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$new_status, $order_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Failed to update order status");
    }

    echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
} catch (Exception $e) {
    error_log("Error in admin_update_order_status.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>