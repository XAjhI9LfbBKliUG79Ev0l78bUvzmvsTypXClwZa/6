<?php
header('Content-Type: application/json; charset=utf-8');

$posts_dir = __DIR__ . '/../posts';
$files = scandir($posts_dir, SCANDIR_SORT_DESCENDING);

$posts = [];
foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }
    $filepath = $posts_dir . '/' . $file;
    $data = file_get_contents($filepath);
    $post_data = explode(';', $data, 5);

    if (count($post_data) !== 5) {
        continue;
    }

    list($uuid, $title, $content, $post_time, $viewed_state) = $post_data;

    if (empty($uuid) || empty($title) || empty($content) || empty($post_time)) {
        continue;
    }

    $posts[] = [
        'id' => $uuid,
        'title' => $title,
        'content' => $content,
        'published_at' => $post_time,
        'viewed' => (bool)$viewed_state
    ];
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$offset = ($page - 1) * $limit;

$paginated_posts = array_slice($posts, $offset, $limit);

echo json_encode($paginated_posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
