<?php
require_once '../db_connection.php';

header('Content-Type: application/json');

try {
    $query = "SELECT rr.*, u.name as user_name, r.name as restaurant_name 
              FROM restaurant_reviews rr
              JOIN users u ON rr.user_id = u.id
              JOIN restaurants r ON rr.restaurant_id = r.id
              ORDER BY rr.created_at DESC";

    $stmt = $pdo->query($query);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reviews);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>