<?php
require 'db_connection.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM restaurants ORDER BY id DESC");
$restaurants = $stmt->fetchAll();

echo json_encode($restaurants);
?>