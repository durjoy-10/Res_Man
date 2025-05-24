<?php
require 'check_auth.php';
require 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$restaurant_id = $_POST['restaurant_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$review_text = $_POST['review_text'] ?? '';

if (!$restaurant_id || !$rating || empty($review_text)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Check if user already reviewed this restaurant
$check_stmt = $pdo->prepare("SELECT id FROM restaurant_reviews WHERE restaurant_id = ? AND user_id = ?");
$check_stmt->execute([$restaurant_id, $_SESSION['user_id']]);
$existing_review = $check_stmt->fetch();

if ($existing_review) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this restaurant']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO restaurant_reviews (restaurant_id, user_id, rating, review_text) 
                          VALUES (?, ?, ?, ?)");
    $stmt->execute([$restaurant_id, $_SESSION['user_id'], $rating, $review_text]);
    
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>