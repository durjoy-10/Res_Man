<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$restaurantId = $_GET['restaurant_id'] ?? null;
$status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';
$limit = $_GET['limit'] ?? null;

if (!$restaurantId) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    $query = "
        SELECT o.id, o.order_date, o.status, o.total_amount, o.delivery_address, 
               o.special_instructions, GROUP_CONCAT(oi.item_name SEPARATOR ', ') as items,
               u.name as user_name, u.phone as user_phone
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN users u ON o.user_id = u.id
        WHERE o.restaurant_id = ?
    ";
    
    $params = [$restaurantId];
    
    // Add status filter
    if ($status !== 'all') {
        $query .= " AND o.status = ?";
        $params[] = $status;
    }
    
    // Add search filter
    if (!empty($search)) {
        $query .= " AND (o.id LIKE ? OR u.name LIKE ? OR u.phone LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $query .= " GROUP BY o.id";
    
    // Add order
    $query .= " ORDER BY o.order_date DESC";
    
    // Add limit if provided
    if ($limit) {
        $query .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $orders
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>