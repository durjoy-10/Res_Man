<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$offerId = $_GET['offer_id'] ?? null;

if (!$offerId) {
    echo json_encode(['success' => false, 'message' => 'Offer ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
    $stmt->execute([$offerId]);
    
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>