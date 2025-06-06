<?php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

$restaurant_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$restaurant_id || $restaurant_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid restaurant ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurant_id]);
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($restaurant) {
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $restaurant['id'],
                'name' => $restaurant['name'],
                'description' => $restaurant['description'],
                'owner_name' => $restaurant['owner_name'],
                'owner_email' => $restaurant['owner_email'],
                'phone' => $restaurant['phone'],
                'address' => $restaurant['address'],
                'image_path' => $restaurant['image_path'],
                'created_at' => $restaurant['created_at'],
                'updated_at' => $restaurant['updated_at']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Restaurant not found']);
    }
} catch (Exception $e) {
    error_log("Error in owner_get_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>