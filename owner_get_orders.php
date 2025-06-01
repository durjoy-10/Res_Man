<?php
// owner_get_orders.php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

$restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_SANITIZE_NUMBER_INT);
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
$month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT);
$year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);

if (!$restaurant_id) {
    echo json_encode([]);
    exit;
}

try {
    $query = "SELECT o.id, o.user_id, u.username AS user_name, r.name, o.created_at AS order_date, 
                     o.delivery_address, o.special_instructions, o.status, o.total_amount, 
                     o.payment_method, o.payment_number, o.transaction_id,
                     GROUP_CONCAT(CONCAT(mi.name, ' (', oi.quantity, ' × $', oi.price, ')') SEPARATOR ', ') AS items
              FROM orders o
              JOIN restaurants r ON r.id = o.restaurant_id
              JOIN users u ON o.user_id = u.id
              JOIN order_items oi ON o.id = oi.order_id
              JOIN menu_items mi ON oi.menu_item_id = mi.id
              WHERE o.restaurant_id = ?";

    $params = [$restaurant_id];
    if ($status && $status !== 'all') {
        $query .= " AND o.status = ?";
        $params[] = $status;
    }
    if ($search) {
        $query .= " AND (o.id LIKE ? OR u.username LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
    }
    if ($month && $year) {
        $query .= " AND MONTH(o.created_at) = ? AND YEAR(o.created_at) = ?";
        $params[] = $month;
        $params[] = $year;
    }

    $query .= " GROUP BY o.id ORDER BY o.created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    echo json_encode($orders);
} catch (Exception $e) {
    error_log("Error in owner_get_orders.php: " . $e->getMessage());
    echo json_encode([]);
}
?>