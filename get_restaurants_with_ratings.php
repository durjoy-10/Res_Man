<?php
require 'db_connection.php';

header('Content-Type: application/json');

$query = "SELECT r.id, r.name, 
          AVG(rr.rating) as avg_rating, 
          COUNT(rr.id) as review_count
          FROM restaurants r
          LEFT JOIN restaurant_reviews rr ON r.id = rr.restaurant_id
          GROUP BY r.id
          ORDER BY r.name";

$stmt = $pdo->query($query);
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($restaurants);
?>