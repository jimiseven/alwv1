<?php
require_once './config/config.php';

// Redirect based on session status
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: auth/login.php');
}
exit;