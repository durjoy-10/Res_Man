<?php
session_start();

// This is a soft check - doesn't redirect if not logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
?>