<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

try {
    $restaurantId = isset($_GET['id']) ? (int)$_GET['id'] : null;

    if ($restaurantId) {
        $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
        $stmt->execute([$restaurantId]);
        $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($restaurants)) {
            echo json_encode(['success' => false, 'message' => 'Restaurant not found']);
            exit;
        }
    } else {
        $stmt = $pdo->query("SELECT * FROM restaurants");
        $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Convert image paths to web-accessible URLs
    foreach ($restaurants as &$restaurant) {
        if (!empty($restaurant['image_path'])) {
            $restaurant['image_url'] = convertPathToUrl($restaurant['image_path']);
        } else {
            $restaurant['image_url'] = null;
        }
    }
    
    echo json_encode(['success' => true, 'data' => $restaurants]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}

/**
 * Convert server path to web URL
 */
function convertPathToUrl($path) {
    // Remove the server base path
    $basePath = '/opt/lampp/htdocs/restaurant_management/';
    $relativePath = str_replace($basePath, '', $path);
    
    // Construct full URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    
    // Handle cases where app might be in a subdirectory
    $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
    if ($baseUrl === '/') {
        $baseUrl = '';
    }
    
    return "{$protocol}://{$host}{$baseUrl}/{$relativePath}";
}
?>