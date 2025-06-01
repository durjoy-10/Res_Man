<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

try {
    $query = "SELECT o.order_id, o.user_id, u.name AS user_name, o.restaurant_id, r.name, o.order_date, 
                     o.delivery_address, o.special_instructions, o.status, o.total_amount, 
                     o.payment_method, o.payment_number, o.transaction_id,
                     GROUP_CONCAT(CONCAT(oi.quantity, ' x ', mi.name) SEPARATOR ', ') AS items
              FROM orders o
              LEFT JOIN users u ON o.user_id = u.id
              LEFT JOIN restaurants r ON o.restaurant_id = r.id
              LEFT JOIN order_items oi ON o.order_id = oi.order_id
              LEFT JOIN menu_items mi ON oi.item_id = mi.id";
    
    $params = [];
    $conditions = [];

    if ($status !== 'all') {
        $conditions[] = "o.status = ?";
        $params[] = $status;
    }

    if (!empty($search)) {
        $conditions[] = "(o.order_id LIKE ? OR u.name LIKE ? OR u.email LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $query .= " GROUP BY o.order_id ORDER BY o.order_date DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $orders]);
} catch (Exception $e) {
    error_log("Error in admin_get_orders.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>