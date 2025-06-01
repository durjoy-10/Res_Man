<?php
// owner_update_order_status.php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$order_id = filter_var($input['order_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
$new_status = filter_var($input['status'] ?? '', FILTER_SANITIZE_STRING);
$restaurant_id = filter_var($input['restaurant_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);

$valid_statuses = ['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled'];

if (!$order_id || !is_numeric($order_id)) {
    error_log("Invalid order_id: $order_id");
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}
if (!$restaurant_id || !is_numeric($restaurant_id)) {
    error_log("Invalid restaurant_id: $restaurant_id");
    echo json_encode(['success' => false, 'message' => 'Invalid restaurant ID']);
    exit;
}
if (!in_array($new_status, $valid_statuses)) {
    error_log("Invalid status: $new_status");
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$new_status, $order_id, $restaurant_id]);
    if ($stmt->rowCount() > 0) {
        error_log("Order $order_id status updated to $new_status for restaurant $restaurant_id");
        echo json_encode(['success' => true]);
    } else {
        error_log("Order $order_id not found or not associated with restaurant $restaurant_id");
        echo json_encode(['success' => false, 'message' => 'Order not found or not associated with this restaurant']);
    }
} catch (Exception $e) {
    error_log("Error in owner_update_order_status.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>