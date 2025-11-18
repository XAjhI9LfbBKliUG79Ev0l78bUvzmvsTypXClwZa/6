<?php
require_once __DIR__ . '/../src/init.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['login'] !== 'admin') {
    header('Location: feed.php');
    exit();
}

function get_session_status_string() {
    $status = session_status();
    switch ($status) {
        case PHP_SESSION_DISABLED:
            return 'Отключены (PHP_SESSION_DISABLED)';
        case PHP_SESSION_NONE:
            return 'Нет активной сессии (PHP_SESSION_NONE)';
        case PHP_SESSION_ACTIVE:
            return 'Активна (PHP_SESSION_ACTIVE)';
        default:
            return 'Неизвестный статус';
    }
}

$page_title = __('admin_title');
$active_page = 'admin';
$current_view = $_GET['view'] ?? 'session';

require __DIR__ . '/../src/templates/header.php';
?>
<main class="main-container">
    <h1><?= __('admin_header') ?></h1>

    <nav class="admin-nav">
        <a href="admin.php?view=session" class="<?= ($current_view === 'session') ? 'active' : '' ?>"><?= __('session_info') ?></a>
        <a href="admin.php?view=cookies" class="<?= ($current_view === 'cookies') ? 'active' : '' ?>"><?= __('cookies_info') ?></a>
    </nav>

    <?php if ($current_view === 'session'): ?>
    <div class="admin-info">
        <h2><?= __('session_info') ?></h2>
        <div class="info-item">
            <strong>Имя сеанса:</strong>
            <p><?= htmlspecialchars(session_name()) ?></p>
        </div>
        <div class="info-item">
            <strong>Идентификатор сеанса:</strong>
            <p><?= htmlspecialchars(session_id()) ?></p>
        </div>
        <div class="info-item">
            <strong>Путь сохранения сеанса:</strong>
            <p><?= htmlspecialchars(session_save_path()) ?></p>
        </div>
        <div class="info-item">
            <strong>Обработчик сессий:</strong>
            <p><?= htmlspecialchars(session_module_name()) ?></p>
        </div>
        <div class="info-item">
            <strong>Текущий статус сессии:</strong>
            <p><?= get_session_status_string() ?></p>
        </div>
        <div class="info-item">
            <strong>Ограничитель кеширования:</strong>
            <p><?= htmlspecialchars(session_cache_limiter()) ?></p>
        </div>
        <div class="info-item">
            <strong>Срок действия кеша (в минутах):</strong>
            <p><?= htmlspecialchars(session_cache_expire()) ?></p>
        </div>
        <div class="info-item">
            <strong>Параметры cookie сессии:</strong>
            <pre><?php print_r(session_get_cookie_params()); ?></pre>
        </div>
        <div class="info-item">
            <strong>Содержимое сессии (переменные):</strong>
            <pre><?php print_r($_SESSION); ?></pre>
        </div>
        <div class="info-item">
            <strong>Закодированный сеанс:</strong>
            <p class="encoded-session"><?= htmlspecialchars(session_encode()) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($current_view === 'cookies'): ?>
    <div class="admin-info">
        <h2><?= __('cookies_info') ?></h2>
        <div class="info-item">
            <strong>Содержимое $_COOKIE:</strong>
            <pre><?php print_r($_COOKIE); ?></pre>
        </div>
    </div>
    <?php endif; ?>
</main>
</body>
</html>
