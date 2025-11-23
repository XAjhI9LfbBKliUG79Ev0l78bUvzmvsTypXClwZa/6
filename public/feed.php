<?php
require_once __DIR__ . '/../src/init.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$page_title = __('feed_title');
$active_page = 'feed';

require __DIR__ . '/../src/templates/header.php';
?>
<style>
    .post.viewed {
        opacity: 0.5;
    }
    .viewed-posts {
        margin-top: 20px;
        padding: 10px;
        border: 1px solid #ccc;
    }
</style>
<main class="main-container">
    <h1><?= __('feed_header') ?></h1>

    <div class="feed-wrapper">
        <div class="feed-content">
            <!-- Posts will be loaded here by JavaScript -->
        </div>

        <aside class="viewed-posts">
            <h2>Viewed Posts</h2>
            <ul>
                <!-- Viewed posts will be loaded here by JavaScript -->
            </ul>
        </aside>
    </div>

</main>
<script src="/assets/js/feed.js"></script>
</body>
</html>
