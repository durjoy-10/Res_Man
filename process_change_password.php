<?php
require 'db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: change_password.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Validate inputs
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    header("Location: change_password.php?error=empty_fields");
    exit;
}

// Check if new passwords match
if ($new_password !== $confirm_password) {
    header("Location: change_password.php?error=new_password_mismatch");
    exit;
}

// Check password requirements
if (strlen($new_password) < 8 || !preg_match('/[0-9]/', $new_password) || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password)) {
    header("Location: change_password.php?error=password_requirements");
    exit;
}

// Get current password from database
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !password_verify($current_password, $user['password'])) {
    header("Location: change_password.php?error=current_password_incorrect");
    exit;
}

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password in database
$update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$update_stmt->execute([$hashed_password, $user_id]);

header("Location: change_password.php?success=1");
exit;
?>