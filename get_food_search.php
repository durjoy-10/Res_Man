<?php
require 'db_connection.php';

header('Content-Type: application/json');

$query = $_GET['query'] ?? '';

if (empty($query)) {
    echo json_encode([]);
    exit;
}

$searchQuery = '%' . $query . '%';

$stmt = $pdo->prepare("
    SELECT m.*, r.name AS restaurant_name 
    FROM menu_items m
    JOIN restaurants r ON m.restaurant_id = r.id
    WHERE m.name LIKE ? OR m.description LIKE ?
");
$stmt->execute([$searchQuery, $searchQuery]);
$results = $stmt->fetchAll();

echo json_encode($results);
?>