<?php
require_once __DIR__ . '/../src/init.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$page_title = __('feed_title');
$active_page = 'feed';

$viewed_posts_ids = isset($_COOKIE['viewed_posts']) ? explode(',', $_COOKIE['viewed_posts']) : [];
$viewed_posts = [];
if(!empty($viewed_posts_ids)) {
    $posts_dir = __DIR__ . '/../posts';
    $files = scandir($posts_dir, SCANDIR_SORT_DESCENDING);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filepath = $posts_dir . '/' . $file;
        $data = file_get_contents($filepath);
        list($uuid, $title, $content, $post_time, $viewed_state) = explode(';', $data, 5);
        if(in_array($uuid, $viewed_posts_ids)){
            $viewed_posts[] = [
                'id' => $uuid,
                'title' => $title,
            ];
        }
    }
}


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

        <?php if (!empty($viewed_posts)): ?>
        <aside class="viewed-posts">
            <h2>Viewed Posts</h2>
            <ul>
                <?php foreach ($viewed_posts as $post): ?>
                    <li><?= htmlspecialchars($post['title']) ?></li>
                <?php endforeach; ?>
            </ul>
        </aside>
        <?php endif; ?>
    </div>

</main>
<script src="/assets/js/feed.js"></script>
</body>
</html>
