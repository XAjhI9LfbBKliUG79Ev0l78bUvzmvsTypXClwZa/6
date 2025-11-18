<?php
require_once 'news.php';

header('Content-Type: application/json; charset=utf-8');

echo json_encode($news_posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
