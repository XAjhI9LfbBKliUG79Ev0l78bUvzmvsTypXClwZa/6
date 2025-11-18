<?php
require_once __DIR__ . '/../src/init.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$views = isset($_COOKIE['about_views']) ? (int)$_COOKIE['about_views'] : 0;
$views++;
setcookie('about_views', $views, time() + 3600 * 24 * 365, '/');

$page_title = __('about_title');
$active_page = 'about';
require __DIR__ . '/../src/templates/header.php';
?>
<main class="main-container">
    <h1><?= __('about_header') ?></h1>

    <div class="message">
        <p><?= __('page_views') ?> <?= $views ?> <?= __('times_word') ?></p>
        <a href="about.php"><?= __('update_button') ?></a>
    </div>

    <div class="about-content">
        <p>Добро пожаловать на наш сайт "Одногрупнички"!</p>
    </div>
</main>
</body>
</html>
