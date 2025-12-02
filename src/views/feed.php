<?php require __DIR__ . '/../templates/header.php'; ?>
<style>
    .post {
        margin-bottom: 20px;
    }
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

    </div>

</main>
<script>
    window.isUserLoggedIn = <?= isset($_SESSION['user']) ? 'true' : 'false' ?>;
</script>
<script src="/assets/js/feed.js"></script>
</body>
</html>
