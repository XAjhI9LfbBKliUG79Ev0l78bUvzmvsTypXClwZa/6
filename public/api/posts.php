<?php
header('Content-Type: application/json');

function generate_random_text($length = 100) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 .,!?';
    $text = '';
    for ($i = 0; $i < $length; $i++) {
        $text .= $characters[rand(0, strlen($characters) - 1)];
    }
    return ucfirst(strtolower($text));
}

function get_post_filepath($id) {
    return __DIR__ . '/../../posts/' . $id . '.json';
}

function generate_post() {
    $id = uniqid();
    $post = [
        'id' => $id,
        'title' => 'Random Post ' . $id,
        'content' => generate_random_text(rand(200, 500)),
        'published_at' => date('Y-m-d H:i:s', time() - rand(0, 3 * 24 * 3600)),
    ];

    file_put_contents(get_post_filepath($id), json_encode($post));

    return $post;
}

$sort_order = $_GET['sort'] ?? 'newest';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 5;

$posts_dir = __DIR__ . '/../../posts/';
if (!is_dir($posts_dir)) {
    mkdir($posts_dir, 0755, true);
}

$post_files = glob($posts_dir . '*.json');

if (count($post_files) < $offset + $limit) {
    for ($i = 0; $i < ($offset + $limit) - count($post_files); $i++) {
        generate_post();
    }
    $post_files = glob($posts_dir . '*.json');
}

$posts = array_map(function ($file) {
    return json_decode(file_get_contents($file), true);
}, $post_files);

usort($posts, function ($a, $b) use ($sort_order) {
    if ($sort_order === 'oldest') {
        return strtotime($a['published_at']) - strtotime($b['published_at']);
    }
    return strtotime($b['published_at']) - strtotime($a['published_at']);
});

$viewed_posts = isset($_COOKIE['viewed_posts']) ? json_decode($_COOKIE['viewed_posts'], true) : [];
$posts = array_filter($posts, function ($post) use ($viewed_posts) {
    return !in_array($post['id'], $viewed_posts);
});

$paginated_posts = array_slice($posts, $offset, $limit);

echo json_encode($paginated_posts);
