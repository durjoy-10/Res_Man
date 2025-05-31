<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get and log raw input
$rawInput = file_get_contents('php://input');
error_log("Raw input received: " . $rawInput);

$data = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid JSON input: ' . json_last_error_msg()
    ]);
    exit;
}

// Trim and validate inputs
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

error_log("Login attempt for email: [$email]"); // Log with brackets to show whitespace

if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Email and password are required'
    ]);
    exit;
}

try {
    // Verify database connection
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    // Case-insensitive email search
    $stmt = $pdo->prepare("
        SELECT r.id as restaurant_id, r.owner_name, r.owner_password 
        FROM restaurants r 
        WHERE r.owner_email = :email
    ");
    
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    $owner = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$owner) {
        error_log("No owner found with email: $email");
        echo json_encode([
            'success' => false, 
            'message' => 'No account found with this email'
        ]);
        exit;
    }

    // Debug output - remove in production
    error_log("Stored hash: " . $owner['owner_password']);
    error_log("Input password: $password");
    
    // Verify password
    if (!password_verify($password, $owner['owner_password'])) {
        // Additional check for plain text passwords (during development)
        if ($owner['owner_password'] === $password) {
            error_log("Warning: Password stored in plain text!");
            echo json_encode([
                'success' => false,
                'message' => 'Security alert: Password not hashed properly'
            ]);
        } else {
            error_log("Password verification failed");
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid password'
            ]);
        }
        exit;
    }

    // Login successful
    error_log("Login successful for restaurant ID: " . $owner['restaurant_id']);
    echo json_encode([
        'success' => true,
        'owner_id' => $owner['restaurant_id'],
        'restaurant_id' => $owner['restaurant_id'],
        'owner_name' => $owner['owner_name']
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>