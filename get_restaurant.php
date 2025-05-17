<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM restaurants");
    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert image paths to web-accessible URLs
    foreach ($restaurants as &$restaurant) {
        if (!empty($restaurant['image_path'])) {
            $restaurant['image_url'] = convertPathToUrl($restaurant['image_path']);
        } else {
            $restaurant['image_url'] = null;
        }
    }
    
    echo json_encode($restaurants);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
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