<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/models/user.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['postId'] ?? null;

if (!$post_id) {
    http_response_code(400);
    exit();
}

$user = User::find_by_login($_SESSION['user']['login']);

if ($user) {
    // Remove the post ID if it already exists to avoid duplicates
    $user->viewed_posts = array_diff($user->viewed_posts, [$post_id]);
    // Prepend the new post ID to the beginning of the array
    array_unshift($user->viewed_posts, $post_id);
    // Enforce a limit on the number of viewed posts
    $user->viewed_posts = array_slice($user->viewed_posts, 0, 10);
    $user->save();
}
