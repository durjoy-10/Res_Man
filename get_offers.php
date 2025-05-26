<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$restaurantId = isset($_GET['restaurant_id']) ? (int)$_GET['restaurant_id'] : null;

if (!$restaurantId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    // Check if restaurant exists
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurantId]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Restaurant not found']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT description, valid_until FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurantId]);
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $offers]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>