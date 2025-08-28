<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth->logout();
redirect('index.php', 'You have been logged out successfully.', 'info');
?>