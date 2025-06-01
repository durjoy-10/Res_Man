<?php
// owner_update_restaurant.php
require_once 'owner_db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$restaurant_id = filter_input(INPUT_POST, 'restaurant_id', FILTER_SANITIZE_NUMBER_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$owner_name = filter_input(INPUT_POST, 'owner_name', FILTER_SANITIZE_STRING);
$owner_email = filter_input(INPUT_POST, 'owner_email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

if (!$restaurant_id) {
    echo json_encode(['success' => false, 'message' => 'Restaurant ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Update restaurant details
    $stmt = $pdo->prepare("UPDATE restaurants SET name = ?, description = ?, owner_name = ?, owner_email = ?, phone = ?, address = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$name, $description, $owner_name, $owner_email, $phone, $address, $restaurant_id]);

    // Handle image upload
    if (isset($_FILES['restaurant_image']) && $_FILES['restaurant_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/restaurants摘要

aurants/';
        $image_name = 'restaurant_' . uniqid() . '.' . pathinfo($_FILES['restaurant_image']['name'], PATHINFO_EXTENSION);
        $image_path = $upload_dir . $image_name;
        if (move_uploaded_file($_FILES['restaurant_image']['tmp_name'], $image_path)) {
            $stmt = $pdo->prepare("UPDATE restaurants SET image_path = ? WHERE id = ?");
            $stmt->execute([$image_path, $restaurant_id]);
        }
    }

    // Handle categories
    $category_ids = $_POST['category_id'] ?? [];
    $category_names = $_POST['category_name'] ?? [];
    $category_descriptions = $_POST['category_description'] ?? [];

    // Get existing categories to determine deletions
    $stmt = $pdo->prepare("SELECT id FROM menu_categories WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $existing_categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $submitted_category_ids = array_filter($category_ids, function($id) { return !empty($id); });

    // Delete categories that are no longer present
    $categories_to_delete = array_diff($existing_categories, $submitted_category_ids);
    if (!empty($categories_to_delete)) {
        $stmt = $pdo->prepare("DELETE FROM menu_categories WHERE id IN (" . implode(',', array_fill(0, count($categories_to_delete), '?')) . ") AND restaurant_id = ?");
        $stmt->execute([...$categories_to_delete, $restaurant_id]);
    }

    // Update or insert categories
    foreach ($category_names as $index => $category_name) {
        $category_id = $category_ids[$index];
        $category_description = $category_descriptions[$index];

        if ($category_id) {
            // Update existing category
            $stmt = $pdo->prepare("UPDATE menu_categories SET name = ?, description = ? WHERE id = ? AND restaurant_id = ?");
            $stmt->execute([$category_name, $category_description, $category_id, $restaurant_id]);
        } else {
            // Insert new category
            $stmt = $pdo->prepare("INSERT INTO menu_categories (restaurant_id, name, description) VALUES (?, ?, ?)");
            $stmt->execute([$restaurant_id, $category_name, $category_description]);
            $category_id = $pdo->lastInsertId();
        }

        // Handle menu items for this category
        $item_ids = $_POST["item_id"][$index] ?? [];
        $item_names = $_POST["item_name"][$index] ?? [];
        $item_descriptions = $_POST["item_description"][$index] ?? [];
        $item_prices = $_POST["item_price"][$index] ?? [];
        $item_stocks = $_POST["item_stock"][$index] ?? [];

        // Get existing items for this category
        $stmt = $pdo->prepare("SELECT id FROM menu_items WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $existing_items = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $submitted_item_ids = array_filter($item_ids, function($id) { return !empty($id); });

        // Delete items that are no longer present
        $items_to_delete = array_diff($existing_items, $submitted_item_ids);
        if (!empty($items_to_delete)) {
            $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id IN (" . implode(',', array_fill(0, count($items_to_delete), '?')) . ") AND category_id = ?");
            $stmt->execute([...$items_to_delete, $category_id]);
        }

        // Update or insert menu items
        foreach ($item_names as $item_index => $item_name) {
            $item_id = $item_ids[$item_index];
            $item_description = $item_descriptions[$item_index];
            $item_price = $item_prices[$item_index];
            $item_stock = $item_stocks[$item_index];

            // Handle item image
            $image_path = null;
            $file_key = "item_image_" . $index . "_" . $item_index;
            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/menu_items/';
                $image_name = 'item_' . uniqid() . '.' . pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
                $image_path = $upload_dir . $image_name;
                move_uploaded_file($_FILES[$file_key]['tmp_name'], $image_path);
            }

            if ($item_id) {
                // Update existing item
                $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, description = ?, price = ?, stock = ?, image_path = COALESCE(?, image_path) WHERE id = ? AND category_id = ?");
                $stmt->execute([$item_name, $item_description, $item_price, $item_stock, $image_path, $item_id, $category_id]);
            } else {
                // Insert new item
                $stmt = $pdo->prepare("INSERT INTO menu_items (category_id, name, description, price, image_path, stock) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$category_id, $item_name, $item_description, $item_price, $image_path, $item_stock]);
            }
        }
    }

    // Handle offers
    $offer_ids = $_POST['offer_id'] ?? [];
    $offer_descriptions = $_POST['offer_description'] ?? [];
    $offer_valid_untils = $_POST['offer_valid_until'] ?? [];

    // Get existing offers
    $stmt = $pdo->prepare("SELECT id FROM offers WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $existing_offers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $submitted_offer_ids = array_filter($offer_ids, function($id) { return !empty($id); });

    // Delete offers that are no longer present
    $offers_to_delete = array_diff($existing_offers, $submitted_offer_ids);
    if (!empty($offers_to_delete)) {
        $stmt = $pdo->prepare("DELETE FROM offers WHERE id IN (" . implode(',', array_fill(0, count($offers_to_delete), '?')) . ") AND restaurant_id = ?");
        $stmt->execute([...$offers_to_delete, $restaurant_id]);
    }

    // Update or insert offers
    foreach ($offer_descriptions as $index => $description) {
        $offer_id = $offer_ids[$index];
        $valid_until = $offer_valid_untils[$index] ?: null;

        if ($offer_id) {
            // Update existing offer
            $stmt = $pdo->prepare("UPDATE offers SET description = ?, valid_until = ? WHERE id = ? AND restaurant_id = ?");
            $stmt->execute([$description, $valid_until, $offer_id, $restaurant_id]);
        } else {
            // Insert new offer
            $stmt = $pdo->prepare("INSERT INTO offers (restaurant_id, description, valid_until) VALUES (?, ?, ?)");
            $stmt->execute([$restaurant_id, $description, $valid_until]);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in owner_update_restaurant.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>