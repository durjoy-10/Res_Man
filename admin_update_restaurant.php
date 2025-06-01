<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

function handleFileUpload($file, $uploadDir, $prefix) {
    $baseDir = __DIR__ . '/';
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

    // Validate inputs
    $restaurant_id = $_POST['restaurant_id'] ?? '';
    if (empty($restaurant_id)) {
        throw new Exception('Restaurant ID is required');
    }

    // Validate email uniqueness (excluding current restaurant)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM restaurants WHERE owner_email = ? AND id != ?");
    $stmt->execute([$_POST['owner_email'], $restaurant_id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Owner email already exists");
    }

    // Handle restaurant image
    $restaurantImagePath = null;
    if (!empty($_FILES['restaurant_image']['name'])) {
        $restaurantImagePath = handleFileUpload(
            $_FILES['restaurant_image'], 
            'uploads/restaurants/', 
            'restaurant_'
        );
    }

    // Update restaurant details
    $params = [
        $_POST['name'],
        $_POST['description'],
        $_POST['owner_name'],
        $_POST['owner_email'],
        $_POST['phone'],
        $_POST['address']
    ];
    $query = "UPDATE restaurants SET name = ?, description = ?, owner_name = ?, owner_email = ?, phone = ?, address = ?";
    if ($restaurantImagePath) {
        $query .= ", image_path = ?";
        $params[] = $restaurantImagePath;
    }
    if (!empty($_POST['owner_password'])) {
        $query .= ", owner_password = ?";
        $params[] = password_hash($_POST['owner_password'], PASSWORD_BCRYPT);
    }
    $query .= " WHERE id = ?";
    $params[] = $restaurant_id;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    // Handle categories
    $category_ids = $_POST['category_id'] ?? [];
    $category_names = $_POST['category_name'] ?? [];
    $category_descriptions = $_POST['category_description'] ?? [];

    $stmt = $pdo->prepare("SELECT id FROM menu_categories WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $existing_categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $new_category_ids = [];
    foreach ($category_names as $index => $cat_name) {
        if (empty($cat_name)) continue;
        $cat_id = $category_ids[$index] ?? '';
        $cat_desc = $category_descriptions[$index] ?? '';

        if ($cat_id) {
            $stmt = $pdo->prepare("UPDATE menu_categories SET name = ?, description = ? WHERE id = ? AND restaurant_id = ?");
            $stmt->execute([$cat_name, $cat_desc, $cat_id, $restaurant_id]);
            $new_category_ids[] = $cat_id;
        } else {
            $stmt = $pdo->prepare("INSERT INTO menu_categories (restaurant_id, name, description) VALUES (?, ?, ?)");
            $stmt->execute([$restaurant_id, $cat_name, $cat_desc]);
            $new_category_ids[] = $pdo->lastInsertId();
        }
    }

    $categories_to_delete = array_diff($existing_categories, $new_category_ids);
    if (!empty($categories_to_delete)) {
        $stmt = $pdo->prepare("DELETE FROM menu_categories WHERE id IN (" . implode(',', array_fill(0, count($categories_to_delete), '?')) . ") AND restaurant_id = ?");
        $params = array_merge($categories_to_delete, [$restaurant_id]);
        $stmt->execute($params);
    }

    // Handle menu items
    $item_ids = $_POST['item_id'] ?? [];
    $item_names = $_POST['item_name'] ?? [];
    $item_descriptions = $_POST['item_description'] ?? [];
    $item_prices = $_POST['item_price'] ?? [];
    $item_stocks = $_POST['item_stock'] ?? [];
    $item_images = $_FILES['item_image'] ?? [];

    foreach ($item_names as $cat_index => $items) {
        $category_id = $new_category_ids[$cat_index] ?? null;
        if (!$category_id) continue;

        $stmt = $pdo->prepare("SELECT id FROM menu_items WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $existing_items = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $new_item_ids = [];
        foreach ($items as $item_index => $item_name) {
            if (empty($item_name)) continue;
            $item_id = $item_ids[$cat_index][$item_index] ?? '';
            $desc = $item_descriptions[$cat_index][$item_index] ?? '';
            $price = $item_prices[$cat_index][$item_index] ?? 0;
            $stock = $item_stocks[$cat_index][$item_index] ?? 0;

            $image_path = null;
            if (!empty($item_images['name'][$cat_index][$item_index])) {
                $file = [
                    'name' => $item_images['name'][$cat_index][$item_index],
                    'type' => $item_images['type'][$cat_index][$item_index],
                    'tmp_name' => $item_images['tmp_name'][$cat_index][$item_index],
                    'error' => $item_images['error'][$cat_index][$item_index],
                    'size' => $item_images['size'][$cat_index][$item_index]
                ];
                $image_path = handleFileUpload($file, 'uploads/menu_items/', 'item_');
            }

            if ($item_id) {
                $query = "UPDATE menu_items SET name = ?, description = ?, price = ?, stock = ?";
                $params = [$item_name, $desc, $price, $stock];
                if ($image_path) {
                    $query .= ", image_path = ?";
                    $params[] = $image_path;
                }
                $query .= " WHERE id = ? AND category_id = ?";
                $params[] = $item_id;
                $params[] = $category_id;
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $new_item_ids[] = $item_id;
            } else {
                $query = "INSERT INTO menu_items (category_id, name, description, price, stock";
                $params = [$category_id, $item_name, $desc, $price, $stock];
                if ($image_path) {
                    $query .= ", image_path";
                    $params[] = $image_path;
                }
                $query .= ") VALUES (" . implode(',', array_fill(0, count($params), '?')) . ")";
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $new_item_ids[] = $pdo->lastInsertId();
            }
        }

        $items_to_delete = array_diff($existing_items, $new_item_ids);
        if (!empty($items_to_delete)) {
            $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id IN (" . implode(',', array_fill(0, count($items_to_delete), '?')) . ") AND category_id = ?");
            $params = array_merge($items_to_delete, [$category_id]);
            $stmt->execute($params);
        }
    }

    // Handle offers
    $offer_ids = $_POST['offer_id'] ?? [];
    $offer_descriptions = $_POST['offer_description'] ?? [];
    $offer_valid_until = $_POST['offer_valid_until'] ?? [];

    $stmt = $pdo->prepare("SELECT id FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $existing_offers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $new_offer_ids = [];
    foreach ($offer_descriptions as $index => $desc) {
        if (empty($desc)) continue;
        $offer_id = $offer_ids[$index] ?? '';
        $valid_until = $offer_valid_until[$index] ?? null;

        if ($offer_id) {
            $stmt = $pdo->prepare("UPDATE offers SET description = ?, valid_until = ? WHERE id = ? AND restaurant_id = ?");
            $stmt->execute([$desc, $valid_until, $offer_id, $restaurant_id]);
            $new_offer_ids[] = $offer_id;
        } else {
            $stmt = $pdo->prepare("INSERT INTO offers (restaurant_id, description, valid_until) VALUES (?, ?, ?)");
            $stmt->execute([$restaurant_id, $desc, $valid_until]);
            $new_offer_ids[] = $pdo->lastInsertId();
        }
    }

    $offers_to_delete = array_diff($existing_offers, $new_offer_ids);
    if (!empty($offers_to_delete)) {
        $stmt = $pdo->prepare("DELETE FROM offers WHERE id IN (" . implode(',', array_fill(0, count($offers_to_delete), '?')) . ") AND restaurant_id = ?");
        $params = array_merge($offers_to_delete, [$restaurant_id]);
        $stmt->execute($params);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Restaurant updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in admin_update_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>