<?php
require_once '../check_auth.php';
require_once '../db_connection.php';

header('Content-Type: application/json');

// Get token from header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Authorization token missing']);
    exit;
}

// Verify token and get user ID
$user_id = verifyToken($token); // You'll need to implement this function
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['restaurant_id']) || !isset($data['rating']) || !isset($data['review_text'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    // Check if user already reviewed this restaurant
    $check_stmt = $pdo->prepare("SELECT id FROM restaurant_reviews WHERE restaurant_id = ? AND user_id = ?");
    $check_stmt->execute([$data['restaurant_id'], $user_id]);
    $existing_review = $check_stmt->fetch();

    if ($existing_review) {
        http_response_code(400);
        echo json_encode(['error' => 'You have already reviewed this restaurant']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO restaurant_reviews (restaurant_id, user_id, rating, review_text) 
                          VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['restaurant_id'], $user_id, $data['rating'], $data['review_text']]);
    
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

function verifyToken($token) {
    // Implement your token verification logic here
    // This should return the user ID if valid, false otherwise
    // Example using JWT:
    // try {
    //     $decoded = JWT::decode($token, $secretKey, ['HS256']);
    //     return $decoded->user_id;
    // } catch (Exception $e) {
    //     return false;
    // }
    return 1; // Temporary implementation - replace with actual verification
}
?>