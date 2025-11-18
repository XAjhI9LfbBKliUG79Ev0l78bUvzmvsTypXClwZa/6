<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/persistence.php';

if (isset($_COOKIE['remember_me'])) {
    $user = find_user_by_token($_COOKIE['remember_me']);
    if ($user) {
        $user['remember_token_hash'] = '';
        update_user($user['login'], $user);
    }
    setcookie('remember_me', '', time() - 3600, '/');
}

setcookie('last_activity', '', time() - 3600, '/');

$_SESSION = [];
session_destroy();
header('Location: index.php');
exit();
