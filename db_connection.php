<?php
$host = 'localhost';
$dbname = 'restaurant_management_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //PDO::ATTR_ERRMODE → এটি PDO-র একটি অ্যাট্রিবিউট (বৈশিষ্ট্য), যা বলে PDO কীভাবে এরর হ্যান্ডল করবে।
                //PDO::ERRMODE_EXCEPTION → এই মানটি সেট করলে PDO কোনো এরর হলে exception (ব্যতিক্রম/ত্রুটি) ছুড়ে দেয়, যেটি try-catch ব্লক দিয়ে ধরা যায়।

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>