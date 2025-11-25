<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/models/post.php';
require_once __DIR__ . '/../src/models/user.php';

header('Content-Type: application/json; charset=utf-8');

$user = null;
if (isset($_SESSION['user'])) {
    $user = User::find_by_login($_SESSION['user']['login']);
}

$all_posts = Post::get_all();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$offset = ($page - 1) * $limit;

$paginated_posts = array_slice($all_posts, $offset, $limit);

if ($user) {
    foreach ($paginated_posts as &$post) {
        if (in_array($post['id'], $user->viewed_posts)) {
            $post['viewed'] = true;
        }
    }
    unset($post);
} else {
    $viewed_posts = isset($_COOKIE['viewed_posts']) ? explode(',', $_COOKIE['viewed_posts']) : [];
    foreach ($paginated_posts as &$post) {
        if (in_array($post['id'], $viewed_posts)) {
            $post['viewed'] = true;
        }
    }
    unset($post);
}

echo json_encode($paginated_posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
