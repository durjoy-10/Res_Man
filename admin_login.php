<?php
header('Content-Type: application/json');   //এটি ক্লায়েন্ট (যেমন: JavaScript) বুঝিয়ে দেয় যে JSON ডেটা পাঠানো হচ্ছে।

// Simple database config
$db_host = 'localhost';
$db_name = 'restaurant_management_system';
$db_user = 'root';
$db_pass = '';

// Get input
$data = json_decode(file_get_contents('php://input'), true);  //POST রিকোয়েস্ট থেকে JSON ডেটা পড়া হয়।
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);   //PHP Data Objects
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  //PDO ব্যবহার করে ডাটাবেজে সংযোগ করা হচ্ছে।

    // Check admin credentials
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]); //admins টেবিল থেকে ইউজারনেম মিলে কিনা তা খোঁজা হচ্ছে।
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit;
    }

    // Check if super admin
    if (!$admin['is_super_admin']) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}