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

$viewed_post_ids = [];
if ($user) {
    $viewed_post_ids = $user->viewed_posts;
} else {
    $viewed_post_ids = isset($_COOKIE['viewed_posts']) ? explode(',', $_COOKIE['viewed_posts']) : [];
}

$unread_posts = array_filter($all_posts, function ($post) use ($viewed_post_ids) {
    return !in_array($post['id'], $viewed_post_ids);
});

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$offset = ($page - 1) * $limit;

$paginated_posts = array_slice(array_values($unread_posts), $offset, $limit);

echo json_encode($paginated_posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
