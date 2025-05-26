<?php 
require 'check_auth.php';
require 'db_connection.php';

// Get user details
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    
    // Handle profile photo upload
    $profile_photo = $user['profile_photo']; // Keep existing if no new upload
    
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'static/uploads/profile_photos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExt = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $fileName = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExt;
        $targetPath = $uploadDir . $fileName;
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES['profile_photo']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetPath)) {
                // Delete old profile photo if it exists
                if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])) {
                    unlink($user['profile_photo']);
                }
                $profile_photo = $targetPath;
            }
        }
    }
    
    // Update user in database
    $update_stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, profile_photo = ? WHERE id = ?");
    $update_stmt->execute([$name, $email, $phone, $address, $profile_photo, $_SESSION['user_id']]);
    
    // Refresh user data
    $user_stmt->execute([$_SESSION['user_id']]);
    $user = $user_stmt->fetch();
    
    $success_message = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Restaurant Management System</title>
    <link rel="stylesheet" href="static/css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="profile-container">
            <h2>My Profile</h2>
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="profile-form">
                <div class="profile-photo-section">
                    <div class="profile-photo-container">
                        <?php if (!empty($user['profile_photo'])): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" 
                                 alt="Profile Photo" 
                                 class="profile-photo"
                                 onerror="this.onerror=null;this.src='static/media/default-profile.png'">
                        <?php else: ?>
                            <div class="default-profile-photo">
                                <i class="fas fa-user-circle"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="photo-upload">
                        <label for="profile-photo-input" class="upload-btn">
                            <i class="fas fa-camera"></i> Change Photo
                        </label>
                        <input type="file" id="profile-photo-input" name="profile_photo" accept="image/*" style="display: none;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="save-btn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Preview profile photo before upload
        document.getElementById('profile-photo-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const profilePhotoContainer = document.querySelector('.profile-photo-container');
                    
                    // Remove default icon if it exists
                    const defaultPhoto = profilePhotoContainer.querySelector('.default-profile-photo');
                    if (defaultPhoto) {
                        defaultPhoto.remove();
                    }
                    
                    // Remove existing image if it exists
                    const existingImg = profilePhotoContainer.querySelector('img');
                    if (existingImg) {
                        existingImg.remove();
                    }
                    
                    // Create new image element
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.className = 'profile-photo';
                    profilePhotoContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>