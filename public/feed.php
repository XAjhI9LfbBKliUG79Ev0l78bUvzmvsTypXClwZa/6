<?php
require_once __DIR__ . '/../src/init.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$views = isset($_COOKIE['feed_views']) ? (int)$_COOKIE['feed_views'] : 0;
$views++;
setcookie('feed_views', $views, time() + 3600 * 24 * 365, '/');

$show_profile_prompt = $_SESSION['show_profile_prompt'] ?? false;
unset($_SESSION['show_profile_prompt']);

$page_title = __('feed_title');
$active_page = 'feed';
require __DIR__ . '/../src/templates/header.php';
?>
<main class="main-container">
    <h1><?= __('feed_header') ?></h1>

    <?php if ($show_profile_prompt): ?>
        <div class="message info">Регистрация завершена! Предлагаем вам <a href="profile.php">заполнить данные своего профиля</a>.</div>
    <?php endif; ?>

    <div class="feed-content">
        <div class="message">
            <p><?= __('page_views') ?> <?= $views ?> <?= __('times_word') ?></p>
            <a href="feed.php"><?= __('update_button') ?></a>
        </div>

    </div>
</main>
<script src="/assets/js/feed.js"></script>
</body>
</html>
