<?php
$user = $_SESSION['user'] ?? null;
if ($user) {
    $profile_image = $user['profile_image'] ?: 'assets/default-profile.png';
}
$theme_class = $_SESSION['theme'] ?? 'light';
$current_lang = $_SESSION['lang'] ?? 'ru';
$last_visited = $_COOKIE['last_visited'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_lang) ?>" class="theme-<?= htmlspecialchars($theme_class) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? __('app_title')) ?></title>
<link rel="stylesheet" href="/style.css">
</head>
<body>
<?php if ($user): ?>
<header class="navbar">
    <div class="container">
        <a href="/feed.php" class="navbar-brand"><?= __('app_title') ?></a>
        <nav class="nav-links">
            <a href="/feed.php" class="<?= ($active_page === 'feed') ? 'active' : '' ?>"><?= __('feed_link') ?></a>
            <a href="/about.php" class="<?= ($active_page === 'about') ? 'active' : '' ?>"><?= __('about_link') ?></a>
            <?php if ($user['login'] === 'admin'): ?>
                <a href="/admin.php" class="<?= ($active_page === 'admin') ? 'active' : '' ?>"><?= __('admin_panel_link') ?></a>
            <?php endif; ?>
        </nav>

        <form action="/settings_handler.php" method="POST" class="settings-form" onchange="this.submit()">
            <select name="lang" id="lang-select">
                <option value="ru" <?= $current_lang === 'ru' ? 'selected' : '' ?>>Русский</option>
                <option value="en" <?= $current_lang === 'en' ? 'selected' : '' ?>>English</option>
            </select>
            <select name="theme" id="theme-select">
                <option value="light" <?= $theme_class === 'light' ? 'selected' : '' ?>><?= __('light_theme') ?></option>
                <option value="dark" <?= $theme_class === 'dark' ? 'selected' : '' ?>><?= __('dark_theme') ?></option>
            </select>
        </form>

        <div class="user-info">
            <div class="user-details">
                <span><?= __('greeting') ?>, <?= htmlspecialchars($user['login']) ?>!</span>
                <?php if ($last_visited): ?>
                    <small class="last-visited"><?= __('last_visited') ?>: <?= htmlspecialchars($last_visited) ?></small>
                <?php endif; ?>
            </div>
            <img src="/<?= htmlspecialchars($profile_image) ?>" alt="Аватар" class="user-avatar">
            <a href="/profile.php" class="settings-link <?= ($active_page === 'profile') ? 'active' : '' ?>" title="<?= __('profile_settings_link') ?>">
                <img src="/assets/gear.svg" alt="Настройки">
            </a>
            <a href="/logout.php" class="logout-link"><?= __('logout_link') ?></a>
        </div>
    </div>
</header>
<?php endif; ?>
