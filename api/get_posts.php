<?php
require_once __DIR__ . '/../src/models/post.php';

header('Content-Type: application/json; charset=utf-8');

$posts = Post::get_all();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$offset = ($page - 1) * $limit;

$paginated_posts = array_slice($posts, $offset, $limit);

echo json_encode($paginated_posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
