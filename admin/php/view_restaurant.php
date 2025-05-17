<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$restaurantId = $_GET['id'] ?? null;

if (!$restaurantId) {
    echo json_encode(['error' => 'No restaurant ID provided']);
    exit;
}

try {
    // Get restaurant basic info
    $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurantId]);
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$restaurant) {
        echo json_encode(['error' => 'Restaurant not found']);
        exit;
    }
    
    // Convert relative image paths to absolute URLs
    if (!empty($restaurant['image_path'])) {
        $restaurant['image_url'] = getAbsoluteUrl($restaurant['image_path']);
    } else {
        $restaurant['image_url'] = null;
    }
    
    // Get categories with items
    $stmt = $pdo->prepare("
        SELECT c.id, c.name, c.description, 
               JSON_ARRAYAGG(
                   JSON_OBJECT(
                       'id', i.id,
                       'name', i.name,
                       'description', i.description,
                       'price', i.price,
                       'image_path', i.image_path
                   )
               ) as items
        FROM menu_categories c
        LEFT JOIN menu_items i ON i.category_id = c.id
        WHERE c.restaurant_id = ?
        GROUP BY c.id
    ");
    $stmt->execute([$restaurantId]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert JSON strings to arrays and process image paths
    foreach ($categories as &$category) {
        $items = json_decode($category['items'], true) ?: [];
        foreach ($items as &$item) {
            if (!empty($item['image_path'])) {
                $item['image_url'] = getAbsoluteUrl($item['image_path']);
            } else {
                $item['image_url'] = null;
            }
        }
        $category['items'] = $items;
    }
    
    // Get offers
    $stmt = $pdo->prepare("SELECT * FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurantId]);
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $restaurant['categories'] = $categories;
    $restaurant['offers'] = $offers;
    
    echo json_encode($restaurant);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Convert relative file path to absolute URL
 */
function getAbsoluteUrl($relativePath) {
    // Determine the protocol (http or https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    
    // Get the server host
    $host = $_SERVER['HTTP_HOST'];
    
    // Get the base path (if your application is in a subdirectory)
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    
    // Remove any leading slashes from the relative path
    $relativePath = ltrim($relativePath, '/');
    
    // Construct the full URL
    return "{$protocol}://{$host}{$basePath}/{$relativePath}";
}
?>