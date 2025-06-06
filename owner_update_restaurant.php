<?php
// owner_update_restaurant.php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method in owner_update_restaurant.php: {$_SERVER['REQUEST_METHOD']}");
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

function handleFileUpload($file, $uploadDir, $prefix) {
    $baseDir = '/opt/lampp/htdocs/restaurant_management/uploads/';
    $fullUploadDir = rtrim($baseDir, '/') . '/' . ltrim($uploadDir, '/');
    
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
        throw new Exception("Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.");
    }
    
    $maxSize = 5 * 1024 * 1024; // 5MB limit
    if ($file['size'] > $maxSize) {
        throw new Exception("File size exceeds 5MB limit.");
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $prefix . uniqid() . '.' . $extension;
    $destination = $fullUploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Failed to move uploaded file to $destination. Check permissions.");
    }
    
    // Return the path relative to the web root for storage in the database
    return '/restaurant_management/uploads/' . $uploadDir . $filename;
}

try {
    $pdo->beginTransaction();

    // Validate restaurant_id
    $restaurantId = filter_input(INPUT_POST, 'restaurant_id', FILTER_VALIDATE_INT);
    if (!$restaurantId || $restaurantId <= 0) {
        throw new Exception('Invalid or missing restaurant ID');
    }

    // Verify restaurant exists
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurantId]);
    if (!$stmt->fetch()) {
        throw new Exception('Restaurant not found');
    }

    // Handle restaurant image
    $restaurantImagePath = null;
    if (!empty($_FILES['restaurant_image']['name'])) {
        $restaurantImagePath = handleFileUpload(
            $_FILES['restaurant_image'],
            'restaurants/',
            'restaurant_image_'
        );
    }

    // Update restaurant details
    $stmt = $pdo->prepare("
        UPDATE restaurants 
        SET name = ?, description = ?, owner_name = ?, owner_email = ?, phone = ?, address = ?, 
            image_path = COALESCE(?, image_path), updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $_POST['name'] ?? '',
        $_POST['description'] ?? '',
        $_POST['owner_name'] ?? '',
        filter_var($_POST['owner_email'] ?? '', FILTER_VALIDATE_EMAIL) ?? '',
        $_POST['phone'] ?? '',
        $_POST['address'] ?? '',
        $restaurantImagePath,
        $restaurantId
    ]);

    // Delete existing categories, items, and offers
    $stmt = $pdo->prepare("DELETE FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurantId]);

    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE category_id IN (SELECT id FROM menu_categories WHERE restaurant_id = ?)");
    $stmt->execute([$restaurantId]);

    $stmt = $pdo->prepare("DELETE FROM menu_categories WHERE restaurant_id = ?");
    $stmt->execute([$restaurantId]);

    // Insert new categories and items
    if (isset($_POST['category_name']) && is_array($_POST['category_name'])) {
        foreach ($_POST['category_name'] as $index => $categoryName) {
            if (empty($categoryName)) continue;

            $stmt = $pdo->prepare("INSERT INTO menu_categories 
                (restaurant_id, name, description) 
                VALUES (?, ?, ?)");
            $stmt->execute([
                $restaurantId,
                $categoryName,
                $_POST['category_description'][$index] ?? null
            ]);
            $categoryId = $pdo->lastInsertId();

            if (isset($_POST['item_name'][$index]) && is_array($_POST['item_name'][$index])) {
                foreach ($_POST['item_name'][$index] as $itemIndex => $itemName) {
                    if (empty($itemName)) continue;

                    $itemImagePath = null;
                    $itemFileKey = "item_image_{$index}_{$itemIndex}";
                    $existingImagePath = $_POST['item_image_path'][$index][$itemIndex] ?? '';

                    if (!empty($_FILES[$itemFileKey]['name'])) {
                        $file = [
                            'name' => $_FILES[$itemFileKey]['name'],
                            'type' => $_FILES[$itemFileKey]['type'],
                            'tmp_name' => $_FILES[$itemFileKey]['tmp_name'],
                            'error' => $_FILES[$itemFileKey]['error'],
                            'size' => $_FILES[$itemFileKey]['size']
                        ];
                        $itemImagePath = handleFileUpload($file, 'menu_items/', 'item_');
                    } else {
                        // Preserve existing image path if no new file is uploaded
                        $itemImagePath = $existingImagePath ?: null;
                    }

                    $stmt = $pdo->prepare("INSERT INTO menu_items 
                        (category_id, name, description, price, stock, image_path) 
                        VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $categoryId,
                        $itemName,
                        $_POST['item_description'][$index][$itemIndex] ?? null,
                        floatval($_POST['item_price'][$index][$itemIndex] ?? 0),
                        intval($_POST['item_stock'][$index][$itemIndex] ?? 0),
                        $itemImagePath
                    ]);
                }
            }
        }
    }

    // Insert new offers
    if (isset($_POST['offer_description']) && is_array($_POST['offer_description'])) {
        foreach ($_POST['offer_description'] as $index => $offerDesc) {
            if (empty($offerDesc)) continue;

            $validUntil = !empty($_POST['offer_valid_until'][$index]) ? 
                date('Y-m-d', strtotime($_POST['offer_valid_until'][$index])) : null;

            $stmt = $pdo->prepare("INSERT INTO offers 
                (restaurant_id, description, valid_until) 
                VALUES (?, ?, ?)");
            $stmt->execute([
                $restaurantId,
                $offerDesc,
                $validUntil
            ]);
        }
    }

    $pdo->commit();

    error_log("Restaurant updated successfully for ID: $restaurantId");
    echo json_encode([
        'success' => true,
        'restaurant_id' => $restaurantId
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in owner_update_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>