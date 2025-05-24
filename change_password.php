<?php require 'check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Restaurant Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="static/css/Change_password.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="change-password-container">
        <div class="change-password-card">
            <h2><i class="fas fa-key"></i> Change Password</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    $error = htmlspecialchars($_GET['error']);
                    if ($error === 'current_password_incorrect') {
                        echo 'Your current password is incorrect.';
                    } elseif ($error === 'new_password_mismatch') {
                        echo 'New passwords do not match.';
                    } elseif ($error === 'password_requirements') {
                        echo 'Password must be at least 8 characters long and contain at least one number and one special character.';
                    } else {
                        echo 'An error occurred. Please try again.';
                    }
                    ?>
                </div>
            <?php elseif (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    Password changed successfully!
                </div>
            <?php endif; ?>
            
            <form action="process_change_password.php" method="POST" id="change-password-form">
                <div class="form-group">
                    <label for="current_password"><i class="fas fa-lock"></i> Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('current_password')"></i>
                </div>
                
                <div class="form-group">
                    <label for="new_password"><i class="fas fa-key"></i> New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i>
                    <div class="password-hints">
                        <p id="length" class="invalid">At least 8 characters</p>
                        <p id="number" class="invalid">Contains a number</p>
                        <p id="special" class="invalid">Contains a special character</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-key"></i> Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
                    <span id="password-match-message"></span>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="window.location.href='profile.php'">Cancel</button>
                    <button type="submit" class="submit-btn">Change Password</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const icon = passwordField.nextElementSibling;
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Password validation
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        const length = document.getElementById('length');
        const number = document.getElementById('number');
        const special = document.getElementById('special');
        const matchMessage = document.getElementById('password-match-message');
        
        newPassword.addEventListener('input', function() {
            // Validate length
            if (newPassword.value.length >= 8) {
                length.classList.remove('invalid');
                length.classList.add('valid');
            } else {
                length.classList.remove('valid');
                length.classList.add('invalid');
            }
            
            // Validate numbers
            if (/\d/.test(newPassword.value)) {
                number.classList.remove('invalid');
                number.classList.add('valid');
            } else {
                number.classList.remove('valid');
                number.classList.add('invalid');
            }
            
            // Validate special characters
            if (/[!@#$%^&*(),.?":{}|<>]/.test(newPassword.value)) {
                special.classList.remove('invalid');
                special.classList.add('valid');
            } else {
                special.classList.remove('valid');
                special.classList.add('invalid');
            }
        });
        
        confirmPassword.addEventListener('input', function() {
            if (confirmPassword.value === newPassword.value && newPassword.value !== '') {
                matchMessage.textContent = 'Passwords match!';
                matchMessage.style.color = 'green';
            } else {
                matchMessage.textContent = 'Passwords do not match!';
                matchMessage.style.color = 'red';
            }
        });
        
        document.getElementById('change-password-form').addEventListener('submit', function(e) {
            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                alert('New passwords do not match!');
                return;
            }
            
            if (newPassword.value.length < 8 || 
                !/\d/.test(newPassword.value) || 
                !/[!@#$%^&*(),.?":{}|<>]/.test(newPassword.value)) {
                e.preventDefault();
                alert('Password must be at least 8 characters long and contain at least one number and one special character.');
                return;
            }
        });
    </script>
</body>
</html>