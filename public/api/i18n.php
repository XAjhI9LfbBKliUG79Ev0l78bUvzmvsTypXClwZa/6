<?php
require_once __DIR__ . '/../../src/init.php';
require_once __DIR__ . '/../../src/i18n.php';
header('Content-Type: application/json');

global $translations;
$lang = $_SESSION['lang'] ?? 'ru';

echo json_encode($translations[$lang]);
