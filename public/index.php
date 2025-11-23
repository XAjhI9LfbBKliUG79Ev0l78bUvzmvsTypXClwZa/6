<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/controllers/auth_controller.php';
require_once __DIR__ . '/../src/models/user.php';

if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    $user = User::find_by_token($_COOKIE['remember_me']);
    if ($user) {
        $_SESSION['user'] = (array)$user;
        setcookie('last_activity', time(), time() + INACTIVITY_TIMEOUT, '/');
        header('Location: feed.php');
        exit();
    }
}

if (isset($_SESSION['user'])) {
    header('Location: feed.php');
    exit();
}

$error_message = handle_login_request();
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

if (isset($_GET['timeout'])) {
    $error_message = 'Ваша сессия истекла из-за неактивности. Пожалуйста, войдите снова.';
}

$page_title = __('login_title');

require_once __DIR__ . '/../src/views/login.php';
