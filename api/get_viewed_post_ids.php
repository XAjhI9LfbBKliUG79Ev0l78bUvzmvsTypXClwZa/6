<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/models/user.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit();
}

$user = User::find_by_login($_SESSION['user']['login']);

if ($user) {
    echo json_encode($user->viewed_posts);
} else {
    http_response_code(401);
    exit();
}
