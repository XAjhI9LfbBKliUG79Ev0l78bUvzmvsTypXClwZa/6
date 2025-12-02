<?php
require_once __DIR__ . '/../bootstrap.php';

require_once __DIR__ . '/../models/user.php';

header('Content-Type: application/json; charset=utf-8');

$user = null;
if (isset($_SESSION['user'])) {
    $user = User::find_by_login($_SESSION['user']['login']);
}
