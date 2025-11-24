<?php
class Session {
    public static function start() {
        session_start();
    }

    public static function manage() {
        if (isset($_SESSION['user'])) {
            if (isset($_COOKIE['last_activity']) && (time() - $_COOKIE['last_activity']) > INACTIVITY_TIMEOUT) {
                $_SESSION = [];
                session_destroy();
                setcookie('remember_me', '', time() - 3600, '/');
                header('Location: /index.php?timeout=1');
                exit();
            }
            setcookie('last_activity', time(), time() + INACTIVITY_TIMEOUT, '/');
        }
    }

    public static function set_defaults() {
        if (!isset($_SESSION['lang'])) {
            $_SESSION['lang'] = $_COOKIE['lang'] ?? 'ru';
        }
        if (!isset($_SESSION['theme'])) {
            $_SESSION['theme'] = $_COOKIE['theme'] ?? 'light';
        }
    }
}
