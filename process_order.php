<?php
require 'db_connection.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get user ID from session if available
$user_id = $_SESSION['user_id'] ?? null;

$restaurant_id = $_POST['restaurant_id'] ?? null;
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$instructions = $_POST['instructions'] ?? '';

if (!$restaurant_id || empty($name) || empty($email) || empty($phone) || empty($address)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Create the order
    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, restaurant_id, customer_name, customer_email, customer_phone, delivery_address, special_instructions, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$user_id, $restaurant_id, $name, $email, $phone, $address, $instructions]);
    $order_id = $pdo->lastInsertId();
    
    // In a real application, you would add the actual items from the cart
    // For this example, we'll add one sample item
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, menu_item_id, quantity, price)
        SELECT ?, id, 1, price FROM menu_items WHERE restaurant_id = ? LIMIT 1
    ");
    $stmt->execute([$order_id, $restaurant_id]);
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error processing order: ' . $e->getMessage()]);
}
?>