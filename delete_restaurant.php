<?php
require 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing restaurant ID']);
    exit;
}

try {
    // First delete related records to maintain referential integrity
    $pdo->beginTransaction();
    
    // Delete offers
    $stmt = $pdo->prepare("DELETE FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$id]);
    
    // Delete menu items
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE restaurant_id = ?");
    $stmt->execute([$id]);
    
    // Delete the restaurant
    $stmt = $pdo->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt->execute([$id]);
    
    $pdo->commit();
    
    // Delete the restaurant directory
    $restaurant_dir = "Restaurant" . $id;
    if (file_exists($restaurant_dir)) {
        deleteDirectory($restaurant_dir);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}
?>