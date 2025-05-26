<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['restaurant_id']) || !isset($input['items']) || empty($input['items'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}

$restaurantId = (int)$input['restaurant_id'];
$items = $input['items'];
$deliveryAddress = $input['delivery_address'] ?? '';
$specialInstructions = $input['special_instructions'] ?? '';
$paymentMethod = $input['payment_method'] ?? '';
$paymentNumber = $input['payment_number'] ?? null;
$transactionId = $input['transaction_id'] ?? null;
$userId = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // Step 1: Fetch the restaurant name
    $stmt = $pdo->prepare("SELECT name FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurantId]);
    $restaurant = $stmt->fetch();
    if (!$restaurant) {
        throw new Exception("Restaurant with ID $restaurantId not found");
    }
    $restaurantName = $restaurant['name'];

    // Step 2: Check stock for all items
    $itemIds = array_column($items, 'id');
    $stmt = $pdo->prepare("SELECT id, stock FROM menu_items WHERE id IN (" . implode(',', array_fill(0, count($itemIds), '?')) . ") FOR UPDATE");
    $stmt->execute($itemIds);
    $stockData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stockMap = array_column($stockData, 'stock', 'id');

    foreach ($items as $item) {
        $itemId = $item['id'];
        $quantity = $item['quantity'];

        if (!isset($stockMap[$itemId])) {
            throw new Exception("Item with ID $itemId not found");
        }

        $currentStock = $stockMap[$itemId];
        if ($quantity > $currentStock) {
            throw new Exception("Insufficient stock for item ID $itemId. Requested: $quantity, Available: $currentStock");
        }
    }

    // Step 3: Validate payment details
    if ($paymentMethod === 'bkash' || $paymentMethod === 'nagad') {
        if (!$paymentNumber || !$transactionId) {
            throw new Exception("Payment number and transaction ID are required for $paymentMethod");
        }
    }

    // Step 4: Create the order with payment details
    $status = ($paymentMethod === 'cash_on_delivery') ? 'pending' : 'payment_pending';
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, restaurant_id, restaurant_name, total_amount, status, delivery_address, special_instructions, payment_method, payment_number, transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $totalAmount = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $items));
    $stmt->execute([$userId, $restaurantId, $restaurantName, $totalAmount, $status, $deliveryAddress, $specialInstructions, $paymentMethod, $paymentNumber, $transactionId]);
    $orderId = $pdo->lastInsertId();

    // Step 5: Insert order items and update stock
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
    $updateStmt = $pdo->prepare("UPDATE menu_items SET stock = stock - ? WHERE id = ?");

    foreach ($items as $item) {
        $itemId = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        // Log the item details for debugging
        error_log("Processing item ID $itemId with quantity $quantity");

        // Insert into order_items
        $stmt->execute([$orderId, $itemId, $quantity, $price]);

        // Update stock
        $updateStmt->execute([$quantity, $itemId]);
        $affectedRows = $updateStmt->rowCount();
        if ($affectedRows === 0) {
            error_log("Stock update failed for item ID $itemId: No rows affected");
            throw new Exception("Failed to update stock for item ID $itemId");
        } else {
            error_log("Stock updated for item ID $itemId: Reduced by $quantity");
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'restaurant_name' => $restaurantName,
        'total_amount' => $totalAmount
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Order processing failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Throwable $t) {
    $pdo->rollBack();
    error_log("Unexpected error in order processing: " . $t->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unexpected error: ' . $t->getMessage()]);
}
?>