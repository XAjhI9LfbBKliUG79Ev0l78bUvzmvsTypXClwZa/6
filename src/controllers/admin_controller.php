<?php

function handle_admin_request() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['login'] !== 'admin') {
        header('Location: feed.php');
        exit();
    }
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
