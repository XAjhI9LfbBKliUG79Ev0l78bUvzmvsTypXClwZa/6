<?php
header('Content-Type: application/json; charset=utf-8');

function lorem_ipsum($paragraphs = 1) {
    $base_text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
    $sentences = explode('. ', $base_text);
    $result = '';
    for ($i = 0; $i < $paragraphs; $i++) {
        shuffle($sentences);
        $result .= '<p>' . implode('. ', array_slice($sentences, 0, rand(3, 5))) . '.</p>';
    }
    return $result;
}

$count = isset($_GET['count']) ? (int)$_GET['count'] : 5;
if ($count > 10) {
    $count = 10;
}

$posts = [];

for ($i = 0; $i < $count; $i++) {
    $days_ago = rand(0, 2);
    $hours_ago = rand(0, 23);
    $minutes_ago = rand(0, 59);
    $seconds_ago = rand(0, 59);
    $timestamp = strtotime("-$days_ago days -$hours_ago hours -$minutes_ago minutes -$seconds_ago seconds");
    $date = date('Y-m-d H:i:s', $timestamp);

    $posts[] = [
        'id' => time() + $i,
        'title' => 'Random Post Title ' . uniqid(),
        'content' => lorem_ipsum(rand(2, 4)),
        'published_at' => $date
    ];
}

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
