<?php
session_start();
if (isset($_POST['redirect'])) {
    $_SESSION['redirect_after_login'] = $_POST['redirect'];
}
?>