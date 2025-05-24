<?php
require 'db_connection.php';
header('Content-Type: application/json');

try {
    // Get status filter from query parameters
    $status = isset($_GET['status']) ? $_GET['status'] : 'pending';
    
    // Prepare the query based on status filter
    if ($status === 'all') {
        $query = "SELECT * FROM order_details ORDER BY order_date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    } else {
        $query = "SELECT * FROM order_details WHERE status = :status ORDER BY order_date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['status' => $status]);
    }
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data for frontend
    foreach ($orders as &$order) {
        $order['total_amount'] = (float)$order['total_amount'];
    }
    
    echo json_encode($orders);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>