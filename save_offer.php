<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$restaurantId = $_POST['restaurant_id'] ?? null;
$offerId = $_POST['offer_id'] ?? null;
$description = $_POST['description'] ?? '';
$validUntil = $_POST['valid_until'] ?? null;
$isNew = isset($_POST['is_new']);

if (!$restaurantId || !$description) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
    exit;
}

try {
    if ($isNew) {
        // Insert new offer
        $stmt = $pdo->prepare("
            INSERT INTO offers (restaurant_id, description, valid_until)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$restaurantId, $description, $validUntil ?: null]);
    } else {
        // Update existing offer
        $stmt = $pdo->prepare("
            UPDATE offers 
            SET description = ?, valid_until = ?
            WHERE id = ? AND restaurant_id = ?
        ");
        $stmt->execute([$description, $validUntil ?: null, $offerId, $restaurantId]);
    }
    
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>