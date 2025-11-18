<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['id'] ?? null;

if (!$post_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Post ID is required']);
    exit();
}

$viewed_posts = isset($_COOKIE['viewed_posts']) ? json_decode($_COOKIE['viewed_posts'], true) : [];
if (!in_array($post_id, $viewed_posts)) {
    $viewed_posts[] = $post_id;
}

setcookie('viewed_posts', json_encode($viewed_posts), time() + (86400 * 30), "/"); // 30 days

echo json_encode(['success' => true, 'viewed_posts' => $viewed_posts]);
