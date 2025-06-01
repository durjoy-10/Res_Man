<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

function handleFileUpload($file, $uploadDir, $prefix) {
    $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/restaurant_management/';
    $fullUploadDir = $baseDir . ltrim($uploadDir, '/');
    
    if (!file_exists($fullUploadDir)) {
        if (!mkdir($fullUploadDir, 0755, true)) {
            throw new Exception("Failed to create upload directory: $fullUploadDir");
        }
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error: " . $file['error']);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Invalid file type. Only images are allowed.");
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . uniqid() . '.' . strtolower($extension);
    $destination = $fullUploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Failed to move uploaded file.");
    }
    
    return $uploadDir . $filename;
}

try {
    $pdo->beginTransaction();

    $restaurantId = $_POST['restaurant_id'];

    // Verify ownership (simplified check)
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurantId]);
    if ($stmt->rowCount() === 0) {
        throw new Exception("Restaurant not found.");
    }

    // Handle restaurant image
    $restaurantImagePath = null;
    if (!empty($_FILES['restaurant_image']['name'])) {
        $restaurantImagePath = handleFileUpload($_FILES['restaurant_image'], 'uploads/restaurants/', 'restaurant_');
    }

    // Update restaurant details
    $stmt = $pdo->prepare("UPDATE restaurants 
        SET name = ?, description = ?, owner_name = ?, owner_email = ?, phone = ?, address = ?, image_path = COALESCE(?, image_path)
        WHERE id = ?");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['owner_name'],
        $_POST['owner_email'],
        $_POST['phone'],
        $_POST['address'],
        $restaurantImagePath,
        $restaurantId
    ]);

    // Fetch existing categories
    $stmt = $pdo->prepare("SELECT id, name FROM menu_categories WHERE restaurant_id = ?");
    $stmt->execute([$restaurantId]);
    $existingCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $existingCategoryIds = array_column($existingCategories, 'id');

    if (isset($_POST['category_name'])) {
        foreach ($_POST['category_name'] as $index => $categoryName) {
            $categoryDescription = $_POST['category_description'][$index] ?? null;
            $categoryId = isset($_POST['category_id'][$index]) ? $_POST['category_id'][$index] : null;

            if ($categoryId && in_array($categoryId, $existingCategoryIds)) {
                $stmt = $pdo->prepare("UPDATE menu_categories SET name = ?, description = ? WHERE id = ?");
                $stmt->execute([$categoryName, $categoryDescription, $categoryId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO menu_categories (restaurant_id, name, description) VALUES (?, ?, ?)");
                $stmt->execute([$restaurantId, $categoryName, $categoryDescription]);
                $categoryId = $pdo->lastInsertId();
            }

            // Handle items
            if (isset($_POST['item_name'][$index])) {
                $stmt = $pdo->prepare("DELETE FROM menu_items WHERE category_id = ?");
                $stmt->execute([$categoryId]);
                foreach ($_POST['item_name'][$index] as $itemIndex => $itemName) {
                    $itemImagePath = null;
                    if (!empty($_FILES['item_image']['name'][$index][$itemIndex])) {
                        $file = [
                            'name' => $_FILES['item_image']['name'][$index][$itemIndex],
                            'type' => $_FILES['item_image']['type'][$index][$itemIndex],
                            'tmp_name' => $_FILES['item_image']['tmp_name'][$index][$itemIndex],
                            'error' => $_FILES['item_image']['error'][$index][$itemIndex],
                            'size' => $_FILES['item_image']['size'][$index][$itemIndex]
                        ];
                        $itemImagePath = handleFileUpload($file, 'uploads/menu_items/', 'item_');
                    }
                    $stmt = $pdo->prepare("INSERT INTO menu_items (category_id, name, description, price, stock, image_path) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $categoryId,
                        $itemName,
                        $_POST['item_description'][$index][$itemIndex] ?? null,
                        $_POST['item_price'][$index][$itemIndex],
                        $_POST['item_stock'][$index][$itemIndex] ?? 0,
                        $itemImagePath
                    ]);
                }
            }
        }
    }

    // Delete categories not submitted
    $submittedCategoryIds = $_POST['category_id'] ?? [];
    $categoriesToDelete = array_diff($existingCategoryIds, array_filter($submittedCategoryIds, 'strlen'));
    if (!empty($categoriesToDelete)) {
        $stmt = $pdo->prepare("DELETE FROM menu_categories WHERE id IN (" . implode(',', array_fill(0, count($categoriesToDelete), '?')) . ")");
        $stmt->execute(array_values($categoriesToDelete));
    }

    // Handle offers
    $stmt = $pdo->prepare("DELETE FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurantId]);
    if (isset($_POST['offer_description'])) {
        foreach ($_POST['offer_description'] as $index => $offerDesc) {
            if (!empty($offerDesc)) {
                $validUntil = !empty($_POST['offer_valid_until'][$index]) ? $_POST['offer_valid_until'][$index] : null;
                $stmt = $pdo->prepare("INSERT INTO offers (restaurant_id, description, valid_until) VALUES (?, ?, ?)");
                $stmt->execute([$restaurantId, $offerDesc, $validUntil]);
            }
        }
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'restaurant_id' => $restaurantId]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in update_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>