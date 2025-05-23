<?php 
require 'check_auth.php';
require 'db_connection.php';

// Get user details
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? $user['name'];
    $email = $_POST['email'] ?? $user['email'];
    $phone = $_POST['phone'] ?? $user['phone'];
    $address = $_POST['address'] ?? $user['address'];
    
    try {
        $update_stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $update_stmt->execute([$name, $email, $phone, $address, $_SESSION['user_id']]);
        
        $_SESSION['success_message'] = 'Profile updated successfully!';
        header('Location: profile.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Error updating profile: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="static/css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php require 'header.php'; ?>
    
    <div class="container">
        <h2>My Profile</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert error">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="profile-form">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea name="address"><?= htmlspecialchars($user['address']) ?></textarea>
            </div>
            
            <button type="submit" class="btn">Update Profile</button>
        </form>
        
        <div class="profile-actions">
            <a href="change_password.php" class="btn secondary">
                <i class="fas fa-key"></i> Change Password
            </a>
        </div>
    </div>
</body>
</html>