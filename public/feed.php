<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/controllers/feed_controller.php';

handle_feed_request();

$page_title = __('feed_title');
$active_page = 'feed';

require_once __DIR__ . '/../src/views/feed.php';
