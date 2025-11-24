<?php
require_once __DIR__ . '/../src/models/post.php';

header('Content-Type: application/json; charset=utf-8');

$post_ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];

$posts = Post::get_by_ids($post_ids);

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
