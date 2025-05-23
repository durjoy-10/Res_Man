<?php
require 'db_connection.php';
require 'check_auth.php';

header('Content-Type: application/json');

try {
    // Get the raw POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Invalid input data');
    }

    // Get restaurant name
    $stmt = $pdo->prepare("SELECT name FROM restaurants WHERE id = ?");
    $stmt->execute([$data['restaurant_id']]);
    $restaurant = $stmt->fetch();
    
    if (!$restaurant) {
        throw new Exception('Restaurant not found');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, restaurant_id, restaurant_name, delivery_address, special_instructions, total_amount) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    // Calculate total amount
    $totalAmount = 0;
    foreach ($data['items'] as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }
    
    $stmt->execute([
        $_SESSION['user_id'],
        $data['restaurant_id'],
        $restaurant['name'],
        $data['delivery_address'],
        $data['special_instructions'] ?? null,
        $totalAmount
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    // Insert order items
    $stmt = $pdo->prepare("
        INSERT INTO order_items 
        (order_id, menu_item_id, quantity, price) 
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($data['items'] as $item) {
        $stmt->execute([
            $orderId,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'message' => 'Order placed successfully'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>