<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/controllers/auth_controller.php';

if (isset($_SESSION['user'])) {
    header('Location: feed.php');
    exit();
}

$error_message = handle_register_request();

$page_title = __('register_title');

require_once __DIR__ . '/../src/views/register.php';
