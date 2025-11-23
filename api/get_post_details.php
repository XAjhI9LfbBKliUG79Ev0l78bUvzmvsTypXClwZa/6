<?php
header('Content-Type: application/json; charset=utf-8');

$post_ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];

if (empty($post_ids)) {
    echo json_encode([]);
    exit();
}

$posts_dir = __DIR__ . '/../posts';
$files = scandir($posts_dir);

$found_posts = [];
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

    if (in_array($uuid, $post_ids)) {
        $found_posts[$uuid] = [
            'id' => $uuid,
            'title' => $title,
        ];
    }
}

$sorted_posts = [];
foreach ($post_ids as $id) {
    if (isset($found_posts[$id])) {
        $sorted_posts[] = $found_posts[$id];
    }
}

echo json_encode($sorted_posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
