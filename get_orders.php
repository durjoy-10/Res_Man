<?php
require 'db_connection.php';

header('Content-Type: application/json');

try {
    $status = $_GET['status'] ?? 'all';
    $search = $_GET['search'] ?? '';

    $query = "SELECT 
                o.id,
                o.user_id,
                u.username AS user_name,
                o.restaurant_id,
                o.restaurant_name,
                o.created_at AS order_date,
                o.delivery_address,
                o.special_instructions,
                o.status,
                o.total_amount,
                o.payment_method,
                o.payment_number,
                o.transaction_id,
                GROUP_CONCAT(
                    CONCAT(oi.item_name, ' (', oi.quantity, ' × $', oi.price, ')')
                    SEPARATOR ', '
                ) AS items
              FROM orders o
              JOIN users u ON o.user_id = u.id
              LEFT JOIN order_items oi ON o.id = oi.order_id
              WHERE 1=1";
    
    $params = [];
    
    // Validate and filter by status
    $allowed_statuses = ['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled'];
    if ($status !== 'all' && in_array($status, $allowed_statuses)) {
        $query .= " AND o.status = ?";
        $params[] = $status;
    }
    
    // Search filter
    if (!empty($search)) {
        $query .= " AND (o.id LIKE ? OR u.username LIKE ? OR o.restaurant_name LIKE ?)";
        $searchTerm = "%$search%";
        array_push($params, $searchTerm, $searchTerm, $searchTerm);
    }
    
    $query .= " GROUP BY o.id ORDER BY o.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data
    foreach ($orders as &$order) {
        $order['total_amount'] = (float)$order['total_amount'];
        $order['order_date'] = date('Y-m-d H:i:s', strtotime($order['order_date']));
        
        // Ensure items is always a string
        $order['items'] = $order['items'] ?? 'No items information';
    }
    
    echo json_encode([
        'success' => true,
        'data' => $orders,
        'count' => count($orders)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage(),
        'query' => $query ?? '',
        'params' => $params ?? []
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>