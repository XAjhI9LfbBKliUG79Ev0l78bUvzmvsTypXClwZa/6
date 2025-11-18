<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['lang']) && in_array($_POST['lang'], ['ru', 'en'])) {
        $_SESSION['lang'] = $_POST['lang'];
        setcookie('lang', $_POST['lang'], time() + 3600 * 24 * 365, '/');
    }

    if (isset($_POST['theme']) && in_array($_POST['theme'], ['light', 'dark'])) {
        $_SESSION['theme'] = $_POST['theme'];
        setcookie('theme', $_POST['theme'], time() + 3600 * 24 * 365, '/');
    }
}

$referrer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: " . $referrer);
exit();
